<?php

use yii\db\Migration;

class m260305_000001_add_archivo_to_comentarios extends Migration
{
    public function up()
    {
        $this->addColumn('comentarios', 'archivo', $this->string(255)->null()->after('tipo'));
    }

    public function down()
    {
        $this->dropColumn('comentarios', 'archivo');
    }
}
