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
        
        $clientes = Clientes::find()->asArray()->all();
        $sistemas = Sistemas::find()->asArray()->all();
        $servicios = Servicios::find()->asArray()->all();
        $Usuarios = Usuarios::find()->asArray()->all();

        $query = Tickets::find();

        // Filtro automático para Consultores
        $currentUser = Yii::$app->user->identity;
        if ($currentUser && $currentUser->rol === 'Consultor') {
            if (empty($_GET['asignado_a']) && empty($_GET['folio']) && empty($_GET['cliente_id']) && 
                empty($_GET['sistema_id']) && empty($_GET['servicio_id']) && empty($_GET['prioridad']) && 
                empty($_GET['estado']) && empty($_GET['mes'])) {
                $query->andWhere(['Asignado_a' => $currentUser->id]);
            }
        }

        // Aplicar filtros
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

        // ✅ SOLO FILTRAR POR MES SI SE ESPECIFICA
        if (!empty($_GET['mes'])) {
            $mes = $_GET['mes'];
            $primerDia = $mes . '-01 00:00:00';
            $ultimoDia = date('Y-m-t 23:59:59', strtotime($mes . '-01'));
            $query->andWhere(['>=', 'Fecha_creacion', $primerDia]);
            $query->andWhere(['<=', 'Fecha_creacion', $ultimoDia]);
        }
        // ✅ REMOVER EL ELSE QUE FORZABA MES ACTUAL

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 50,
            ],
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
    
    // ✅ GENERAR FOLIO CORRECTAMENTE
    $ultimoId = Tickets::find()->max('id') ?? 0;
    $model->Folio = str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT);
    
    // ✅ OBTENER FECHA DEL POST SI EXISTE (viene del calendario)
    $fechaSeleccionada = Yii::$app->request->post('fecha_seleccionada');
    if ($fechaSeleccionada) {
        // Convertir fecha del calendario (YYYY-MM-DD) a formato datetime
        $model->HoraProgramada = $fechaSeleccionada . ' 09:00:00';
        $model->HoraInicio = $fechaSeleccionada . ' 09:00:00';
        
        // Guardar en sesión para mostrar mensaje
        Yii::$app->session->setFlash('fechaDesdeCalendario', $fechaSeleccionada);
    }
    
    // ✅ ESTABLECER VALORES POR DEFECTO
    $model->Estado = 'ABIERTO';
    $model->Fecha_creacion = date('Y-m-d H:i:s');
    $model->Fecha_actualizacion = date('Y-m-d H:i:s');
    $model->Creado_por = Yii::$app->user->id;
    
    if ($this->request->isPost && $model->load($this->request->post())) {
        // ✅ ACTUALIZAR FECHA DE ACTUALIZACIÓN ANTES DE GUARDAR
        $model->Fecha_actualizacion = date('Y-m-d H:i:s');
        
        if ($model->save()) {
            // ✅ CREAR NOTIFICACIÓN SI SE ASIGNA A ALGUIEN
            if ($model->Asignado_a) {
                $usuarioActual = Yii::$app->user->identity->email;
                $this->crearNotificacion(
                    $model->Asignado_a,
                    'asignado',
                    'Nuevo ticket asignado: ' . $model->Folio,
                    $usuarioActual . ' te asignó un nuevo ticket',
                    $model->id
                );
            }
            
            Yii::$app->session->setFlash('success', 'Ticket creado exitosamente.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
    } else {
        $model->loadDefaultValues();
    }

    return $this->render('create', [
        'model' => $model,
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

        // ✅ OBTENER TODAS LAS VARIABLES NECESARIAS PARA LA VISTA
        $clientes = Clientes::find()->asArray()->all();
        $sistemas = Sistemas::find()->asArray()->all(); 
        $servicios = Servicios::find()->asArray()->all();
        $usuarios = Usuarios::find()->asArray()->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'clientes' => $clientes,
            'sistemas' => $sistemas,
            'servicios' => $servicios,
            'usuarios' => $usuarios, // ✅ Agregar esta línea
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
        try {
            $model = $this->findModel($id);
            $folio = $model->Folio; // Guardar el folio antes de eliminar
            
            if ($model->delete()) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['success' => true, 'message' => "Ticket {$folio} eliminado correctamente"];
                }
                
                Yii::$app->session->setFlash('success', "Ticket {$folio} eliminado correctamente");
                return $this->redirect(['index']);
            } else {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['success' => false, 'message' => 'No se pudo eliminar el ticket'];
                }
                
                Yii::$app->session->setFlash('error', 'No se pudo eliminar el ticket');
                return $this->redirect(['index']);
            }
        } catch (\Exception $e) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => $e->getMessage()];
            }
            
            Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
            return $this->redirect(['index']);
        }
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
                $usuario = Usuarios::findOne($usuario_id);
                if (!$usuario || !$usuario->email) {
                    \Yii::error("Usuario sin email: $usuario_id");
                    return false;
                }
                
                $ticket = Tickets::findOne($ticket_id);
                if (!$ticket) {
                    \Yii::error("Ticket no encontrado: $ticket_id");
                    return false;
                }
                
                $asignador = \Yii::$app->user->identity->email;
                $clienteNombre = $ticket->cliente ? $ticket->cliente->Nombre : 'N/A';
                $sistemaNombre = $ticket->sistema ? $ticket->sistema->Nombre : 'N/A';
                $servicioNombre = $ticket->servicio ? $ticket->servicio->Nombre : 'N/A';
                $prioridadColor = $this->getPrioridadColor($ticket->Prioridad);
                $ticketUrl = \yii\helpers\Url::to(['tickets/view', 'id' => $ticket->id], true);
                $fechaFormateada = date('d M, Y · H:i', strtotime($ticket->HoraInicio));

                $htmlBody = <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin: 0; padding: 0; background-color: #fafafa; font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; -webkit-font-smoothing: antialiased;">
            
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fafafa; padding: 60px 20px;">
                <tr>
                    <td align="center">
                        <table role="presentation" width="520" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 8px 40px rgba(139, 165, 144, 0.08);">
                            
                            <!-- Accent Line -->
                            <tr>
                                <td style="height: 4px; background: linear-gradient(90deg, #8BA590 0%, #a8c4ad 50%, #8BA590 100%);"></td>
                            </tr>

                            <!-- Header Minimal -->
                            <tr>
                                <td style="padding: 48px 48px 0 48px;">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td>
                                                <div style="display: inline-block; background: rgba(139, 165, 144, 0.1); border-radius: 12px; padding: 10px 16px;">
                                                    <span style="font-size: 11px; font-weight: 600; color: #8BA590; text-transform: uppercase; letter-spacing: 1.5px;">Nuevo Ticket</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top: 20px;">
                                                <h1 style="margin: 0; font-size: 32px; font-weight: 300; color: #1a1a1a; letter-spacing: -1px; line-height: 1.2;">
                                                    Ticket <span style="font-weight: 600; color: #8BA590;">#{$ticket->Folio}</span>
                                                </h1>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top: 12px;">
                                                <p style="margin: 0; font-size: 15px; color: #8c8c8c; line-height: 1.6;">
                                                    Asignado por <span style="color: #5a5a5a; font-weight: 500;">{$asignador}</span>
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Divider -->
                            <tr>
                                <td style="padding: 32px 48px;">
                                    <div style="height: 1px; background: linear-gradient(90deg, transparent 0%, #e8e8e8 20%, #e8e8e8 80%, transparent 100%);"></div>
                                </td>
                            </tr>

                            <!-- Content Cards -->
                            <tr>
                                <td style="padding: 0 48px;">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                        
                                        <!-- Priority Badge + Service -->
                                        <tr>
                                            <td style="padding-bottom: 24px;">
                                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td width="50%" style="vertical-align: top;">
                                                            <span style="font-size: 11px; font-weight: 500; color: #b0b0b0; text-transform: uppercase; letter-spacing: 0.8px;">Prioridad</span>
                                                            <div style="margin-top: 8px;">
                                                                <span style="display: inline-block; background: {$prioridadColor}; color: white; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; letter-spacing: 0.3px;">{$ticket->Prioridad}</span>
                                                            </div>
                                                        </td>
                                                        <td width="50%" style="vertical-align: top;">
                                                            <span style="font-size: 11px; font-weight: 500; color: #b0b0b0; text-transform: uppercase; letter-spacing: 0.8px;">Servicio</span>
                                                            <div style="margin-top: 8px; font-size: 15px; color: #3a3a3a; font-weight: 500;">{$servicioNombre}</div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>

                                        <!-- Info Grid -->
                                        <tr>
                                            <td style="background: #f8f9f8; border-radius: 16px; padding: 24px;">
                                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td style="padding-bottom: 20px; border-bottom: 1px solid rgba(139, 165, 144, 0.15);">
                                                            <span style="font-size: 11px; font-weight: 500; color: #8BA590; text-transform: uppercase; letter-spacing: 0.8px;">Cliente</span>
                                                            <div style="margin-top: 6px; font-size: 16px; color: #2a2a2a; font-weight: 500;">{$clienteNombre}</div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 20px 0; border-bottom: 1px solid rgba(139, 165, 144, 0.15);">
                                                            <span style="font-size: 11px; font-weight: 500; color: #8BA590; text-transform: uppercase; letter-spacing: 0.8px;">Sistema</span>
                                                            <div style="margin-top: 6px; font-size: 16px; color: #2a2a2a; font-weight: 500;">{$sistemaNombre}</div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding-top: 20px;">
                                                            <span style="font-size: 11px; font-weight: 500; color: #8BA590; text-transform: uppercase; letter-spacing: 0.8px;">Hora de inicio</span>
                                                            <div style="margin-top: 6px; font-size: 15px; color: #5a5a5a;">{$fechaFormateada}</div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>

                                        <!-- Description -->
                                        <tr>
                                            <td style="padding-top: 24px;">
                                                <span style="font-size: 11px; font-weight: 500; color: #b0b0b0; text-transform: uppercase; letter-spacing: 0.8px;">Descripción</span>
                                                <div style="margin-top: 10px; font-size: 14px; color: #5a5a5a; line-height: 1.7; padding: 16px; background: #fefefe; border-left: 3px solid #8BA590; border-radius: 0 8px 8px 0;">{$ticket->Descripcion}</div>
                                            </td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>

                            <!-- CTA Button -->
                            <tr>
                                <td style="padding: 40px 48px;">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center">
                                                <a href="{$ticketUrl}" style="display: inline-block; background: #8BA590; color: #ffffff; padding: 16px 40px; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 14px; letter-spacing: 0.3px; box-shadow: 0 2px 8px rgba(139, 165, 144, 0.3), 0 4px 20px rgba(139, 165, 144, 0.15);">
                                                    Ver detalles del ticket
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td style="background: #f8f9f8; padding: 32px 48px; border-top: 1px solid #f0f0f0;">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center">
                                                <p style="margin: 0; font-size: 13px; color: #a0a0a0; font-weight: 400;">
                                                    Wicontrol
                                                </p>
                                                <p style="margin: 8px 0 0; font-size: 11px; color: #c0c0c0;">
                                                    Este mensaje fue generado automáticamente
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                        </table>

                        <!-- Subtle branding -->
                        <table role="presentation" width="520" cellspacing="0" cellpadding="0" style="margin-top: 24px;">
                            <tr>
                                <td align="center">
                                    <p style="margin: 0; font-size: 11px; color: #c8c8c8;">
                                        winetpc.com
                                    </p>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>
            </table>

        </body>
        </html>
        HTML;

                \Yii::$app->mailer->compose()
                    ->setFrom(['consultoria@winetpc.com' => 'Wicontrol'])
                    ->setTo($usuario->email)
                    ->setSubject('Ticket #' . $ticket->Folio . ' · ' . $servicioNombre)
                    ->setHtmlBody($htmlBody)
                    ->send();

                \Yii::info("Correo enviado a: " . $usuario->email . " para ticket: " . $ticket->Folio);
                return true;

            } catch (\Exception $e) {
                \Yii::error("Error enviando correo: " . $e->getMessage());
                return false;
            }
        }

        private function getPrioridadColor($prioridad)
        {
            switch (strtoupper($prioridad)) {
                case 'ALTA':
                    return '#c9756b';  // Rojo suave
                case 'MEDIA':
                    return '#d4a574';  // Ámbar suave  
                case 'BAJA':
                    return '#8BA590';  // Verde sage
                default:
                    return '#a0a0a0';  // Gris neutro
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
