<?php

use yii\db\Migration;

class m251124_214634_Servicios extends Migration
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
        echo "m251124_214634_Servicios cannot be reverted.\n";

        return false;
    }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('Servicios', [
            'id' => $this->primaryKey(),
            'Nombre' => $this->string()->notNull()->unique(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    public function down()
    {
        echo "m251124_214634_Servicios cannot be reverted.\n";

        return false;
    }

}
