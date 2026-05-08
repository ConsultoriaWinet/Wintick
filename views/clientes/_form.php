<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Clientes $model */

$form = ActiveForm::begin([
    'id'      => 'form-clientes',
    'action'  => $model->isNewRecord ? ['create'] : ['update', 'id' => $model->id],
    'method'  => 'post',
    'options' => ['novalidate' => true],
]);

$inputStyle  = 'width:100%;padding:7px 10px;border:1px solid var(--border,#e5e7eb);border-radius:8px;font-size:13px;background:var(--surface,#fff);color:var(--text,#111827);transition:border-color .15s;';
$selectStyle = $inputStyle;
$labelStyle  = 'display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-3,#6b7280);margin-bottom:5px;';
$sectionStyle= 'margin-bottom:18px;';
$sectionHead = 'font-size:12px;font-weight:700;color:var(--text-2,#374151);margin:0 0 12px;display:flex;align-items:center;gap:6px;';
?>

<style>
.cli-form-input:focus, .cli-form-select:focus {
    outline: none;
    border-color: var(--accent, #3b82f6) !important;
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}
.cli-form-field { margin-bottom: 0; }
.cli-form-field .help-block { font-size: 11px; color: #ef4444; margin-top: 3px; }
.cli-form-field.has-error .cli-form-input,
.cli-form-field.has-error .cli-form-select { border-color: #ef4444 !important; }
</style>

<!-- Header -->
<div style="padding:14px 18px 12px;border-bottom:1px solid var(--border,#e5e7eb);display:flex;align-items:center;gap:10px;">
    <div style="width:34px;height:34px;border-radius:9px;background:var(--accent-light,#eff6ff);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fas fa-building" style="color:var(--accent,#3b82f6);font-size:14px;"></i>
    </div>
    <div>
        <div style="font-size:14px;font-weight:700;color:var(--text,#111827);">
            <?= $model->isNewRecord ? 'Nuevo Cliente' : Html::encode($model->Nombre) ?>
        </div>
        <div style="font-size:11.5px;color:var(--text-3,#6b7280);">
            <?= $model->isNewRecord ? 'Completa los datos del cliente' : 'Editar información del cliente' ?>
        </div>
    </div>
</div>

<!-- Cuerpo -->
<div style="padding:18px 18px 8px;">

    <!-- Información General -->
    <div style="<?= $sectionStyle ?>">
        <p style="<?= $sectionHead ?>">
            <span style="width:20px;height:20px;border-radius:5px;background:var(--surface-2,#f3f4f6);display:inline-flex;align-items:center;justify-content:center;">
                <i class="fas fa-info-circle" style="font-size:10px;color:var(--text-3,#6b7280);"></i>
            </span>
            Información General
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <?= $form->field($model, 'Nombre', ['options'=>['class'=>'cli-form-field'], 'labelOptions'=>['style'=>$labelStyle]])
                    ->textInput(['class'=>'cli-form-input', 'style'=>$inputStyle, 'placeholder'=>'Nombre del cliente']) ?>
            </div>
            <div>
                <?= $form->field($model, 'Razon_social', ['options'=>['class'=>'cli-form-field'], 'labelOptions'=>['style'=>$labelStyle]])
                    ->textInput(['class'=>'cli-form-input', 'style'=>$inputStyle, 'placeholder'=>'Razón social']) ?>
            </div>
            <div>
                <?= $form->field($model, 'RFC', ['options'=>['class'=>'cli-form-field'], 'labelOptions'=>['style'=>$labelStyle]])
                    ->textInput(['class'=>'cli-form-input', 'style'=>$inputStyle . 'font-family:monospace;text-transform:uppercase;', 'placeholder'=>'RFC']) ?>
            </div>
            <div>
                <?= $form->field($model, 'Correo', ['options'=>['class'=>'cli-form-field'], 'labelOptions'=>['style'=>$labelStyle]])
                    ->input('email', ['class'=>'cli-form-input', 'style'=>$inputStyle, 'placeholder'=>'correo@empresa.com']) ?>
            </div>
        </div>
    </div>

    <!-- Separador -->
    <div style="border-top:1px solid var(--border,#e5e7eb);margin-bottom:18px;"></div>

    <!-- Contacto -->
    <div style="<?= $sectionStyle ?>">
        <p style="<?= $sectionHead ?>">
            <span style="width:20px;height:20px;border-radius:5px;background:var(--surface-2,#f3f4f6);display:inline-flex;align-items:center;justify-content:center;">
                <i class="fas fa-phone" style="font-size:10px;color:var(--text-3,#6b7280);"></i>
            </span>
            Contacto
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <?= $form->field($model, 'Contacto_nombre', ['options'=>['class'=>'cli-form-field'], 'labelOptions'=>['style'=>$labelStyle]])
                    ->textInput(['class'=>'cli-form-input', 'style'=>$inputStyle, 'placeholder'=>'Nombre del contacto']) ?>
            </div>
            <div>
                <?= $form->field($model, 'Tiempo', ['options'=>['class'=>'cli-form-field'], 'labelOptions'=>['style'=>$labelStyle]])
                    ->textInput(['class'=>'cli-form-input', 'style'=>$inputStyle, 'placeholder'=>'Horas disponibles']) ?>
            </div>
            <div>
                <?= $form->field($model, 'Whatsapp_contacto', ['options'=>['class'=>'cli-form-field'], 'labelOptions'=>['style'=>$labelStyle]])
                    ->textInput(['class'=>'cli-form-input', 'style'=>$inputStyle, 'placeholder'=>'Número WhatsApp']) ?>
            </div>
            <div>
                <?= $form->field($model, 'Telefono', ['options'=>['class'=>'cli-form-field'], 'labelOptions'=>['style'=>$labelStyle]])
                    ->textInput(['class'=>'cli-form-input', 'style'=>$inputStyle, 'placeholder'=>'Teléfono']) ?>
            </div>
        </div>
    </div>

    <!-- Separador -->
    <div style="border-top:1px solid var(--border,#e5e7eb);margin-bottom:18px;"></div>

    <!-- Servicio -->
    <div style="<?= $sectionStyle ?>">
        <p style="<?= $sectionHead ?>">
            <span style="width:20px;height:20px;border-radius:5px;background:var(--surface-2,#f3f4f6);display:inline-flex;align-items:center;justify-content:center;">
                <i class="fas fa-cog" style="font-size:10px;color:var(--text-3,#6b7280);"></i>
            </span>
            Servicio
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <?= $form->field($model, 'Prioridad', ['options'=>['class'=>'cli-form-field'], 'labelOptions'=>['style'=>$labelStyle]])
                    ->dropDownList(['Alta'=>'Alta','Media'=>'Media','Baja'=>'Baja'], ['class'=>'cli-form-select', 'style'=>$selectStyle]) ?>
            </div>
            <div>
                <?= $form->field($model, 'Tipo_servicio', ['options'=>['class'=>'cli-form-field'], 'labelOptions'=>['style'=>$labelStyle]])
                    ->dropDownList(['POLIZA'=>'Póliza','EVENTO'=>'Evento'], ['class'=>'cli-form-select', 'style'=>$selectStyle]) ?>
            </div>
            <div>
                <?= $form->field($model, 'Estado', ['options'=>['class'=>'cli-form-field'], 'labelOptions'=>['style'=>$labelStyle]])
                    ->dropDownList(['10'=>'Activo','0'=>'Inactivo'], ['class'=>'cli-form-select', 'style'=>$selectStyle]) ?>
            </div>
            <div>
                <?= $form->field($model, 'Criticidad', ['options'=>['class'=>'cli-form-field'], 'labelOptions'=>['style'=>$labelStyle]])
                    ->dropDownList(['Baja'=>'Baja','Media'=>'Media','Urgente'=>'Urgente'], ['class'=>'cli-form-select', 'style'=>$selectStyle]) ?>
            </div>
        </div>
    </div>

</div>

<!-- Footer -->
<div style="padding:12px 18px;border-top:1px solid var(--border,#e5e7eb);display:flex;justify-content:flex-end;gap:8px;background:var(--surface,#fff);">
    <?= Html::a(
        '<i class="fas fa-times"></i> Cancelar',
        ['clientes/index'],
        ['style' => 'display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:500;background:var(--surface,#fff);color:var(--text-2,#374151);text-decoration:none;border:1px solid var(--border,#e5e7eb);']
    ) ?>
    <?= Html::submitButton(
        '<i class="fas fa-save"></i> Guardar',
        ['style' => 'display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;background:var(--accent,#3b82f6);color:#fff;border:none;cursor:pointer;']
    ) ?>
</div>

<?php ActiveForm::end(); ?>
