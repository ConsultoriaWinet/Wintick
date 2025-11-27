<?php

use yii\db\Migration;

class m251118_164351_Sistemas extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // Configuración para soporte Unicode
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // 1. Crear la tabla
        $this->createTable('Sistemas', [
            'id' => $this->primaryKey(),
            'Nombre' => $this->string()->notNull()->unique(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // 2. Preparar los datos
        $time = time();
        $items = [
            'ADMINPAQ', 'ANTIVIRUS', 'CFDI EN LINEA+', 'CONECTOR WANSOFT-CONTPAQI',
            'CONTPAQI BANCOS', 'CONTPAQI COBRA', 'CONTPAQI COMERCIAL PREMIUM',
            'CONTPAQI COMERCIAL PRO', 'CONTPAQI COMERCIAL START', 'CONTPAQI CONTABILIDAD',
            'CONTPAQI CONTABILIZA', 'CONTPAQI DECIDE', 'CONTPAQI ESCRITORIO VIRTUAL',
            'CONTPAQI EVALUA 035', 'CONTPAQI FACTURA ELECTRONICA', 'CONTPAQI NOMINAS',
            'CONTPAQI PERSONIA', 'CONTPAQI PRODUCCION', 'CONTPAQI PUNTO DE VENTA',
            'CONTPAQI RESPALDOS', 'CONTPAQI VENDE', 'CONTPAQI VIATICOS',
            'CONTPAQI WOPEN', 'CONTPAQI XML EN LINEA+', 'SISTEMAS CONTPAQi',
            'CORREO', 'DIOT', 'LAYOUT IMPORTADOR FACTURAS/DOCUMENTOS EXTERNOS',
            'OFFICE', 'PERIFERICOS', 'SUA', 'TEMPO CONTROL RELOJ CHECADOR',
            'WINDOWS', 'AutoCAD', 'CABLEADO Y REDES', 'SISTEMA FLUJO DE BANCOS',
            'CONFIGURACION PAGINAS DE INTERNET', 'CONFIGURACION DE IMPRESORA',
            'Conect@r', 'CONTPAQI COMPONENTES', 'CRM', 'WICONTROL', 'DROPBOX',
            'CONECTOR NÓMINAS', 'CONECTOR CONTABILIDAD', 'CONECTOR COMERCIAL',
            'PROGRAMAS EXTERNOS', 'TEMPO CONTROL'
        ];

        $rows = [];
        foreach ($items as $item) {
            // Estructura: [Nombre, created_at, updated_at]
            $rows[] = [$item, $time, $time];
        }

        // 3. Insertar los datos en lote
        $this->batchInsert('Sistemas', ['Nombre', 'created_at', 'updated_at'], $rows);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('Sistemas');
    }
}