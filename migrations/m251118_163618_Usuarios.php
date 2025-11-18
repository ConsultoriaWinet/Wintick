<?php

use yii\db\Migration;

class m251118_163618_Usuarios extends Migration
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
        echo "m251118_163618_Usuarios cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
          $this->createTable('Usuarios', [
            'id' => $this->primaryKey(),
            'Nombre' => $this->string()->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            "rol" => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable("Usuarios"); 
        echo "m251118_163618_Usuarios cannot be reverted.\n";

        return false;
    }

}
