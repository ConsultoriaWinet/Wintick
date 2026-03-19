<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'language' => 'es-MX',
    'timeZone' => 'America/Mexico_City',
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'request' => [
            'cookieValidationKey' => 'fk3TG_tHdrVbcTFW13tFkkK5RmwTCPbe',
            'enableCsrfValidation' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Usuarios',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => false,

            // Configuración de correo con Zoho Mail
            'transport' => [
                'class' => 'Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport',
                'scheme' => 'smtps', // SSL
                'host' => 'smtppro.zoho.com',
                'username' => 'consultoria@winetpc.com',
                'password' => 'H8EXh51ffYqq',
                'port' => 465,
                'streamOptions' => [
                    'ssl' => [
                        'allow_self_signed' => false,
                        'verify_peer' => true,
                        'verify_peer_name' => true,
                    ],
                ],
    
              
            ],
        ],

        //Autenticacion Usuarios

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],

    ],
    'params' => $params,

    // Redirige al rol Monitor a site/index si intenta acceder a cualquier otra ruta
    'on beforeAction' => function (\yii\base\ActionEvent $event) {
        $app  = \Yii::$app;
        $user = $app->user;

        if ($user->isGuest) return;

        $identity = $user->identity;
        if (!$identity || ($identity->rol ?? '') !== 'Monitor') return;

        $controllerId = $app->controller->id;
        $actionId     = $app->controller->action->id;

        $permitidas = [
            'site' => ['index', 'get-tickets', 'check-update', 'logout', 'error'],
        ];

        $ok = isset($permitidas[$controllerId])
            && in_array($actionId, $permitidas[$controllerId], true);

        if (!$ok) {
            $event->isValid = false;
            $app->response->redirect($app->homeUrl)->send();
        }
    },
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
