<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';

//$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    body {
        background-color: #A0BAA5;
    }

    .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        cursor: pointer;
    }


    .site-login {
        width: 100%;
        max-width: 380px;
        margin: 80px auto;
        background: #ffffff;
        padding: 35px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
        text-align: center;
    }

    .site-login h1 {
        margin-bottom: 25px;
        font-size: 24px;
        color: #2d6a2d;
        font-weight: bold;
    }

    .btn-login {
        background-color: #4CAF50;
        color: white;
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        font-size: 16px;
        border: none;
    }

    .btn-login:hover {
        background-color: #45a049;
        color: #fff;
    }
</style>

<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>Please fill out the following fields to login:</p>

    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
                    'inputOptions' => ['class' => 'col-lg-3 form-control'],
                    'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                ],
            ]); ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <div style="position: relative;">
                <?= $form->field($model, 'password')->passwordInput(['id' => 'password']) ?>
                <span class="password-toggle mx-5" onclick="togglePassword()">010</span>
            </div>

            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div class=\"custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
            ]) ?>

            <div class="form-group">
                <div>
                    <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

            <div style="color:#999;">
                You may login with <strong>admin/admin</strong> or <strong>demo/demo</strong>.<br>
                To modify the username/password, please check out the code <code>app\models\User::$users</code>.
            </div>

        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const pass = document.getElementById('password');
        pass.type = pass.type === 'password' ? 'text' : 'password';
    }
</script>