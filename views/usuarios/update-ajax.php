<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $model app\models\Usuarios */
?>

<div>
    <?php $form = ActiveForm::begin([
        'id' => 'formEditUsuario',
        'enableAjaxValidation' => false,
    ]); ?>

    <?= $form->field($model, 'Nombre')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'color')->input('color') ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar Cambios', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>