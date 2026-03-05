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
    $searchModel  = new TicketsSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    $request = Yii::$app->request;

    // Para la vista - obtener el parámetro asignado_a si existe
    $asignadoParam = $request->get('asignado_a', null);
    if ($asignadoParam === null) {
        $asignadoFiltro = Yii::$app->user->isGuest ? '' : Yii::$app->user->id;
    } else {
        $asignadoFiltro = $asignadoParam;
    }

    $clientes  = Clientes::find()
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
        'searchModel'     => $searchModel,
        'dataProvider'    => $dataProvider,
        'clientes'        => $clientes,
        'sistemas'        => $sistemas,
        'servicios'       => $servicios,
        'Usuarios'        => $Usuarios,
        'asignadoFiltro'  => $asignadoFiltro, 
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
        // Folio provisional para mostrar en el form (puede colisionar en concurrencia)
        // El folio definitivo se asigna después del INSERT usando el ID real
        $model->Folio = str_pad(Tickets::find()->max('id') + 1, 4, '0', STR_PAD_LEFT);

        // CORRECCIÓN: Seleccionar 'id' y 'Nombre', y mapear id => Nombre
        $consultores = \app\models\Usuarios::find()
            ->select(['id', 'Nombre']) 
            ->where(['rol' => 'Consultores'])
            ->orderBy(['Nombre' => SORT_ASC])
            ->asArray()
            ->all();



        // El primer parámetro es la clave (lo que se guarda: id), el segundo es el valor (lo que se ve: Nombre)
        $consultoresList = \yii\helpers\ArrayHelper::map($consultores, 'id', 'Nombre');
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
            
            //  ASEGURAR QUE CREADO_POR SE GUARDE CON EL USUARIO ACTUAL (ANTES DE SAVE)
            $model->Creado_por = Yii::$app->user->id;
            
            if ($model->save()) {

                // Folio definitivo basado en el ID real asignado por la BD (sin race condition)
                $model->Folio = str_pad($model->id, 4, '0', STR_PAD_LEFT);
                $model->save(false); // solo actualizar Folio, sin re-validar

                if ($model->Asignado_a) {
                    $this->crearNotificacion(
                        $model->Asignado_a,
                        'asignado',
                        'Nuevo ticket asignado',
                        'Se te ha asignado un nuevo ticket: ' . $model->Folio,
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
            'consultoresList' => $consultoresList,

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

        // Guardar valores originales ANTES del load() para proteger campos y detectar cambios
        $asignadoAntes  = $model->Asignado_a;
        $estadoAntes    = $model->Estado;
        $solucionOrig   = $model->Solucion;
        $tiempoEfOrig   = $model->TiempoEfectivo;
        $horaFinOrig    = $model->HoraFinalizo;

        $clientes = Clientes::find()->asArray()->all();
        $sistemas = Sistemas::find()->asArray()->all();
        $servicios = Servicios::find()->asArray()->all();
        $usuarios = Usuarios::find()->asArray()->all();

        if ($model->load(Yii::$app->request->post())) {
            // Bloquear cierre desde esta vista — debe hacerse desde index con el flujo completo
            if (mb_strtolower(trim($estadoAntes)) !== 'cerrado' &&
                mb_strtolower(trim($model->Estado)) === 'cerrado') {
                $model->Estado = $estadoAntes;
                Yii::$app->session->setFlash('error', 'Para cerrar un ticket debes hacerlo desde la lista de tickets (incluye solución, tiempo efectivo y fecha de cierre).');
            }

            // Los campos de cierre solo se asignan por actionSaveSolution — nunca desde aquí
            $model->Solucion       = $solucionOrig;
            $model->TiempoEfectivo = $tiempoEfOrig;
            $model->HoraFinalizo   = $horaFinOrig;

            if ($model->save()) {
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
            $folio = $model->Folio; // Guardar el folio antes de eliminar
            $asignadoA = $model->Asignado_a; // Guardar quién estaba asignado

            if ($model->delete()) {
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

                // Folio definitivo basado en el ID real
                $ticket->Folio = str_pad($ticket->id, 4, '0', STR_PAD_LEFT);
                $ticket->save(false);

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

        $ticket = Tickets::findOne($data['id'] ?? null);
        if (!$ticket) {
            return ['success' => false, 'message' => 'Ticket no encontrado'];
        }

        $estadoAnterior = (string)$ticket->Estado;
        $ticket->Estado = (string)($data['estado'] ?? $ticket->Estado);

        if (!$ticket->save()) {
            return ['success' => false, 'message' => 'No se pudo guardar', 'errors' => $ticket->errors];
        }

      
        $usuarioActualEmail = Yii::$app->user->identity->email ?? 'Alguien';

     
        if ($estadoAnterior !== $ticket->Estado && $ticket->Asignado_a) {
            $this->crearNotificacion(
                (int)$ticket->Asignado_a,
                'estado_cambio',
                'Cambio de estado: ' . $ticket->Folio,
                $usuarioActualEmail . ' cambió el estado a ' . $ticket->Estado,
                $ticket->id
            );
        }

      
        $estadoNormalizado = mb_strtolower(trim($ticket->Estado));
        if ($estadoAnterior !== $ticket->Estado && $estadoNormalizado === 'cerrado') {

            $skip = [];
            if (!empty($ticket->Asignado_a)) $skip[] = (int)$ticket->Asignado_a;
            if (!Yii::$app->user->isGuest) $skip[] = (int)Yii::$app->user->id;

            $this->notificarRoles(
                ['Administracion', 'Administradores', 'Desarrolladores'],
                'ticket_cerrado',
                'Ticket cerrado: ' . $ticket->Folio,
                $usuarioActualEmail . ' cerró el ticket ' . $ticket->Folio,
                $ticket->id,
                $skip
            );
        }

        return ['success' => true];
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
                        $horaFinalizo = date('Y-m-d\TH:i', (int)$ticket->HoraFinalizo);
                    } else {
                        $horaFinalizo = date('Y-m-d\TH:i', strtotime($ticket->HoraFinalizo));
                    }
                }

                return [
                    'success' => true,
                    'ticket' => [
                        'HoraInicio'      => $horaInicio,
                        'HoraFinalizo'    => $horaFinalizo,
                        'Solucion'        => $ticket->Solucion ?? '',
                        'TiempoEfectivo'  => $ticket->TiempoEfectivo ?? '',
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
                $ticket->Solucion       = $data['solucion'] ?? null;
                $ticket->TiempoEfectivo = $this->minutesToHM($nuevoMin);
                $ticket->Estado         = 'CERRADO'; // cerrar atómicamente con la solución

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
                        $cliente->Tiempo = $this->minutesToHM($clienteMinAntes - $deltaMin);

                        if (!$cliente->save()) {
                            $transaction->rollBack();
                            return [
                                'success' => false,
                                'message' => 'Ticket guardado, pero falló actualizar tiempo del cliente',
                                'errors'  => $cliente->errors,
                            ];
                        }
                    }
                }

                $transaction->commit();

                return [
                    'success'      => true,
                    'message'      => 'Solución guardada y ticket cerrado',
                    'ticket_viejo' => $this->minutesToHM($viejoMin),
                    'ticket_nuevo' => $this->minutesToHM($nuevoMin),
                    'delta_min'    => $deltaMin,
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
    if ($valor === null) return 0;
    $s = trim((string)$valor);
    if ($s === '') return 0;

    $s = str_replace(',', '.', $s);

    // H:MM
    if (preg_match('/^\s*(-?\d+)\s*:\s*(\d{1,2})\s*$/', $s, $m)) {
        $h  = (int)$m[1];
        $mm = (int)$m[2];
        return ($h * 60) + ($h < 0 ? -$mm : $mm);
    }

    //  H.MM donde MM son minutos (acepta "0.16", "1.30", ".16", "-.16")
    if (preg_match('/^\s*(-?\d*)\.(\d{1,2})\s*$/', $s, $m)) {
        $hStr = $m[1];
        $mm   = (int)$m[2];

        // si viene ".16" => horas = 0
        $h = ($hStr === '' || $hStr === '-') ? 0 : (int)$hStr;

        // si viene "-.16" => horas = 0 pero con signo negativo
        $isNeg = ($hStr === '-');

        $min = ($h * 60) + $mm;
        return $isNeg ? -$min : ($h < 0 ? -abs($min) : $min);
    }

    // Entero: "1" = 1 hora (60 min)
    if (preg_match('/^\s*-?\d+\s*$/', $s)) {
        return ((int)$s) * 60;
    }

    // Fallback: si viene "1.5" (decimal real) => horas * 60
    if (preg_match('/-?\d+(\.\d+)?/', $s, $m)) {
        return (int)round(((float)$m[0]) * 60);
    }

    return 0;
}


        protected function roundUpTo15(int $minutes): int
        {
            if ($minutes <= 0) return 0;
            return (int)(ceil($minutes / 15) * 15);
        }

        protected function minutesToHM(int $minutes): string
        {
            $sign = $minutes < 0 ? '-' : '';
            $minutes = abs($minutes);

            $h = intdiv($minutes, 60);
            $m = $minutes % 60;

            return $sign . $h . '.' . str_pad((string)$m, 2, '0', STR_PAD_LEFT);
        }












    private function crearNotificacionMencion(int $usuarioId, Tickets $ticket, int $comentarioId): void
    {
        // Evitar duplicar la misma notificación por comentario+usuario
        $exists = Notificaciones::find()
            ->where([
                'usuario_id' => $usuarioId,
                'ticket_id'  => $ticket->id,
                'tipo'       => 'mencion',
                // Si NO tienes un campo "ref_id" o similar, puedes quitar esta parte.
                // 'ref_id'  => $comentarioId,
            ])
            ->andWhere(['>=', 'fecha_creacion', date('Y-m-d H:i:s', time() - 60)]) // anti-duplicado básico 60s
            ->exists();

        if ($exists) return;

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
            $this->crearNotificacion((int)$u['id'], $tipo, $titulo, $mensaje, $ticketId);
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
        $query = Tickets::find()
            ->with(['cliente', 'sistema', 'servicio', 'usuarioAsignado']);

        // --- Filtros ---
        if (!empty($_GET['folio'])) {
            $query->andWhere(['like', 'Folio', $_GET['folio']]);
        }
        if (!empty($_GET['Cliente_id'])) {
            $query->andWhere(['Cliente_id' => $_GET['Cliente_id']]);
        }
        if (!empty($_GET['Sistema_id'])) {
            $query->andWhere(['Sistema_id' => $_GET['Sistema_id']]);
        }
        if (!empty($_GET['Servicio_id'])) {
            $query->andWhere(['Servicio_id' => $_GET['Servicio_id']]);
        }
        if (!empty($_GET['Asignado_a'])) {
            $query->andWhere(['Asignado_a' => $_GET['Asignado_a']]);
        }
        if (!empty($_GET['Prioridad'])) {
            $query->andWhere(['Prioridad' => $_GET['Prioridad']]);
        }
        if (!empty($_GET['Estado'])) {
            $query->andWhere(['Estado' => $_GET['Estado']]);
        }

        // ✅ FILTROS DE FECHA (HoraProgramada)
        // Filtro por mes
        if (!empty($_GET['mes'])) {
            $mes = $_GET['mes'];
            $primerDia = $mes . '-01 00:00:00';
            $ultimoDia = date('Y-m-t 23:59:59', strtotime($mes . '-01'));
            $query->andWhere(['>=', 'HoraProgramada', $primerDia])
                  ->andWhere(['<=', 'HoraProgramada', $ultimoDia]);
        }

        // Filtro por rango de fechas (DESDE - HASTA)
        if (!empty($_GET['fecha_inicio'])) {
            $fechaInicio = strtotime($_GET['fecha_inicio']);
            if ($fechaInicio) {
                $query->andWhere(['>=', 'HoraProgramada', date('Y-m-d 00:00:00', $fechaInicio)]);
            }
        }

        if (!empty($_GET['fecha_fin'])) {
            $fechaFin = strtotime($_GET['fecha_fin']);
            if ($fechaFin) {
                $query->andWhere(['<=', 'HoraProgramada', date('Y-m-d 23:59:59', $fechaFin)]);
            }
        }

        $tickets = $query->orderBy(['id' => SORT_DESC])->all();

        // --- Crear Excel ---
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $headers = [
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
        ];

        $sheet->fromArray($headers, null, 'A1');

        function hexToARGB($hex)
        {
            $hex = str_replace('#', '', $hex);
            return 'FF' . strtoupper($hex); // FF = 100% opaco
        }
        // --- Estilos encabezados ---
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


        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);

        // Autosize columnas
        foreach (range('A', 'L') as $col) {
            $spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        }

        // --- Datos ---
        $row = 2;
        foreach ($tickets as $t) {
            $sheet->setCellValue("A$row", $t->Folio);
            $sheet->setCellValue("B$row", $t->cliente->Nombre ?? '');
            $sheet->setCellValue("C$row", $t->sistema->Nombre ?? '');
            $sheet->setCellValue("D$row", $t->servicio->Nombre ?? '');
            $sheet->setCellValue("E$row", $t->Usuario_reporta ?? '');
            $sheet->setCellValue("F$row", $t->usuarioAsignado->email ?? '');
            $sheet->setCellValue("G$row", $t->HoraProgramada ? date('d/m/Y H:i', strtotime($t->HoraProgramada)) : '');
            $sheet->setCellValue("H$row", $t->HoraInicio ? date('d/m/Y H:i', strtotime($t->HoraInicio)) : '');
            $sheet->setCellValue("I$row", $t->Prioridad);
            $sheet->setCellValue("J$row", $t->Estado);
            $sheet->setCellValue("K$row", $t->Descripcion);
            $sheet->setCellValue("L$row", date('d/m/Y H:i', strtotime($t->Fecha_creacion)));

            // --- Colorear filas según Estado ---
            $estado = strtolower($t->Estado);

            $row++;
        }

        // --- Descargar XLSX ---
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="tickets_' . date('Y-m-d_His') . '.xlsx"');
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
                $ticketId     = $data['ticket_id'] ?? null;
                $comentarioTx = $data['comentario'] ?? null;
                $tipo         = $data['tipo'] ?? 'comentario';
            } else {
                $ticketId     = $request->post('ticket_id');
                $comentarioTx = $request->post('comentario');
                $tipo         = $request->post('tipo', 'comentario');
            }

            if (empty($ticketId) || empty($comentarioTx)) {
                return ['success' => false, 'message' => 'Datos incompletos'];
            }

            $ticket = Tickets::findOne((int)$ticketId);
            if (!$ticket) {
                return ['success' => false, 'message' => 'Ticket no encontrado'];
            }

            $comentario = new Comentarios();
            $comentario->ticket_id  = (int)$ticketId;
            $comentario->usuario_id = \Yii::$app->user->id;
            $comentario->comentario = (string)$comentarioTx;
            $comentario->tipo       = $tipo;

            // ── Manejo de archivo adjunto ──────────────────────────────────
            $archivoNombre = null;
            $uploadDir     = \Yii::getAlias('@webroot/uploads/comentarios/');
            $uploadedFile  = \yii\web\UploadedFile::getInstanceByName('archivo');

            if ($uploadedFile && $uploadedFile->size > 0) {
                $ext = strtolower($uploadedFile->extension);
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'docx', 'xlsx'];

                if (!in_array($ext, $allowed)) {
                    return ['success' => false, 'message' => 'Tipo de archivo no permitido. Formatos: ' . implode(', ', $allowed)];
                }
                if ($uploadedFile->size > 8 * 1024 * 1024) {
                    return ['success' => false, 'message' => 'El archivo no debe superar 8 MB'];
                }

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

            $usuarioActualEmail = Yii::$app->user->identity->email ?? '';

            // Notificación al asignado
            if ($ticket->Asignado_a && (int)$ticket->Asignado_a !== (int)Yii::$app->user->id) {
                $this->crearNotificacion(
                    (int)$ticket->Asignado_a,
                    'comentario',
                    'Nuevo comentario en ticket ' . $ticket->Folio,
                    $usuarioActualEmail . ' agregó un comentario',
                    $ticket->id
                );
            }

            // Notificaciones por menciones
            $emails = $this->extractMentionEmails($comentario->comentario);
            if (!empty($emails)) {
                $usuariosMencionados = Usuarios::find()
                    ->where(['lower(email)' => $emails])
                    ->all();

                foreach ($usuariosMencionados as $u) {
                    $uid = (int)$u->id;
                    if ($uid === (int)Yii::$app->user->id) continue;
                    $this->crearNotificacionMencion($uid, $ticket, (int)$comentario->id);
                }
            }

            $archivoUrl = $archivoNombre
                ? \yii\helpers\Url::to('@web/uploads/comentarios/' . $archivoNombre, true)
                : null;

            return [
                'success' => true,
                'comentario' => [
                    'id'         => $comentario->id,
                    'usuario'    => $usuarioActualEmail,
                    'comentario' => $comentario->comentario,
                    'tipo'       => $comentario->tipo,
                    'fecha'      => date('d/m/Y H:i'),
                    'archivo'    => $archivoUrl,
                    'esImagen'   => $archivoNombre ? in_array(strtolower(pathinfo($archivoNombre, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif','webp']) : false,
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

            $comentarios = Comentarios::find()
                ->where(['ticket_id' => $ticketId])
                ->with('usuario')
                ->orderBy(['fecha_creacion' => SORT_ASC])
                ->all();

            $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $result = [];
            foreach ($comentarios as $com) {
                $archivoUrl = null;
                $esImagen   = false;
                if (!empty($com->archivo)) {
                    $archivoUrl = \yii\helpers\Url::to('@web/uploads/comentarios/' . $com->archivo, true);
                    $esImagen   = in_array(strtolower(pathinfo($com->archivo, PATHINFO_EXTENSION)), $imageExts);
                }

                $result[] = [
                    'id'         => $com->id,
                    'usuario'    => $com->usuario->email ?? 'Usuario desconocido',
                    'nombre'     => $com->usuario->Nombre ?? 'Usuario',
                    'comentario' => $com->comentario,
                    'tipo'       => $com->tipo,
                    'fecha'      => $com->fecha_creacion ? date('d/m/Y H:i', strtotime($com->fecha_creacion)) : date('d/m/Y H:i'),
                    'archivo'    => $archivoUrl,
                    'esImagen'   => $esImagen,
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
