<?php
/**
 * Script temporal para restaurar datos del backup wintickc.sql
 * Ejecutar SOLO UNA VEZ desde la raíz del proyecto.
 * Eliminar después de usar.
 */

define('YII_DEBUG', false);
define('YII_ENV', 'prod');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/console.php';
new yii\console\Application($config);

$db = Yii::$app->db;
$sqlFile = 'C:/Users/Consultoria/Downloads/wintickc.sql';

// Tablas a restaurar (en orden correcto por FK)
$tablasPermitidas = [
    'auth_item',
    'auth_item_child',
    'auth_rule',
    'auth_assignment',
    'sistemas',
    'servicios',
    'usuarios',
    'clientes',
    'tickets',
    'comentarios',
    'ticket_historial',
    'notificaciones',
    'audit_log',
    'login_attempts',
];

$content = file_get_contents($sqlFile);
$lines = explode("\n", $content);

$inserts = [];
$currentInsert = '';
$collecting = false;

foreach ($lines as $line) {
    $trimmed = trim($line);

    if (strpos($trimmed, 'INSERT INTO') === 0) {
        // Detect which table
        $skip = false;
        $tableMatch = false;
        foreach ($tablasPermitidas as $tabla) {
            if (strpos($trimmed, "`{$tabla}`") !== false) {
                $tableMatch = true;
                break;
            }
        }
        if (!$tableMatch) {
            $collecting = false;
            continue;
        }
        $collecting = true;
        $currentInsert = $trimmed;
    } elseif ($collecting) {
        $currentInsert .= "\n" . $trimmed;
    }

    // End of statement
    if ($collecting && substr(rtrim($trimmed), -1) === ';') {
        $inserts[] = $currentInsert;
        $collecting = false;
        $currentInsert = '';
    }
}

echo "Encontrados " . count($inserts) . " bloques INSERT\n";

$db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();

$ok = 0;
$err = 0;
foreach ($inserts as $sql) {
    try {
        $db->createCommand($sql)->execute();
        $ok++;
        // Get table name for output
        preg_match('/INSERT INTO `(\w+)`/', $sql, $m);
        echo "  OK: " . ($m[1] ?? '?') . "\n";
    } catch (\Exception $e) {
        $err++;
        preg_match('/INSERT INTO `(\w+)`/', $sql, $m);
        echo "  ERROR en " . ($m[1] ?? '?') . ": " . $e->getMessage() . "\n";
    }
}

$db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();

echo "\nRestauración completada: {$ok} OK, {$err} errores\n";
