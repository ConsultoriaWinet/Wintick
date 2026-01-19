<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Clientes $model */

$this->title = "Cliente: {$model->Nombre}";
?>

<style>
    body {
        padding-top: 0px;
    }
</style>


<div class="clientes-view">

    <h1 class="mb-4"><?= Html::encode($this->title) ?></h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <?php $form = ActiveForm::begin(['options' => ['class' => 'row g-3']]); ?>

            <div class="col-md-6">
                <?= $form->field($model, 'Nombre')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'Razon_social')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'RFC')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'Correo')->input('email') ?>
                <?= $form->field($model, 'Contacto_nombre')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'Tiempo')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'Whatsapp_contacto')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'Telefono')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'Prioridad')->dropDownList([
                    'Alta' => 'Alta',
                    'Media' => 'Media',
                    'Baja' => 'Baja'
                ], ['prompt' => 'Selecciona Prioridad']) ?>

                <?= $form->field($model, 'Criticidad')->dropDownList([
                    'Baja' => 'Baja',
                    'Media' => 'Media',
                    'Urgente' => 'Urgente'
                ], ['prompt' => 'Selecciona Criticidad']) ?>
                <?= $form->field($model, 'Estado')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'created_at')->textInput(['readonly' => true]) ?>
                <?= $form->field($model, 'updated_at')->textInput(['readonly' => true]) ?>
            </div>

            <div class="col-12 mt-3 d-flex justify-content-end gap-2">
                <?= Html::a('Regresar', ['index'], ['class' => 'btn btn-secondary']) ?>
                <?= Html::submitButton('Guardar Cambios', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Eliminar Cliente', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '¿Seguro deseas eliminar este cliente?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>


            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>

<style>
    .card {
        border-radius: 16px;
        padding: 20px;
        background-color: #ffffff;
    }

    .card-body {
        padding: 2rem;
    }

    .btn-success {
        background-color: #8BA590;
        border-color: #8BA590;
    }

    .btn-success:hover {
        background-color: #7a9582;
        border-color: #7a9582;
    }

    .btn-danger {
        background-color: #ef4444;
        border-color: #ef4444;
    }

    .btn-danger:hover {
        background-color: #dc2626;
        border-color: #dc2626;
    }

    @media (max-width: 768px) {
        .row.g-3>.col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>