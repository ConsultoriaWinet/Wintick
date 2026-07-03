<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Json;

/** @var yii\web\View $this */
/** @var app\models\Clientes $model */

$form = ActiveForm::begin([
    'id' => 'form-clientes',
    'action' => $model->isNewRecord ? ['create'] : ['update', 'id' => $model->id],
    'method' => 'post',
    'options' => ['novalidate' => true],
]);

$inputStyle = 'width:100%;padding:7px 10px;border:1px solid var(--border,#e5e7eb);border-radius:8px;font-size:13px;background:var(--surface,#fff);color:var(--text,#111827);transition:border-color .15s;';
$selectStyle = $inputStyle;
$labelStyle = 'display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-3,#6b7280);margin-bottom:5px;';
$sectionStyle = 'margin-bottom:18px;';
$sectionHead = 'font-size:12px;font-weight:700;color:var(--text-2,#374151);margin:0 0 12px;display:flex;align-items:center;gap:6px;';

// Datos existentes para pre-poblar filas
$telefonosData = Json::encode($model->getTelefonos());
$whatsappsData = Json::encode($model->getWhatsapps());
$correosData = Json::encode($model->getCorreos());
?>

<style>
    .cli-form-input:focus,
    .cli-form-select:focus {
        outline: none;
        border-color: var(--accent, #3b82f6) !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
    }

    .cli-form-field {
        margin-bottom: 0;
    }

    .cli-form-field .help-block {
        font-size: 11px;
        color: #ef4444;
        margin-top: 3px;
    }

    .cli-form-field.has-error .cli-form-input,
    .cli-form-field.has-error .cli-form-select {
        border-color: #ef4444 !important;
    }

    .multi-row {
        display: flex;
        gap: 8px;
        margin-bottom: 6px;
        align-items: center;
    }

    .multi-row .row-label {
        width: 32%;
        flex-shrink: 0;
    }

    .multi-row .row-valor {
        flex: 1;
    }

    .multi-row .btn-remove {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 6px;
        background: var(--surface, #fff);
        color: #ef4444;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    .multi-row .btn-remove:hover {
        background: #fef2f2;
    }

    .btn-add-row {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 600;
        color: var(--accent, #3b82f6);
        background: none;
        border: 1px dashed var(--accent, #3b82f6);
        border-radius: 7px;
        padding: 5px 10px;
        cursor: pointer;
        margin-top: 4px;
    }

    .btn-add-row:hover {
        background: var(--accent-light, #eff6ff);
    }

    .multi-header {
        display: flex;
        gap: 8px;
        margin-bottom: 4px;
        padding-right: 36px;
    }

    .multi-header span {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: var(--text-3, #6b7280);
    }

    .multi-header .lbl-col {
        width: 32%;
        flex-shrink: 0;
    }

    .multi-header .val-col {
        flex: 1;
    }
</style>

<!-- Header -->
<div
    style="padding:14px 18px 12px;border-bottom:1px solid var(--border,#e5e7eb);display:flex;align-items:center;gap:10px;">
    <div
        style="width:34px;height:34px;border-radius:9px;background:var(--accent-light,#eff6ff);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
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
            <span
                style="width:20px;height:20px;border-radius:5px;background:var(--surface-2,#f3f4f6);display:inline-flex;align-items:center;justify-content:center;">
                <i class="fas fa-info-circle" style="font-size:10px;color:var(--text-3,#6b7280);"></i>
            </span>
            Información General
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <?= $form->field($model, 'Nombre', ['options' => ['class' => 'cli-form-field'], 'labelOptions' => ['style' => $labelStyle]])
                    ->textInput(['class' => 'cli-form-input', 'style' => $inputStyle, 'placeholder' => 'Nombre del cliente']) ?>
            </div>
            <div>
                <?= $form->field($model, 'Razon_social', ['options' => ['class' => 'cli-form-field'], 'labelOptions' => ['style' => $labelStyle]])
                    ->textInput(['class' => 'cli-form-input', 'style' => $inputStyle, 'placeholder' => 'Razón social']) ?>
            </div>
            <div>
                <?= $form->field($model, 'RFC', ['options' => ['class' => 'cli-form-field'], 'labelOptions' => ['style' => $labelStyle]])
                    ->textInput(['class' => 'cli-form-input', 'style' => $inputStyle . 'font-family:monospace;text-transform:uppercase;', 'placeholder' => 'RFC']) ?>
            </div>
            <div>
                <?= $form->field($model, 'Contacto_nombre', ['options' => ['class' => 'cli-form-field'], 'labelOptions' => ['style' => $labelStyle]])
                    ->textInput(['class' => 'cli-form-input', 'style' => $inputStyle, 'placeholder' => 'Nombre del contacto principal']) ?>
            </div>
        </div>
    </div>

    <div style="border-top:1px solid var(--border,#e5e7eb);margin-bottom:18px;"></div>

    <!-- Teléfonos -->
    <div style="<?= $sectionStyle ?>">
        <p style="<?= $sectionHead ?>">
            <span
                style="width:20px;height:20px;border-radius:5px;background:var(--surface-2,#f3f4f6);display:inline-flex;align-items:center;justify-content:center;">
                <i class="fas fa-phone" style="font-size:10px;color:var(--text-3,#6b7280);"></i>
            </span>
            Teléfonos
        </p>
        <div class="multi-header">
            <span class="lbl-col">Etiqueta</span>
            <span class="val-col">Número</span>
        </div>
        <div id="telefonos-container"></div>
        <button type="button" class="btn-add-row" onclick="multiAddRow('telefonos-container','tel-tpl')">
            <i class="fas fa-plus" style="font-size:10px;"></i> Agregar teléfono
        </button>
        <?= Html::hiddenInput('Clientes[Telefono]', $model->Telefono ?? '[]', ['id' => 'telefono-hidden']) ?>
    </div>

    <div style="border-top:1px solid var(--border,#e5e7eb);margin-bottom:18px;"></div>

    <!-- WhatsApp -->
    <div style="<?= $sectionStyle ?>">
        <p style="<?= $sectionHead ?>">
            <span
                style="width:20px;height:20px;border-radius:5px;background:var(--surface-2,#f3f4f6);display:inline-flex;align-items:center;justify-content:center;">
                <i class="fab fa-whatsapp" style="font-size:10px;color:var(--text-3,#6b7280);"></i>
            </span>
            WhatsApp
        </p>
        <div class="multi-header">
            <span class="lbl-col">Etiqueta</span>
            <span class="val-col">Número</span>
        </div>
        <div id="whatsapps-container"></div>
        <button type="button" class="btn-add-row" onclick="multiAddRow('whatsapps-container','wa-tpl')">
            <i class="fas fa-plus" style="font-size:10px;"></i> Agregar WhatsApp
        </button>
        <?= Html::hiddenInput('Clientes[Whatsapp_contacto]', $model->Whatsapp_contacto ?? '[]', ['id' => 'whatsapp-hidden']) ?>
    </div>

    <div style="border-top:1px solid var(--border,#e5e7eb);margin-bottom:18px;"></div>

    <!-- Correos -->
    <div style="<?= $sectionStyle ?>">
        <p style="<?= $sectionHead ?>">
            <span
                style="width:20px;height:20px;border-radius:5px;background:var(--surface-2,#f3f4f6);display:inline-flex;align-items:center;justify-content:center;">
                <i class="fas fa-envelope" style="font-size:10px;color:var(--text-3,#6b7280);"></i>
            </span>
            Correos
        </p>
        <div class="multi-header">
            <span class="lbl-col">Etiqueta</span>
            <span class="val-col">Correo electrónico</span>
        </div>
        <div id="correos-container"></div>
        <button type="button" class="btn-add-row" onclick="multiAddRow('correos-container','correo-tpl')">
            <i class="fas fa-plus" style="font-size:10px;"></i> Agregar correo
        </button>
        <?= Html::hiddenInput('Clientes[Correo]', $model->Correo ?? '[]', ['id' => 'correo-hidden']) ?>
    </div>

    <div style="border-top:1px solid var(--border,#e5e7eb);margin-bottom:18px;"></div>

    <!-- Servicio -->
    <div style="<?= $sectionStyle ?>">
        <p style="<?= $sectionHead ?>">
            <span
                style="width:20px;height:20px;border-radius:5px;background:var(--surface-2,#f3f4f6);display:inline-flex;align-items:center;justify-content:center;">
                <i class="fas fa-cog" style="font-size:10px;color:var(--text-3,#6b7280);"></i>
            </span>
            Servicio
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <?= $form->field($model, 'Tiempo', ['options' => ['class' => 'cli-form-field'], 'labelOptions' => ['style' => $labelStyle]])
                    ->textInput(['class' => 'cli-form-input', 'style' => $inputStyle, 'placeholder' => 'Horas disponibles']) ?>
            </div>
            <div>
                <?= $form->field($model, 'Tipo_servicio', ['options' => ['class' => 'cli-form-field'], 'labelOptions' => ['style' => $labelStyle]])
                    ->dropDownList(['POLIZA' => 'Póliza', 'EVENTO' => 'Evento'], ['class' => 'cli-form-select', 'style' => $selectStyle]) ?>
            </div>
            <div>
                <?= $form->field($model, 'Prioridad', ['options' => ['class' => 'cli-form-field'], 'labelOptions' => ['style' => $labelStyle]])
                    ->dropDownList(['Alta' => 'Alta', 'Media' => 'Media', 'Baja' => 'Baja'], ['class' => 'cli-form-select', 'style' => $selectStyle]) ?>
            </div>
            <div>
                <?= $form->field($model, 'Criticidad', ['options' => ['class' => 'cli-form-field'], 'labelOptions' => ['style' => $labelStyle]])
                    ->dropDownList(['Baja' => 'Baja', 'Media' => 'Media', 'Urgente' => 'Urgente'], ['class' => 'cli-form-select', 'style' => $selectStyle]) ?>
            </div>
            <div>
                <?= $form->field($model, 'Estado', ['options' => ['class' => 'cli-form-field'], 'labelOptions' => ['style' => $labelStyle]])
                    ->dropDownList(['1' => 'Activo', '0' => 'Inactivo'], ['class' => 'cli-form-select', 'style' => $selectStyle]) ?>
            </div>
        </div>
    </div>

</div>

<!-- Footer -->
<div
    style="padding:12px 18px;border-top:1px solid var(--border,#e5e7eb);display:flex;justify-content:space-between;align-items:center;background:var(--surface,#fff);">

    <div>

        <?php if (
            !$model->isNewRecord &&
            Yii::$app->user->identity->esDev()
        ): ?>

            <button type="button" id="btnEliminarCliente" data-id="<?= $model->id ?>" style="
                    background:#dc2626;
                    color:#fff;
                    border:none;
                    padding:8px 16px;
                    border-radius:8px;
                    font-weight:600;
                    cursor:pointer;
                ">
                <i class="fas fa-trash"></i>
                Eliminar Cliente
            </button>

        <?php endif; ?>

    </div>

    <div style="display:flex;gap:8px;">

        <?= Html::a('Cancelar', '#', [
            'class' => 'btn btn-secondary',
            'data-bs-dismiss' => 'modal'
        ]) ?>

        <?= Html::submitButton(
            $model->isNewRecord ? 'Crear' : 'Guardar',
            ['class' => 'btn btn-primary']
        ) ?>

    </div>

</div>

<?php if (!$model->isNewRecord && Yii::$app->user->identity->esDev()): ?>

    <script>

        $('#btnEliminarCliente').on('click', function () {

            const id = $(this).data('id');

            Swal.fire({

                title: '¿Eliminar cliente?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'

            }).then((result) => {

                if (result.isConfirmed) {

                    console.log('URL:', '<?= \yii\helpers\Url::to(['clientes/delete']) ?>');

                    $.ajax({
                        url: '<?= \yii\helpers\Url::to(['clientes/delete']) ?>',
                        type: 'POST',
                        data: {
                            id: id,
                            _csrf: yii.getCsrfToken()
                        },
                        success: function (data) {

                            if (data.success) {

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Cliente eliminado'
                                }).then(() => {
                                    location.reload();
                                });

                            } else {

                                Swal.fire({
                                    icon: 'error',
                                    title: 'No se pudo eliminar',
                                    text: data.message
                                });

                            }

                        },
                        error: function (xhr) {

                            Swal.fire({
                                icon: 'error',
                                title: 'No se pudo eliminar',
                                text: xhr.responseText
                            });

                        }
                    });

                }

            });

        });

    </script>

<?php endif; ?>

<?php ActiveForm::end(); ?>

<!-- Templates ocultos para filas -->
<template id="tel-tpl">
    <div class="multi-row">
        <input type="text" class="cli-form-input row-label" style="<?= $inputStyle ?>width:32%;"
            placeholder="Ej: Oficina, CP. Norma">
        <input type="text" class="cli-form-input row-valor" style="<?= $inputStyle ?>flex:1;"
            placeholder="Número de teléfono">
        <button type="button" class="btn-remove" onclick="multiRemoveRow(this)">×</button>
    </div>
</template>
<template id="wa-tpl">
    <div class="multi-row">
        <input type="text" class="cli-form-input row-label" style="<?= $inputStyle ?>width:32%;"
            placeholder="Ej: Sonia, Gerente">
        <input type="text" class="cli-form-input row-valor" style="<?= $inputStyle ?>flex:1;"
            placeholder="Número WhatsApp">
        <button type="button" class="btn-remove" onclick="multiRemoveRow(this)">×</button>
    </div>
</template>
<template id="correo-tpl">
    <div class="multi-row">
        <input type="text" class="cli-form-input row-label" style="<?= $inputStyle ?>width:32%;"
            placeholder="Ej: Facturación, Principal">
        <input type="text" class="cli-form-input row-valor" style="<?= $inputStyle ?>flex:1;"
            placeholder="correo@empresa.com">
        <button type="button" class="btn-remove" onclick="multiRemoveRow(this)">×</button>
    </div>
</template>

<script>
    (function () {
        var telefonos = <?= $telefonosData ?>;
        var whatsapps = <?= $whatsappsData ?>;
        var correos = <?= $correosData ?>;

        function buildRow(tplId, label, valor) {
            var tpl = document.getElementById(tplId);
            var clone = tpl.content.cloneNode(true);
            clone.querySelector('.row-label').value = label || '';
            clone.querySelector('.row-valor').value = valor || '';
            return clone;
        }

        function initContainer(containerId, tplId, data) {
            var container = document.getElementById(containerId);
            if (data && data.length) {
                data.forEach(function (item) {
                    container.appendChild(buildRow(tplId, item.label, item.valor));
                });
            }
        }

        initContainer('telefonos-container', 'tel-tpl', telefonos);
        initContainer('whatsapps-container', 'wa-tpl', whatsapps);
        initContainer('correos-container', 'correo-tpl', correos);

        window.multiAddRow = function (containerId, tplId) {
            document.getElementById(containerId).appendChild(buildRow(tplId, '', ''));
        };

        window.multiRemoveRow = function (btn) {
            btn.closest('.multi-row').remove();
        };

        function serializeContainer(containerId) {
            var items = [];
            document.querySelectorAll('#' + containerId + ' .multi-row').forEach(function (row) {
                var valor = row.querySelector('.row-valor').value.trim();
                if (valor) {
                    items.push({
                        label: row.querySelector('.row-label').value.trim(),
                        valor: valor
                    });
                }
            });
            return JSON.stringify(items);
        }

        $('#form-clientes').on('beforeSubmit', function () {
            document.getElementById('telefono-hidden').value = serializeContainer('telefonos-container');
            document.getElementById('whatsapp-hidden').value = serializeContainer('whatsapps-container');
            document.getElementById('correo-hidden').value = serializeContainer('correos-container');
            return true;
        });
    })();
</script>