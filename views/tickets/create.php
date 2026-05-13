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
/** @var bool $desdeCalendario */

$desdeCalendario = $desdeCalendario ?? false;
$this->title = 'Crear Ticket';

// Obtener datos para los dropdowns
$clientes  = Clientes::find()->asArray()->all();
$sistemas  = Sistemas::find()->asArray()->all();
$servicios = Servicios::find()->asArray()->all();
$usuarios  = Usuarios::find()->where(['rol' => ['consultor','Consultores']])->asArray()->all();

// Texto pre-seleccionado para ss-wrap (al re-renderizar tras error de validación)
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


?>


<style>
    .ticket-create {
        width: 90%;
        margin: 30px auto;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
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

    .form-control,
    .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 14px;
        transition: all 0.2s ease;
        width: 100%;
    }

    .form-control:focus,
    .form-select:focus {
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
                    $consultoresList,
                    [
                        'prompt' => 'Seleccionar Consultor',
                        'class' => 'form-select'
                    ]
                )->label('Asignado A') ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'Estado')->dropDownList([
                        'ABIERTO'     => 'Abierto',
                        'PROGRAMADO'  => 'Programado',
                        'EN PROCESO'  => 'En Proceso',
                        'CONTPAQi'    => 'CONTPAQi',
                        'CERRADO'     => 'Cerrado',
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
                    <label class="form-label">Cliente</label>
                    <div class="ss-wrap">
                        <input type="text" class="form-control ss-input" id="ss-cliente-text"
                               placeholder="Buscar cliente..."
                               value="<?= Html::encode($clienteNombre) ?>">
                        <div class="ss-dropdown"></div>
                        <select id="tickets-cliente_id" name="Tickets[Cliente_id]" style="display:none">
                            <option value="">Seleccionar Cliente</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c['id'] ?>"
                                    data-prioridad="<?= Html::encode($c['Prioridad'] ?? '') ?>"
                                    <?= $model->Cliente_id == $c['id'] ? 'selected' : '' ?>>
                                    <?= Html::encode($c['Nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Sistema</label>
                    <div class="ss-wrap">
                        <input type="text" class="form-control ss-input" id="ss-sistema-text"
                               placeholder="Buscar sistema..."
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

                <div class="form-group">
                    <label class="form-label">Servicio</label>
                    <div class="ss-wrap">
                        <input type="text" class="form-control ss-input" id="ss-servicio-text"
                               placeholder="Buscar servicio..."
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
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Fechas y Tiempos -->
        <div class="form-section">
            <h3><i class="fas fa-clock" style="color: #A0BAA5;"></i> Fechas y Tiempos</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Hora Reporte</label>
                    <input type="datetime-local" class="form-control"
                           name="Tickets[HoraProgramada]"
                           value="<?= Html::encode($model->HoraProgramada ? date('Y-m-d\TH:i', strtotime($model->HoraProgramada)) : '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Hora Inicio</label>
                    <input type="datetime-local" class="form-control"
                           name="Tickets[HoraInicio]"
                           value="<?= Html::encode($model->HoraInicio ? date('Y-m-d\TH:i', strtotime($model->HoraInicio)) : '') ?>">
                </div>

                <div class="form-group">
                    <?php
                    $tiempoOpts = ['class' => 'form-control', 'placeholder' => 'Ej: 2 horas, 30 minutos'];
                    if ($desdeCalendario) {
                        $tiempoOpts['disabled']    = true;
                        $tiempoOpts['placeholder'] = 'Se completa al cerrar el ticket';
                    }
                    echo $form->field($model, 'TiempoEfectivo')->textInput($tiempoOpts)->label('Tiempo Efectivo');
                    ?>
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
                    <?php
                    $solucionOpts = ['rows' => 3, 'class' => 'form-control', 'placeholder' => 'Describe la solución aplicada (opcional)...'];
                    if ($desdeCalendario) {
                        $solucionOpts['disabled']    = true;
                        $solucionOpts['placeholder'] = 'Se completa al cerrar el ticket';
                    }
                    echo $form->field($model, 'Solucion')->textarea($solucionOpts)->label('Solución (Opcional)');
                    ?>
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
document.addEventListener('DOMContentLoaded', function () {

    // ── Selects buscables (cliente / sistema / servicio) ──
    (function initSearchableSelects() {

        function normalize(s) {
            return (s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
        }

        function initWrap(wrap, onSelect) {
            const input    = wrap.querySelector('.ss-input');
            const dropdown = wrap.querySelector('.ss-dropdown');
            const select   = wrap.querySelector('select');
            if (!input || !dropdown || !select) return;

            const allOptions = Array.from(select.options).filter(o => o.value !== '');
            let _guardFocus  = false;

            function render(items) {
                if (!items.length) {
                    const empty = document.createElement('div');
                    empty.className   = 'ss-empty';
                    empty.textContent = 'Sin resultados';
                    dropdown.replaceChildren(empty);
                } else {
                    const q    = normalize(input.value);
                    const re   = q ? new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi') : null;
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
                            if (onSelect) onSelect(select);
                        });
                        frag.appendChild(el);
                    });
                    dropdown.replaceChildren(frag);
                }
                dropdown.style.display = 'block';
            }

            function filterAndShow() {
                const q        = normalize(input.value);
                const filtered = q ? allOptions.filter(o => normalize(o.text).includes(q)) : allOptions;
                render(filtered.slice(0, 12));
                if (!_guardFocus && document.activeElement !== input) {
                    _guardFocus = true;
                    const ss = input.selectionStart ?? input.value.length;
                    const se = input.selectionEnd   ?? input.value.length;
                    input.focus();
                    try { input.setSelectionRange(ss, se); } catch (_) {}
                    _guardFocus = false;
                }
            }

            input.addEventListener('focus',   () => { if (!_guardFocus) filterAndShow(); });
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
                        if (opt) {
                            input.value  = opt.text;
                            select.value = opt.value;
                            dropdown.style.display = 'none';
                            if (onSelect) onSelect(select);
                        }
                    }
                } else if (e.key === 'Escape') {
                    dropdown.style.display = 'none';
                }
            });
        }

        function onClienteSelect(sel) {
            const opt      = sel.options[sel.selectedIndex];
            const prioridad = opt ? opt.getAttribute('data-prioridad') : null;
            if (prioridad) {
                const prioridadSelect = document.querySelector('#tickets-prioridad');
                if (prioridadSelect) prioridadSelect.value = prioridad.toUpperCase();
            }
        }

        document.querySelectorAll('.ss-wrap').forEach(wrap => {
            const sel = wrap.querySelector('select');
            if (!sel) return;
            const cb = sel.id === 'tickets-cliente_id' ? onClienteSelect : null;
            initWrap(wrap, cb);
        });
    })();

});
</script>