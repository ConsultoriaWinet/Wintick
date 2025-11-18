<?php

use yii\db\Migration;

class m251118_164345_Clientes extends Migration
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
        echo "m251118_164345_Clientes cannot be reverted.\n";

        return false;
    }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('Clientes', [
            'id' => $this->primaryKey(),
            'Nombre' => $this->string()->notNull(),         //Nombre de la empresa
            'Razon_social' => $this->string()->notNull(),   //Nombre de la empresa registrado en el CSF
            'RFC' => $this->string()->unique(),            //RFC de la empresa
            'Correo' => $this->string()->notNull(),         //Correo de la empresa
            'Contacto_nombre' => $this->string()->notNull(),       //Contacto principal de la empresa
            'Tiempo' => $this->integer()->notNull(),        //Tiempo Comprado de la empresa - OPCIONAL
            "Whatsapp_contacto" => $this->integer()->notNull(),      //Whatsapp de la empresa
            "Telefono" => $this->integer()->notNull(),      //Telefono de la empresa
            'Estado' => $this->boolean()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    public function down()
    {
        echo "m251118_164345_Clientes cannot be reverted.\n";

        return false;
    }

}
