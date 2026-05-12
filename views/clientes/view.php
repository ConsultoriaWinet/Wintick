<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Clientes $model */

$this->title = "Cliente: {$model->Nombre}";

$badge = function ($texto, $color = '#6b7280') {
    return "<span style='display:inline-flex;align-items:center;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;background:{$color}22;color:{$color};'>"
        . Html::encode($texto) . "</span>";
};

$prioridadColor = ['Alta' => '#ef4444', 'Media' => '#f59e0b', 'Baja' => '#10b981'];
$criticidadColor = ['Urgente' => '#ef4444', 'Media' => '#f59e0b', 'Baja' => '#10b981'];
$pColor = $prioridadColor[$model->Prioridad] ?? '#6b7280';
$cColor = $criticidadColor[$model->Criticidad] ?? '#6b7280';
?>

<style>
body { padding-top: 0; }
.cv-card {
    background: var(--surface, #fff);
    border: 1px solid var(--border, #e5e7eb);
    border-radius: 14px;
    margin-bottom: 16px;
    overflow: hidden;
}
.cv-card-head {
    padding: 12px 18px;
    border-bottom: 1px solid var(--border, #e5e7eb);
    font-size: 12px;
    font-weight: 700;
    color: var(--text-2, #374151);
    display: flex;
    align-items: center;
    gap: 7px;
    background: var(--surface-2, #f9fafb);
}
.cv-card-head i { color: var(--text-3, #9ca3af); font-size: 12px; }
.cv-body { padding: 16px 18px; }
.cv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.cv-field label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--text-3, #9ca3af);
    margin-bottom: 3px;
}
.cv-field .cv-val {
    font-size: 13px;
    color: var(--text, #111827);
    word-break: break-word;
}
.cv-field .cv-val.mono { font-family: monospace; }
.cv-field .cv-val.empty { color: var(--text-3, #9ca3af); font-style: italic; }
.multi-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 6px; }
.multi-list li {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--text, #111827);
}
.multi-list .ml-label {
    font-size: 10px;
    font-weight: 700;
    color: var(--text-3, #9ca3af);
    min-width: 90px;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.multi-list .ml-val { word-break: break-all; }
.multi-empty { font-size: 12px; color: var(--text-3, #9ca3af); font-style: italic; }
</style>

<div style="max-width:860px;margin:0 auto;padding:20px 16px;">

    <!-- Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:42px;height:42px;border-radius:11px;background:var(--accent-light,#eff6ff);display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-building" style="color:var(--accent,#3b82f6);font-size:16px;"></i>
            </div>
            <div>
                <div style="font-size:18px;font-weight:700;color:var(--text,#111827);"><?= Html::encode($model->Nombre) ?></div>
                <?php if ($model->Razon_social): ?>
                    <div style="font-size:12px;color:var(--text-3,#9ca3af);"><?= Html::encode($model->Razon_social) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <?= $badge($model->Prioridad ?: '—', $pColor) ?>
            <?= $badge($model->Criticidad ?: '—', $cColor) ?>
            <?= $badge($model->Estado ? 'Activo' : 'Inactivo', $model->Estado ? '#10b981' : '#6b7280') ?>
            <?= Html::a('<i class="fas fa-edit"></i> Editar', ['update', 'id' => $model->id],
                ['style'=>'display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;background:var(--accent,#3b82f6);color:#fff;text-decoration:none;']) ?>
            <?= Html::a('<i class="fas fa-arrow-left"></i> Volver', ['index'],
                ['style'=>'display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:500;background:var(--surface,#fff);color:var(--text-2,#374151);text-decoration:none;border:1px solid var(--border,#e5e7eb);']) ?>
        </div>
    </div>

    <!-- Info General -->
    <div class="cv-card">
        <div class="cv-card-head"><i class="fas fa-info-circle"></i> Información General</div>
        <div class="cv-body">
            <div class="cv-grid">
                <div class="cv-field">
                    <label>RFC</label>
                    <?php if ($model->RFC): ?>
                        <div class="cv-val mono"><?= Html::encode($model->RFC) ?></div>
                    <?php else: ?>
                        <div class="cv-val empty">Sin RFC</div>
                    <?php endif; ?>
                </div>
                <div class="cv-field">
                    <label>Contacto principal</label>
                    <div class="cv-val"><?= $model->Contacto_nombre ? Html::encode($model->Contacto_nombre) : '<span class="empty">—</span>' ?></div>
                </div>
                <div class="cv-field">
                    <label>Tipo de servicio</label>
                    <div class="cv-val"><?= Html::encode($model->Tipo_servicio === 'POLIZA' ? 'Póliza' : ($model->Tipo_servicio === 'EVENTO' ? 'Evento' : ($model->Tipo_servicio ?: '—'))) ?></div>
                </div>
                <div class="cv-field">
                    <label>Tiempo (hrs)</label>
                    <div class="cv-val"><?= Html::encode($model->Tiempo ?: '0') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contacto -->
    <div class="cv-card">
        <div class="cv-card-head"><i class="fas fa-address-book"></i> Contacto</div>
        <div class="cv-body" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">

            <!-- Teléfonos -->
            <div>
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-3,#9ca3af);margin-bottom:8px;">
                    <i class="fas fa-phone" style="margin-right:4px;"></i>Teléfonos
                </div>
                <?php $tels = $model->getTelefonos(); ?>
                <?php if ($tels): ?>
                    <ul class="multi-list">
                        <?php foreach ($tels as $t): ?>
                            <li>
                                <?php if ($t['label']): ?>
                                    <span class="ml-label"><?= Html::encode($t['label']) ?></span>
                                <?php endif; ?>
                                <span class="ml-val"><?= Html::encode($t['valor']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <span class="multi-empty">Sin teléfonos</span>
                <?php endif; ?>
            </div>

            <!-- WhatsApp -->
            <div>
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-3,#9ca3af);margin-bottom:8px;">
                    <i class="fab fa-whatsapp" style="margin-right:4px;color:#25d366;"></i>WhatsApp
                </div>
                <?php $was = $model->getWhatsapps(); ?>
                <?php if ($was): ?>
                    <ul class="multi-list">
                        <?php foreach ($was as $w): ?>
                            <li>
                                <?php if ($w['label']): ?>
                                    <span class="ml-label"><?= Html::encode($w['label']) ?></span>
                                <?php endif; ?>
                                <span class="ml-val"><?= Html::encode($w['valor']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <span class="multi-empty">Sin WhatsApp</span>
                <?php endif; ?>
            </div>

            <!-- Correos -->
            <div>
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-3,#9ca3af);margin-bottom:8px;">
                    <i class="fas fa-envelope" style="margin-right:4px;"></i>Correos
                </div>
                <?php $correos = $model->getCorreos(); ?>
                <?php if ($correos): ?>
                    <ul class="multi-list">
                        <?php foreach ($correos as $c): ?>
                            <li>
                                <?php if ($c['label']): ?>
                                    <span class="ml-label"><?= Html::encode($c['label']) ?></span>
                                <?php endif; ?>
                                <a class="ml-val" href="mailto:<?= Html::encode($c['valor']) ?>" style="color:var(--accent,#3b82f6);text-decoration:none;">
                                    <?= Html::encode($c['valor']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <span class="multi-empty">Sin correos</span>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>
