<?php

use yii\db\Migration;

class m260513_000001_fix_clientes_tiempo_column extends Migration
{
    public function up()
    {
        $this->alterColumn('clientes', 'Tiempo', $this->string(20)->notNull()->defaultValue('0'));
    }

    public function down()
    {
        $this->alterColumn('clientes', 'Tiempo', $this->decimal(10, 0)->notNull()->defaultValue(0));
    }
}
