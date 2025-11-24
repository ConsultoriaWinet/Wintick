<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Restablecer Contraseña';
?>

<style>
    body {
        background: linear-gradient(135deg, #A0BAA5 0%, #7FA588 100%);
        min-height: 100vh;
    }

    .reset-password-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 20px;
    }

    .reset-password-card {
        width: 100%;
        max-width: 450px;
        background: #ffffff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .reset-password-card h1 {
        text-align: center;
        margin-bottom: 10px;
        font-size: 28px;
        color: #2d6a2d;
        font-weight: 600;
    }

    .reset-password-card .subtitle {
        text-align: center;
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 30px;
        line-height: 1.5;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }

    .form-control {
        padding: 12px 15px;
        font-size: 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #4CAF50;
        box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
    }

    .btn-submit {
        background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        color: white;
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        border: none;
        transition: all 0.3s;
    }

    .btn-submit:hover {
        background: linear-gradient(135deg, #45a049 0%, #3d8b40 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
    }

    .back-to-login {
        text-align: center;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .back-to-login a {
        color: #4CAF50;
        font-size: 14px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
    }

    .back-to-login a:hover {
        color: #45a049;
        text-decoration: underline;
    }

    .icon-key {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .icon-key svg {
        width: 60px;
        height: 60px;
        color: #4CAF50;
    }

    @media (max-width: 576px) {
        .reset-password-card {
            padding: 30px 20px;
        }

        .reset-password-card h1 {
            font-size: 24px;
        }

        .form-control {
            font-size: 14px;
        }
    }
</style>

<div class="reset-password-container">
    <div class="reset-password-card">
        <!-- Icono decorativo -->
        <div class="icon-key">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
        </div>

        <h1><?= Html::encode($this->title) ?></h1>
        <p class="subtitle">
            Ingresa el código que recibiste en tu correo y tu nueva contraseña.
        </p>

        <?php $form = ActiveForm::begin([
            'id' => 'reset-password-form',
            'options' => ['autocomplete' => 'off']
        ]); ?>

            <div class="form-group">
                <label for="token" class="form-label fw-semibold">Código de Verificación</label>
                <?= Html::input('text', 'token', '', [
                    'class' => 'form-control',
                    'placeholder' => '123456',
                    'required' => true,
                    'id' => 'token',
                    'maxlength' => 6,
                    'pattern' => '[0-9]{6}',
                    'autocomplete' => 'off'
                ]) ?>
            </div>

            <div class="form-group">
                <label for="new_password" class="form-label fw-semibold">Nueva Contraseña</label>
                <?= Html::input('password', 'new_password', '', [
                    'class' => 'form-control',
                    'placeholder' => '••••••••',
                    'required' => true,
                    'id' => 'new_password',
                    'minlength' => 6
                ]) ?>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label fw-semibold">Confirmar Contraseña</label>
                <?= Html::input('password', 'confirm_password', '', [
                    'class' => 'form-control',
                    'placeholder' => '••••••••',
                    'required' => true,
                    'id' => 'confirm_password',
                    'minlength' => 6
                ]) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton(
                    '<i class="bi bi-check-circle-fill me-2"></i>Cambiar Contraseña', 
                    ['class' => 'btn btn-submit', 'name' => 'reset-button']
                ) ?>
            </div>

        <?php ActiveForm::end(); ?>

        <div class="back-to-login">
            <i class="bi bi-arrow-left me-1"></i>
            <?= Html::a('Volver al inicio de sesión', ['site/login']) ?>
        </div>
    </div>
</div>