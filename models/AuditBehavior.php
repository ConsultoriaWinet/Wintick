<?php

namespace app\models;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Behavior que registra automáticamente creaciones, actualizaciones y
 * eliminaciones en la tabla audit_log.
 *
 * Uso en un modelo:
 *
 *   public function behaviors(): array
 *   {
 *       return [
 *           ['class' => AuditBehavior::class, 'camposIgnorados' => ['updated_at']],
 *       ];
 *   }
 */
class AuditBehavior extends Behavior
{
    /**
     * Campos que NO se auditarán en actualizaciones (timestamps internos, etc.)
     */
    public array $camposIgnorados = ['Fecha_actualizacion', 'updated_at'];

    // Guardamos los valores anteriores antes del update para poder
    // compararlos en EVENT_AFTER_UPDATE.
    private array $_oldAttributes = [];

    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT  => 'onAfterInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'onBeforeUpdate',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'onAfterUpdate',
            ActiveRecord::EVENT_BEFORE_DELETE => 'onBeforeDelete',
        ];
    }

    // ── Eventos ───────────────────────────────────────────────────────────────

    public function onAfterInsert(): void
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        AuditLog::registrar(
            $owner->tableName(),
            (int) $owner->getPrimaryKey(),
            'crear'
        );
    }

    public function onBeforeUpdate(): void
    {
        // Capturamos los valores actuales (antes de que se guarden)
        $this->_oldAttributes = $this->owner->getOldAttributes();
    }

    public function onAfterUpdate($event): void
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        // $event->changedAttributes contiene los atributos con sus valores ANTERIORES
        $changed = $event->changedAttributes ?? [];

        $cambios = [];
        foreach ($changed as $campo => $valorAnterior) {
            if (in_array($campo, $this->camposIgnorados, true)) {
                continue;
            }
            $cambios[$campo] = [
                'antes'   => $valorAnterior,
                'despues' => $owner->$campo,
            ];
        }

        if (empty($cambios)) {
            return; // Nada relevante cambió
        }

        AuditLog::registrar(
            $owner->tableName(),
            (int) $owner->getPrimaryKey(),
            'actualizar',
            $cambios
        );
    }

    public function onBeforeDelete(): void
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        // Guardamos un snapshot del registro que se va a eliminar
        $snapshot = [];
        foreach ($owner->attributes as $campo => $valor) {
            if (!in_array($campo, $this->camposIgnorados, true)) {
                $snapshot[$campo] = ['antes' => $valor, 'despues' => null];
            }
        }

        AuditLog::registrar(
            $owner->tableName(),
            (int) $owner->getPrimaryKey(),
            'eliminar',
            $snapshot
        );
    }
}
