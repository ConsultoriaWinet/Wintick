<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * Registro de auditoría de intentos de login (ISO 27001 A.12.4.1).
 *
 * @property int    $id
 * @property string $email
 * @property string $ip_address
 * @property string $user_agent
 * @property int    $success
 * @property int    $created_at
 */
class LoginAttempts extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'login_attempts';
    }

    /**
     * Registra un intento de login.
     */
    public static function record(string $email, bool $success): void
    {
        $request   = Yii::$app->request;
        $attempt   = new self();
        $attempt->email      = mb_strtolower(trim($email));
        $attempt->ip_address = $request->userIP ?? '0.0.0.0';
        $attempt->user_agent = mb_substr($request->userAgent ?? '', 0, 512);
        $attempt->success    = $success ? 1 : 0;
        $attempt->created_at = time();
        $attempt->save(false);
    }

    /**
     * Cuenta intentos fallidos de una IP en la ventana de tiempo dada (segundos).
     */
    public static function countFailedByIp(string $ip, int $windowSeconds): int
    {
        return (int) static::find()
            ->where(['ip_address' => $ip, 'success' => 0])
            ->andWhere(['>=', 'created_at', time() - $windowSeconds])
            ->count();
    }

    /**
     * Elimina registros más antiguos que $olderThanDays días (limpieza periódica).
     * Llamar desde un cron o tarea programada.
     */
    public static function purgeOld(int $olderThanDays = 90): int
    {
        return (int) static::deleteAll(['<', 'created_at', time() - ($olderThanDays * 86400)]);
    }
}
