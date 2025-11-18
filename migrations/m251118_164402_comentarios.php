<?php

use yii\db\Migration;

class m251118_164402_comentarios extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('comentarios', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer()->notNull(),
            'usuario_id' => $this->integer()->notNull(),
            'comentario' => $this->text()->notNull(),
            'tipo' => $this->string(20)->defaultValue('comentario')->comment('comentario, nota_interna, solucion'),
            'fecha_creacion' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);


        $this->createIndex('idx_comentarios_ticket_id', 'comentarios', 'ticket_id');
        $this->createIndex('idx_comentarios_usuario_id', 'comentarios', 'usuario_id');
        $this->createIndex('idx_comentarios_fecha', 'comentarios', 'fecha_creacion');


        $this->addForeignKey(
            'fk_comentarios_ticket',
            'comentarios',
            'ticket_id',
            'tickets',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_comentarios_usuario',
            'comentarios',
            'usuario_id',
            'usuarios',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {

        $this->dropForeignKey('fk_comentarios_usuario', 'comentarios');
        $this->dropForeignKey('fk_comentarios_ticket', 'comentarios');
        $this->dropTable('comentarios');
    }
}
