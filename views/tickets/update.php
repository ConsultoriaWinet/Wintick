<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\Tickets $model */
/** @var array $clientes */
/** @var array $sistemas */
/** @var array $servicios */
/** @var array $usuarios */

$this->title = 'Actualizar Ticket: ' . $model->Folio;


$this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<style>
    .ticket-update {
     
        margin:auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .update-header {
       
        color: white;
        padding: 25px 30px;
        text-align: center;
    }
    
    .update-header h1 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }
    
    .update-form {
        padding: 30px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        display: block;
    }
    
    .form-control, .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .btn-group {
        display: flex;
        gap: 10px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #e5e7eb;
    }
    
    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 2px solid;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: #667eea;
        border-color: #667eea;
        color: white;
    }
    
    .btn-primary:hover {
        background: #5a67d8;
        transform: translateY(-2px);
    }
    
    .btn-secondary {
        background: #6b7280;
        border-color: #6b7280;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #4b5563;
        transform: translateY(-2px);
    }
</style>

<div class="ticket-update">
    <div class="update-header">
        <h1><i class="fas fa-edit"></i> <?= Html::encode($this->title) ?></h1>
    </div>

    <div class="update-form">
        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'row g-3'],
        ]); ?>

        <div class="col-md-6">
            <?= $form->field($model, 'Folio')->textInput([
                'class' => 'form-control',
                'readonly' => true,
                'style' => 'background: #f3f4f6; font-weight: bold;'
            ])->label('Folio del Ticket') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'Cliente_id')->dropDownList(
                ArrayHelper::map($clientes, 'id', 'Nombre'),
                [
                    'prompt' => 'Seleccionar Cliente',
                    'class' => 'form-select'
                ]
            )->label('Cliente') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'Sistema_id')->dropDownList(
                ArrayHelper::map($sistemas, 'id', 'Nombre'),
                [
                    'prompt' => 'Seleccionar Sistema',
                    'class' => 'form-select'
                ]
            )->label('Sistema') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'Servicio_id')->dropDownList(
                ArrayHelper::map($servicios, 'id', 'Nombre'),
                [
                    'prompt' => 'Seleccionar Servicio',
                    'class' => 'form-select'
                ]
            )->label('Servicio') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'Usuario_reporta')->textInput([
                'class' => 'form-control',
                'placeholder' => 'Nombre de quien reporta'
            ])->label('Usuario que Reporta') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'Asignado_a')->dropDownList(
                ArrayHelper::map($usuarios, 'id', 'email'),
                [
                    'prompt' => 'Seleccionar Consultor',
                    'class' => 'form-select'
                ]
            )->label('Asignado A') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'HoraProgramada')->textInput([
                'class' => 'form-control flatpickr-datetime',
                'placeholder' => 'Seleccionar fecha y hora'
            ])->label('Hora Programada') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'HoraInicio')->textInput([
                'class' => 'form-control flatpickr-datetime',
                'placeholder' => 'Seleccionar fecha y hora'
            ])->label('Hora de Inicio') ?>
        </div>

        <div class="col-12">
            <?= $form->field($model, 'Descripcion')->textarea([
                'rows' => 4,
                'class' => 'form-control',
                'placeholder' => 'Describe el problema o solicitud...'
            ])->label('Descripción del Ticket') ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'Prioridad')->dropDownList([
                'BAJA' => 'Baja',
                'MEDIA' => 'Media', 
                'ALTA' => 'Alta'
            ], [
                'prompt' => 'Seleccionar Prioridad',
                'class' => 'form-select'
            ])->label('Prioridad') ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'Estado')->dropDownList([
                'ABIERTO' => 'Abierto',
                'EN PROCESO' => 'En Proceso',
                'CERRADO' => 'Cerrado'
            ], [
                'class' => 'form-select'
            ])->label('Estado') ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'TiempoEfectivo')->textInput([
                'class' => 'form-control',
                'placeholder' => 'Ej: 2 horas, 30 minutos'
            ])->label('Tiempo Efectivo') ?>
        </div>

        <div class="col-12">
            <?= $form->field($model, 'Solucion')->textarea([
                'rows' => 3,
                'class' => 'form-control',
                'placeholder' => 'Describe la solución aplicada...'
            ])->label('Solución') ?>
        </div>

        <div class="btn-group col-12">
            <?= Html::submitButton('<i class="fas fa-save"></i> Guardar Cambios', [
                'class' => 'btn btn-primary'
            ]) ?>
            
            <?= Html::a('<i class="fas fa-arrow-left"></i> Regresar', ['index'], [
                'class' => 'btn btn-secondary'
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Flatpickr para campos de fecha/hora
    document.querySelectorAll('.flatpickr-datetime').forEach(function(element) {
        flatpickr(element, {
            enableTime: true,
            dateFormat: "Y-m-d H:i:s",
            time_24hr: true,
            locale: "es",
            minuteIncrement: 15,
            allowInput: true,
            clickOpens: true
        });
    });
});
</script>
