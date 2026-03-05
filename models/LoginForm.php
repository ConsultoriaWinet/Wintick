<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Login con protección anti-brute force.
 *
 * Cumple con:
 *   PCI DSS 8.3.4       — bloqueo tras 5 intentos, mínimo 30 min
 *   OWASP Auth Cheat Sheet — lockout por cuenta + por IP, mensajes genéricos
 *   ISO 27001 A.9.4.2   — inicio de sesión seguro con control de intentos
 *   ISO 27001 A.12.4.1  — auditoría de todos los intentos (tabla login_attempts)
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    // ── Configuración (leída de params.php) ───────────────────────────────
    private function maxAttempts(): int    { return (int)(Yii::$app->params['security.maxLoginAttempts'] ?? 5); }
    private function lockoutMinutes(): int { return (int)(Yii::$app->params['security.lockoutMinutes']   ?? 30); }
    private function ipMaxAttempts(): int  { return (int)(Yii::$app->params['security.ipMaxAttempts']    ?? 20); }
    private function ipWindowSecs(): int   { return (int)(Yii::$app->params['security.ipWindowMinutes']  ?? 15) * 60; }

    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['rememberMe', 'boolean'],
            // 1. Verificar bloqueo de IP y de cuenta ANTES de validar la contraseña
            ['email', 'checkRateLimit'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'email'      => 'Correo',
            'password'   => 'Contraseña',
            'rememberMe' => 'Recuérdame',
        ];
    }

    // ── Verificar bloqueo de IP y de cuenta ───────────────────────────────

    public function checkRateLimit(string $attribute): void
    {
        if ($this->hasErrors()) return;

        $ip = Yii::$app->request->userIP ?? '0.0.0.0';

        // 1) Bloqueo por IP (protege contra ataques distribuidos por usuario)
        $failedByIp = LoginAttempts::countFailedByIp($ip, $this->ipWindowSecs());
        if ($failedByIp >= $this->ipMaxAttempts()) {
            $this->addError($attribute,
                'Demasiados intentos fallidos desde tu red. Intenta de nuevo en ' .
                $this->ipWindowSecs() / 60 . ' minutos.');
            LoginAttempts::record($this->email ?? '', false);
            return;
        }

        // 2) Bloqueo por cuenta
        $user = $this->getUser();
        if ($user && $user->lockout_until !== null) {
            $lockoutTs = strtotime($user->lockout_until);
            if ($lockoutTs > time()) {
                $minutosRestantes = (int) ceil(($lockoutTs - time()) / 60);
                $this->addError($attribute,
                    "Cuenta bloqueada por seguridad. Intenta de nuevo en {$minutosRestantes} " .
                    ($minutosRestantes === 1 ? 'minuto' : 'minutos') . '.');
                LoginAttempts::record($this->email, false);
                return;
            }
            // Bloqueo expirado: limpiar automáticamente
            $this->resetAccountLockout($user);
        }
    }

    // ── Validar contraseña ────────────────────────────────────────────────

    public function validatePassword(string $attribute): void
    {
        if ($this->hasErrors()) return;

        $user = $this->getUser();

        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, 'Email o contraseña incorrectos.');
            $this->recordFailedAttempt();
            return;
        }

        if ($user->status !== 10) {
            $this->addError($attribute, 'Tu cuenta está desactivada. Contacta al administrador.');
            // No contar como intento fallido de brute force
        }
    }

    // ── Login exitoso ─────────────────────────────────────────────────────

    public function login(): bool
    {
        if ($this->validate()) {
            $user = $this->getUser();

            // Limpiar contadores al ingresar correctamente
            $this->resetAccountLockout($user);

            // Auditoría: registrar acceso exitoso
            LoginAttempts::record($this->email, true);

            return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    // ── Helpers privados ──────────────────────────────────────────────────

    private function recordFailedAttempt(): void
    {
        $user = $this->getUser();
        $ip   = Yii::$app->request->userIP ?? '0.0.0.0';

        // Auditoría
        LoginAttempts::record($this->email ?? '', false);

        if (!$user) return; // email inexistente — no revelar cuál de los dos falló

        $user->failed_attempts = ($user->failed_attempts ?? 0) + 1;

        if ($user->failed_attempts >= $this->maxAttempts()) {
            // Bloquear cuenta
            $until = date('Y-m-d H:i:s', time() + $this->lockoutMinutes() * 60);
            $user->lockout_until = $until;
            Yii::warning(
                "Cuenta bloqueada por brute force: {$user->email} | IP: {$ip} | " .
                "Intentos: {$user->failed_attempts} | Hasta: {$until}",
                'security'
            );
        }

        $user->save(false);
    }

    private function resetAccountLockout(Usuarios $user): void
    {
        if ($user->failed_attempts > 0 || $user->lockout_until !== null) {
            $user->failed_attempts = 0;
            $user->lockout_until   = null;
            $user->save(false);
        }
    }

    public function getUser(): ?Usuarios
    {
        if ($this->_user === false) {
            $this->_user = Usuarios::findByEmail($this->email);
        }
        return $this->_user;
    }
}
