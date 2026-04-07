<?php

use app\models\DevLog;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var DevLog[] $logs */
/** @var int $total */
/** @var int $page */
/** @var int $totalPages */
/** @var int $perPage */
/** @var string $filtroTipo */
/** @var string $filtroModulo */
/** @var string $filtroUsuario */
/** @var string $filtroFecha */
/** @var string $filtroFechaFin */
/** @var string $filtroBuscar */
/** @var array $statsHoy */
/** @var int $usuariosActivos */
/** @var array $tiposDisponibles */
/** @var array $modulosDisponibles */

$this->title = 'Registro de Actividad';

$tipoColors = DevLog::tipoColors();
$tipoIcons  = DevLog::tipoIcons();
$tipoLabels = DevLog::tipoLabels();
$totalHoy   = array_sum($statsHoy);

$detalleUrl = Url::to(['detalle']);
$colorsJson = json_encode(DevLog::tipoColors());
?>

<style>
/* ── Header igual al resto del proyecto ── */
.log-card-header {
    background: linear-gradient(135deg, #A0BAA5 0%, #8BA590 100%);
    color: #fff;
    padding: 1rem 1.25rem;
    border-bottom: none;
    border-radius: 8px 8px 0 0;
}
.log-card-header h4 { font-size: 1.2rem; font-weight: 600; margin: 0; }
.btn-header-action {
    background: #fff;
    color: #5a7d62;
    border: none;
    font-weight: 600;
    border-radius: 8px;
    padding: .35rem .85rem;
    font-size: .83rem;
    transition: background .2s;
}
.btn-header-action:hover { background: #e8f0e9; color: #3d5e44; }

/* ── Stat cards ── */
.stat-mini {
    border-radius: 10px;
    padding: .85rem 1rem;
    border: 1px solid #dce8de;
    background: #fff;
    display: flex; align-items: center; gap: .75rem;
    transition: box-shadow .15s;
}
.stat-mini:hover { box-shadow: 0 3px 10px rgba(0,0,0,.07); }
.stat-mini .s-icon {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}
.stat-mini .s-val { font-size: 1.4rem; font-weight: 800; line-height: 1; }
.stat-mini .s-lbl { font-size: .68rem; color: #6b7280; text-transform: uppercase; letter-spacing: .4px; }

/* ── Filtros ── */
.filter-section {
    background: #f7fbf8;
    border: 1px solid #dce8de;
    border-radius: 8px;
    padding: .75rem 1rem;
    margin-bottom: 1rem;
}
.filter-section label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #6b7280; margin-bottom: 2px; display: block; }
.filter-section .form-control,
.filter-section .form-select { font-size: .83rem; border-color: #dce8de; border-radius: 6px; }
.filter-section .form-control:focus,
.filter-section .form-select:focus { border-color: #A0BAA5; box-shadow: 0 0 0 .15rem rgba(160,186,165,.25); }

/* ── Tabla ── */
.log-table th {
    font-size: .7rem; text-transform: uppercase; letter-spacing: .5px;
    color: #6b7280; background: #f7fbf8;
    border-bottom: 2px solid #dce8de;
    padding: .55rem .75rem; white-space: nowrap;
}
.log-table td { font-size: .82rem; padding: .45rem .75rem; vertical-align: middle; border-bottom: 1px solid #f3f4f6; }
.log-table tbody tr:hover td { background: #f7fbf8; }

/* ── Badges de tipo ── */
.tipo-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .2rem .55rem; border-radius: 50px;
    font-size: .7rem; font-weight: 700; color: #fff;
    white-space: nowrap;
}

/* ── Texto acción ── */
.accion-cell {
    max-width: 340px;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    font-size: .8rem; color: #374151;
    cursor: pointer;
    display: block;
}
.accion-cell:hover { color: #5a7d62; text-decoration: underline; }

/* ── Módulo chip ── */
.modulo-chip {
    font-size: .72rem; background: #e8f0e9; color: #5a7d62;
    padding: .15rem .45rem; border-radius: 4px; font-weight: 600;
}

/* ── Rol chip ── */
.rol-chip {
    font-size: .7rem; background: #f3f4f6; color: #374151;
    padding: .15rem .45rem; border-radius: 50px; font-weight: 600;
}

/* ── Botón ver detalle ── */
.btn-detalle {
    background: #f0f4f1; border: none; border-radius: 6px;
    color: #5a7d62; font-size: .75rem; padding: .2rem .5rem;
    transition: background .15s;
}
.btn-detalle:hover { background: #dce8de; }

/* ── Live dot ── */
.live-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #22c55e; display: inline-block;
    animation: blink 1.4s infinite;
}
@keyframes blink { 0%,100% { opacity:1; } 50% { opacity:.3; } }

/* ── Modal JSON ── */
.json-pre {
    background: #1e2a22;
    color: #a8d5b5;
    border-radius: 8px;
    padding: 1rem;
    font-size: .78rem;
    max-height: 380px;
    overflow-y: auto;
    font-family: 'Courier New', monospace;
    white-space: pre-wrap;
    word-break: break-all;
}
.detail-lbl { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #9ca3af; margin-bottom: 2px; }
.detail-val { font-size: .85rem; color: #1f2933; margin-bottom: .75rem; }

/* ── Responsive ── */
@media (max-width: 768px) {
    .col-ip, .col-ua, .col-rol { display: none; }
    .accion-cell { max-width: 180px; }
}
</style>

<div class="logs-index">
<div class="card shadow-sm border-0" style="border-radius:8px;">

    <!-- ═══ HEADER ══════════════════════════════════════════════════ -->
    <div class="log-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-journal-text fs-5"></i>
            <h4><?= Html::encode($this->title) ?></h4>
            <span style="background:rgba(255,255,255,.2); font-size:.7rem; font-weight:700; padding:.2rem .55rem; border-radius:4px; letter-spacing:.5px;">
                SOLO DESARROLLADORES
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="d-flex align-items-center gap-1" style="font-size:.75rem; opacity:.8;">
                <span class="live-dot"></span> En vivo
            </span>
            <button class="btn-header-action" onclick="toggleRefresh()" id="btnRefresh">
                <i class="bi bi-arrow-clockwise me-1"></i>Auto: OFF
            </button>
        </div>
    </div>

    <div class="card-body p-3">

        <!-- ═══ STATS ════════════════════════════════════════════════ -->
        <div class="row g-2 mb-3">
            <?php
            $statsConf = [
                ['lbl'=>'Eventos hoy',        'val'=>$totalHoy,                    'color'=>'#8BA590', 'bg'=>'#e8f0e9', 'icon'=>'bi-activity'],
                ['lbl'=>'Usuarios activos',   'val'=>$usuariosActivos,             'color'=>'#3b82f6', 'bg'=>'#eff6ff', 'icon'=>'bi-people-fill'],
                ['lbl'=>'Logins hoy',         'val'=>$statsHoy['login']    ?? 0,  'color'=>'#22c55e', 'bg'=>'#f0fdf4', 'icon'=>'bi-box-arrow-in-right'],
                ['lbl'=>'Creaciones hoy',     'val'=>$statsHoy['crear']    ?? 0,  'color'=>'#A0BAA5', 'bg'=>'#f7fbf8', 'icon'=>'bi-plus-circle-fill'],
                ['lbl'=>'Eliminaciones hoy',  'val'=>$statsHoy['eliminar'] ?? 0,  'color'=>'#ef4444', 'bg'=>'#fef2f2', 'icon'=>'bi-trash-fill'],
                ['lbl'=>'Errores / Fallos',   'val'=>$statsHoy['error']    ?? 0,  'color'=>'#f59e0b', 'bg'=>'#fffbeb', 'icon'=>'bi-exclamation-triangle-fill'],
            ];
            foreach ($statsConf as $s): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="stat-mini">
                        <div class="s-icon" style="background:<?= $s['bg'] ?>; color:<?= $s['color'] ?>;">
                            <i class="bi <?= $s['icon'] ?>"></i>
                        </div>
                        <div>
                            <div class="s-val" style="color:<?= $s['color'] ?>;"><?= number_format($s['val']) ?></div>
                            <div class="s-lbl"><?= $s['lbl'] ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ═══ FILTROS ══════════════════════════════════════════════ -->
        <div class="filter-section">
            <form method="get" action="">
                <div class="row g-2 align-items-end">
                    <div class="col-6 col-md-2">
                        <label>Tipo</label>
                        <select name="tipo" class="form-select form-select-sm">
                            <option value="">— Todos —</option>
                            <?php foreach ($tiposDisponibles as $k => $v): ?>
                                <option value="<?= $k ?>" <?= $filtroTipo === $k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label>Módulo</label>
                        <select name="modulo" class="form-select form-select-sm">
                            <option value="">— Todos —</option>
                            <?php foreach ($modulosDisponibles as $m): ?>
                                <option value="<?= Html::encode($m) ?>" <?= $filtroModulo === $m ? 'selected' : '' ?>>
                                    <?= Html::encode(ucfirst($m)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label>Desde</label>
                        <input type="date" name="fecha" class="form-control form-control-sm"
                               value="<?= Html::encode($filtroFecha) ?>">
                    </div>
                    <div class="col-6 col-md-2">
                        <label>Hasta</label>
                        <input type="date" name="fecha_fin" class="form-control form-control-sm"
                               value="<?= Html::encode($filtroFechaFin) ?>">
                    </div>
                    <div class="col-8 col-md-2">
                        <label>Buscar</label>
                        <input type="text" name="buscar" class="form-control form-control-sm"
                               placeholder="Acción, usuario, IP..."
                               value="<?= Html::encode($filtroBuscar) ?>">
                    </div>
                    <div class="col-2 col-md-1">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-sm w-100"
                                style="background:#8BA590; color:#fff; border-radius:6px; border:none;">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <div class="col-2 col-md-1">
                        <label>&nbsp;</label>
                        <?= Html::a('<i class="bi bi-x-lg"></i>', ['index'], [
                            'class' => 'btn btn-sm btn-outline-secondary w-100',
                            'style' => 'border-radius:6px;',
                            'title' => 'Limpiar filtros'
                        ]) ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- ═══ BARRA INFO + CONTROLES ═══════════════════════════════ -->
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
            <small class="text-muted">
                <strong><?= count($logs) ?></strong> de <strong><?= number_format($total) ?></strong> registros
                &nbsp;·&nbsp; Página <?= $page ?>/<?= $totalPages ?>
            </small>
            <div class="d-flex gap-2 align-items-center">
                <select class="form-select form-select-sm" style="width:auto; border-color:#dce8de;"
                        onchange="changePerPage(this.value)">
                    <?php foreach ([50,100,200,500] as $pp): ?>
                        <option value="<?= $pp ?>" <?= $perPage===$pp?'selected':''?>><?= $pp ?>/pág</option>
                    <?php endforeach; ?>
                </select>
                <form method="post" action="<?= Url::to(['limpiar']) ?>" id="formLimpiar" style="display:inline;">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                    <input type="hidden" name="dias" id="diasLimpiar" value="90">
                    <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:6px;"
                            onclick="confirmLimpiar()">
                        <i class="bi bi-trash me-1"></i>Limpiar
                    </button>
                </form>
            </div>
        </div>

        <!-- ═══ TABLA ════════════════════════════════════════════════ -->
        <div class="table-responsive" style="border-radius:8px; border:1px solid #dce8de;">
            <table class="table table-hover log-table mb-0">
                <thead>
                    <tr>
                        <th style="width:55px;">#ID</th>
                        <th>Tipo</th>
                        <th>Módulo</th>
                        <th>Acción</th>
                        <th>Usuario</th>
                        <th class="col-rol">Rol</th>
                        <th class="col-ip">IP</th>
                        <th>Fecha/Hora</th>
                        <th style="width:40px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bi bi-journal-x fs-3 d-block mb-2 text-muted"></i>
                                <span class="text-muted">Sin registros para los filtros seleccionados</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log):
                            $color = $tipoColors[$log->tipo] ?? '#6b7280';
                            $icon  = $tipoIcons[$log->tipo]  ?? 'bi-circle';
                            $label = $tipoLabels[$log->tipo] ?? $log->tipo;
                        ?>
                        <tr>
                            <td class="text-muted" style="font-size:.73rem; font-family:monospace;"><?= $log->id ?></td>
                            <td>
                                <span class="tipo-badge" style="background:<?= $color ?>;">
                                    <i class="bi <?= $icon ?>"></i><?= $label ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($log->modulo): ?>
                                    <span class="modulo-chip"><?= Html::encode(ucfirst($log->modulo)) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="accion-cell" onclick="verDetalle(<?= $log->id ?>)"
                                      title="<?= Html::encode($log->accion) ?>">
                                    <?= Html::encode($log->accion) ?>
                                </span>
                                <?php if ($log->modelo_id): ?>
                                    <span style="font-size:.68rem; color:#9ca3af;">#<?= $log->modelo_id ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:.82rem; font-weight:600; color:#1f2933;">
                                <?= Html::encode($log->usuario_nombre ?? 'Anónimo') ?>
                            </td>
                            <td class="col-rol">
                                <?php if ($log->usuario_rol): ?>
                                    <span class="rol-chip"><?= Html::encode($log->usuario_rol) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="col-ip" style="font-family:monospace; font-size:.75rem; color:#6b7280;">
                                <?= Html::encode($log->ip ?? '') ?>
                            </td>
                            <td style="font-size:.75rem; color:#6b7280; white-space:nowrap;">
                                <?= Html::encode($log->created_at) ?>
                            </td>
                            <td>
                                <button class="btn-detalle" onclick="verDetalle(<?= $log->id ?>)" title="Ver detalle">
                                    <i class="bi bi-braces"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- ═══ PAGINACIÓN ════════════════════════════════════════ -->
        <?php if ($totalPages > 1): ?>
        <nav class="mt-3">
            <ul class="pagination pagination-sm justify-content-center" style="--bs-pagination-active-bg:#8BA590; --bs-pagination-active-border-color:#8BA590;">
                <?php if ($page > 1): ?>
                    <li class="page-item"><?= Html::a('«', array_merge($_GET, ['page'=>$page-1]), ['class'=>'page-link']) ?></li>
                <?php endif; ?>
                <?php for ($p=max(1,$page-3); $p<=min($totalPages,$page+3); $p++): ?>
                    <li class="page-item <?= $p===$page?'active':'' ?>">
                        <?= Html::a($p, array_merge($_GET, ['page'=>$p]), ['class'=>'page-link']) ?>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><?= Html::a('»', array_merge($_GET, ['page'=>$page+1]), ['class'=>'page-link']) ?></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

    </div><!-- /card-body -->
</div><!-- /card -->
</div><!-- /logs-index -->

<!-- ═══ MODAL DETALLE ═══════════════════════════════════════════════ -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0" style="border-radius:10px;">
            <div class="modal-header py-2 text-white" style="background:linear-gradient(135deg,#A0BAA5,#8BA590);border-radius:10px 10px 0 0;border:none;">
                <h6 class="modal-title mb-0">
                    <i class="bi bi-braces me-2"></i>Detalle — Log #<span id="modalLogId"></span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3" id="modalDetalleBody">
                <div class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <span class="ms-2">Cargando...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let refreshTimer = null;
const detalleUrl = '<?= Url::to(['detalle']) ?>';
const colorsJson = <?= json_encode(DevLog::tipoColors()) ?>;

window.verDetalle = function(id) {
    document.getElementById('modalLogId').textContent = id;
    document.getElementById('modalDetalleBody').innerHTML =
        '<div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm"></div><span class="ms-2">Cargando...</span></div>';
    new bootstrap.Modal(document.getElementById('modalDetalle')).show();

    fetch(detalleUrl + '?id=' + id)
        .then(r => r.json())
        .then(data => {
            const c = colorsJson[data.tipo] || '#6b7280';

            document.getElementById('modalDetalleBody').innerHTML = `
                <div class='row g-2 mb-3'>
                    <div class='col-6 col-md-3'>
                        <div class='detail-lbl'>Tipo</div>
                        <div class='detail-val'>
                            <span class='tipo-badge' style='background:${c}'>${data.tipo_label}</span>
                        </div>
                    </div>
                    <div class='col-6 col-md-3'>
                        <div class='detail-lbl'>Módulo</div>
                        <div class='detail-val'>${data.modulo || '—'}</div>
                    </div>
                    <div class='col-6 col-md-3'>
                        <div class='detail-lbl'>Modelo / ID</div>
                        <div class='detail-val'>${data.modelo || '—'}${data.modelo_id ? ' #' + data.modelo_id : ''}</div>
                    </div>
                    <div class='col-6 col-md-3'>
                        <div class='detail-lbl'>Fecha/Hora</div>
                        <div class='detail-val'>${data.created_at}</div>
                    </div>
                    <div class='col-6 col-md-3'>
                        <div class='detail-lbl'>Usuario</div>
                        <div class='detail-val' style='font-weight:600'>${data.usuario_nombre}</div>
                    </div>
                    <div class='col-6 col-md-3'>
                        <div class='detail-lbl'>Rol</div>
                        <div class='detail-val'><span class='rol-chip'>${data.usuario_rol}</span></div>
                    </div>
                    <div class='col-6 col-md-3'>
                        <div class='detail-lbl'>IP</div>
                        <div class='detail-val'><code>${data.ip}</code></div>
                    </div>
                    <div class='col-12'>
                        <div class='detail-lbl'>User-Agent</div>
                        <div class='detail-val' style='font-size:.78rem;color:#6b7280;'>${data.user_agent}</div>
                    </div>
                    <div class='col-12'>
                        <div class='detail-lbl'>Acción completa</div>
                        <div style='background:#f7fbf8;padding:.5rem .75rem;border-radius:6px;border:1px solid #dce8de;font-size:.85rem;margin-top:2px;'>
                            ${data.accion}
                        </div>
                    </div>
                    <div class='col-12'>
                        <div class='detail-lbl mt-2'>Datos del evento (JSON)</div>
                        <pre class='json-pre mt-1'>${JSON.stringify(data.datos, null, 2)}</pre>
                    </div>
                </div>
            `;
        })
        .catch(err => {
            document.getElementById('modalDetalleBody').innerHTML =
                '<div class="text-danger p-3">Error al cargar: ' + err + '</div>';
        });
};

window.toggleRefresh = function() {
    const btn = document.getElementById('btnRefresh');
    if (refreshTimer) {
        clearInterval(refreshTimer);
        refreshTimer = null;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Auto: OFF';
        btn.style.background = '#fff';
    } else {
        refreshTimer = setInterval(() => location.reload(), 15000);
        btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Auto: ON (15s)';
        btn.style.background = '#e8f0e9';
    }
};

window.changePerPage = function(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
};

window.confirmLimpiar = function() {
    Swal.fire({
        title: '¿Limpiar logs antiguos?',
        html: 'Eliminar logs con más de <b id="sDias">90</b> días.<br>' +
              '<input type="range" min="7" max="365" value="90" class="form-range mt-2" ' +
              'oninput="document.getElementById(\'sDias\').textContent=this.value" id="rangeD">',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Limpiar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => document.getElementById('rangeD').value
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('diasLimpiar').value = r.value;
            document.getElementById('formLimpiar').submit();
        }
    });
};
</script>
