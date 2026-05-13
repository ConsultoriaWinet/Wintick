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

// Texto pre-seleccionado para ss-wrap
$clienteNombre = '';
if ($model->Cliente_id) {
    foreach ($clientes as $c) { if ($c['id'] == $model->Cliente_id) { $clienteNombre = $c['Nombre']; break; } }
}
$sistemaNombre = '';
if ($model->Sistema_id) {
    foreach ($sistemas as $s) { if ($s['id'] == $model->Sistema_id) { $sistemaNombre = $s['Nombre']; break; } }
}
$servicioNombre = '';
if ($model->Servicio_id) {
    foreach ($servicios as $s) { if ($s['id'] == $model->Servicio_id) { $servicioNombre = $s['Nombre']; break; } }
}

// Valores en formato datetime-local (YYYY-MM-DDTHH:MM)
$horaProgramadaVal = $model->HoraProgramada ? date('Y-m-d\TH:i', strtotime($model->HoraProgramada)) : '';
$horaInicioVal     = $model->HoraInicio     ? date('Y-m-d\TH:i', strtotime($model->HoraInicio))     : '';
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
        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" style="border-radius:8px;">
                <i class="fas fa-exclamation-triangle"></i>
                <?= Html::encode(Yii::$app->session->getFlash('error')) ?>
            </div>
        <?php endif; ?>

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
            <label class="form-label">Cliente</label>
            <div class="ss-wrap">
                <input type="text" class="form-control ss-input" placeholder="Buscar cliente..."
                       value="<?= Html::encode($clienteNombre) ?>">
                <div class="ss-dropdown"></div>
                <select id="tickets-cliente_id" name="Tickets[Cliente_id]" style="display:none">
                    <option value="">Seleccionar Cliente</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= $model->Cliente_id == $c['id'] ? 'selected' : '' ?>>
                            <?= Html::encode($c['Nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Sistema</label>
            <div class="ss-wrap">
                <input type="text" class="form-control ss-input" placeholder="Buscar sistema..."
                       value="<?= Html::encode($sistemaNombre) ?>">
                <div class="ss-dropdown"></div>
                <select id="tickets-sistema_id" name="Tickets[Sistema_id]" style="display:none">
                    <option value="">Seleccionar Sistema</option>
                    <?php foreach ($sistemas as $s): ?>
                        <option value="<?= $s['id'] ?>"
                            <?= $model->Sistema_id == $s['id'] ? 'selected' : '' ?>>
                            <?= Html::encode($s['Nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Servicio</label>
            <div class="ss-wrap">
                <input type="text" class="form-control ss-input" placeholder="Buscar servicio..."
                       value="<?= Html::encode($servicioNombre) ?>">
                <div class="ss-dropdown"></div>
                <select id="tickets-servicio_id" name="Tickets[Servicio_id]" style="display:none">
                    <option value="">Seleccionar Servicio</option>
                    <?php foreach ($servicios as $s): ?>
                        <option value="<?= $s['id'] ?>"
                            <?= $model->Servicio_id == $s['id'] ? 'selected' : '' ?>>
                            <?= Html::encode($s['Nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
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
            <div class="form-group">
                <label class="form-label">Hora Reporte</label>
                <input type="datetime-local" class="form-control"
                       name="Tickets[HoraProgramada]"
                       value="<?= Html::encode($horaProgramadaVal) ?>">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">Hora Inicio</label>
                <input type="datetime-local" class="form-control"
                       name="Tickets[HoraInicio]"
                       value="<?= Html::encode($horaInicioVal) ?>">
            </div>
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
                'ABIERTO'    => 'Abierto',
                'EN PROCESO' => 'En Proceso',
                'EN ESPERA'  => 'En Espera',
            ], [
                'class' => 'form-select'
            ])->label('Estado') ?>
        </div>

        <div class="col-md-8">
            <div class="alert alert-info d-flex align-items-center gap-2 mb-0 mt-3" style="border-radius:8px; font-size:13px;">
                <i class="fas fa-info-circle fa-lg"></i>
                <div>
                    Para <strong>cerrar</strong> el ticket con solución, tiempo efectivo y fecha de cierre,
                    hazlo desde la <a href="<?= \yii\helpers\Url::to(['/tickets/index']) ?>">lista de tickets</a>.
                </div>
            </div>
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

    // ── Selects buscables (cliente / sistema / servicio) ──
    (function() {
        function normalize(s) {
            return (s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
        }

        function initWrap(wrap) {
            const input    = wrap.querySelector('.ss-input');
            const dropdown = wrap.querySelector('.ss-dropdown');
            const select   = wrap.querySelector('select');
            if (!input || !dropdown || !select) return;

            const allOptions = Array.from(select.options).filter(o => o.value !== '');
            let _guard = false;

            function render(items) {
                if (!items.length) {
                    const empty = document.createElement('div');
                    empty.className = 'ss-empty';
                    empty.textContent = 'Sin resultados';
                    dropdown.replaceChildren(empty);
                } else {
                    const q   = normalize(input.value);
                    const re  = q ? new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi') : null;
                    const frag = document.createDocumentFragment();
                    items.forEach(opt => {
                        const el = document.createElement('div');
                        el.className     = 'ss-item';
                        el.dataset.value = String(opt.value);
                        el.innerHTML = re ? opt.text.replace(re, '<strong>$1</strong>') : opt.text;
                        el.addEventListener('mousedown', e => {
                            e.preventDefault();
                            input.value  = opt.text;
                            select.value = opt.value;
                            dropdown.style.display = 'none';
                        });
                        frag.appendChild(el);
                    });
                    dropdown.replaceChildren(frag);
                }
                dropdown.style.display = 'block';
            }

            function filterAndShow() {
                const q = normalize(input.value);
                render((q ? allOptions.filter(o => normalize(o.text).includes(q)) : allOptions).slice(0, 12));
                if (!_guard && document.activeElement !== input) {
                    _guard = true;
                    const ss = input.selectionStart ?? input.value.length;
                    const se = input.selectionEnd   ?? input.value.length;
                    input.focus();
                    try { input.setSelectionRange(ss, se); } catch (_) {}
                    _guard = false;
                }
            }

            input.addEventListener('focus',   () => { if (!_guard) filterAndShow(); });
            input.addEventListener('input',   filterAndShow);
            input.addEventListener('blur',    () => setTimeout(() => { dropdown.style.display = 'none'; }, 160));
            input.addEventListener('keydown', e => {
                if (dropdown.style.display === 'none') return;
                const items  = [...dropdown.querySelectorAll('.ss-item')];
                const active = dropdown.querySelector('.ss-active');
                const idx    = items.indexOf(active);
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    items.forEach(i => i.classList.remove('ss-active'));
                    (items[Math.min(idx + 1, items.length - 1)] || items[0])?.classList.add('ss-active');
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    items.forEach(i => i.classList.remove('ss-active'));
                    (items[Math.max(idx - 1, 0)])?.classList.add('ss-active');
                } else if (e.key === 'Enter' || e.key === 'Tab') {
                    const target = dropdown.querySelector('.ss-active') || items[0];
                    if (target) {
                        e.preventDefault();
                        const opt = allOptions.find(o => o.value === target.dataset.value);
                        if (opt) { input.value = opt.text; select.value = opt.value; dropdown.style.display = 'none'; }
                    }
                } else if (e.key === 'Escape') {
                    dropdown.style.display = 'none';
                }
            });
        }

        document.querySelectorAll('.ss-wrap').forEach(initWrap);
    })();
});
</script>
