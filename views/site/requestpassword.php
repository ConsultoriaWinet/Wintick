<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Recuperar Contraseña';
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
.auth-field { margin-bottom: 18px; }
.auth-btn {
    width: 100%; padding: 11px; background: var(--accent, oklch(0.60 0.13 38)); color: #fff;
    border: none; border-radius: 9px; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: background .15s, box-shadow .15s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
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
</style>

<div class="auth-wrap">
    <div class="auth-card">

        <div class="auth-header">
            <div class="auth-icon" style="background:var(--accent-light);">
                <i class="fas fa-envelope" style="font-size:20px;color:var(--accent);"></i>
            </div>
            <h1>Recuperar Contraseña</h1>
            <p>Te enviaremos un enlace a tu correo para restablecer tu contraseña</p>
        </div>

        <div class="auth-body">
            <?php $form = ActiveForm::begin([
                'id'      => 'request-password-form',
                'options' => ['autocomplete' => 'off'],
            ]); ?>

            <div class="auth-field">
                <label class="auth-label" for="email">Correo electrónico</label>
                <?= Html::input('email', 'email', '', [
                    'class'        => 'auth-input',
                    'placeholder'  => 'correo@empresa.com',
                    'required'     => true,
                    'id'           => 'email',
                    'autocomplete' => 'email',
                    'autofocus'    => true,
                ]) ?>
            </div>

            <?= Html::submitButton(
                '<i class="fas fa-paper-plane"></i> Enviar enlace',
                ['class' => 'auth-btn', 'name' => 'request-button', 'encode' => false]
            ) ?>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="auth-footer">
            <?= Html::a('<i class="fas fa-arrow-left"></i> Volver al inicio de sesión', ['site/login']) ?>
        </div>

    </div>
</div>
