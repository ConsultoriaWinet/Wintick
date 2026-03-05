<?php

use yii\db\Migration;

/**
 * Anti-brute force: agrega campos de bloqueo a usuarios y tabla de auditoría.
 *
 * Cumple con:
 *   PCI DSS 8.3.4  — bloqueo tras N intentos fallidos, mínimo 30 min
 *   ISO 27001 A.9.4.2  — procedimientos de inicio de sesión seguro
 *   ISO 27001 A.12.4.1 — registro de eventos de acceso
 *   OWASP Authentication Cheat Sheet — lockout por cuenta + por IP
 */
class m260305_000002_antibruteforce extends Migration
{
    public function up()
    {
        // ── Campos de bloqueo en usuarios ─────────────────────────────────
        $this->addColumn('usuarios', 'failed_attempts',
            $this->smallInteger()->notNull()->defaultValue(0)->after('color'));

        $this->addColumn('usuarios', 'lockout_until',
            $this->dateTime()->null()->after('failed_attempts'));

        // ── Tabla de auditoría de intentos de login ───────────────────────
        $this->createTable('login_attempts', [
            'id'          => $this->primaryKey(),
            'email'       => $this->string(255)->notNull(),
            'ip_address'  => $this->string(45)->notNull(),   // IPv6 max = 45 chars
            'user_agent'  => $this->string(512)->null(),
            'success'     => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'created_at'  => $this->integer()->unsigned()->notNull(),
        ]);

        $this->createIndex('idx_login_attempts_email',   'login_attempts', 'email');
        $this->createIndex('idx_login_attempts_ip',      'login_attempts', 'ip_address');
        $this->createIndex('idx_login_attempts_created', 'login_attempts', 'created_at');
    }

    public function down()
    {
        $this->dropTable('login_attempts');
        $this->dropColumn('usuarios', 'lockout_until');
        $this->dropColumn('usuarios', 'failed_attempts');
    }
}
