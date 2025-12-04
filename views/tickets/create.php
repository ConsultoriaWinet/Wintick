<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Clientes;
use app\models\Sistemas;
use app\models\Servicios;
use app\models\Usuarios;

/** @var yii\web\View $this */
/** @var app\models\Tickets $model */

$this->title = 'Crear Ticket';

// Obtener datos para los dropdowns
$clientes = Clientes::find()->asArray()->all();
$sistemas = Sistemas::find()->asArray()->all();
$servicios = Servicios::find()->asArray()->all();
$usuarios = Usuarios::find()->where(['rol' => 'consultor'])->asArray()->all();

$this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js', ['position' => \yii\web\View::POS_HEAD]);
?>


<style>
.ticket-create {
    width: 90%;
    margin: 30px auto;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.create-header {
    background: #A0BAA5;
    color: white;
    padding: 30px 40px;
    position: relative;
}

.create-title {
    font-size: 28px;
    font-weight: 600;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.create-subtitle {
    font-size: 16px;
    opacity: 0.95;
    margin: 0;
    font-weight: 400;
}

.create-content {
    padding: 35px;
}

.form-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    border-left: 4px solid #A0BAA5;
}

.form-section h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 0;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    font-weight: 600;
    color: #64748b;
    font-size: 14px;
    margin-bottom: 8px;
    display: block;
}

.form-control, .form-select {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 14px;
    transition: all 0.2s ease;
    width: 100%;
}

.form-control:focus, .form-select:focus {
    border-color: #A0BAA5;
    box-shadow: 0 0 0 3px rgba(160, 186, 165, 0.1);
    outline: none;
}

.form-control[readonly] {
    background: #f3f4f6;
    font-weight: bold;
    color: #A0BAA5;
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.section-divider {
    height: 1px;
    background: #e2e8f0;
    margin: 25px 0;
}

.action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px solid #e2e8f0;
    justify-content: center;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
}

.btn-primary {
    background: #A0BAA5;
    color: white;
}

.btn-primary:hover {
    background: #8fa994;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #64748b;
    color: white;
}

.btn-secondary:hover {
    background: #475569;
    transform: translateY(-1px);
}

.alert-info {
    background: #f0fdf4;
    color: #059669;
    border-left: 4px solid #A0BAA5;
    border-radius: 8px;
    padding: 15px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.empty-field {
    color: #94a3b8;
    font-style: italic;
}

@media (max-width: 768px) {
    .ticket-create {
        width: 95%;
        margin: 20px auto;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: stretch;
    }
    
    .create-content {
        padding: 25px;
    }
    
    .create-header {
        padding: 25px;
    }
}
</style>

<div class="ticket-create">
    <div class="create-header">
        <div class="create-title">
            <i class="fas fa-plus-circle"></i>
            <?= Html::encode($this->title) ?>
        </div>
        <p class="create-subtitle">
            Complete el formulario para crear un nuevo ticket
        </p>
    </div>

    <div class="create-content">
        <?php if (Yii::$app->session->hasFlash('fechaDesdeCalendario')): ?>
            <div class="alert-info">
                <i class="fas fa-calendar-check"></i>
                <div>
                    <strong>Fecha seleccionada desde el calendario:</strong> 
                    <?= Yii::$app->session->getFlash('fechaDesdeCalendario') ?>
                </div>
            </div>
        <?php endif; ?>

        <?php $form = ActiveForm::begin([
            'options' => ['autocomplete' => 'off'],
        ]); ?>

        <!-- Información General -->
        <div class="form-section">
            <h3><i class="fas fa-info-circle" style="color: #A0BAA5;"></i> Información General</h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <?= $form->field($model, 'Folio')->textInput([
                        'class' => 'form-control',
                        'readonly' => true,
                    ])->label('Folio del Ticket') ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'Usuario_reporta')->textInput([
                        'class' => 'form-control',
                        'placeholder' => 'Nombre de quien reporta'
                    ])->label('Usuario que Reporta') ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'Asignado_a')->dropDownList(
                        ArrayHelper::map($usuarios, 'id', 'email'),
                        [
                            'prompt' => 'Seleccionar Consultor',
                            'class' => 'form-select'
                        ]
                    )->label('Asignado A') ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'Estado')->dropDownList([
                        'ABIERTO' => 'Abierto',
                        'EN PROCESO' => 'En Proceso',
                        'CERRADO' => 'Cerrado'
                    ], [
                        'class' => 'form-select',
                    ])->label('Estado') ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'Prioridad')->dropDownList([
                        'BAJA' => 'Baja',
                        'MEDIA' => 'Media', 
                        'ALTA' => 'Alta'
                    ], [
                        'prompt' => 'Seleccionar Prioridad',
                        'class' => 'form-select'
                    ])->label('Prioridad') ?>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Información del Servicio -->
        <div class="form-section">
            <h3><i class="fas fa-cogs" style="color: #A0BAA5;"></i> Información del Servicio</h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <?= $form->field($model, 'Cliente_id')->dropDownList(
                        ArrayHelper::map($clientes, 'id', 'Nombre'),
                        [
                            'prompt' => 'Seleccionar Cliente',
                            'class' => 'form-select'
                        ]
                    )->label('Cliente') ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'Sistema_id')->dropDownList(
                        ArrayHelper::map($sistemas, 'id', 'Nombre'),
                        [
                            'prompt' => 'Seleccionar Sistema',
                            'class' => 'form-select'
                        ]
                    )->label('Sistema') ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'Servicio_id')->dropDownList(
                        ArrayHelper::map($servicios, 'id', 'Nombre'),
                        [
                            'prompt' => 'Seleccionar Servicio',
                            'class' => 'form-select'
                        ]
                    )->label('Servicio') ?>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Fechas y Tiempos -->
        <div class="form-section">
            <h3><i class="fas fa-clock" style="color: #A0BAA5;"></i> Fechas y Tiempos</h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <?= $form->field($model, 'HoraProgramada')->textInput([
                        'class' => 'form-control flatpickr-datetime',
                        'placeholder' => 'Seleccionar fecha y hora'
                    ])->label('Hora Programada') ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'HoraInicio')->textInput([
                        'class' => 'form-control flatpickr-datetime',
                        'placeholder' => 'Seleccionar fecha y hora'
                    ])->label('Hora de Inicio') ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'TiempoEfectivo')->textInput([
                        'class' => 'form-control',
                        'placeholder' => 'Ej: 2 horas, 30 minutos'
                    ])->label('Tiempo Efectivo') ?>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Descripción y Solución -->
        <div class="form-section">
            <h3><i class="fas fa-file-alt" style="color: #A0BAA5;"></i> Descripción del Problema</h3>
            
            <div class="form-grid">
                <div class="form-group full-width">
                    <?= $form->field($model, 'Descripcion')->textarea([
                        'rows' => 4,
                        'class' => 'form-control',
                        'placeholder' => 'Describe el problema o solicitud del cliente...'
                    ])->label(false) ?>
                </div>

                <div class="form-group full-width">
                    <?= $form->field($model, 'Solucion')->textarea([
                        'rows' => 3,
                        'class' => 'form-control',
                        'placeholder' => 'Describe la solución aplicada (opcional)...'
                    ])->label('Solución (Opcional)') ?>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="action-buttons">
            <?= Html::submitButton('<i class="fas fa-save"></i> Crear Ticket', [
                'class' => 'btn btn-primary'
            ]) ?>
            
            <?= Html::a('<i class="fas fa-arrow-left"></i> Volver a Tickets', ['index'], [
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

    // Auto-cargar prioridad cuando se selecciona cliente
    const clienteSelect = document.querySelector('#tickets-cliente_id');
    if (clienteSelect) {
        clienteSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const prioridad = selectedOption.getAttribute('data-prioridad');
            
            if (prioridad) {
                const prioridadSelect = document.querySelector('#tickets-prioridad');
                if (prioridadSelect) {
                    prioridadSelect.value = prioridad.toUpperCase();
                }
            }
        });
    }
});
</script>
