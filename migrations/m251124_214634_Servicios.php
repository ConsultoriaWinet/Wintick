<?php

use yii\db\Migration;

class m251124_214634_Servicios extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // 1. Crear la tabla
        $this->createTable('Servicios', [
            'id' => $this->primaryKey(),
            'Nombre' => $this->string()->notNull()->unique(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // 2. Preparar los datos
        $time = time();
        $items = [
            'Instalacion', 'Capacitacion', 'Actualizacion', 'Migracion',
            'Soporte tecnico', 'Mantenimiento', 'Reunion Area Soporte',
            'Revisión de sistemas Compaq', 'Inducción', 'Soporte Linea CONTPAQi',
            'Apoyo Telefonico', 'Reunión por video llamada', 'Servicio Interno',
            'Apoyo Interno', 'Capacitacion Interna', 'Implementacion',
            'Configuración', 'Webinar', 'Reunión Interna', 'WICONTROL',
            'DEPURACION DE DISCO', 'Demo'
        ];

        $rows = [];
        foreach ($items as $item) {
            $rows[] = [$item, $time, $time];
        }

        // 3. Insertar los datos en lote
        $this->batchInsert('Servicios', ['Nombre', 'created_at', 'updated_at'], $rows);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('Servicios');
    }
}