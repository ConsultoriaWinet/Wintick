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
                        'actions' => ['login', 'error', 'requestpassword', 'resetpassword'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'logout', 'get-tickets'],
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

        return $this->render('index', [
            'consultores' => $consultores,
        ]);
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

            return $this->goBack();
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

    // ✅ Si es consultor (no puede asignar tickets), forzamos a que solo vea los suyos
    if (Yii::$app->user->can('verTickets') && !Yii::$app->user->can('asignarTicket')) {
        $consultorId = $userId;
    }

    $query = \app\models\Tickets::find()
        ->with(['cliente','sistema','servicio','usuarioAsignado']);

    if (!empty($consultorId)) {
        $query->andWhere(['Asignado_a' => (int)$consultorId]);
    }

    $tickets = $query->all();

    $events = [];
    foreach ($tickets as $t) {
        // ✅ Usa HoraInicio si existe, si no usa Fecha_creacion
        $start = $t->HoraInicio ?: $t->Fecha_creacion;

        if (!$start) {
            continue;
        }

        $events[] = [
            'id'    => $t->id,
            'title' => $t->Folio,
            'start' => date('c', strtotime($start)), // ISO 8601 (FullCalendar friendly)

            // Opcional: colores por consultor
            'backgroundColor' => $t->usuarioAsignado->color ?? '#8BA590',
            'borderColor'     => $t->usuarioAsignado->color ?? '#8BA590',

            'extendedProps' => [
                'consultorNombre' => $t->usuarioAsignado->Nombre ?? $t->usuarioAsignado->email ?? 'N/A',
                'cliente'   => $t->cliente->Nombre ?? 'N/A',
                'sistema'   => $t->sistema->Nombre ?? 'N/A',
                'servicio'  => $t->servicio->Nombre ?? 'N/A',
                'prioridad' => $t->Prioridad ?? 'N/A',
                'estado'    => $t->Estado ?? 'N/A',
                'description' => $t->Descripcion ?? '',
            ],
        ];
    }

    return $events;
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
                // Generar token aleatorio de 6 dígitos
                $token = sprintf('%06d', mt_rand(100000, 999999));

                // Guardar token
                $usuario->password_reset_token = $token;

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
                            ->setFrom(['arturo.villa.rey@gmail.com' => 'Wintick - Sistema de Tickets'])
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
