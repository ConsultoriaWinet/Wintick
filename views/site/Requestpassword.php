<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Recuperar Contraseña';
?>

<style>
    body {
      background-color: #A0BAA5;
        min-height: 100vh;
    }

    .request-password-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 20px;
    }

    .request-password-card {
        width: 100%;
        max-width: 450px;
        background: #ffffff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .request-password-card h1 {
        text-align: center;
        margin-bottom: 10px;
        font-size: 28px;
        color: #2d6a2d;
        font-weight: 600;
    }

    .request-password-card .subtitle {
        text-align: center;
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 30px;
        line-height: 1.5;
    }

    .form-group {
        margin-bottom: 25px;
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

    .icon-envelope {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .icon-envelope svg {
        width: 60px;
        height: 60px;
        color: #4CAF50;
    }

    @media (max-width: 576px) {
        .request-password-card {
            padding: 30px 20px;
        }

        .request-password-card h1 {
            font-size: 24px;
        }

        .form-control {
            font-size: 14px;
        }
    }
</style>

<div class="request-password-container">
    <div class="request-password-card">
        <!-- Icono decorativo -->
        <div class="icon-envelope">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>

        <h1><?= Html::encode($this->title) ?></h1>
        <p class="subtitle">
            Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
        </p>

        <?php $form = ActiveForm::begin([
            'id' => 'request-password-form',
            'options' => ['autocomplete' => 'off']
        ]); ?>

            <div class="form-group">
                <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                <?= Html::input('email', 'email', '', [
                    'class' => 'form-control',
                    'placeholder' => 'correo@ejemplo.com',
                    'required' => true,
                    'id' => 'email',
                    'autocomplete' => 'email'
                ]) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton(
                    '<i class="bi bi-send-fill me-2"></i>Enviar enlace de recuperación', 
                    ['class' => 'btn btn-submit', 'name' => 'request-button']
                ) ?>
            </div>

        <?php ActiveForm::end(); ?>

        <div class="back-to-login">
            <i class="bi bi-arrow-left me-1"></i>
            <?= Html::a('Volver al inicio de sesión', ['site/login']) ?>
        </div>
    </div>
</div>

