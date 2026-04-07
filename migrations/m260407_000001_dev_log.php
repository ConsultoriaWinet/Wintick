<?php

use yii\db\Migration;

/**
 * Tabla de log completo para desarrolladores.
 * Registra absolutamente todos los movimientos del sistema:
 * login, logout, crear, actualizar, eliminar, errores, etc.
 */
class m260407_000001_dev_log extends Migration
{
    public function safeUp()
    {
        $this->createTable('dev_log', [
            'id'             => $this->primaryKey()->unsigned()->comment('ID único del evento'),
            'usuario_id'     => $this->integer()->null()->comment('ID del usuario que generó el evento (null = anónimo)'),
            'usuario_nombre' => $this->string(255)->null()->comment('Nombre del usuario en el momento del evento'),
            'usuario_rol'    => $this->string(100)->null()->comment('Rol del usuario en el momento del evento'),
            'tipo'           => "ENUM('login','logout','crear','actualizar','eliminar','vista','error','sistema') NOT NULL DEFAULT 'sistema' COMMENT 'Categoría del evento'",
            'modulo'         => $this->string(100)->null()->comment('Módulo afectado: tickets, clientes, usuarios, etc.'),
            'accion'         => $this->text()->notNull()->comment('Descripción legible del evento para el desarrollador'),
            'modelo'         => $this->string(100)->null()->comment('Clase del modelo afectado (ej: Tickets, Clientes)'),
            'modelo_id'      => $this->integer()->null()->comment('ID del registro afectado en la tabla correspondiente'),
            'datos'          => $this->text()->null()->comment('JSON con datos completos del evento (antes/después, parámetros, etc.)'),
            'ip'             => $this->string(45)->null()->comment('IP del cliente (soporta IPv6)'),
            'user_agent'     => $this->text()->null()->comment('User-Agent del navegador/cliente'),
            'created_at'     => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Timestamp exacto del evento'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="Log completo de actividad del sistema para desarrolladores"');

        // Índices para consultas rápidas en la vista de logs
        $this->createIndex('idx_devlog_tipo',       'dev_log', 'tipo');
        $this->createIndex('idx_devlog_usuario_id',  'dev_log', 'usuario_id');
        $this->createIndex('idx_devlog_created_at',  'dev_log', 'created_at');
        $this->createIndex('idx_devlog_modulo',      'dev_log', 'modulo');
        $this->createIndex('idx_devlog_modelo',      'dev_log', ['modelo', 'modelo_id']);

        // FK opcional: si el usuario se elimina, el log conserva el nombre pero pierde el ID
        $this->addForeignKey(
            'fk_devlog_usuario',
            'dev_log', 'usuario_id',
            'usuarios', 'id',
            'SET NULL', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_devlog_usuario', 'dev_log');
        $this->dropTable('dev_log');
    }
}
