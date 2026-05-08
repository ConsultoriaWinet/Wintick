<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Restablecer Contraseña';
?>

<style>
body { background: var(--surface-2, #F5F1E8) !important; }
.auth-wrap {
    min-height: 100vh; display: flex;
    align-items: center; justify-content: center; padding: 24px 16px;
}
.auth-card {
    width: 100%; max-width: 420px;
    background: var(--surface, #fff); border: 1px solid var(--border, #E8E2D2);
    border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.08); overflow: hidden;
}
.auth-header {
    padding: 30px 36px 22px; text-align: center;
    border-bottom: 1px solid var(--border, #E8E2D2);
}
.auth-icon {
    width: 52px; height: 52px; border-radius: 13px;
    display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 14px;
}
.auth-header h1 { font-size: 19px; font-weight: 700; color: var(--text, #1A1814); margin: 0 0 5px; }
.auth-header p  { font-size: 13px; color: var(--text-3, #807868); margin: 0; line-height: 1.5; }
.auth-body { padding: 26px 36px 30px; }
.auth-label {
    display: block; font-size: 11.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: var(--text-3, #807868); margin-bottom: 6px;
}
.auth-input {
    width: 100%; padding: 10px 14px;
    border: 1px solid var(--border, #E8E2D2); border-radius: 9px;
    font-size: 14px; color: var(--text, #1A1814); background: var(--surface, #fff);
    transition: border-color .15s, box-shadow .15s;
}
.auth-input:focus {
    outline: none; border-color: var(--accent, oklch(0.60 0.13 38));
    box-shadow: 0 0 0 3px var(--accent-ring, oklch(0.60 0.13 38 / 0.18));
}
.auth-input::placeholder { color: var(--text-3, #807868); }
.auth-field { margin-bottom: 16px; position: relative; }
.pw-wrap { position: relative; }
.pw-toggle {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    cursor: pointer; color: var(--text-3, #807868); font-size: 14px;
    background: none; border: none; padding: 4px; transition: color .15s;
}
.pw-toggle:hover { color: var(--text-2, #4D483F); }
.auth-hint {
    font-size: 11.5px; color: var(--text-3, #807868); margin-top: 4px;
}
.auth-divider {
    border: none; border-top: 1px solid var(--border, #E8E2D2); margin: 18px 0;
}
.auth-btn {
    width: 100%; padding: 11px; background: var(--accent, oklch(0.60 0.13 38)); color: #fff;
    border: none; border-radius: 9px; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: background .15s, box-shadow .15s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    margin-top: 6px;
}
.auth-btn:hover { background: var(--accent-dark, oklch(0.50 0.13 38)); box-shadow: 0 4px 14px var(--accent-ring, oklch(0.60 0.13 38 / 0.18)); }
.auth-footer {
    text-align: center; padding: 16px 36px 20px;
    border-top: 1px solid var(--border, #E8E2D2);
}
.auth-footer a {
    font-size: 13px; color: var(--accent, oklch(0.60 0.13 38)); text-decoration: none;
    font-weight: 500; display: inline-flex; align-items: center; gap: 5px;
}
.auth-footer a:hover { text-decoration: underline; color: var(--accent-dark, oklch(0.50 0.13 38)); }

/* Indicador de fortaleza */
.pw-strength { display: flex; gap: 4px; margin-top: 6px; }
.pw-strength-bar {
    height: 3px; flex: 1; border-radius: 2px;
    background: var(--border, #E8E2D2); transition: background .25s;
}
.pw-strength-label { font-size: 11px; color: var(--text-3, #807868); margin-top: 3px; }
</style>

<div class="auth-wrap">
    <div class="auth-card">

        <div class="auth-header">
            <div class="auth-icon" style="background:#f0fdf4;">
                <i class="fas fa-key" style="font-size:20px;color:#16a34a;"></i>
            </div>
            <h1>Nueva Contraseña</h1>
            <p>Ingresa el código de verificación y tu nueva contraseña</p>
        </div>

        <div class="auth-body">
            <?php $form = ActiveForm::begin([
                'id'      => 'reset-password-form',
                'options' => ['autocomplete' => 'off'],
            ]); ?>

            <!-- Token -->
            <div class="auth-field">
                <label class="auth-label" for="token">Código de verificación</label>
                <?= Html::input('text', 'token', '', [
                    'class'        => 'auth-input',
                    'placeholder'  => 'Código recibido por correo',
                    'required'     => true,
                    'id'           => 'token',
                    'maxlength'    => 32,
                    'autocomplete' => 'off',
                    'autofocus'    => true,
                    'style'        => 'font-family:monospace;letter-spacing:.08em;',
                ]) ?>
            </div>

            <hr class="auth-divider">

            <!-- Nueva contraseña -->
            <div class="auth-field">
                <label class="auth-label" for="new_password">Nueva contraseña</label>
                <div class="pw-wrap">
                    <?= Html::input('password', 'new_password', '', [
                        'class'       => 'auth-input',
                        'placeholder' => '••••••••',
                        'required'    => true,
                        'id'          => 'new_password',
                        'minlength'   => 6,
                        'oninput'     => 'checkStrength(this.value)',
                    ]) ?>
                    <button type="button" class="pw-toggle" onclick="togglePw('new_password','icon-np')" tabindex="-1">
                        <i id="icon-np" class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="pw-strength" id="pw-bars">
                    <div class="pw-strength-bar" id="bar1"></div>
                    <div class="pw-strength-bar" id="bar2"></div>
                    <div class="pw-strength-bar" id="bar3"></div>
                    <div class="pw-strength-bar" id="bar4"></div>
                </div>
                <div class="pw-strength-label" id="pw-label"></div>
            </div>

            <!-- Confirmar contraseña -->
            <div class="auth-field">
                <label class="auth-label" for="confirm_password">Confirmar contraseña</label>
                <div class="pw-wrap">
                    <?= Html::input('password', 'confirm_password', '', [
                        'class'       => 'auth-input',
                        'placeholder' => '••••••••',
                        'required'    => true,
                        'id'          => 'confirm_password',
                        'minlength'   => 6,
                        'oninput'     => 'checkMatch()',
                    ]) ?>
                    <button type="button" class="pw-toggle" onclick="togglePw('confirm_password','icon-cp')" tabindex="-1">
                        <i id="icon-cp" class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="auth-hint" id="match-hint"></div>
            </div>

            <?= Html::submitButton(
                '<i class="fas fa-check"></i> Cambiar contraseña',
                ['class' => 'auth-btn', 'name' => 'reset-button', 'id' => 'reset-submit', 'encode' => false]
            ) ?>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="auth-footer">
            <?= Html::a('<i class="fas fa-arrow-left"></i> Volver al inicio de sesión', ['site/login']) ?>
        </div>

    </div>
</div>

<script>
function togglePw(inputId, iconId) {
    const inp  = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    const show = inp.type === 'password';
    inp.type   = show ? 'text' : 'password';
    icon.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
}

function checkStrength(val) {
    const bars   = [1,2,3,4].map(i => document.getElementById('bar' + i));
    const label  = document.getElementById('pw-label');
    const colors = ['#ef4444','#f59e0b','#3b82f6','#16a34a'];
    const labels = ['Muy débil','Débil','Buena','Fuerte'];
    let score = 0;
    if (val.length >= 6)  score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val) && /[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    bars.forEach((b, i) => {
        b.style.background = i < score ? colors[score - 1] : '#e5e7eb';
    });
    label.textContent = val.length ? labels[score - 1] || '' : '';
    label.style.color = val.length ? colors[score - 1] : '#9ca3af';
}

function checkMatch() {
    const np = document.getElementById('new_password').value;
    const cp = document.getElementById('confirm_password').value;
    const hint = document.getElementById('match-hint');
    if (!cp) { hint.textContent = ''; return; }
    if (np === cp) {
        hint.textContent = '✓ Las contraseñas coinciden';
        hint.style.color = '#16a34a';
    } else {
        hint.textContent = '✗ Las contraseñas no coinciden';
        hint.style.color = '#ef4444';
    }
}
</script>
