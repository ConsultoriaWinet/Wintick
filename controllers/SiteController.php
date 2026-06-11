<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Tickets;
use app\models\Usuarios;
use app\models\DevLog;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'requestpassword', 'resetpassword', 'contact', 'captcha', 'about'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'logout', 'get-tickets', 'get-tickets-dia', 'check-update', 'get-cheka', 'get-dashboard-stats'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */


    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $consultores = Usuarios::find()
            ->where(['id' => Tickets::find()->select('Asignado_a')->distinct()])
            ->all();

        $esMonitor = !Yii::$app->user->isGuest
            && Yii::$app->user->identity->rol === 'Monitor';

        $inicio = date('Y-m-01 00:00:00');
        $fin = date('Y-m-t 23:59:59');

        $estadisticasTickets = [
            'total' => Tickets::find()
                ->where(['between', 'Fecha_creacion', $inicio, $fin])
                ->count(),

            'abiertos' => Tickets::find()
                ->where(['Estado' => 'ABIERTO'])
                ->andWhere(['between', 'Fecha_creacion', $inicio, $fin])
                ->count(),

            'enProceso' => Tickets::find()
                ->where(['Estado' => 'EN PROCESO'])
                ->andWhere(['between', 'Fecha_creacion', $inicio, $fin])
                ->count(),

            'cerrados' => Tickets::find()
                ->where(['Estado' => 'CERRADO'])
                ->andWhere(['between', 'Fecha_creacion', $inicio, $fin])
                ->count(),
        ];

        $comparacionMes = [
            'diferencia' => 0,
            'porcentaje' => 0,
        ];

        $tasaResolucion = [
            'tasa' => $estadisticasTickets['total'] > 0
                ? round(
                    ($estadisticasTickets['cerrados'] / $estadisticasTickets['total']) * 100,
                    2
                )
                : 0,
        ];

        return $this->render('index', [
            'consultores' => $consultores,
            'esMonitor' => $esMonitor,
            'estadisticasTickets' => $estadisticasTickets,
            'comparacionMes' => $comparacionMes,
            'tasaResolucion' => $tasaResolucion,
        ]);
    }

    /**
     * Endpoint Cheka: devuelve tickets del día agrupados por consultor.
     */
    public function actionGetCheka()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $fecha = Yii::$app->request->get('fecha', date('Y-m-d'));
        // Validar formato
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $fecha = date('Y-m-d');
        }

        // Todos los consultores que tienen tickets programados en esa fecha
        // o simplemente todos los usuarios que tienen tickets alguna vez
        $usuarios = Usuarios::find()
            ->where(['id' => Tickets::find()->select('Asignado_a')->distinct()])
            ->orderBy(['Nombre' => SORT_ASC])
            ->all();

        $result = [];
        foreach ($usuarios as $u) {
            $tickets = Tickets::find()
                ->where(['Asignado_a' => $u->id])
                ->andWhere(['LIKE', 'HoraInicio', $fecha . '%', false])
                ->orderBy(['HoraInicio' => SORT_ASC])
                ->with(['cliente', 'sistema', 'servicio'])
                ->all();

            // Incluir usuario aunque no tenga tickets (para mostrar fila vacía)
            $avatar = $u->avatar ? (Yii::getAlias('@web') . $u->avatar) : null;
            $row = [
                'id' => $u->id,
                'nombre' => $u->Nombre ?: $u->email,
                'color' => $u->color ?: '#6b7280',
                'avatar' => $avatar,
                'tickets' => [],
            ];

            foreach ($tickets as $t) {
                if (empty($t->HoraInicio))
                    continue;
                $ts = strtotime($t->HoraInicio);
                $horaStr = date('H:i', $ts);
                $horaMin = (int) date('H', $ts) * 60 + (int) date('i', $ts);

                // Duración en minutos desde TiempoEfectivo (formato H.MM) o 60 min por defecto
                $durMin = 60;
                if (!empty($t->TiempoEfectivo)) {
                    $parts = explode('.', $t->TiempoEfectivo);
                    $h = (int) ($parts[0] ?? 0);
                    $m = (int) ($parts[1] ?? 0);
                    $durMin = max(30, $h * 60 + $m);
                }

                $row['tickets'][] = [
                    'id' => $t->id,
                    'folio' => $t->Folio,
                    'titulo' => $t->Descripcion ?? '',
                    'hora' => $horaStr,
                    'horaMin' => $horaMin,
                    'durMin' => $durMin,
                    'cliente' => $t->cliente ? $t->cliente->Nombre : '-',
                    'sistema' => $t->sistema ? $t->sistema->Nombre : '-',
                    'servicio' => $t->servicio ? $t->servicio->Nombre : '-',
                    'estado' => $t->Estado,
                    'prioridad' => $t->Prioridad,
                ];
            }

            $result[] = $row;
        }

        return ['fecha' => $fecha, 'usuarios' => $result];
    }


    /** Endpoint para actualizar tarjetas*/
    public function actionGetDashboardStats()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $inicio = date('Y-m-01 00:00:00');
        $fin = date('Y-m-t 23:59:59');

        $total = Tickets::find()
            ->where(['between', 'Fecha_creacion', $inicio, $fin])
            ->count();

        $abiertos = Tickets::find()
            ->where(['Estado' => 'ABIERTO'])
            ->andWhere(['between', 'Fecha_creacion', $inicio, $fin])
            ->count();

        $enProceso = Tickets::find()
            ->where(['Estado' => 'EN PROCESO'])
            ->andWhere(['between', 'Fecha_creacion', $inicio, $fin])
            ->count();

        $cerrados = Tickets::find()
            ->where(['Estado' => 'CERRADO'])
            ->andWhere(['between', 'Fecha_creacion', $inicio, $fin])
            ->count();

        return [
            'total' => $total,
            'abiertos' => $abiertos,
            'enProceso' => $enProceso,
            'cerrados' => $cerrados,
        ];
    }

    /** Endpoint para actualizar tarjetas FIN*/
    /**
     * Endpoint liviano para el polling del Monitor.
     * Devuelve el timestamp del último cambio en tickets.
     */
    public function actionCheckUpdate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['lastUpdate' => Tickets::find()->max('Fecha_actualizacion')];
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            // Usuario autenticado
            $usuario = Yii::$app->user->identity;

            // ===============================================================
            // 🔵 1. Sincronizar el rol de la base de datos con RBAC
            // ===============================================================
            $auth = Yii::$app->authManager;

            // Remove any existing assignments
            $auth->revokeAll($usuario->id);

            // Get the role from the DB
            $rolBD = $usuario->rol;

            // Assign the role from BD if it exists in RBAC
            $rolRBAC = $auth->getRole($rolBD);
            if ($rolRBAC) {
                $auth->assign($rolRBAC, $usuario->id);
            }

            // ===============================================================
            // 🟢 2. Mensaje de bienvenida
            // ===============================================================
            $nombre = $usuario->Nombre ?? $usuario->email;

            Yii::$app->session->setFlash('welcome', [
                'nombre' => $nombre,
                'rol' => $rolBD,
                'email' => $usuario->email,
            ]);

            // ── LOG: inicio de sesión exitoso ──
            DevLog::log(
                DevLog::TIPO_LOGIN,
                "Inicio de sesión exitoso — usuario [{$nombre}] con rol [{$rolBD}] desde " . Yii::$app->request->userIP,
                [
                    'usuario_id' => $usuario->id,
                    'usuario_email' => $usuario->email,
                    'rol' => $rolBD,
                    'user_agent' => Yii::$app->request->userAgent,
                    'remember_me' => (bool) Yii::$app->request->post('LoginForm')['rememberMe'] ?? false,
                ],
                'site'
            );

            // Monitor va al calendario, el resto directo a tickets
            if ($rolBD === 'Monitor') {
                return $this->goHome();
            }
            return $this->redirect(['/tickets/index']);
        }

        // Limpia password del formulario
        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }


    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        // ── LOG: cierre de sesión (antes de hacer logout para tener identidad) ──
        if (!Yii::$app->user->isGuest) {
            $identity = Yii::$app->user->identity;
            DevLog::log(
                DevLog::TIPO_LOGOUT,
                "Cierre de sesión — usuario [{$identity->Nombre}] con rol [{$identity->rol}]",
                [
                    'usuario_id' => $identity->id,
                    'usuario_email' => $identity->email,
                    'rol' => $identity->rol,
                ],
                'site'
            );
        }

        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Get tickets for calendar (filtrados por consultor)
     */
    public function actionGetTickets($consultorId = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $userId = Yii::$app->user->id;

        // Roles con visibilidad total ven todos los tickets del calendario
        $rol = Yii::$app->user->identity->rol ?? '';
        $rolesVerTodo = ['Administradores', 'Supervisores', 'Desarrolladores', 'Administracion'];

        if (!in_array($rol, $rolesVerTodo, true)) {
            // Consultores y otros roles: solo ven sus propios tickets
            $consultorId = $userId;
        }

        // Solo cargar tickets de los últimos 3 meses y los próximos 2 meses
        $desde = date('Y-m-d 00:00:00', strtotime('-3 months'));
        $hasta = date('Y-m-d 23:59:59', strtotime('+2 months'));

        $query = \app\models\Tickets::find()
            ->with(['cliente', 'sistema', 'servicio', 'usuarioAsignado'])
            ->where(['between', 'Fecha_creacion', $desde, $hasta])
            ->limit(500);

        if (!empty($consultorId)) {
            $query->andWhere(['Asignado_a' => (int) $consultorId]);
        }

        $tickets = $query->all();

        $events = [];
        foreach ($tickets as $t) {
            //  Usa HoraInicio si existe, si no usa Fecha_creacion
            $start = $t->HoraInicio ?: $t->Fecha_creacion;

            if (!$start) {
                continue;
            }

            $events[] = [
                'id' => $t->id,
                'title' => $t->Folio,
                'start' => date('c', strtotime($start)), // Formato ISO 8601

                // Opcional: colores por consultor
                'backgroundColor' => $t->usuarioAsignado->color ?? '#8BA590',
                'borderColor' => $t->usuarioAsignado->color ?? '#8BA590',

                'extendedProps' => [
                    'consultorNombre' => $t->usuarioAsignado->Nombre ?? $t->usuarioAsignado->email ?? 'N/A',
                    'cliente' => $t->cliente->Nombre ?? 'N/A',
                    'sistema' => $t->sistema->Nombre ?? 'N/A',
                    'servicio' => $t->servicio->Nombre ?? 'N/A',
                    'prioridad' => $t->Prioridad ?? 'N/A',
                    'estado' => $t->Estado ?? 'N/A',
                    'description' => $t->Descripcion ?? '',
                ],
            ];
        }

        return $events;
    }

    public function actionGetTicketsDia($fecha = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!$fecha || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $fecha = date('Y-m-d');
        }

        $desde = $fecha . ' 00:00:00';
        $hasta = $fecha . ' 23:59:59';

        $userId = Yii::$app->user->id;
        $soloMios = Yii::$app->user->can('verTickets') && !Yii::$app->user->can('asignarTicket');

        $query = \app\models\Tickets::find()
            ->with(['cliente', 'sistema', 'servicio', 'usuarioAsignado'])
            ->where(
                '(HoraInicio BETWEEN :d AND :h) OR (HoraInicio IS NULL AND Fecha_creacion BETWEEN :d2 AND :h2)',
                [':d' => $desde, ':h' => $hasta, ':d2' => $desde, ':h2' => $hasta]
            )
            ->orderBy(['HoraInicio' => SORT_ASC, 'Fecha_creacion' => SORT_ASC]);

        if ($soloMios) {
            $query->andWhere(['Asignado_a' => $userId]);
        }

        $tickets = $query->all();
        $result = [];

        foreach ($tickets as $t) {
            $u = $t->usuarioAsignado;
            $avatarRaw = $u->avatar ?? null;
            $avatarUrl = ($avatarRaw && str_starts_with($avatarRaw, '/uploads/'))
                ? \yii\helpers\Url::to('@web' . $avatarRaw, true) : null;

            $start = $t->HoraInicio ?: $t->Fecha_creacion;

            $result[] = [
                'id' => $t->id,
                'folio' => $t->Folio,
                'estado' => $t->Estado,
                'prioridad' => $t->Prioridad,
                'descripcion' => $t->Descripcion,
                'cliente' => $t->cliente->Nombre ?? '—',
                'sistema' => $t->sistema->Nombre ?? '—',
                'servicio' => $t->servicio->Nombre ?? '—',
                'hora' => $start ? date('H:i', strtotime($start)) : null,
                'asignado' => [
                    'nombre' => $u->Nombre ?? $u->email ?? '—',
                    'color' => $u->color ?? '#6b7280',
                    'avatar' => $avatarUrl,
                    'ini' => $u ? mb_strtoupper(mb_substr($u->Nombre ?? $u->email ?? '?', 0, 1, 'UTF-8'), 'UTF-8') : '?',
                ],
            ];
        }

        return $result;
    }


    /**
     * Request password reset
     */
    public function actionRequestpassword()
    {
        // Si ya está logeado, redirigir
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        // Si se envió el formulario
        if (Yii::$app->request->isPost) {
            $email = Yii::$app->request->post('email');

            // Buscar usuario por email
            $usuario = Usuarios::findOne(['email' => $email]);

            if ($usuario) {
                // Token criptográfico seguro de 8 chars (base64url) — ~68 mil millones de combinaciones
                $token = Yii::$app->security->generateRandomString(8);

                // Guardar token y marcar timestamp para verificar expiración (1 hora)
                $usuario->password_reset_token = $token;
                $usuario->updated_at = time();

                if ($usuario->save(false)) {
                    // Enviar email con el token
                    try {
                        Yii::$app->mailer->compose(
                            ['html' => 'password-reset-token-html'],
                            [
                                'token' => $token,
                                'userName' => $usuario->Nombre ?? $usuario->email,
                            ]
                        )
                            ->setFrom(['consultoria@winetpc.com' => 'Wintick - Sistema de Tickets'])
                            ->setTo($usuario->email)
                            ->setSubject('Recuperación de Contraseña - Token de Verificación')
                            ->send();

                        Yii::$app->session->setFlash(
                            'success',
                            'Se ha enviado un código de verificación a tu correo electrónico.'
                        );

                        // Redirigir a la página de reset
                        return $this->redirect(['site/resetpassword']);

                    } catch (\Exception $e) {
                        Yii::$app->session->setFlash(
                            'error',
                            'Error al enviar el correo: ' . $e->getMessage()
                        );
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Error al generar el token.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'No existe ningún usuario con ese email.');
            }
        }

        return $this->render('requestpassword');
    }

    /**
     * Reset password with token
     */
    public function actionResetpassword()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        // Si se envió el formulario
        if (Yii::$app->request->isPost) {
            $token = Yii::$app->request->post('token');
            $newPassword = Yii::$app->request->post('new_password');
            $confirmPassword = Yii::$app->request->post('confirm_password');

            // Validar que las contraseñas coincidan
            if ($newPassword !== $confirmPassword) {
                Yii::$app->session->setFlash('error', 'Las contraseñas no coinciden.');
                return $this->refresh();
            }

            // Buscar usuario con el token
            $usuario = Usuarios::findOne(['password_reset_token' => $token]);

            if ($usuario) {
                // Verificar expiración del token (1 hora = 3600 segundos desde updated_at)
                if ($usuario->updated_at + 3600 < time()) {
                    $usuario->password_reset_token = null;
                    $usuario->save(false);
                    Yii::$app->session->setFlash('error', 'El código ha expirado. Solicita uno nuevo.');
                    return $this->redirect(['site/requestpassword']);
                }

                // Cambiar la contraseña
                $usuario->password_hash = Yii::$app->security->generatePasswordHash($newPassword);

                // BORRAR EL TOKEN
                $usuario->password_reset_token = null;

                if ($usuario->save(false)) {
                    Yii::$app->session->setFlash(
                        'success',
                        'Contraseña cambiada exitosamente. Ya puedes iniciar sesión.'
                    );
                    return $this->redirect(['site/login']);
                } else {
                    Yii::$app->session->setFlash('error', 'Error al cambiar la contraseña.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Token inválido.');
            }
        }

        return $this->render('resetpassword');
    }
}
