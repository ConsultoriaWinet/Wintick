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
    body {
        background: #A0BAA5;
        min-height: 100vh;
    }

    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 20px;
    }

    .login-card {
        width: 100%;
        max-width: 450px;
        background: #ffffff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .icon-lock {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .icon-lock svg {
        width: 60px;
        height: 60px;
        color: #4CAF50;
    }

    .login-card h1 {
        text-align: center;
        margin-bottom: 10px;
        font-size: 28px;
        color: #2d6a2d;
        font-weight: 600;
    }

    .login-card .subtitle {
        text-align: center;
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 30px;
        line-height: 1.5;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .password-wrapper {
        position: relative;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        padding: 12px 45px 12px 15px;
        font-size: 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
        transition: all 0.3s;
        width: 100%;
    }

    .form-control:focus {
        border-color: #4CAF50;
        box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        font-size: 18px;
        color: #6c757d;
        z-index: 10;
        background: white;
        padding: 5px;
    }

    .password-toggle:hover {
        color: #4CAF50;
    }

    .invalid-feedback {
        display: block;
        margin-top: 5px;
        font-size: 13px;
        color: #dc3545;
    }

    .remember-forgot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        margin-top: 5px;
    }

    .remember-forgot .form-check {
        margin-bottom: 0;
    }

    .remember-forgot .form-check-label {
        font-size: 14px;
        color: #6c757d;
    }

    .remember-forgot a {
        color: #4CAF50;
        font-size: 14px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
    }

    .remember-forgot a:hover {
        color: #45a049;
        text-decoration: underline;
    }

    .btn-login {
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

    .btn-login:hover {
        background: linear-gradient(135deg, #45a049 0%, #3d8b40 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        color: white;
    }

    @media (max-width: 576px) {
        .login-card {
            padding: 30px 20px;
        }

        .login-card h1 {
            font-size: 24px;
        }

        .form-control {
            font-size: 14px;
        }

        .remember-forgot {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
    }
</style>

<div class="login-container">
    <div class="login-card">
        <!-- Icono decorativo -->
        <div class="icon-lock">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>

        <h1><?= Html::encode($this->title) ?></h1>
        <p class="subtitle">Ingresa tus credenciales para acceder al sistema</p>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'labelOptions' => ['class' => 'form-label'],
                'inputOptions' => ['class' => 'form-control'],
                'errorOptions' => ['class' => 'invalid-feedback'],
            ],
        ]); ?>

        <div class="form-group">
            <?= $form->field($model, 'email')->textInput([
                'autofocus' => true,
                'placeholder' => 'correo@ejemplo.com'
            ])->label('Correo Electrónico') ?>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Contraseña</label>
            <div class="password-wrapper">
                <?= Html::activePasswordInput($model, 'password', [
                    'id' => 'password',
                    'class' => 'form-control',
                    'placeholder' => '••••••••'
                ]) ?>
                <span class="password-toggle" onclick="togglePassword()">
                    <i id="password-icon" class="bi bi-eye"></i>
                </span>
            </div>
            <?= Html::error($model, 'password', ['class' => 'invalid-feedback']) ?>
        </div>

        <div class="remember-forgot">
            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div class=\"form-check\">{input} {label}</div>",
                'class' => 'form-check-input',
                'label' => 'Recordarme',
            ]) ?>
            <a href="<?= Url::to(['site/requestpassword']) ?>" class="forgot-password">
                ¿Olvidaste tu contraseña?
            </a>
        </div>

        <div class="form-group">
            <?= Html::submitButton(
                '<i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión',
                ['class' => 'btn btn-primary btn-login', 'name' => 'login-button']
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<script>
    function togglePassword() {
        const pass = document.getElementById('password');
        const icon = document.getElementById('password-icon');
        if (pass.type === 'password') {
            pass.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            pass.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>