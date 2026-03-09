<?php

use yii\db\Migration;

class m260309_000001_audit_log extends Migration
{
    public function safeUp()
    {
        $this->createTable('audit_log', [
            'id'          => $this->primaryKey(),
            'tabla'       => $this->string(50)->notNull()->comment('Tabla afectada'),
            'registro_id' => $this->integer()->notNull()->comment('ID del registro afectado'),
            'accion'      => "ENUM('crear','actualizar','eliminar') NOT NULL",
            'cambios'     => $this->text()->null()->comment('JSON con valores anteriores y nuevos'),
            'usuario_id'  => $this->integer()->null()->comment('Usuario que realizó la acción'),
            'ip'          => $this->string(45)->null(),
            'created_at'  => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx_audit_tabla_registro', 'audit_log', ['tabla', 'registro_id']);
        $this->createIndex('idx_audit_usuario',        'audit_log', 'usuario_id');
        $this->createIndex('idx_audit_created_at',     'audit_log', 'created_at');

        $this->addForeignKey(
            'fk_audit_usuario',
            'audit_log', 'usuario_id',
            'usuarios', 'id',
            'SET NULL', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_audit_usuario', 'audit_log');
        $this->dropTable('audit_log');
    }
}
