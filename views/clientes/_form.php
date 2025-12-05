<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Clientes $model */

$form = ActiveForm::begin([
    'id' => 'form-clientes',
    'action' => ['update', 'id' => $model->id],
    'method' => 'post',
    'options' => ['class' => 'needs-validation', 'novalidate' => true],
]);
?>

<div class="card shadow-sm border-0 rounded">
    <div class="card-header bg-primary text-white py-2">
        <h5 class="mb-0"><i class="bi bi-person-lines-fill"></i> Datos del Cliente</h5>
    </div>

    <div class="card-body py-2">

        <!-- Sección: Información General -->
        <div class="p-2 mb-2 bg-light rounded">
            <h6 class="text-secondary mb-2"><i class="bi bi-info-circle"></i> Información General</h6>
            <div class="row gx-2 gy-2">
                <div class="col-md-6">
                    <?= $form->field($model, 'Nombre')->textInput(['class' => 'form-control form-control-sm', 'placeholder' => 'Nombre del cliente'])->label(false) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'Razon_social')->textInput(['class' => 'form-control form-control-sm', 'placeholder' => 'Razón social'])->label(false) ?>
                </div>
            </div>

            <div class="row gx-2 gy-2">
                <div class="col-md-6">
                    <?= $form->field($model, 'RFC')->textInput(['class' => 'form-control form-control-sm', 'placeholder' => 'RFC'])->label(false) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'Correo')->input('email', ['class' => 'form-control form-control-sm', 'placeholder' => 'Correo electrónico'])->label(false) ?>
                </div>
            </div>
        </div>

        <!-- Sección: Contacto -->
        <div class="p-2 mb-2 bg-light rounded">
            <h6 class="text-secondary mb-2"><i class="bi bi-telephone"></i> Contacto</h6>
            <div class="row gx-2 gy-2">
                <div class="col-md-6">
                    <?= $form->field($model, 'Contacto_nombre')->textInput(['class' => 'form-control form-control-sm', 'placeholder' => 'Nombre del contacto'])->label(false) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'Tiempo')->textInput(['class' => 'form-control form-control-sm', 'placeholder' => 'Tiempo de respuesta'])->label(false) ?>
                </div>
            </div>

            <div class="row gx-2 gy-2">
                <div class="col-md-6">
                    <?= $form->field($model, 'Whatsapp_contacto')->textInput(['class' => 'form-control form-control-sm', 'placeholder' => 'Número de WhatsApp'])->label(false) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'Telefono')->textInput(['class' => 'form-control form-control-sm', 'placeholder' => 'Teléfono'])->label(false) ?>
                </div>
            </div>
        </div>

        <!-- Sección: Servicio -->
        <div class="p-2 mb-2 bg-light rounded">
            <h6 class="text-secondary mb-2"><i class="bi bi-gear"></i> Servicio</h6>
            <div class="row gx-2 gy-2">
                <div class="col-md-6">
                    <?= $form->field($model, 'Prioridad')->dropDownList(
                        ['Alta' => 'Alta', 'Media' => 'Media', 'Baja' => 'Baja'],
                        ['class' => 'form-select form-select-sm']
                    )->label(false) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'Tipo_servicio')->dropDownList(
                        ['Servicio A' => 'Servicio A', 'Servicio B' => 'Servicio B'],
                        ['class' => 'form-select form-select-sm']
                    )->label(false) ?>
                </div>
            </div>

            <div class="row gx-2 gy-2">
                <div class="col-md-6">
                    <?= $form->field($model, 'Estado')->dropDownList(
                        ['10' => 'Activo', '0' => 'Inactivo'],
                        ['class' => 'form-select form-select-sm']
                    )->label(false) ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Footer del formulario -->
    <div class="card-footer bg-light text-end py-2">
        <?= Html::submitButton('<i class="bi bi-save"></i> Guardar', ['class' => 'btn btn-success btn-sm me-2']) ?>
        <?= Html::a('<i class="bi bi-x-circle"></i> Cancelar', ['clientes/index'], ['class' => 'btn btn-secondary btn-sm']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>