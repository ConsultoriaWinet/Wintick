<?php

use yii\db\Migration;

class m251118_164331_Tickets extends Migration
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
        echo "m251118_164331_Tickets cannot be reverted.\n";

        return false;
    }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('Tickets', [
            'id' => $this->primaryKey(),
            "Folio" => $this->string()->notNull()->unique(),
            "Usuario_reporta" => $this->string()->notNull(),                //USUARIO QUE REPORTA
            'Asignado_a' => $this->integer()->notNull(),                    //CONSULTOR
            'Estado' => $this->string()->notNull(),                         //ABIERTO, EN PROCESO, CERRADO, PROGRAMADO
            'Descripcion' => $this->text()->notNull(),                      //DESCRIPCION DEL TICKET    
            "Solucion" => $this->text()->null(),                            //SOLUCION DEL TICKET
            "HoraProgramada" => $this->dateTime()->null(),                  //HORA PROGRAMADA PARA ATENCION
            "HoraInicio" => $this->dateTime()->null(),                      //HORA DE INICIO
            "TiempoRestante" => $this->integer()->null(),                   //Tiempo Restante de Servicio
            "HoraFinalizo" => $this->integer()->null(),                     //TIEMPO DE ATENCION
            "TiempoEfectivo" => $this->integer()->null(),                   //TIMEPO EFECTIVO DE ATENCION EN HORAS           
            'Cliente_id' => $this->integer()->notNull(),
            'Sistema_id' => $this->integer()->notNull(),
            'Servicio_id' => $this->integer()->notNull(),
            'Creado_por' => $this->integer()->notNull(),
            'Fecha_creacion' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'Fecha_actualizacion' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    public function down()
    {
        $this->dropTable('Tickets');
        echo "m251118_164331_Tickets cannot be reverted.\n";

        return false;
    }

}
