<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TicketsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tickets-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'Folio') ?>

    <?= $form->field($model, 'Usuario_reporta') ?>

    <?= $form->field($model, 'Asignado_a') ?>

    <?= $form->field($model, 'Estado') ?>

    <?php // echo $form->field($model, 'Descripcion') ?>

    <?php // echo $form->field($model, 'Solucion') ?>

    <?php // echo $form->field($model, 'HoraProgramada') ?>

    <?php // echo $form->field($model, 'HoraInicio') ?>

    <?php // echo $form->field($model, 'TiempoRestante') ?>

    <?php // echo $form->field($model, 'HoraFinalizo') ?>

    <?php // echo $form->field($model, 'TiempoEfectivo') ?>

    <?php // echo $form->field($model, 'Cliente_id') ?>

    <?php // echo $form->field($model, 'Sistema_id') ?>

    <?php // echo $form->field($model, 'Servicio_id') ?>

    <?php // echo $form->field($model, 'Creado_por') ?>

    <?php // echo $form->field($model, 'Fecha_creacion') ?>

    <?php // echo $form->field($model, 'Fecha_actualizacion') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
