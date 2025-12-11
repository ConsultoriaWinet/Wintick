<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Usuarios $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="usuarios-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'Nombre')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password_hash')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password_reset_token')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(['10' => 'Activo','0'=> 'Inactivo']) ?>

    <?= $form->field($model, 'rol')->dropDownList([   
    'Consultores'=> 'Consultores',
    'Administracion' => 'Administracion',
    'Administradores'=> 'Administradores',
    'Desarrolladores'=> 'Desarrolladores',
    'Supervisores'=> 'Supervisores',
    ]) ?>

    <?= $form->field($model, 'created_at')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'updated_at')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'color')->input('color', ['value' => $model->color ?: '#3788d8']) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Usuario', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>