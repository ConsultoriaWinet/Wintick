<?php

use yii\db\Migration;

class m260508_000001_add_destinatario_to_comentarios extends Migration
{
    public function up()
    {
        $this->addColumn('comentarios', 'destinatario_id', $this->integer()->null()->defaultValue(null)->after('tipo'));
        $this->addForeignKey(
            'fk_comentarios_destinatario',
            'comentarios', 'destinatario_id',
            'usuarios', 'id',
            'SET NULL', 'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_comentarios_destinatario', 'comentarios');
        $this->dropColumn('comentarios', 'destinatario_id');
    }
}
