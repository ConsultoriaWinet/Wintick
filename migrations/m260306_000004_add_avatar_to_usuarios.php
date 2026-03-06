<?php

use yii\db\Migration;

class m260306_000004_add_avatar_to_usuarios extends Migration
{
    public function up()
    {
        $this->addColumn('usuarios', 'avatar', $this->string(10)->null()->defaultValue(null));
    }

    public function down()
    {
        $this->dropColumn('usuarios', 'avatar');
    }
}
