<?php

$secrets = require __DIR__ . '/secrets.php';

return [
    'class' => 'yii\db\Connection',
    'dsn' => $secrets['db']['dsn'],
    'username' => $secrets['db']['username'],
    'password' => $secrets['db']['password'],
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
