<?php

use yii\db\Migration;

class m260306_000003_ticket_historial extends Migration
{
    public function up()
    {
        $this->createTable('ticket_historial', [
            'id'             => $this->primaryKey(),
            'ticket_id'      => $this->integer()->notNull(),
            'usuario_id'     => $this->integer()->notNull(),
            'campo'          => $this->string(50)->notNull(),
            'valor_anterior' => $this->text()->null(),
            'valor_nuevo'    => $this->text()->null(),
            'fecha'          => $this->dateTime()->notNull(),
        ]);

        $this->addForeignKey(
            'fk_historial_ticket',
            'ticket_historial', 'ticket_id',
            'tickets', 'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_historial_usuario',
            'ticket_historial', 'usuario_id',
            'usuarios', 'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_historial_usuario', 'ticket_historial');
        $this->dropForeignKey('fk_historial_ticket', 'ticket_historial');
        $this->dropTable('ticket_historial');
    }
}
