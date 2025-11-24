<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;



/** @var yii\web\View $this */
/** @var app\models\Tickets $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tickets-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'Folio')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Usuario_reporta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Asignado_a')->dropDownList(
        $model->consultoresList,
        ['prompt' => 'Seleccione un consultor...']
    ) ?>

    <?= $form->field($model, 'Estado')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Descripcion')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'Solucion')->textarea(['rows' => 6]) ?>
   
    <?php 
    // Input con ID específico para Flatpickr
    echo $form->field($model, 'HoraProgramada')->textInput(['id' => 'hora-programada-picker']); 
    ?>

    <?= $form->field($model, 'HoraInicio')->textInput(['id' => 'hora-inicio-picker']) ?>

    <?= $form->field($model, 'TiempoRestante')->textInput([ 
        'type' => 'double',
        'min' => 0,
        'step' => 1
    ]) ?>

    <?= $form->field($model, 'HoraFinalizo')->textInput(['id' => 'hora-finalizo-picker']) ?>

    <?= $form->field($model, 'TiempoEfectivo')->textInput([
        'type' => 'dobule',
        'min' => 0,
        'step' => 1
    ]) ?>

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

<?php
// Registrar el script correctamente en Yii2
$script = <<< JS
    // Hora Programada
    flatpickr("#hora-programada-picker", {
        enableTime: true,
        dateFormat: "Y-m-d H:i:S", 
        time_24hr: false,
        locale: "es",
        defaultDate: "{$model->HoraProgramada}"
    });

    // Hora Inicio
    flatpickr("#hora-inicio-picker", {
        enableTime: true,
        dateFormat: "Y-m-d H:i:S", 
        time_24hr: false,
        locale: "es",
        defaultDate: "{$model->HoraInicio}"
    });

    // Hora Finalizó
    flatpickr("#hora-finalizo-picker", {
        enableTime: true,
        dateFormat: "Y-m-d H:i:S", 
        time_24hr: false,
        locale: "es",
        defaultDate: "{$model->HoraFinalizo}"
    });

 
JS;

// Registrar el script para que se ejecute cuando el DOM esté listo
$this->registerJs($script, \yii\web\View::POS_READY);
?>
