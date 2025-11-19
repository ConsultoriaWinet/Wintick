<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Tickets $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tickets-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'Folio')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Usuario_reporta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Asignado_a')->textInput() ?>

    <?= $form->field($model, 'Estado')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Descripcion')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'Solucion')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'HoraProgramada')->textInput() ?>

    <?= $form->field($model, 'HoraInicio')->textInput() ?>

    <?= $form->field($model, 'TiempoRestante')->textInput() ?>

    <?= $form->field($model, 'HoraFinalizo')->textInput() ?>

    <?= $form->field($model, 'TiempoEfectivo')->textInput() ?>

    <?= $form->field($model, 'Cliente_id')->textInput() ?>

    <?= $form->field($model, 'Sistema_id')->textInput() ?>

    <?= $form->field($model, 'Servicio_id')->textInput() ?>

    <?= $form->field($model, 'Creado_por')->textInput() ?>

    <?= $form->field($model, 'Fecha_creacion')->textInput() ?>

    <?= $form->field($model, 'Fecha_actualizacion')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
