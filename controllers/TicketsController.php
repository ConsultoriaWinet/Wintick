<?php

namespace app\controllers;

use app\models\Tickets;
use app\models\TicketsSearch;
use app\models\Clientes;
use app\models\Sistemas;
use app\models\Servicios;
use app\models\Usuarios;
use app\models\Notificaciones;
use app\models\Comentarios;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use Yii;
use yii\filters\AccessControl;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use yii\web\Response;



/**
 * TicketsController implements the CRUD actions for Tickets model.
 */
class TicketsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Tickets models.
     *
     * @return string
     */
     public function actionIndex()
    {
        $searchModel = new TicketsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        

        $clientes = Clientes::find()->asArray()->all();
        $sistemas = Sistemas::find()->asArray()->all();
        $servicios = Servicios::find()->asArray()->all();
        $Usuarios = Usuarios::find()->asArray()->all();

        $query = Tickets::find();

        // Filtro automático para Consultores
        $currentUser = Yii::$app->user->identity;
        if ($currentUser && $currentUser->rol=== 'Consultor') {
            if (empty($_GET['asignado_a']) && empty($_GET['folio']) && empty($_GET['cliente_id']) && 
                empty($_GET['sistema_id']) && empty($_GET['servicio_id']) && empty($_GET['prioridad']) && 
                empty($_GET['estado']) && empty($_GET['mes'])) {
                $query->andWhere(['Asignado_a' => $currentUser->id]);
                // Mostrar solo del mes actual
                $mesActual = date('Y-m');
                $query->andWhere(['>=', 'Fecha_creacion', $mesActual . '-01 00:00:00']);
                $query->andWhere(['<', 'Fecha_creacion', date('Y-m-01 00:00:00', strtotime('+1 month'))]);
            }
        }

        if (!empty($_GET['folio'])) {
            $query->andWhere(['like', 'Folio', $_GET['folio']]);
        }
        if (!empty($_GET['cliente_id'])) {
            $query->andWhere(['Cliente_id' => $_GET['cliente_id']]);
        }
        if (!empty($_GET['sistema_id'])) {
            $query->andWhere(['Sistema_id' => $_GET['sistema_id']]);
        }
        if (!empty($_GET['servicio_id'])) {
            $query->andWhere(['Servicio_id' => $_GET['servicio_id']]);
        }
        if (!empty($_GET['asignado_a'])) {
            $query->andWhere(['Asignado_a' => $_GET['asignado_a']]);
        }
        if (!empty($_GET['prioridad'])) {
            $query->andWhere(['Prioridad' => $_GET['prioridad']]);
        }
        if (!empty($_GET['estado'])) {
            $query->andWhere(['Estado' => $_GET['estado']]);
        }

        
        // Filtro por mes
        if (!empty($_GET['mes'])) {
            $mes = $_GET['mes'];
            $primerDia = $mes . '-01 00:00:00';
            $ultimoDia = date('Y-m-t 23:59:59', strtotime($mes . '-01'));
            $query->andWhere(['>=', 'Fecha_creacion', $primerDia]);
            $query->andWhere(['<=', 'Fecha_creacion', $ultimoDia]);
        } else {
            // Si no hay filtro de mes, mostrar mes actual por defecto
            $mesActual = date('Y-m');
            $primerDia = $mesActual . '-01 00:00:00';
            $ultimoDia = date('Y-m-t 23:59:59');
            $query->andWhere(['>=', 'Fecha_creacion', $primerDia]);
            $query->andWhere(['<=', 'Fecha_creacion', $ultimoDia]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['id' => SORT_DESC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientes' => $clientes,
            'sistemas' => $sistemas,
            'servicios' => $servicios,
            'Usuarios' => $Usuarios,
        ]);
    }

    /**
     * Displays a single Tickets model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Tickets model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Tickets();
        $model->Folio = str_pad(Tickets::find()->max('id') + 1, 4, '0', STR_PAD_LEFT);
        
        // CORRECCIÓN: Seleccionar 'id' y 'email', y mapear id => email
        $consultores = \app\models\Usuarios::find()
            ->select(['id', 'email']) // Necesitamos el ID para guardarlo
            ->where(['rol' => 'consultor'])
            ->asArray()
            ->all();
            
        // El primer parámetro es la clave (lo que se guarda: id), el segundo es el valor (lo que se ve: email)
        $consultoresList = \yii\helpers\ArrayHelper::map($consultores, 'id', 'email');
        $model->consultoresList = $consultoresList;
        
        // Obtener fecha del POST si existe (viene del calendario)
        $fechaSeleccionada = Yii::$app->request->post('fecha_seleccionada');
        if ($fechaSeleccionada) {
            // Convertir fecha del calendario (YYYY-MM-DD) a formato datetime
            $model->Fecha_creacion = $fechaSeleccionada . ' ' . date('H:i:s');
            $model->Fecha_actualizacion = $fechaSeleccionada . ' ' . date('H:i:s');
            
            // Guardar en sesión para mostrar mensaje
            Yii::$app->session->setFlash('fechaDesdeCalendario', $fechaSeleccionada);
        } else {
            // Si no hay fecha en POST, usar fecha actual
            $model->Fecha_creacion = date('Y-m-d H:i:s');
            $model->Fecha_actualizacion = date('Y-m-d H:i:s');
        }
        
        $model->Estado = 'Abierto';
        
        if ($this->request->isPost && $model->load($this->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Ticket creado exitosamente.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'consultoresList'=> $consultoresList,
        ]);
    }

    /**
     * Updates an existing Tickets model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Tickets model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Tickets model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Tickets the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tickets::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetNextFolio()
    {
        // Importante: Asegúrate de tener "use yii\web\Response;" arriba
        Yii::$app->response->format = Response::FORMAT_JSON;

        $ultimoTicket = Tickets::find()->orderBy(['id' => SORT_DESC])->one();
        
        // Calculamos siguiente ID
        $siguienteId = $ultimoTicket ? ($ultimoTicket->id + 1) : 1;

        // Formato: 0005 (Igual que en tu actionCreate)
        $folio = str_pad($siguienteId, 4, '0', STR_PAD_LEFT);

        return [
            'nextFolio' => $folio
        ];
    }

    
    public function actionSaveBulk()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $input = \Yii::$app->request->getRawBody();
        $data = json_decode($input, true);
        $tickets = $data['tickets'] ?? [];
        
        try {
            $usuarioActual = \Yii::$app->user->identity->email;
            foreach ($tickets as $ticketData) {
                $ticket = new Tickets();
                $ticket->Folio = $ticketData['Folio'] ?? null;
                $ticket->Cliente_id = $ticketData['Cliente_id'] ?? null;
                $ticket->Sistema_id = $ticketData['Sistema_id'] ?? null;
                $ticket->Servicio_id = $ticketData['Servicio_id'] ?? null;
                $ticket->Usuario_reporta = $ticketData['Usuario_reporta'] ?? null;
                $ticket->Asignado_a = $ticketData['Asignado_a'] ?? null;
                $ticket->Descripcion = $ticketData['Descripcion'] ?? null;
                $ticket->Prioridad = $ticketData['Prioridad'] ?? null;
                $ticket->Estado = $ticketData['Estado'] ?? 'ABIERTO';
                $ticket->HoraProgramada = $ticketData['HoraProgramada'] ?? null;
                $ticket->HoraInicio = $ticketData['HoraInicio'] ?? null;
                $ticket->Creado_por = \Yii::$app->user->id;
                
                if (!$ticket->save()) {
                    return ['success' => false, 'errors' => $ticket->errors];
                }
                
                // ✅ CREAR NOTIFICACIÓN CUANDO SE ASIGNA
                if ($ticket->Asignado_a) {
                    $this->crearNotificacion(
                        $ticket->Asignado_a,
                        'asignado',
                        'Nuevo ticket asignado: ' . $ticket->Folio,
                        $usuarioActual . ' te asignó un nuevo ticket',
                        $ticket->id
                    );
                }
            }
            
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function actionUpdateEstado()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $input = \Yii::$app->request->getRawBody();
        $data = json_decode($input, true);
        
        $ticket = Tickets::findOne($data['id']);
        if ($ticket) {
            $estadoAnterior = $ticket->Estado;
            $ticket->Estado = $data['estado'];
            
            if ($ticket->save()) {
                // ✅ CREAR NOTIFICACIÓN SI CAMBIÓ EL ESTADO
                if ($estadoAnterior !== $ticket->Estado && $ticket->Asignado_a) {
                    $usuarioActual = \Yii::$app->user->identity->email;
                    $this->crearNotificacion(
                        $ticket->Asignado_a,
                        'estado_cambio',
                        'Cambio de estado: ' . $ticket->Folio,
                        $usuarioActual . ' cambió el estado a ' . $ticket->Estado,
                        $ticket->id
                    );
                }
                
                return ['success' => true];
            }
        }
        
        return ['success' => false];
    }

        public function actionGetTicketData()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            try {
                $input = \Yii::$app->request->getRawBody();
                $data = json_decode($input, true);
                
                $ticket = Tickets::findOne($data['id'] ?? null);
                if (!$ticket) {
                    return ['success' => false, 'message' => 'Ticket no encontrado'];
                }
                
                // Si HoraFinalizo es integer (timestamp)
                $horaFinalizo = '';
                if ($ticket->HoraFinalizo) {
                    if (is_numeric($ticket->HoraFinalizo)) {
                        $horaFinalizo = date('Y-m-d\TH:i', (int)$ticket->HoraFinalizo);
                    } else {
                        $horaFinalizo = date('Y-m-d\TH:i', strtotime($ticket->HoraFinalizo));
                    }
                }
                
                return [
                    'success' => true,
                    'ticket' => [
                        'HoraFinalizo' => $horaFinalizo,
                        'Solucion' => $ticket->Solucion ?? '',
                        'TiempoEfectivo' => $ticket->TiempoEfectivo ?? '',
                    ]
                ];
            } catch (\Exception $e) {
                return ['success' => false, 'error' => $e->getMessage()];
            }
        }

        public function actionSaveSolution()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            try {
                $input = \Yii::$app->request->getRawBody();
                $data = json_decode($input, true);
                
                $ticket = Tickets::findOne($data['id'] ?? null);
                if (!$ticket) {
                    return ['success' => false, 'message' => 'Ticket no encontrado'];
                }
                
                $ticket->Solucion = $data['solucion'] ?? null;
                $ticket->TiempoEfectivo = $data['tiempoEfectivo'] ?? null;
                
                // Convertir fecha HTML a timestamp
                if (!empty($data['horaFinalizo'])) {
                    $timestamp = strtotime($data['horaFinalizo']);
                    if ($timestamp === false) {
                        return ['success' => false, 'message' => 'Formato de fecha inválido'];
                    }
                    $ticket->HoraFinalizo = $timestamp;
                }
                
                if ($ticket->save()) {
                    return ['success' => true, 'message' => 'Solución guardada correctamente'];
                }
                
                return ['success' => false, 'errors' => $ticket->errors];
            } catch (\Exception $e) {
                return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
            }
        }

    /**
     * Obtener notificaciones del usuario actual
     */
    public function actionObtenerNotificaciones()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        try {
            $userId = \Yii::$app->user->id;
            
            if (!$userId) {
                return ['success' => false, 'message' => 'Usuario no autenticado', 'user_id' => null];
            }
            
            \Yii::error("DEBUG: Buscando notificaciones para usuario_id: $userId");
            
            $notificaciones = Notificaciones::find()
                ->where(['usuario_id' => $userId])
                ->orderBy(['fecha_creacion' => SORT_DESC])
                ->limit(10)
                ->all();
            
            \Yii::error("DEBUG: Total de notificaciones encontradas: " . count($notificaciones));
            
            $result = [];
            foreach ($notificaciones as $notif) {
                $result[] = [
                    'id' => $notif->id,
                    'tipo' => $notif->tipo ?? 'asignado',
                    'titulo' => $notif->titulo,
                    'mensaje' => $notif->mensaje,
                    'leida' => (bool)$notif->leida,
                    'fecha' => date('d/m H:i', strtotime($notif->fecha_creacion))
                ];
            }
            
            return ['success' => true, 'notificaciones' => $result];
        } catch (\Exception $e) {
            \Yii::error("ERROR en obtenerNotificaciones: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Marcar notificación como leída
     */
    public function actionMarcarNotificacion()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $input = \Yii::$app->request->getRawBody();
        $data = json_decode($input, true);
        
        if (empty($data['notif_id'])) {
            return ['success' => false, 'message' => 'ID de notificación requerido'];
        }
        
        $userId = \Yii::$app->user->id;
        $notif = Notificaciones::findOne([
            'id' => $data['notif_id'],
            'usuario_id' => $userId
        ]);
        
        if (!$notif) {
            return ['success' => false, 'message' => 'Notificación no encontrada'];
        }
        
        $notif->leida = 1;
        if ($notif->save()) {
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Error al marcar notificación'];
    }

    // ✅ NUEVO MÉTODO: Marcar todas como leídas
    public function actionMarcarTodasLeidas()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        try {
            $userId = \Yii::$app->user->id;
            
            Notificaciones::updateAll(
                ['leida' => 1],
                ['usuario_id' => $userId, 'leida' => 0]
            );
            
            return ['success' => true, 'message' => 'Todas las notificaciones marcadas como leídas'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Crear notificación (función privada)
     */
    private function crearNotificacion($usuario_id, $tipo, $titulo, $mensaje, $ticket_id = null)
    {
        try {
            if (!$usuario_id) {
                return false;
            }
            
            $notif = new Notificaciones();
            $notif->usuario_id = (int)$usuario_id;
            $notif->ticket_id = $ticket_id;
            $notif->tipo = $tipo;
            $notif->titulo = $titulo;
            $notif->mensaje = $mensaje;
            $notif->leida = 0;
            $notif->fecha_creacion = date('Y-m-d H:i:s');
            
            if (!$notif->save()) {
                \Yii::error("Error guardando notificación: " . json_encode($notif->errors));
                return false;
            }
            
            // 📧 ENVIAR CORREO si es tipo 'asignado' (DESPUÉS DE GUARDAR)
            if ($tipo === 'asignado' && $ticket_id) {
                $this->enviarCorreoAsignacion($usuario_id, $ticket_id);
            }
            
            return true; 
            
        } catch (\Exception $e) {
            \Yii::error("Excepción en crearNotificacion: " . $e->getMessage());
            return false;
        }
    }

          private function enviarCorreoAsignacion($usuario_id, $ticket_id)
    {
        try {
            // Obtener datos del usuario
            $usuario = Usuarios::findOne($usuario_id);
            if (!$usuario || !$usuario->email) {
                \Yii::error("Usuario sin email: $usuario_id");
                return false;
            }
            
            // Obtener datos del ticket
            $ticket = Tickets::findOne($ticket_id);
            if (!$ticket) {
                \Yii::error("Ticket no encontrado: $ticket_id");
                return false;
            }
            
            // Usuario que asignó
            $asignador = \Yii::$app->user->identity->email;
            
            // Cliente, Sistema, Servicio
            $clienteNombre = $ticket->cliente ? $ticket->cliente->Nombre : 'N/A';
            $sistemaNombre = $ticket->sistema ? $ticket->sistema->Nombre : 'N/A';
            $servicioNombre = $ticket->servicio ? $ticket->servicio->Nombre : 'N/A';
            
            // Enviar correo
            \Yii::$app->mailer->compose()
                ->setFrom(['arturo.villa.rey@gmail.com' => 'Sistema Wicontrol'])
                ->setTo($usuario->email)
                ->setSubject('🎫 Nuevo Ticket Asignado - Folio: ' . $ticket->Folio)
                ->setHtmlBody('
                    <div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff;">
                        <!-- Header -->
                        <div style="background: linear-gradient(135deg, #8BA590 0%, #6b8a70 100%); color: white; padding: 30px 20px; border-radius: 0; text-align: center;">
                            <h1 style="margin: 0; font-size: 26px; font-weight: 600; letter-spacing: -0.5px;">🎫 Nuevo Ticket Asignado</h1>
                        </div>
                        
                        <!-- Body -->
                        <div style="background: #f9fafb; padding: 30px 20px;">
                            <p style="margin: 0 0 20px; font-size: 16px; color: #000000; line-height: 1.6;">
                                Hola <strong style="color: #8BA590;">' . htmlspecialchars($usuario->email) . '</strong>,
                            </p>
                            
                            <p style="margin: 0 0 25px; color: #6b7280; font-size: 15px; line-height: 1.6;">
                                <strong style="color: #000000;">' . htmlspecialchars($asignador) . '</strong> te ha asignado un nuevo ticket que requiere tu atención.
                            </p>
                            
                            <!-- Ticket Details Card -->
                            <div style="background: #ffffff; padding: 20px; border-radius: 8px; margin: 25px 0; border: 2px solid #8BA590; box-shadow: 0 2px 8px rgba(139, 165, 144, 0.1);">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="padding: 12px 0; font-weight: 600; color: #000000; width: 35%; border-bottom: 1px solid #e5e7eb;">📋 Folio:</td>
                                        <td style="padding: 12px 0; color: #8BA590; border-bottom: 1px solid #e5e7eb;">
                                            <strong style="font-size: 18px; font-weight: 700;">' . htmlspecialchars($ticket->Folio) . '</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; font-weight: 600; color: #000000; border-bottom: 1px solid #e5e7eb;">👤 Cliente:</td>
                                        <td style="padding: 12px 0; color: #6b7280; border-bottom: 1px solid #e5e7eb;">' . htmlspecialchars($clienteNombre) . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; font-weight: 600; color: #000000; border-bottom: 1px solid #e5e7eb;">🖥️ Sistema:</td>
                                        <td style="padding: 12px 0; color: #6b7280; border-bottom: 1px solid #e5e7eb;">' . htmlspecialchars($sistemaNombre) . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; font-weight: 600; color: #000000; border-bottom: 1px solid #e5e7eb;">🔧 Servicio:</td>
                                        <td style="padding: 12px 0; color: #6b7280; border-bottom: 1px solid #e5e7eb;">' . htmlspecialchars($servicioNombre) . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; font-weight: 600; color: #000000; border-bottom: 1px solid #e5e7eb;">⚠️ Prioridad:</td>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                            <span style="background: ' . $this->getPrioridadColor($ticket->Prioridad) . '; color: white; padding: 6px 12px; border-radius: 16px; font-size: 13px; font-weight: 600; text-transform: uppercase;">' . htmlspecialchars($ticket->Prioridad) . '</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; font-weight: 600; color: #000000; vertical-align: top; border-bottom: 1px solid #e5e7eb;">📝 Descripción:</td>
                                        <td style="padding: 12px 0; color: #6b7280; line-height: 1.6; border-bottom: 1px solid #e5e7eb;">' . nl2br(htmlspecialchars($ticket->Descripcion)) . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; font-weight: 600; color: #000000;">📅 Fecha:</td>
                                        <td style="padding: 12px 0; color: #6b7280;">' . date('d/m/Y H:i', strtotime($ticket->Fecha_creacion)) . '</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- CTA Button -->
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="' . \yii\helpers\Url::to(['tickets/view', 'id' => $ticket->id], true) . '" 
                                style="background: linear-gradient(135deg, #8BA590 0%, #6b8a70 100%); 
                                        color: white; 
                                        padding: 14px 40px; 
                                        text-decoration: none; 
                                        border-radius: 8px; 
                                        font-weight: 600; 
                                        font-size: 15px;
                                        display: inline-block;
                                        box-shadow: 0 4px 12px rgba(139, 165, 144, 0.3);
                                        transition: all 0.3s ease;">
                                    👀 Ver Ticket Completo
                                </a>
                            </div>
                            
                            <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 30px 0;">
                            
                            <!-- Footer -->
                            <div style="text-align: center; padding: 20px 0;">
                                <p style="margin: 0 0 5px; font-size: 13px; color: #6b7280;">
                                    Este es un mensaje automático del <strong style="color: #8BA590;">Sistema Wicontrol</strong>
                                </p>
                                <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                                    Por favor, no respondas a este correo
                                </p>
                            </div>
                        </div>
                    </div>
                ')
                ->send();

\Yii::error("✅ Correo enviado a: " . $usuario->email . " para ticket: " . $ticket->Folio);
return true;
        } catch (\Exception $e) {
            \Yii::error("❌ Error enviando correo: " . $e->getMessage());
            return false;
        }
    }
    private function getPrioridadColor($prioridad)
    {
        switch (strtoupper($prioridad)) {
            case 'ALTA':
                return '#e74c3c'; // Rojo
            case 'MEDIA':
                return '#f39c12'; // Naranja
            case 'BAJA':
                return '#27ae60'; // Verde
            default:
                return '#7f8c8d'; // Gris
        }
    }

    public function actionTestNotificacion()
    {
        $userId = \Yii::$app->user->id;
        
        $resultado = $this->crearNotificacion(
            $userId,
            'test',
            'Prueba de notificación',
            'Si ves esto, las notificaciones funcionan',
            null
        );
        
        if ($resultado) {
            echo "✅ Notificación creada. Revisa la tabla 'notificaciones' en la BD.";
        } else {
            echo "❌ Error creando notificación. Revisa runtime/logs/app.log";
        }
        
        exit;
    }

    /**
     * Exportar tickets a CSV (sin librerías externas)
     */
    public function actionExportar()
    {
        $query = Tickets::find()
            ->with(['cliente', 'sistema', 'servicio', 'usuarioAsignado']);
        
        // Aplicar TODOS los filtros del index
        if (!empty($_GET['folio'])) {
            $query->andWhere(['like', 'Folio', $_GET['folio']]);
        }
        if (!empty($_GET['cliente_id'])) {
            $query->andWhere(['Cliente_id' => $_GET['cliente_id']]);
        }
        if (!empty($_GET['sistema_id'])) {
            $query->andWhere(['Sistema_id' => $_GET['sistema_id']]);
        }
        if (!empty($_GET['servicio_id'])) {
            $query->andWhere(['Servicio_id' => $_GET['servicio_id']]);
        }
        if (!empty($_GET['asignado_a'])) {
            $query->andWhere(['Asignado_a' => $_GET['asignado_a']]);
        }
        if (!empty($_GET['prioridad'])) {
            $query->andWhere(['Prioridad' => $_GET['prioridad']]);
        }
        if (!empty($_GET['estado'])) {
            $query->andWhere(['Estado' => $_GET['estado']]);
        }
        
        // Filtro por mes
        if (!empty($_GET['mes'])) {
            $mes = $_GET['mes'];
            $primerDia = $mes . '-01 00:00:00';
            $ultimoDia = date('Y-m-t 23:59:59', strtotime($mes . '-01'));
            $query->andWhere(['>=', 'Fecha_creacion', $primerDia]);
            $query->andWhere(['<=', 'Fecha_creacion', $ultimoDia]);
        } else {
            $mesActual = date('Y-m');
            $primerDia = $mesActual . '-01 00:00:00';
            $ultimoDia = date('Y-m-t 23:59:59');
            $query->andWhere(['>=', 'Fecha_creacion', $primerDia]);
            $query->andWhere(['<=', 'Fecha_creacion', $ultimoDia]);
        }

        $tickets = $query->orderBy(['id' => SORT_DESC])->all();
        
        // Configurar headers para CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=tickets_' . date('Y-m-d_His') . '.csv');
        header('Cache-Control: max-age=0');
        
        // Abrir salida
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8 (para que Excel reconozca acentos)
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($output, [
            'Folio',
            'Cliente',
            'Sistema',
            'Servicio',
            'Usuario Reporta',
            'Asignado A',
            'Hora Programada',
            'Hora Inicio',
            'Prioridad',
            'Estado',
            'Descripción',
            'Fecha Creación'
        ]);
        
        // Datos
        foreach ($tickets as $ticket) {
            fputcsv($output, [
                $ticket->Folio,
                $ticket->cliente->Nombre ?? '',
                $ticket->sistema->Nombre ?? '',
                $ticket->servicio->Nombre ?? '',
                $ticket->Usuario_reporta ?? '',
                $ticket->usuarioAsignado->email ?? '',
                $ticket->HoraProgramada ? date('d/m/Y H:i', strtotime($ticket->HoraProgramada)) : '',
                $ticket->HoraInicio ? date('d/m/Y H:i', strtotime($ticket->HoraInicio)) : '',
                $ticket->Prioridad,
                $ticket->Estado,
                $ticket->Descripcion,
                date('d/m/Y H:i', strtotime($ticket->Fecha_creacion))
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Agregar comentario a un ticket
     */
    public function actionAgregarComentario()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        try {
            $input = \Yii::$app->request->getRawBody();
            $data = json_decode($input, true);
            
            if (empty($data['ticket_id']) || empty($data['comentario'])) {
                return ['success' => false, 'message' => 'Datos incompletos'];
            }
            
            $comentario = new Comentarios();
            $comentario->ticket_id = $data['ticket_id'];
            $comentario->usuario_id = \Yii::$app->user->id;
            $comentario->comentario = $data['comentario'];
            $comentario->tipo = $data['tipo'] ?? 'comentario';
            
            if ($comentario->save()) {
                // Crear notificación si el comentario no es del asignado
                $ticket = Tickets::findOne($data['ticket_id']);
                if ($ticket && $ticket->Asignado_a != \Yii::$app->user->id) {
                    $usuarioActual = \Yii::$app->user->identity->email;
                    $this->crearNotificacion(
                        $ticket->Asignado_a,
                        'comentario',
                        'Nuevo comentario en ticket ' . $ticket->Folio,
                        $usuarioActual . ' agregó un comentario',
                        $ticket->id
                    );
                }
                
                return [
                    'success' => true,
                    'comentario' => [
                        'id' => $comentario->id,
                        'usuario' => \Yii::$app->user->identity->email,
                        'comentario' => $comentario->comentario,
                        'tipo' => $comentario->tipo,
                        'fecha' => date('d/m/Y H:i', strtotime($comentario->fecha_creacion))
                    ]
                ];
            }
            
            return ['success' => false, 'errors' => $comentario->errors];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Obtener comentarios de un ticket
     */
    public function actionObtenerComentarios()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        try {
            $ticketId = \Yii::$app->request->get('ticket_id');
            
            if (!$ticketId) {
                return ['success' => false, 'message' => 'ID de ticket requerido'];
            }
            
            $comentarios = Comentarios::find()
                ->where(['ticket_id' => $ticketId])
                ->with('usuario')
                ->orderBy(['fecha_creacion' => SORT_ASC])
                ->all();
            
            $result = [];
            foreach ($comentarios as $com) {
                $result[] = [
                    'id' => $com->id,
                    'usuario' => $com->usuario->email ?? 'Usuario desconocido',
                    'comentario' => $com->comentario,
                    'tipo' => $com->tipo,
                    'fecha' => date('d/m/Y H:i', strtotime($com->fecha_creacion))
                ];
            }
            
            return ['success' => true, 'comentarios' => $result];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
