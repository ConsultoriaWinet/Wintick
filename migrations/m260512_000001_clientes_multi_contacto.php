<?php

use yii\db\Migration;

class m260512_000001_clientes_multi_contacto extends Migration
{
    public function up()
    {
        // Cambiar columnas a TEXT nullable para almacenar JSON
        $this->execute("ALTER TABLE `clientes`
            MODIFY COLUMN `Telefono` TEXT NULL,
            MODIFY COLUMN `Whatsapp_contacto` TEXT NULL,
            MODIFY COLUMN `Correo` TEXT NULL,
            MODIFY COLUMN `Razon_social` VARCHAR(255) NULL,
            MODIFY COLUMN `Contacto_nombre` VARCHAR(255) NULL
        ");

        // Migrar valores existentes a formato JSON y aplicar defaults
        $this->execute("UPDATE `clientes` SET
            `Telefono` = CASE
                WHEN `Telefono` IS NULL OR TRIM(`Telefono`) = '' THEN '[]'
                ELSE CONCAT('[{\"label\":\"\",\"valor\":\"', REPLACE(TRIM(`Telefono`), '\"', '\\\\\"'), '\"}]')
            END,
            `Whatsapp_contacto` = CASE
                WHEN `Whatsapp_contacto` IS NULL OR TRIM(`Whatsapp_contacto`) = '' THEN '[]'
                ELSE CONCAT('[{\"label\":\"\",\"valor\":\"', REPLACE(TRIM(`Whatsapp_contacto`), '\"', '\\\\\"'), '\"}]')
            END,
            `Correo` = CASE
                WHEN `Correo` IS NULL OR TRIM(`Correo`) = '' THEN '[]'
                ELSE CONCAT('[{\"label\":\"\",\"valor\":\"', REPLACE(TRIM(`Correo`), '\"', '\\\\\"'), '\"}]')
            END,
            `Tipo_servicio` = 'POLIZA',
            `Tiempo` = 0
        ");
    }

    public function down()
    {
        echo "m260512_000001_clientes_multi_contacto no puede revertirse.\n";
        return false;
    }
}
