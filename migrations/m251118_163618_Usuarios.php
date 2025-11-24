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
            'color' => $this->string()->null(),
        ]);


        
           //crear un usuario por default para siempre por si las dudas 
    $this->insert('Usuarios', [
        'Nombre' => 'admin',
        'password_hash' => Yii::$app->security->generatePasswordHash('12345'),
        'email' => 'admin@gmail.com',
        'rol' => 'Consultor',
        'status' => 10,
        'created_at' => time(),
        'updated_at' => time(),
        'color'=> null,
    ]);   
}

    public function down()
    {
        $this->dropTable("Usuarios"); 
        echo "m251118_163618_Usuarios cannot be reverted.\n";

        return false;
    }

}
