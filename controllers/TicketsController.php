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
use app\models\DevLog;
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
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                // Sin 'only' → aplica a TODAS las acciones del controlador
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],  // cualquier usuario autenticado
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'get-tickets' => ['GET'],
                    'update-fecha' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Deshabilita CSRF para endpoints que reciben JSON crudo desde JS.
     * El resto del controlador mantiene CSRF activo.
     */
    public function beforeAction($action)
    {
        $jsonActions = [
            'save-bulk',
            'update-estado',
            'get-ticket-data',
            'save-solution',
            'marcar-notificacion',
            'marcar-todas-leidas',
            'obtener-notificaciones',
            'get-next-folio',
            'agregar-comentario',
            'obtener-comentarios',
            'contar-comentarios',
            'update-fecha',
            'verificar-recordatorios',
        ];

        if (in_array($action->id, $jsonActions, true)) {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
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

        $request = Yii::$app->request;

        // Para la vista - obtener el parámetro asignado_a si existe
        $asignadoParam = $request->get('asignado_a', null);
        if ($asignadoParam === null) {
            $asignadoFiltro = Yii::$app->user->isGuest ? '' : Yii::$app->user->id;
        } else {
            $asignadoFiltro = $asignadoParam;
        }

        $clientes = Clientes::find()
            ->select(['id', 'Nombre', 'Prioridad', 'Tipo_servicio'])
            ->orderBy(['Nombre' => SORT_ASC])
            ->asArray()
            ->all();

        $sistemas = Sistemas::find()
            ->select(['id', 'Nombre'])
            ->orderBy(['Nombre' => SORT_ASC])
            ->asArray()
            ->all();

        $servicios = Servicios::find()
            ->select(['id', 'Nombre'])
            ->orderBy(['Nombre' => SORT_ASC])
            ->asArray()
            ->all();

        $Usuarios = Usuarios::find()
            ->select(['id', 'Nombre', 'email'])
            ->orderBy(['Nombre' => SORT_ASC])
            ->asArray()
            ->all();

        // ====== RENDER ======
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientes' => $clientes,
            'sistemas' => $sistemas,
            'servicios' => $servicios,
            'Usuarios' => $Usuarios,
            'asignadoFiltro' => $asignadoFiltro,
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
        $model = $this->findModel($id);
        $historial = null;

        if (!Yii::$app->user->isGuest) {
            $rol = Yii::$app->user->identity->rol;
            if (in_array($rol, ['Administradores', 'Supervisores', 'Desarrolladores', 'Administracion'], true)) {
                $historial = \app\models\TicketHistorial::find()
                    ->with('usuario')
                    ->where(['ticket_id' => $id])
                    ->orderBy(['fecha' => SORT_DESC])
                    ->all();
            }
        }

        return $this->render('view', [
            'model' => $model,
            'historial' => $historial,
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
        // Folio provisional para mostrar en el form (puede colisionar en concurrencia)
        // El folio definitivo se asigna después del INSERT usando el ID real
        $model->Folio = str_pad(Tickets::find()->max('id') + 1, 4, '0', STR_PAD_LEFT);

        // CORRECCIÓN: Seleccionar 'id' y 'Nombre', y mapear id => Nombre
        $consultores = \app\models\Usuarios::find()
            ->select(['id', 'Nombre'])
            ->orderBy(['Nombre' => SORT_ASC])
            ->asArray()
            ->all();



        // El primer parámetro es la clave (lo que se guarda: id), el segundo es el valor (lo que se ve: Nombre)
        $consultoresList = \yii\helpers\ArrayHelper::map($consultores, 'id', 'Nombre');
        $model->consultoresList = $consultoresList;

        // Obtener fecha del POST si existe (viene del calendario)
        $fechaSeleccionada = Yii::$app->request->post('fecha_seleccionada');
        $desdeCalendario = false;
        if ($fechaSeleccionada) {
            // Si trae hora (YYYY-MM-DDTHH:MM o YYYY-MM-DD HH:MM:SS), usarla directamente
            $fechaDatetime = str_replace('T', ' ', $fechaSeleccionada);
            if (strlen($fechaDatetime) === 10) {
                $fechaDatetime .= ' ' . date('H:i:s');
            }
            $model->Fecha_creacion = $fechaDatetime;
            $model->Fecha_actualizacion = $fechaDatetime;
            $model->HoraInicio = $fechaDatetime;
            $model->HoraProgramada = date('Y-m-d H:i:s');

            Yii::$app->session->setFlash('fechaDesdeCalendario', $fechaSeleccionada);
            $desdeCalendario = true;
        } else {
            $model->Fecha_creacion = date('Y-m-d H:i:s');
            $model->Fecha_actualizacion = date('Y-m-d H:i:s');
        }

        $model->Estado = 'Abierto';

        if ($this->request->isPost && $model->load($this->request->post())) {

            // datetime-local envía "YYYY-MM-DDTHH:MM" → convertir a "YYYY-MM-DD HH:MM:SS"
            foreach (['HoraProgramada', 'HoraInicio'] as $campo) {
                if (!empty($model->$campo) && str_contains($model->$campo, 'T')) {
                    $model->$campo = str_replace('T', ' ', $model->$campo) . ':00';
                }
            }

            $model->Creado_por = Yii::$app->user->id;
            // Folio temporal único para evitar colisión en la restricción UNIQUE
            // cuando dos peticiones concurrentes traen el mismo folio provisional del form.
            $model->Folio = 'TMP-' . uniqid();

            if ($model->save()) {

                // Folio definitivo basado en el AUTO_INCREMENT real — garantiza unicidad
                $model->Folio = str_pad($model->id, 4, '0', STR_PAD_LEFT);
                $model->save(false);

                if ($model->Asignado_a) {
                    $this->crearNotificacion(
                        $model->Asignado_a,
                        'asignado',
                        'Nuevo ticket asignado',
                        'Se te ha asignado un nuevo ticket: ' . $model->Folio,
                        $model->id
                    );
                }

                DevLog::log(
                    DevLog::TIPO_CREAR,
                    "Ticket [{$model->Folio}] creado — cliente ID {$model->Cliente_id} | sistema ID {$model->Sistema_id} | asignado a usuario ID {$model->Asignado_a}",
                    [
                        'folio' => $model->Folio,
                        'estado' => $model->Estado,
                        'prioridad' => $model->Prioridad,
                        'cliente_id' => $model->Cliente_id,
                        'sistema_id' => $model->Sistema_id,
                        'servicio_id' => $model->Servicio_id,
                        'asignado_a' => $model->Asignado_a,
                        'descripcion' => mb_substr($model->Descripcion ?? '', 0, 300),
                    ],
                    'tickets',
                    $model->id,
                    'Tickets'
                );

                Yii::$app->session->setFlash('success', 'Ticket creado exitosamente.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'consultoresList' => $consultoresList,
            'desdeCalendario' => $desdeCalendario,
        ]);
    }

    /**
     * Actualiza la fecha/hora de inicio de un ticket desde el drag del calendario.
     * Recibe JSON: { id, start }
     */
    public function actionUpdateFecha()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $body = Yii::$app->request->rawBody;
        $data = json_decode($body, true);
        $id = isset($data['id']) ? (int) $data['id'] : null;
        $start = isset($data['start']) ? $data['start'] : null;

        if (!$id || !$start) {
            Yii::$app->response->statusCode = 400;
            return ['success' => false, 'message' => 'Datos incompletos'];
        }

        $model = Tickets::findOne($id);
        if (!$model) {
            Yii::$app->response->statusCode = 404;
            return ['success' => false, 'message' => 'Ticket no encontrado'];
        }

        // Convertir ISO 8601 a formato MySQL (YYYY-MM-DD HH:MM:SS)
        $fechaDatetime = date('Y-m-d H:i:s', strtotime($start));

        $model->HoraInicio = $fechaDatetime;
        $model->Fecha_actualizacion = date('Y-m-d H:i:s');

        if ($model->save(false)) {
            DevLog::log(DevLog::TIPO_ACTUALIZAR, "Ticket [{$model->Folio}] hora inicio movida a {$fechaDatetime}", ['folio' => $model->Folio, 'hora_inicio' => $fechaDatetime], 'tickets', $model->id, 'Tickets');
            return ['success' => true, 'nueva_fecha' => $fechaDatetime];
        }

        Yii::$app->response->statusCode = 500;
        return ['success' => false, 'message' => 'Error al guardar', 'errors' => $model->errors];
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

        // Guardar valores originales ANTES del load() para proteger campos y detectar cambios
        $asignadoAntes = $model->Asignado_a;
        $estadoAntes = $model->Estado;
        $prioridadAntes = $model->Prioridad;
        $clienteAntes = $model->Cliente_id;
        $sistemaAntes = $model->Sistema_id;
        $servicioAntes = $model->Servicio_id;
        $solucionOrig = $model->Solucion;
        $tiempoEfOrig = $model->TiempoEfectivo;
        $horaFinOrig = $model->HoraFinalizo;

        $clientes = Clientes::find()->select(['id', 'Nombre'])->asArray()->all();
        $sistemas = Sistemas::find()->select(['id', 'Nombre'])->asArray()->all();
        $servicios = Servicios::find()->select(['id', 'Nombre'])->asArray()->all();
        $usuarios = Usuarios::find()->select(['id', 'Nombre', 'email'])->asArray()->all();

        if ($model->load(Yii::$app->request->post())) {
            // datetime-local envía "YYYY-MM-DDTHH:MM" → convertir a "YYYY-MM-DD HH:MM:SS"
            foreach (['HoraProgramada', 'HoraInicio'] as $campo) {
                if (!empty($model->$campo) && str_contains($model->$campo, 'T')) {
                    $model->$campo = str_replace('T', ' ', $model->$campo) . ':00';
                }
            }

            // Bloquear cierre desde esta vista — debe hacerse desde index con el flujo completo
            if (
                mb_strtolower(trim($estadoAntes)) !== 'cerrado' &&
                mb_strtolower(trim($model->Estado)) === 'cerrado'
            ) {
                $model->Estado = $estadoAntes;
                Yii::$app->session->setFlash('error', 'Para cerrar un ticket debes hacerlo desde la lista de tickets (incluye solución, tiempo efectivo y fecha de cierre).');
            }

            // Los campos de cierre solo se asignan por actionSaveSolution — nunca desde aquí
            $model->Solucion = $solucionOrig;
            $model->TiempoEfectivo = $tiempoEfOrig;
            $model->HoraFinalizo = $horaFinOrig;

            if ($model->save()) {
                $userId = (int) Yii::$app->user->id;

                // Historial de cambios
                \app\models\TicketHistorial::registrar($model->id, $userId, 'Estado', $estadoAntes, $model->Estado);
                \app\models\TicketHistorial::registrar($model->id, $userId, 'Prioridad', $prioridadAntes, $model->Prioridad);
                \app\models\TicketHistorial::registrar($model->id, $userId, 'Asignado_a', $asignadoAntes, $model->Asignado_a);
                \app\models\TicketHistorial::registrar($model->id, $userId, 'Cliente_id', $clienteAntes, $model->Cliente_id);
                \app\models\TicketHistorial::registrar($model->id, $userId, 'Sistema_id', $sistemaAntes, $model->Sistema_id);
                \app\models\TicketHistorial::registrar($model->id, $userId, 'Servicio_id', $servicioAntes, $model->Servicio_id);

                // Si cambió la asignación a una nueva persona
                if ($asignadoAntes !== $model->Asignado_a && $model->Asignado_a) {
                    $this->crearNotificacion(
                        $model->Asignado_a,
                        'asignado',
                        'Nuevo ticket asignado',
                        'Se te ha asignado el ticket: ' . $model->Folio,
                        $model->id
                    );
                }

                // Si cambió el estado
                if ($estadoAntes !== $model->Estado && $model->Asignado_a) {
                    $this->crearNotificacion(
                        $model->Asignado_a,
                        'actualizado',
                        'Estado del ticket actualizado',
                        'El ticket ' . $model->Folio . ' cambió de estado a: ' . $model->Estado,
                        $model->id
                    );
                }

                DevLog::log(
                    DevLog::TIPO_ACTUALIZAR,
                    "Ticket [{$model->Folio}] actualizado — estado: [{$estadoAntes}→{$model->Estado}] | prioridad: [{$prioridadAntes}→{$model->Prioridad}] | asignado: [{$asignadoAntes}→{$model->Asignado_a}]",
                    [
                        'folio' => $model->Folio,
                        'estado_antes' => $estadoAntes,
                        'estado_despues' => $model->Estado,
                        'prioridad_antes' => $prioridadAntes,
                        'prioridad_despues' => $model->Prioridad,
                        'asignado_antes' => $asignadoAntes,
                        'asignado_despues' => $model->Asignado_a,
                        'cliente_antes' => $clienteAntes,
                        'cliente_despues' => $model->Cliente_id,
                    ],
                    'tickets',
                    $model->id,
                    'Tickets'
                );

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'clientes' => $clientes,
            'sistemas' => $sistemas,
            'servicios' => $servicios,
            'usuarios' => $usuarios,
        ]);
    }

    /**
     * Deletes an existing Tickets model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response|array
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        try {
            $model = $this->findModel($id);
            $folio = $model->Folio;
            $asignadoA = $model->Asignado_a;
            $estado = $model->Estado;
            $solucion = $model->Solucion;
            $tiempoEf = $model->TiempoEfectivo;
            $clienteId = $model->Cliente_id;

            if ($model->delete()) {
                // Si el ticket estaba cerrado con solución, devolver el tiempo al cliente
                if ($estado === 'CERRADO' && $solucion && $tiempoEf && $clienteId) {
                    $cliente = Clientes::findOne($clienteId);
                    if ($cliente) {
                        $minTicket = $this->roundUpTo15($this->hmToMinutes($tiempoEf));
                        $minCliente = $this->roundUpTo15($this->hmToMinutes($cliente->Tiempo ?? '0.00'));
                        $cliente->updateAttributes(['Tiempo' => $this->minutesToHM($minCliente + $minTicket)]);
                    }
                }
                DevLog::log(
                    DevLog::TIPO_ELIMINAR,
                    "Ticket [{$folio}] ELIMINADO — ID #{$id} | asignado a usuario ID {$asignadoA}",
                    [
                        'ticket_id' => $id,
                        'folio' => $folio,
                        'asignado_a' => $asignadoA,
                        'estado' => $model->Estado,
                        'cliente_id' => $model->Cliente_id,
                    ],
                    'tickets',
                    $id,
                    'Tickets'
                );

                // ✅ CREAR NOTIFICACIÓN AL ELIMINAR
                if ($asignadoA) {
                    $this->crearNotificacion(
                        $asignadoA,
                        'eliminado',
                        'Ticket eliminado',
                        'El ticket ' . $folio . ' ha sido eliminado.',
                        $id
                    );
                }

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
            $foliosCreados = [];
            $idsCreados = [];
            foreach ($tickets as $ticketData) {
                $ticket = new Tickets();
                // Folio temporal único: el definitivo se asigna tras el save con el id
                // auto-increment. Así no colisiona cuando varios usuarios guardan a la vez.
                $ticket->Folio = 'TMP-' . uniqid();
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

                // Folio definitivo basado en el ID real
                $ticket->Folio = str_pad($ticket->id, 4, '0', STR_PAD_LEFT);
                $ticket->save(false);
                $idsCreados[] = $ticket->id;
                $foliosCreados[] = $ticket->Folio;

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

            DevLog::log(DevLog::TIPO_CREAR, count($tickets) . ' ticket(s) creados en lote', ['total' => count($tickets), 'folios' => $foliosCreados], 'tickets', null, 'Tickets');
            return ['success' => true, 'folios' => $foliosCreados, 'ids' => $idsCreados];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function actionUpdateEstado()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $input = \Yii::$app->request->getRawBody();
        $data = json_decode($input, true);

        $ticket = Tickets::findOne($data['id'] ?? null);
        if (!$ticket) {
            return ['success' => false, 'message' => 'Ticket no encontrado'];
        }

        $nuevoEstado = (string) ($data['estado'] ?? $ticket->Estado);

        // CERRADO solo se procesa vía actionSaveSolution (incluye solución + notificaciones).
        if (mb_strtolower(trim($nuevoEstado)) === 'cerrado') {
            return ['success' => false, 'message' => 'Para cerrar un ticket usa el modal de solución.'];
        }

        $estadoAnterior = (string) $ticket->Estado;
        $ticket->Estado = $nuevoEstado;

        if (!$ticket->save()) {
            return ['success' => false, 'message' => 'No se pudo guardar', 'errors' => $ticket->errors];
        }

        // Historial
        \app\models\TicketHistorial::registrar($ticket->id, (int) Yii::$app->user->id, 'Estado', $estadoAnterior, $ticket->Estado);

        DevLog::log(DevLog::TIPO_ACTUALIZAR, "Ticket [{$ticket->Folio}] estado cambiado: {$estadoAnterior} → {$ticket->Estado}", ['folio' => $ticket->Folio, 'estado_antes' => $estadoAnterior, 'estado_nuevo' => $ticket->Estado], 'tickets', $ticket->id, 'Tickets');

        // Notificar cambio de estado al consultor asignado
        if ($estadoAnterior !== $ticket->Estado && $ticket->Asignado_a) {
            $usuarioActualEmail = Yii::$app->user->identity->email ?? 'Alguien';
            $this->crearNotificacion(
                (int) $ticket->Asignado_a,
                'estado_cambio',
                'Cambio de estado: ' . $ticket->Folio,
                $usuarioActualEmail . ' cambió el estado a ' . $ticket->Estado,
                $ticket->id
            );
        }

        return ['success' => true];
    }

    public function actionQuickUpdate()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!\Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        $data = json_decode(\Yii::$app->request->rawBody, true);
        if (!$data || empty($data['id'])) {
            return ['success' => false, 'message' => 'Datos inválidos'];
        }

        $model = Tickets::findOne((int) $data['id']);
        if (!$model) {
            return ['success' => false, 'message' => 'Ticket no encontrado'];
        }

        // Guardar originales antes de modificar
        $estadoAntes = $model->Estado;
        $prioridadAntes = $model->Prioridad;
        $asignadoAntes = $model->Asignado_a;
        $clienteAntes = $model->Cliente_id;
        $sistemaAntes = $model->Sistema_id;
        $servicioAntes = $model->Servicio_id;
        $solucionOrig = $model->Solucion;
        $tiempoEfOrig = $model->TiempoEfectivo;
        $horaFinOrig = $model->HoraFinalizo;

        // Aplicar campos permitidos
        $camposPermitidos = ['Estado', 'Prioridad', 'Asignado_a', 'Cliente_id', 'Sistema_id', 'Servicio_id', 'Usuario_reporta', 'HoraProgramada', 'HoraInicio', 'Descripcion'];
        foreach ($camposPermitidos as $campo) {
            if (array_key_exists($campo, $data)) {
                $model->$campo = ($data[$campo] !== '' && $data[$campo] !== null) ? $data[$campo] : null;
            }
        }

        // Bloquear cierre desde aquí — debe hacerse vía save-solution
        if ($estadoAntes !== 'CERRADO' && $model->Estado === 'CERRADO') {
            $model->Estado = $estadoAntes;
        }

        // Proteger campos de cierre
        $model->Solucion = $solucionOrig;
        $model->TiempoEfectivo = $tiempoEfOrig;
        $model->HoraFinalizo = $horaFinOrig;

        if (!$model->save()) {
            return ['success' => false, 'errors' => $model->errors];
        }

        $userId = (int) Yii::$app->user->id;
        \app\models\TicketHistorial::registrar($model->id, $userId, 'Estado', $estadoAntes, $model->Estado);
        \app\models\TicketHistorial::registrar($model->id, $userId, 'Prioridad', $prioridadAntes, $model->Prioridad);
        \app\models\TicketHistorial::registrar($model->id, $userId, 'Asignado_a', $asignadoAntes, $model->Asignado_a);
        \app\models\TicketHistorial::registrar($model->id, $userId, 'Cliente_id', $clienteAntes, $model->Cliente_id);
        \app\models\TicketHistorial::registrar($model->id, $userId, 'Sistema_id', $sistemaAntes, $model->Sistema_id);
        \app\models\TicketHistorial::registrar($model->id, $userId, 'Servicio_id', $servicioAntes, $model->Servicio_id);

        if ($asignadoAntes !== $model->Asignado_a && $model->Asignado_a) {
            $this->crearNotificacion((int) $model->Asignado_a, 'asignado', 'Nuevo ticket asignado', 'Se te ha asignado el ticket: ' . $model->Folio, $model->id);
        }
        if ($estadoAntes !== $model->Estado && $model->Asignado_a) {
            $this->crearNotificacion((int) $model->Asignado_a, 'actualizado', 'Estado del ticket actualizado', 'El ticket ' . $model->Folio . ' cambió de estado a: ' . $model->Estado, $model->id);
        }

        DevLog::log(DevLog::TIPO_ACTUALIZAR, "Ticket [{$model->Folio}] actualizado rápido", ['folio' => $model->Folio, 'estado' => $model->Estado, 'prioridad' => $model->Prioridad], 'tickets', $model->id, 'Tickets');

        return ['success' => true, 'message' => 'Ticket actualizado correctamente'];
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

            // HoraInicio → datetime-local
            $horaInicio = '';
            if (!empty($ticket->HoraInicio)) {
                $horaInicio = date('Y-m-d\TH:i', strtotime($ticket->HoraInicio));
            }

            // HoraFinalizo → datetime-local
            $horaFinalizo = '';
            if (!empty($ticket->HoraFinalizo)) {
                if (is_numeric($ticket->HoraFinalizo)) {
                    $horaFinalizo = date('Y-m-d\TH:i', (int) $ticket->HoraFinalizo);
                } else {
                    $horaFinalizo = date('Y-m-d\TH:i', strtotime($ticket->HoraFinalizo));
                }
            }

            return [
                'success' => true,
                'ticket' => [
                    'HoraInicio' => $horaInicio,
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

        $data = json_decode(\Yii::$app->request->getRawBody(), true);

        if (empty($data['id'])) {
            return ['success' => false, 'message' => 'ID de ticket no recibido'];
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            /** @var Tickets $ticket */
            $ticket = Tickets::findOne($data['id']);
            if (!$ticket) {
                $transaction->rollBack();
                return ['success' => false, 'message' => 'Ticket no encontrado'];
            }

            // 1) Minutos viejos y nuevos (formato H.MM -> minutos) + redondeo a 15
            $viejoMin = $this->roundUpTo15($this->hmToMinutes($ticket->TiempoEfectivo ?? '0.00'));
            $nuevoMin = $this->roundUpTo15($this->hmToMinutes($data['tiempoEfectivo'] ?? '0.00'));

            // 2) Setear campos del ticket
            $ticket->Solucion = $data['solucion'] ?? null;
            $ticket->TiempoEfectivo = $this->minutesToHM($nuevoMin);
            $ticket->Estado = 'CERRADO'; // cerrar atómicamente con la solución

            if (!empty($data['horaFinalizo'])) {
                $timestamp = strtotime($data['horaFinalizo']);
                if ($timestamp === false) {
                    $transaction->rollBack();
                    return ['success' => false, 'message' => 'Formato de fecha inválido para horaFinalizo'];
                }
                $ticket->HoraFinalizo = date('Y-m-d H:i:s', $timestamp);
            }

            // 3) Guardar ticket
            if (!$ticket->save()) {
                $transaction->rollBack();
                return ['success' => false, 'message' => 'Error al guardar ticket', 'errors' => $ticket->errors];
            }

            // 4) Delta en MINUTOS → actualizar tiempo del cliente
            $deltaMin = $nuevoMin - $viejoMin;

            if ($deltaMin !== 0 && !empty($ticket->Cliente_id)) {
                $cliente = Clientes::findOne($ticket->Cliente_id);
                if ($cliente) {
                    $clienteMinAntes = $this->roundUpTo15($this->hmToMinutes($cliente->Tiempo ?? '0.00'));
                    $nuevoTiempo = $this->minutesToHM($clienteMinAntes - $deltaMin);

                    Yii::$app->db->createCommand()
                        ->update('clientes', ['Tiempo' => $nuevoTiempo], ['id' => $cliente->id])
                        ->execute();
                }
            }

            // Historial: registrar cierre
            $userId = (int) Yii::$app->user->id;
            \app\models\TicketHistorial::registrar($ticket->id, $userId, 'Estado', 'ABIERTO', 'CERRADO');
            \app\models\TicketHistorial::registrar($ticket->id, $userId, 'Solucion', '', $ticket->Solucion ?? '');
            \app\models\TicketHistorial::registrar($ticket->id, $userId, 'TiempoEfectivo', $this->minutesToHM($viejoMin), $this->minutesToHM($nuevoMin));
            \app\models\TicketHistorial::registrar($ticket->id, $userId, 'HoraFinalizo', '', $ticket->HoraFinalizo ?? '');

            $transaction->commit();

            // Notificaciones — se envían DESPUÉS del commit, cuando la solución ya está guardada
            $usuarioActualEmail = Yii::$app->user->identity->email ?? 'Alguien';

            // 1) Notificar al consultor asignado del cambio de estado a CERRADO
            if ($ticket->Asignado_a) {
                $this->crearNotificacion(
                    (int) $ticket->Asignado_a,
                    'estado_cambio',
                    'Ticket cerrado: ' . $ticket->Folio,
                    $usuarioActualEmail . ' cerró el ticket ' . $ticket->Folio . ' con solución.',
                    $ticket->id
                );
            }

            // 2) Notificar a roles administrativos
            $skip = [];
            if (!empty($ticket->Asignado_a))
                $skip[] = (int) $ticket->Asignado_a;
            if (!Yii::$app->user->isGuest)
                $skip[] = (int) Yii::$app->user->id;

            $this->notificarRoles(
                ['Administracion', 'Administradores', 'Desarrolladores'],
                'ticket_cerrado',
                'Ticket cerrado: ' . $ticket->Folio,
                $usuarioActualEmail . ' cerró el ticket ' . $ticket->Folio,
                $ticket->id,
                $skip
            );

            DevLog::log(
                DevLog::TIPO_ACTUALIZAR,
                "Ticket [{$ticket->Folio}] CERRADO con solución — tiempo efectivo: {$ticket->TiempoEfectivo}",
                [
                    'folio' => $ticket->Folio,
                    'solucion' => mb_substr($ticket->Solucion ?? '', 0, 300),
                    'hora_finalizo' => $ticket->HoraFinalizo,
                    'tiempo_efectivo' => $ticket->TiempoEfectivo,
                    'delta_minutos' => $deltaMin,
                    'cliente_id' => $ticket->Cliente_id,
                ],
                'tickets',
                $ticket->id,
                'Tickets'
            );

            return [
                'success' => true,
                'message' => 'Solución guardada y ticket cerrado',
                'ticket_viejo' => $this->minutesToHM($viejoMin),
                'ticket_nuevo' => $this->minutesToHM($nuevoMin),
                'delta_min' => $deltaMin,
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'Error en servidor: ' . $e->getMessage()];
        }
    }




    private function extractMentionEmails(string $text): array
    {
        // tokens: @[email:admin@gmail.com]
        preg_match_all('/@\[(?:email):([^\]]+)\]/i', $text, $m);
        $emails = array_map(fn($e) => mb_strtolower(trim($e)), $m[1] ?? []);
        $emails = array_values(array_unique(array_filter($emails)));
        return $emails;
    }
    protected function hmToMinutes($valor): int
    {
        if ($valor === null)
            return 0;
        $s = trim((string) $valor);
        if ($s === '')
            return 0;

        $s = str_replace(',', '.', $s);

        // H:MM
        if (preg_match('/^\s*(-?\d+)\s*:\s*(\d{1,2})\s*$/', $s, $m)) {
            $h = (int) $m[1];
            $mm = (int) $m[2];
            return ($h * 60) + ($h < 0 ? -$mm : $mm);
        }

        //  H.MM donde MM son minutos (acepta "0.16", "1.30", ".16", "-.16")
        if (preg_match('/^\s*(-?\d*)\.(\d{1,2})\s*$/', $s, $m)) {
            $hStr = $m[1];
            $mm = (int) $m[2];

            // si viene ".16" => horas = 0
            $h = ($hStr === '' || $hStr === '-') ? 0 : (int) $hStr;

            // si viene "-.16" => horas = 0 pero con signo negativo
            $isNeg = ($hStr === '-');

            $min = ($h * 60) + $mm;
            return $isNeg ? -$min : ($h < 0 ? -abs($min) : $min);
        }

        // Entero: "1" = 1 hora (60 min)
        if (preg_match('/^\s*-?\d+\s*$/', $s)) {
            return ((int) $s) * 60;
        }

        // Fallback: si viene "1.5" (decimal real) => horas * 60
        if (preg_match('/-?\d+(\.\d+)?/', $s, $m)) {
            return (int) round(((float) $m[0]) * 60);
        }

        return 0;
    }


    protected function roundUpTo15(int $minutes): int
    {
        if ($minutes <= 0)
            return $minutes;
        return (int) (ceil($minutes / 15) * 15);
    }

    protected function minutesToHM(int $minutes): string
    {
        $sign = $minutes < 0 ? '-' : '';
        $minutes = abs($minutes);

        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return $sign . $h . '.' . str_pad((string) $m, 2, '0', STR_PAD_LEFT);
    }












    private function crearNotificacionMencion(int $usuarioId, Tickets $ticket, int $comentarioId): void
    {
        // Evitar duplicar la misma notificación por comentario+usuario
        $exists = Notificaciones::find()
            ->where([
                'usuario_id' => $usuarioId,
                'ticket_id' => $ticket->id,
                'tipo' => 'mencion',
                // Si NO tienes un campo "ref_id" o similar, puedes quitar esta parte.
                // 'ref_id'  => $comentarioId,
            ])
            ->andWhere(['>=', 'fecha_creacion', date('Y-m-d H:i:s', time() - 60)]) // anti-duplicado básico 60s
            ->exists();

        if ($exists)
            return;

        $actor = Yii::$app->user->identity;
        $actorNombre = $actor->Nombre ?? $actor->email ?? 'Alguien';
        $descripcion = mb_strimwidth($ticket->Descripcion ?? '', 0, 80, '…');
        $this->crearNotificacion(
            $usuarioId,
            'mencion',
            "@{$actorNombre} te mencionó en el ticket {$ticket->Folio}",
            $descripcion ?: 'Haz clic para ver el comentario',
            $ticket->id
        );
    }



    /**
     * Verificar si hay tickets cuyo HoraInicio acaba de llegar y crear recordatorios.
     * Se llama desde el polling JS cada 8s. Anti-duplicado: no genera dos recordatorios
     * para el mismo ticket en un lapso de 2 horas.
     */
    public function actionVerificarRecordatorios()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (\Yii::$app->user->isGuest) {
            return ['success' => false];
        }

        $userId = (int) \Yii::$app->user->id;
        $ahora = date('Y-m-d H:i:s');
        $hace10 = date('Y-m-d H:i:s', strtotime('-10 minutes'));

        // Tickets del usuario que inician en la ventana de los últimos 10 minutos
        $tickets = Tickets::find()
            ->where(['Asignado_a' => $userId])
            ->andWhere(['between', 'HoraInicio', $hace10, $ahora])
            ->andWhere(['!=', 'Estado', 'CERRADO'])
            ->all();

        $creados = 0;
        foreach ($tickets as $ticket) {
            // Anti-duplicado: verificar si ya existe recordatorio para este ticket en las últimas 2h
            $yaExiste = Notificaciones::find()
                ->where([
                    'usuario_id' => $userId,
                    'ticket_id' => $ticket->id,
                    'tipo' => 'recordatorio',
                ])
                ->andWhere(['>=', 'fecha_creacion', date('Y-m-d H:i:s', strtotime('-2 hours'))])
                ->exists();

            if (!$yaExiste) {
                $hora = $ticket->HoraInicio ? date('H:i', strtotime($ticket->HoraInicio)) : '';
                $this->crearNotificacion(
                    $userId,
                    'recordatorio',
                    '⏰ Recordatorio: Ticket ' . $ticket->Folio,
                    'El ticket ' . $ticket->Folio . ' debería iniciar' . ($hora ? ' a las ' . $hora : ' ahora') . '.',
                    $ticket->id
                );
                $creados++;
            }
        }

        return ['success' => true, 'creados' => $creados];
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

            $notificaciones = Notificaciones::find()
                ->where(['usuario_id' => $userId])
                ->orderBy(['fecha_creacion' => SORT_DESC])
                ->limit(10)
                ->all();

            $result = [];
            foreach ($notificaciones as $notif) {
                $result[] = [
                    'id' => $notif->id,
                    'tipo' => $notif->tipo ?? 'asignado',
                    'titulo' => $notif->titulo,
                    'mensaje' => $notif->mensaje,
                    'leida' => (bool) $notif->leida,
                    'fecha' => date('d/m H:i', strtotime($notif->fecha_creacion)),
                    'ticket_id' => $notif->ticket_id,


                    //la url para ir al index y lo de los comentarios apa 
                    'url' => \yii\helpers\Url::to([
                        'tickets/index',
                        'openComments' => 1,
                        'ticket_id' => $notif->ticket_id,
                        'notif_id' => $notif->id, // opcional, para marcar leída
                    ]),
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
            $notif->usuario_id = (int) $usuario_id;
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

            // 📧 ENVIAR CORREO si es tipo 'asignado' o 'actualizado' (DESPUÉS DE GUARDAR)
            if (($tipo === 'asignado' || $tipo === 'actualizado') && $ticket_id) {
                $this->enviarCorreoAsignacion($usuario_id, $ticket_id);
            }

            return true;

        } catch (\Exception $e) {
            \Yii::error("Excepción en crearNotificacion: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Difiere el envío del correo hasta DESPUÉS de enviar la respuesta al
     * navegador. Así la creación/edición del ticket NO espera al SMTP (Zoho
     * puede tardar varios segundos; antes bloqueaba toda la petición ~56s).
     */
    private function enviarCorreoAsignacion($usuario_id, $ticket_id)
    {
        \Yii::$app->response->on(\yii\web\Response::EVENT_AFTER_SEND, function () use ($usuario_id, $ticket_id) {
            // Cierra la conexión con el navegador (PHP-FPM) para no hacerlo esperar
            if (function_exists('fastcgi_finish_request')) {
                @fastcgi_finish_request();
            }
            $this->enviarCorreoAsignacionAhora($usuario_id, $ticket_id);
        });
        return true;
    }

    private function enviarCorreoAsignacionAhora($usuario_id, $ticket_id)
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
            $fechaFormateada = $ticket->HoraInicio ? date('d M, Y · H:i', strtotime($ticket->HoraInicio)) : 'No definida';

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
                                                    Wintick
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
                ->setFrom(['consultoria@winetpc.com' => 'Wintick'])
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

    //funcion para buscar usuario que pertenezcan a esos roles 
    private function notificarRoles(array $roles, string $tipo, string $titulo, string $mensaje, ?int $ticketId = null, array $skipUserIds = []): void
    {
        $query = Usuarios::find()
            ->select(['id'])
            ->where(['rol' => $roles])
            ->andWhere(['status' => 10]); // activos

        if (!empty($skipUserIds)) {
            $query->andWhere(['not in', 'id', $skipUserIds]);
        }

        $usuarios = $query->asArray()->all();

        foreach ($usuarios as $u) {
            $this->crearNotificacion((int) $u['id'], $tipo, $titulo, $mensaje, $ticketId);
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
            echo " Notificación creada. Revisa la tabla 'notificaciones' en la BD.";
        } else {
            echo " Error creando notificación. Revisa runtime/logs/app.log";
        }

        exit;
    }

    /**
     * Exportar tickets a CSV (sin librerías externas)
     */
    public function actionExportar()
    {
        // Mismo TicketsSearch que el index — respeta todos los filtros activos incluyendo asignado_a por defecto
        $searchModel = new TicketsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        $tickets = $dataProvider->getModels();

        // --- Crear Excel ---
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $headers = [
            'Folio',
            'Fecha',
            'Estatus',
            'Consultor / Asignado a',
            'Esquema',
            'Usuario',
            'Cliente',
            'Sistema',
            'Servicio',
            'Descripción',
            'Solución',
            'Tiempo Efectivo',
            'Hora Inicio',
            'Hora Finalización',
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Función color
        function hexToARGB($hex)
        {
            $hex = str_replace('#', '', $hex);
            return 'FF' . strtoupper($hex);
        }

        // --- Estilo encabezado ---
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => hexToARGB('000000')]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => hexToARGB('A0BAA5')]
            ]
        ];

        $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);

        // --- CONFIGURACIÓN GENERAL (FUERA DEL FOREACH) ---
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:N1');
        $sheet->getStyle('A1:N1')->getAlignment()->setHorizontal('center');
        $sheet->getRowDimension(1)->setRowHeight(25);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        // Columnas con texto largo
        $sheet->getColumnDimension('J')->setWidth(45); // Descripción
        $sheet->getColumnDimension('K')->setWidth(45); // Solución

        $sheet->getStyle('J:K')->getAlignment()->setWrapText(true);
        $sheet->getStyle('J:K')->getAlignment()->setVertical(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
        );
        $sheet->getStyle('J:K')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
        );

        // Columnas con ancho fijo
        $sheet->getColumnDimension('L')->setWidth(18); // Tiempo Efectivo
        $sheet->getColumnDimension('M')->setWidth(15); // Hora Inicio
        $sheet->getColumnDimension('N')->setWidth(18); // Hora Finalización

        // AutoSize para el resto de columnas
        foreach (range('A', 'N') as $col) {
            if (!in_array($col, ['J', 'K', 'L', 'M', 'N'])) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // Centrar columnas
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ); // Folio

        $sheet->getStyle('B:B')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ); // Fecha

        $sheet->getStyle('C:C')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ); // Estatus

        $sheet->getStyle('D:D')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ); // Consultor

        $sheet->getStyle('E:E')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ); // Esquema

        $sheet->getStyle('L:L')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ); // Tiempo Efectivo

        $sheet->getStyle('M:N')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ); // Horas

        // --- DATOS ---
        $row = 2;

        $meses = [
            '01' => 'Enero',
            '02' => 'Febrero',
            '03' => 'Marzo',
            '04' => 'Abril',
            '05' => 'Mayo',
            '06' => 'Junio',
            '07' => 'Julio',
            '08' => 'Agosto',
            '09' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre'
        ];

        foreach ($tickets as $t) {

            // Fecha bonita
            if ($t->HoraFinalizo) {
                $fecha = strtotime($t->HoraFinalizo);
                $fechaFormateada = date('d', $fecha) . ' de ' . $meses[date('m', $fecha)] . ' del ' . date('Y', $fecha);
            } else {
                $fechaFormateada = '';
            }

            $sheet->setCellValue("A$row", $t->Folio);
            $sheet->setCellValue("B$row", $fechaFormateada);
            $sheet->setCellValue("C$row", $t->Estado);
            $sheet->setCellValue("D$row", $t->usuarioAsignado->Nombre ?? $t->usuarioAsignado->email ?? '');
            $sheet->setCellValue("E$row", $t->cliente->Tipo_servicio ?? '');
            $sheet->setCellValue("F$row", $t->Usuario_reporta ?? '');
            $sheet->setCellValue("G$row", $t->cliente->Nombre ?? '');
            $sheet->setCellValue("H$row", $t->sistema->Nombre ?? '');
            $sheet->setCellValue("I$row", $t->servicio->Nombre ?? '');
            $sheet->setCellValue("J$row", $t->Descripcion);
            $sheet->setCellValue("K$row", $t->Solucion);
            $sheet->setCellValue("L$row", $t->TiempoEfectivo ?? '');
            $sheet->setCellValue("M$row", $t->HoraInicio ? date('H:i', strtotime($t->HoraInicio)) : '');
            $sheet->setCellValue("N$row", $t->HoraFinalizo ? date('H:i', strtotime($t->HoraFinalizo)) : '');

            // Colores por estado
            $estado = strtolower($t->Estado);
            $color = null;

            if ($estado == 'abierto')
                $color = 'FFF4D262';
            elseif ($estado == 'en proceso')
                $color = 'FFFDE68A';

            // Zebra + estado (prioridad al estado)
            if ($color) {
                $sheet->getStyle("A$row:N$row")->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => $color]
                    ]
                ]);
            } elseif ($row % 2 == 0) {
                $sheet->getStyle("A$row:N$row")->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFF7F9F7']
                    ]
                ]);
            }

            // Ajuste altura automática
            $sheet->getRowDimension($row)->setRowHeight(-1);

            $row++;
        }


        // Bordes (AL FINAL)
        $lastRow = $row - 1;

        $sheet->getStyle("A1:N$lastRow")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC']
                ]
            ]
        ]);

        // --- Descargar ---
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Bitacora_' . date('d-m-Y_His') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Agregar comentario a un ticket (soporta multipart/form-data para adjuntos)
     */
    public function actionAgregarComentario()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $request = Yii::$app->request;

            // Soporte multipart (archivo) y JSON plano
            if (strpos($request->contentType, 'application/json') !== false) {
                $data = json_decode($request->getRawBody(), true);
                $ticketId = $data['ticket_id'] ?? null;
                $comentarioTx = $data['comentario'] ?? null;
                $tipo = $data['tipo'] ?? 'comentario';
                $destinatarioId = $data['destinatario_id'] ?? null;
            } else {
                $ticketId = $request->post('ticket_id');
                $comentarioTx = $request->post('comentario');
                $tipo = $request->post('tipo', 'comentario');
                $destinatarioId = $request->post('destinatario_id') ?: null;
            }

            $tieneArchivo = !empty($_FILES['archivo']['name']);
            if (empty($ticketId) || (empty($comentarioTx) && !$tieneArchivo)) {
                return ['success' => false, 'message' => 'Datos incompletos'];
            }
            // Si solo hay archivo sin texto, usar el nombre del archivo como texto
            if (empty($comentarioTx) && $tieneArchivo) {
                $comentarioTx = $_FILES['archivo']['name'];
            }

            $ticket = Tickets::findOne((int) $ticketId);
            if (!$ticket) {
                return ['success' => false, 'message' => 'Ticket no encontrado'];
            }

            $comentario = new Comentarios();
            $comentario->ticket_id = (int) $ticketId;
            $comentario->usuario_id = \Yii::$app->user->id;
            $comentario->comentario = (string) $comentarioTx;
            $comentario->tipo = $tipo;
            $comentario->destinatario_id = ($tipo === 'nota_interna' && $destinatarioId)
                ? (int) $destinatarioId : null;

            // ── Manejo de archivo adjunto ──────────────────────────────────
            $archivoNombre = null;
            $uploadDir = \Yii::getAlias('@webroot/uploads/comentarios/');
            $uploadedFile = \yii\web\UploadedFile::getInstanceByName('archivo');

            if ($uploadedFile && $uploadedFile->size > 0) {
                $ext = strtolower($uploadedFile->extension) ?: 'bin';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $archivoNombre = uniqid('cmnt_', true) . '.' . $ext;
                if (!$uploadedFile->saveAs($uploadDir . $archivoNombre)) {
                    return ['success' => false, 'message' => 'Error al guardar el archivo'];
                }
                $comentario->archivo = $archivoNombre;
            }
            // ───────────────────────────────────────────────────────────────

            if (!$comentario->save()) {
                if ($archivoNombre) {
                    @unlink($uploadDir . $archivoNombre);
                }
                return ['success' => false, 'errors' => $comentario->errors];
            }

            $usuarioActualNombre = Yii::$app->user->identity->Nombre ?? (Yii::$app->user->identity->email ?? '');
            $usuarioActualEmail = Yii::$app->user->identity->email ?? '';

            // Nota privada P2P → notificar solo al destinatario
            if ($tipo === 'nota_interna' && $comentario->destinatario_id) {
                $destId = (int) $comentario->destinatario_id;
                if ($destId !== (int) Yii::$app->user->id) {
                    $this->crearNotificacion(
                        $destId,
                        'mencion',
                        $usuarioActualNombre . ' te envió una nota privada en ticket ' . $ticket->Folio,
                        mb_substr($comentario->comentario, 0, 100),
                        $ticket->id
                    );
                }
            } else {
                // Notificación al asignado (solo para comentarios/soluciones públicas)
                if ($ticket->Asignado_a && (int) $ticket->Asignado_a !== (int) Yii::$app->user->id) {
                    $this->crearNotificacion(
                        (int) $ticket->Asignado_a,
                        'comentario',
                        'Nuevo comentario en ticket ' . $ticket->Folio,
                        $usuarioActualEmail . ' agregó un comentario',
                        $ticket->id
                    );
                }
            }

            // Notificaciones por menciones
            $emails = $this->extractMentionEmails($comentario->comentario);
            if (!empty($emails)) {
                $usuariosMencionados = Usuarios::find()
                    ->where(['lower(email)' => $emails])
                    ->all();

                foreach ($usuariosMencionados as $u) {
                    $uid = (int) $u->id;
                    if ($uid === (int) Yii::$app->user->id)
                        continue;
                    $this->crearNotificacionMencion($uid, $ticket, (int) $comentario->id);
                }
            }

            DevLog::log(
                DevLog::TIPO_CREAR,
                "Comentario [{$comentario->tipo}] en ticket [{$ticket->Folio}]",
                ['folio' => $ticket->Folio, 'tipo' => $comentario->tipo, 'tiene_archivo' => (bool) $archivoNombre, 'preview' => mb_substr($comentario->comentario, 0, 150)],
                'tickets',
                $ticket->id,
                'Comentarios'
            );

            $archivoUrl = $archivoNombre
                ? \yii\helpers\Url::to('@web/uploads/comentarios/' . $archivoNombre, true)
                : null;

            return [
                'success' => true,
                'comentario' => [
                    'id' => $comentario->id,
                    'usuario' => $usuarioActualEmail,
                    'comentario' => $comentario->comentario,
                    'tipo' => $comentario->tipo,
                    'fecha' => date('d/m/Y H:i'),
                    'archivo' => $archivoUrl,
                    'esImagen' => $archivoNombre ? in_array(strtolower(pathinfo($archivoNombre, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']) : false,
                ]
            ];

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

            $currentUserId = (int) Yii::$app->user->id;

            // Notas privadas solo visibles para remitente o destinatario
            $comentarios = Comentarios::find()
                ->where(['ticket_id' => $ticketId])
                ->andWhere([
                    'OR',
                    ['!=', 'tipo', 'nota_interna'],
                    ['destinatario_id' => null],
                    ['usuario_id' => $currentUserId],
                    ['destinatario_id' => $currentUserId],
                ])
                ->with(['usuario', 'destinatario'])
                ->orderBy(['fecha_creacion' => SORT_ASC])
                ->all();

            $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $result = [];
            foreach ($comentarios as $com) {
                $archivoUrl = null;
                $esImagen = false;
                if (!empty($com->archivo)) {
                    $archivoUrl = \yii\helpers\Url::to('@web/uploads/comentarios/' . $com->archivo);
                    $esImagen = in_array(strtolower(pathinfo($com->archivo, PATHINFO_EXTENSION)), $imageExts);
                }

                $avatarRaw = $com->usuario->avatar ?? null;
                $avatarUrl = ($avatarRaw && str_starts_with($avatarRaw, '/uploads/'))
                    ? \yii\helpers\Url::to('@web' . $avatarRaw, true)
                    : null;

                $result[] = [
                    'id' => $com->id,
                    'usuario' => $com->usuario->email ?? 'Usuario desconocido',
                    'nombre' => $com->usuario->Nombre ?? 'Usuario',
                    'avatar' => $avatarUrl,
                    'comentario' => $com->comentario,
                    'tipo' => $com->tipo,
                    'fecha' => $com->fecha_creacion ? date('d/m/Y H:i', strtotime($com->fecha_creacion)) : date('d/m/Y H:i'),
                    'archivo' => $archivoUrl,
                    'esImagen' => $esImagen,
                    'destinatarioId' => $com->destinatario_id,
                    'destinatarioNombre' => $com->destinatario ? ($com->destinatario->Nombre ?: $com->destinatario->email) : null,
                ];
            }

            return ['success' => true, 'comentarios' => $result];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Obtener cantidad de comentarios para un ticket
     */
    public function actionContarComentarios()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $ticket_id = Yii::$app->request->post('ticket_id');
        if (!$ticket_id) {
            return ['success' => false, 'count' => 0];
        }

        $count = Comentarios::find()
            ->where(['ticket_id' => $ticket_id])
            ->count();

        return ['success' => true, 'count' => $count];
    }
}
