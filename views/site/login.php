<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Iniciar Sesión';
?>

<style>
body { background: var(--surface-2, #F5F1E8) !important; }

.login-wrap {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px 16px;
}

.login-card {
    width: 100%;
    max-width: 420px;
    background: var(--surface, #fff);
    border: 1px solid var(--border, #E8E2D2);
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,.08);
    overflow: hidden;
}

/* Header de la card */
.login-header {
    padding: 32px 36px 24px;
    text-align: center;
    border-bottom: 1px solid var(--border, #E8E2D2);
}
.login-logo {
    width: 56px; height: 56px;
    border-radius: 14px;
    background: var(--accent-light, #F5F1E8);
    display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 16px;
}
.login-logo img { width: 34px; height: 34px; object-fit: contain; border-radius: 6px; }
.login-header h1 {
    font-size: 20px; font-weight: 700; color: var(--text, #1A1814); margin: 0 0 4px;
}
.login-header p {
    font-size: 13px; color: var(--text-3, #807868); margin: 0;
}

/* Body */
.login-body { padding: 28px 36px 32px; }

/* Labels */
.login-label {
    display: block;
    font-size: 11.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: var(--text-3, #807868); margin-bottom: 6px;
}

/* Inputs */
.login-input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--border, #E8E2D2);
    border-radius: 9px;
    font-size: 14px;
    color: var(--text, #1A1814);
    background: var(--surface, #fff);
    transition: border-color .15s, box-shadow .15s;
}
.login-input:focus {
    outline: none;
    border-color: var(--accent, oklch(0.60 0.13 38));
    box-shadow: 0 0 0 3px var(--accent-ring, oklch(0.60 0.13 38 / 0.18));
}
.login-input::placeholder { color: var(--text-3, #807868); }

.login-field { margin-bottom: 18px; }
.login-field .help-block, .login-field .invalid-feedback {
    display: block; font-size: 12px; color: #ef4444; margin-top: 4px;
}
.login-field.has-error .login-input { border-color: #ef4444; }

/* Password wrapper */
.pw-wrap { position: relative; }
.pw-toggle {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    cursor: pointer; color: var(--text-3, #807868); font-size: 15px; background: none; border: none;
    padding: 4px; transition: color .15s;
}
.pw-toggle:hover { color: var(--text-2, #4D483F); }

/* Remember / forgot */
.login-row {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 22px;
}
.login-check { display: flex; align-items: center; gap: 7px; cursor: pointer; }
.login-check input[type=checkbox] {
    width: 15px; height: 15px; accent-color: var(--accent, oklch(0.60 0.13 38)); cursor: pointer;
}
.login-check span { font-size: 13px; color: var(--text-2, #4D483F); }
.login-forgot { font-size: 13px; color: var(--accent, oklch(0.60 0.13 38)); text-decoration: none; font-weight: 500; }
.login-forgot:hover { text-decoration: underline; color: var(--accent-dark, oklch(0.50 0.13 38)); }

/* Botón submit */
.login-btn {
    width: 100%; padding: 11px;
    background: var(--accent, oklch(0.60 0.13 38)); color: #fff;
    border: none; border-radius: 9px;
    font-size: 14px; font-weight: 600;
    cursor: pointer; transition: background .15s, transform .1s, box-shadow .15s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.login-btn:hover { background: var(--accent-dark, oklch(0.50 0.13 38)); box-shadow: 0 4px 14px var(--accent-ring, oklch(0.60 0.13 38 / 0.18)); }
.login-btn:active { transform: scale(.98); }
.login-btn:disabled { opacity: .5; cursor: not-allowed; }

/* Banners lockout — colores semánticos intencionales */
.lockout-banner {
    border-radius: 10px; padding: 13px 15px;
    margin-bottom: 18px;
    display: flex; align-items: flex-start; gap: 11px;
    font-size: 13px; line-height: 1.5;
}
.lockout-banner.warn  { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
.lockout-banner.hard  { background: #fef2f2; border: 1px solid #fecaca; color: #7f1d1d; }
.lockout-banner i     { font-size: 16px; margin-top: 1px; flex-shrink: 0; }
.lockout-banner strong { display: block; font-weight: 700; margin-bottom: 2px; }
.lockout-timer {
    font-size: 26px; font-weight: 800; letter-spacing: 1px;
    margin-top: 6px; font-family: monospace; color: #b91c1c;
}
</style>

<div class="login-wrap">
    <div class="login-card">

        <!-- Header -->
        <div class="login-header">
            <div class="login-logo">
                <img src="<?= Yii::getAlias('@web/LOGOWINTICKICO.ico') ?>" alt="Wintick">
            </div>
            <h1>Wintick</h1>
            <p>Ingresa tus credenciales para continuar</p>
        </div>

        <!-- Body -->
        <div class="login-body">

            <?php
            $lockoutUntilTs = 0;
            $attemptsLeft   = null;
            if (!empty($model->email)) {
                $u = \app\models\Usuarios::findByEmail($model->email);
                if ($u) {
                    if ($u->lockout_until && strtotime($u->lockout_until) > time()) {
                        $lockoutUntilTs = strtotime($u->lockout_until);
                    }
                    $maxAttempts = (int)(Yii::$app->params['security.maxLoginAttempts'] ?? 5);
                    $used        = (int)($u->failed_attempts ?? 0);
                    if ($used > 0 && $lockoutUntilTs === 0) {
                        $attemptsLeft = max(0, $maxAttempts - $used);
                    }
                }
            }
            ?>

            <?php if ($lockoutUntilTs > 0): ?>
            <div class="lockout-banner hard" id="lockout-banner">
                <i class="fas fa-lock"></i>
                <div>
                    <strong>Cuenta bloqueada temporalmente</strong>
                    Varios intentos fallidos detectados. Podrás intentarlo de nuevo en:
                    <div class="lockout-timer" id="lockout-countdown">--:--</div>
                </div>
            </div>
            <script>
            (function() {
                const until = <?= $lockoutUntilTs ?> * 1000;
                function tick() {
                    const diff = Math.max(0, until - Date.now());
                    const m = String(Math.floor(diff / 60000)).padStart(2,'0');
                    const s = String(Math.floor((diff % 60000) / 1000)).padStart(2,'0');
                    const el = document.getElementById('lockout-countdown');
                    if (el) el.textContent = m + ':' + s;
                    if (diff <= 0) {
                        document.getElementById('lockout-banner')?.remove();
                        document.getElementById('login-submit')?.removeAttribute('disabled');
                    } else { setTimeout(tick, 1000); }
                }
                document.getElementById('login-submit')?.setAttribute('disabled','disabled');
                tick();
            })();
            </script>
            <?php elseif ($attemptsLeft !== null && $attemptsLeft <= 2): ?>
            <div class="lockout-banner warn">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Advertencia de seguridad</strong>
                    Te queda<?= $attemptsLeft === 1 ? '' : 'n' ?>
                    <strong><?= $attemptsLeft ?> intento<?= $attemptsLeft !== 1 ? 's' : '' ?></strong>
                    antes de que la cuenta sea bloqueada.
                </div>
            </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin([
                'id'          => 'login-form',
                'fieldConfig' => [
                    'template'     => "{label}\n{input}\n{error}",
                    'options'      => ['class' => 'login-field'],
                    'labelOptions' => ['class' => 'login-label'],
                    'inputOptions' => ['class' => 'login-input'],
                    'errorOptions' => ['class' => 'help-block'],
                ],
            ]); ?>

            <!-- Email -->
            <?= $form->field($model, 'email')
                ->textInput(['autofocus' => true, 'placeholder' => 'correo@empresa.com', 'class' => 'login-input'])
                ->label('Correo electrónico') ?>

            <!-- Password -->
            <div class="login-field">
                <label class="login-label" for="password">Contraseña</label>
                <div class="pw-wrap">
                    <?= Html::activePasswordInput($model, 'password', [
                        'id'          => 'password',
                        'class'       => 'login-input',
                        'placeholder' => '••••••••',
                    ]) ?>
                    <button type="button" class="pw-toggle" onclick="togglePassword()" tabindex="-1">
                        <i id="pw-icon" class="fas fa-eye"></i>
                    </button>
                </div>
                <?= Html::error($model, 'password', ['class' => 'help-block']) ?>
            </div>

            <!-- Remember / forgot -->
            <div class="login-row">
                <label class="login-check">
                    <?= Html::activeCheckbox($model, 'rememberMe', ['label' => false, 'id' => 'remember-me']) ?>
                    <span>Recordarme</span>
                </label>
                <a href="<?= Url::to(['site/requestpassword']) ?>" class="login-forgot">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            <!-- Submit -->
            <?= Html::submitButton(
                '<i class="fas fa-sign-in-alt"></i> Iniciar Sesión',
                ['class' => 'login-btn', 'name' => 'login-button', 'id' => 'login-submit', 'encode' => false]
            ) ?>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

<script>
function togglePassword() {
    const inp  = document.getElementById('password');
    const icon = document.getElementById('pw-icon');
    const show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    icon.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
}
</script>
