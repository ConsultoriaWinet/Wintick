<?php

use yii\db\Migration;

class m260306_000005_avatar_to_path extends Migration
{
    public function up()
    {
        $this->alterColumn('usuarios', 'avatar', $this->string(255)->null()->defaultValue(null));
    }

    public function down()
    {
        $this->alterColumn('usuarios', 'avatar', $this->string(10)->null()->defaultValue(null));
    }
}
