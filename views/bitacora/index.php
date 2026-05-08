<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'Generar Bitácora';

?>

<style>
/* ── Tabla bitácora ──────────────────────────────────── */
.bita-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.bita-table thead tr { border-bottom: 2px solid var(--border, #e5e7eb); }
.bita-table thead th {
    padding: 9px 12px; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: var(--text-3, #6b7280); background: var(--surface, #fff);
    white-space: nowrap;
}
.bita-table tbody tr { border-bottom: 1px solid var(--border, #e5e7eb); transition: background .1s; }
.bita-table tbody tr:hover { background: var(--surface-2, #f9fafb); }
.bita-table td { padding: 9px 12px; color: var(--text, #111827); vertical-align: middle; }
.bita-table .td-muted { color: var(--text-3, #6b7280); font-size: 12px; }
.bita-table .td-horas { font-weight: 700; color: var(--accent-dark, #1d4ed8); }
.bita-table .td-folio { font-weight: 600; font-family: monospace; }
.bita-table .td-detalle { max-width: 300px; white-space: normal; line-height: 1.4; }

/* Pagination */
.pagination { gap: 4px; flex-wrap: wrap; margin: 0; }
.pagination .page-item .page-link {
    border-radius: 6px !important; padding: 5px 11px;
    font-size: 12.5px; color: var(--text-2, #374151);
    border: 1px solid var(--border, #e5e7eb);
    background: var(--surface, #fff); transition: background .15s, color .15s;
}
.pagination .page-item.active .page-link {
    background: var(--accent, #3b82f6); border-color: var(--accent, #3b82f6);
    color: #fff; font-weight: 600;
}
.pagination .page-item:not(.active) .page-link:hover {
    background: var(--accent-light, #eff6ff); color: var(--accent-dark, #1d4ed8);
}
.pagination .page-item.disabled .page-link { color: var(--text-3, #9ca3af); background: var(--surface-2, #f9fafb); }

/* Inputs con focus theme */
.bita-input:focus { outline: none; border-color: var(--accent, #3b82f6) !important; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
</style>

<div style="margin-top:4px;">

    <!-- Page header -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
        <div>
            <h1 style="margin:0; font-size:20px; font-weight:700; color:var(--text,#111827);">Generar Bitácora</h1>
            <p style="margin:2px 0 0; font-size:12.5px; color:var(--text-3,#6b7280);">Reporte de actividades y tiempo por cliente</p>
        </div>
        <?= Html::a('<i class="fas fa-arrow-left"></i> Volver a Clientes', ['clientes/index'], [
            'style' => 'display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:500;background:var(--surface,#fff);color:var(--text-2,#374151);text-decoration:none;border:1px solid var(--border,#e5e7eb);',
        ]) ?>
    </div>

    <!-- Card filtros -->
    <div style="background:var(--surface,#fff);border:1px solid var(--border,#e5e7eb);border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05);margin-bottom:16px;">
        <div style="padding:14px 18px 12px;border-bottom:1px solid var(--border,#e5e7eb);">
            <span style="font-size:13px;font-weight:700;color:var(--text,#111827);">
                <i class="fas fa-filter" style="color:var(--text-3,#9ca3af);margin-right:6px;"></i>Filtros
            </span>
        </div>
        <div style="padding:18px 18px 14px;">
            <?php $form = ActiveForm::begin(['method' => 'get', 'options' => ['style' => '']]); ?>

            <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:12px;margin-bottom:14px;">

                <!-- CLIENTE -->
                <div>
                    <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-3,#6b7280);margin-bottom:5px;display:block;">Cliente</label>
                    <select name="Cliente_id" class="bita-input"
                        style="width:100%;padding:7px 10px;border:1px solid var(--border,#e5e7eb);border-radius:8px;font-size:13px;background:var(--surface,#fff);color:var(--text,#111827);">
                        <option value="">Todos los clientes</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente['id'] ?>" <?= Yii::$app->request->get('Cliente_id') == $cliente['id'] ? 'selected' : '' ?>>
                                <?= Html::encode($cliente['Nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- FOLIO INICIAL -->
                <div>
                    <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-3,#6b7280);margin-bottom:5px;display:block;">Folio Inicial</label>
                    <?= Html::textInput('folio_inicial', Yii::$app->request->get('folio_inicial'), [
                        'class'       => 'bita-input',
                        'placeholder' => 'Ej. 100',
                        'style'       => 'width:100%;padding:7px 10px;border:1px solid var(--border,#e5e7eb);border-radius:8px;font-size:13px;background:var(--surface,#fff);color:var(--text,#111827);',
                    ]) ?>
                </div>

                <!-- FOLIO FINAL -->
                <div>
                    <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-3,#6b7280);margin-bottom:5px;display:block;">Folio Final</label>
                    <?= Html::textInput('folio_final', Yii::$app->request->get('folio_final'), [
                        'class'       => 'bita-input',
                        'placeholder' => 'Ej. 200',
                        'style'       => 'width:100%;padding:7px 10px;border:1px solid var(--border,#e5e7eb);border-radius:8px;font-size:13px;background:var(--surface,#fff);color:var(--text,#111827);',
                    ]) ?>
                </div>

            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:16px;">

                <!-- FECHA INICIO -->
                <div>
                    <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-3,#6b7280);margin-bottom:5px;display:block;">Fecha Inicial</label>
                    <?= Html::input('date', 'fecha_inicio', Yii::$app->request->get('fecha_inicio'), [
                        'class' => 'bita-input',
                        'style' => 'width:100%;padding:7px 10px;border:1px solid var(--border,#e5e7eb);border-radius:8px;font-size:13px;background:var(--surface,#fff);color:var(--text,#111827);',
                    ]) ?>
                </div>

                <!-- FECHA FINAL -->
                <div>
                    <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-3,#6b7280);margin-bottom:5px;display:block;">Fecha Final</label>
                    <?= Html::input('date', 'fecha_fin', Yii::$app->request->get('fecha_fin'), [
                        'class' => 'bita-input',
                        'style' => 'width:100%;padding:7px 10px;border:1px solid var(--border,#e5e7eb);border-radius:8px;font-size:13px;background:var(--surface,#fff);color:var(--text,#111827);',
                    ]) ?>
                </div>

                <!-- MES -->
                <div>
                    <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-3,#6b7280);margin-bottom:5px;display:block;">Mes</label>
                    <?= Html::input('month', 'mes', Yii::$app->request->get('mes'), [
                        'class' => 'bita-input',
                        'style' => 'width:100%;padding:7px 10px;border:1px solid var(--border,#e5e7eb);border-radius:8px;font-size:13px;background:var(--surface,#fff);color:var(--text,#111827);',
                    ]) ?>
                </div>

            </div>

            <!-- Botones -->
            <div style="display:flex;justify-content:flex-end;gap:8px;">
                <?= Html::a('<i class="fas fa-times"></i> Limpiar', ['index'], [
                    'style' => 'display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:500;background:var(--surface,#fff);color:var(--text-2,#374151);text-decoration:none;border:1px solid var(--border,#e5e7eb);',
                ]) ?>
                <?= Html::submitButton('<i class="fas fa-search"></i> Generar Bitácora', [
                    'style' => 'display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;background:var(--accent,#3b82f6);color:#fff;border:none;cursor:pointer;',
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Card resultados -->
    <div style="background:var(--surface,#fff);border:1px solid var(--border,#e5e7eb);border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05);">

        <div style="padding:12px 18px;border-bottom:1px solid var(--border,#e5e7eb);display:flex;align-items:center;justify-content:space-between;gap:10px;">
            <span style="font-size:14px;font-weight:700;color:var(--text,#111827);">
                <?= Html::encode($tituloBitacora) ?>
            </span>
            <div style="display:flex;align-items:center;gap:10px;">
                <!-- Badge total horas -->
                <div style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;background:var(--accent-light,#eff6ff);color:var(--accent-dark,#1d4ed8);font-size:13px;font-weight:700;">
                    <i class="fas fa-clock"></i>
                    Total: <?= number_format($totalHoras, 2) ?> h
                </div>
                <!-- Exportar -->
                <a href="<?= yii\helpers\Url::to(array_merge(['bitacora/exportar-excel'], Yii::$app->request->queryParams)) ?>"
                   style="display:inline-flex;align-items:center;gap:6px;padding:6px 13px;border-radius:8px;font-size:13px;font-weight:600;background:#16a34a;color:#fff;text-decoration:none;">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="bita-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Horas</th>
                        <th>Folio</th>
                        <th>Sistema</th>
                        <th>Detalle</th>
                        <th>Consultor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tickets)): ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td class="td-muted"><?= Html::encode($ticket['HoraProgramada'] ?? '') ?></td>
                                <td class="td-horas"><?= Html::encode($ticket['TiempoEfectivo'] ?? '0') ?> h</td>
                                <td class="td-folio"><?= Html::encode($ticket['Folio'] ?? '') ?></td>
                                <td style="color:var(--text-2,#374151);"><?= Html::encode($ticket['SistemaNombre'] ?? '') ?></td>
                                <td class="td-detalle"><?= Html::encode($ticket['Descripcion'] ?? '') ?></td>
                                <td style="color:var(--text-2,#374151);"><?= Html::encode($ticket['UsuarioNombre'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center;padding:40px 20px;color:var(--text-3,#9ca3af);font-size:13px;">
                                <i class="fas fa-search" style="font-size:24px;margin-bottom:8px;display:block;opacity:.4;"></i>
                                No se encontraron resultados con los filtros seleccionados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div style="display:flex;justify-content:center;padding:12px 0 8px;">
            <?= LinkPager::widget([
                'pagination'           => $pages,
                'options'              => ['class' => 'pagination', 'style' => 'list-style:none;padding:0;'],
                'pageCssClass'         => 'page-item',
                'firstPageCssClass'    => 'page-item',
                'lastPageCssClass'     => 'page-item',
                'prevPageCssClass'     => 'page-item',
                'nextPageCssClass'     => 'page-item',
                'linkContainerOptions' => ['class' => 'pagination'],
            ]) ?>
        </div>

    </div>
</div>
