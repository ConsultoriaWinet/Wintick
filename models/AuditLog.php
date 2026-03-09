<?php

namespace app\models;

use Yii;

/**
 * Registro de auditoría de cambios en entidades críticas.
 *
 * @property int         $id
 * @property string      $tabla
 * @property int         $registro_id
 * @property string      $accion       crear | actualizar | eliminar
 * @property string|null $cambios      JSON con old/new values
 * @property int|null    $usuario_id
 * @property string|null $ip
 * @property string      $created_at
 */
class AuditLog extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'audit_log';
    }

    /**
     * Graba una entrada de auditoría.
     *
     * @param string     $tabla
     * @param int        $registroId
     * @param string     $accion      'crear' | 'actualizar' | 'eliminar'
     * @param array|null $cambios     ['campo' => ['antes' => ..., 'despues' => ...], ...]
     */
    public static function registrar(string $tabla, int $registroId, string $accion, ?array $cambios = null): void
    {
        $log = new self();
        $log->tabla       = $tabla;
        $log->registro_id = $registroId;
        $log->accion      = $accion;
        $log->cambios     = $cambios ? json_encode($cambios, JSON_UNESCAPED_UNICODE) : null;
        $log->usuario_id  = Yii::$app->user->isGuest ? null : (int) Yii::$app->user->id;
        $log->ip          = Yii::$app->request->userIP ?? null;

        // Guardar silenciosamente — no debe interrumpir la operación principal
        if (!$log->save(false)) {
            Yii::error('AuditLog no pudo guardarse: ' . json_encode($log->errors), 'audit');
        }
    }

    // ── Relación ──────────────────────────────────────────────────────────────

    public function getUsuario()
    {
        return $this->hasOne(Usuarios::class, ['id' => 'usuario_id']);
    }

    // ── Helpers de presentación ───────────────────────────────────────────────

    public function getCambiosDecodificados(): array
    {
        return $this->cambios ? (json_decode($this->cambios, true) ?? []) : [];
    }

    public function getAccionLabel(): string
    {
        return match ($this->accion) {
            'crear'      => 'Creación',
            'actualizar' => 'Actualización',
            'eliminar'   => 'Eliminación',
            default      => $this->accion,
        };
    }
}
