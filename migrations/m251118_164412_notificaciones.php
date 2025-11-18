<?php

use yii\db\Migration;

class m251118_164412_notificaciones extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251118_164412_notificaciones cannot be reverted.\n";

        return false;
    }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('Notificaciones', [
            'id' => $this->primaryKey(),
            'usuario_id' => $this->integer()->notNull(),
            'ticket_id' => $this->integer(),
            'tipo' => $this->string(50), // 'asignado', 'comentario', 'estado_cambio'
            'titulo' => $this->string(255)->notNull(),
            'mensaje' => $this->text(),
            'leida' => $this->boolean()->defaultValue(false),
            'fecha_creacion' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        // Índices
        $this->createIndex('idx_notificaciones_usuario', 'notificaciones', 'usuario_id');
        $this->createIndex('idx_notificaciones_ticket', 'notificaciones', 'ticket_id');

        // Foreign keys
        $this->addForeignKey('fk_notificaciones_usuario', 'notificaciones', 'usuario_id', 'usuarios', 'id', 'CASCADE');
        $this->addForeignKey('fk_notificaciones_ticket', 'notificaciones', 'ticket_id', 'tickets', 'id', 'CASCADE');
    }


    public function down()
    {
        $this->dropForeignKey('fk_notificaciones_ticket', 'notificaciones');
        $this->dropForeignKey('fk_notificaciones_usuario', 'notificaciones');
        $this->dropTable('notificaciones');
        echo "m251106_211830_notificaciones cannot be reverted.\n";

        return false;
    }
}
