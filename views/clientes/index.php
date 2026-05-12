<?php

use app\models\Clientes;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ClientesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var int $pageSize */

$this->title = 'Clientes';
?>

<style>
/* ── Tabla ───────────────────────────────────────────── */
.clientes-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.clientes-table thead tr { border-bottom: 2px solid var(--border, #e5e7eb); }
.clientes-table thead th {
    padding: 9px 12px; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: var(--text-3, #6b7280); white-space: nowrap;
    background: var(--surface, #fff);
}
.clientes-table tbody tr {
    border-bottom: 1px solid var(--border, #e5e7eb);
    transition: background .1s; cursor: pointer;
}
.clientes-table tbody tr:hover { background: var(--surface-2, #f9fafb); }
.clientes-table td { padding: 10px 12px; color: var(--text, #111827); vertical-align: middle; }

/* ── Badges tiempo ───────────────────────────────────── */
.badge-tiempo {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11.5px; font-weight: 600; padding: 3px 9px;
    border-radius: 20px; white-space: nowrap;
}
.badge-tiempo.ok  { background: #F0FDF4; color: #16A34A; }
.badge-tiempo.sin { background: #FEF2F2; color: #DC2626; }

/* ── Pagination override ─────────────────────────────── */
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

/* ── Card mobile ─────────────────────────────────────── */
.card-mobile-cli {
    background: var(--surface, #fff);
    border: 1px solid var(--border, #e5e7eb);
    border-left: 3px solid var(--accent, #3b82f6);
    border-radius: 10px; padding: 14px 14px 12px;
    margin-bottom: 10px; cursor: pointer;
    transition: box-shadow .15s;
}
.card-mobile-cli:hover { box-shadow: 0 2px 10px rgba(0,0,0,.08); }
.card-mobile-cli .cli-nombre { font-size: 14px; font-weight: 700; color: var(--text, #111827); margin-bottom: 8px; }
.card-mobile-cli .cli-field { font-size: 12px; color: var(--text-3, #6b7280); margin-bottom: 4px; }
.card-mobile-cli .cli-field span { color: var(--text-2, #374151); font-weight: 500; }

/* ── Historial modal table ───────────────────────────── */
#historial-tabla thead th { font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .04em; color: var(--text-3, #6b7280); background: var(--surface-2, #f9fafb); }
.historial-ticket-row:hover { background: var(--accent-light, #eff6ff) !important; }
</style>

<div class="clientes-index">

    <!-- Header page -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; margin-top:4px;">
        <div>
            <h1 style="margin:0; font-size:20px; font-weight:700; color:var(--text,#111827);">Clientes</h1>
            <p style="margin:2px 0 0; font-size:12.5px; color:var(--text-3,#6b7280);">Gestión de clientes y tiempo disponible</p>
        </div>
        <div style="display:flex; gap:8px;">
            <?= Html::a('<i class="fas fa-plus"></i> Crear Cliente', ['create'], [
                'style' => 'display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:600;background:var(--accent,#3b82f6);color:#fff;text-decoration:none;border:none;',
            ]) ?>
            <?= Html::a('<i class="fas fa-book"></i> Generar Bitácora', ['bitacora/index'], [
                'style' => 'display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:600;background:var(--surface,#fff);color:var(--text-2,#374151);text-decoration:none;border:1px solid var(--border,#e5e7eb);',
            ]) ?>
        </div>
    </div>

    <!-- Card principal -->
    <div style="background:var(--surface,#fff); border:1px solid var(--border,#e5e7eb); border-radius:12px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.05);">

        <!-- Barra de búsqueda -->
        <div style="padding:14px 16px; border-bottom:1px solid var(--border,#e5e7eb); background:var(--surface,#fff);">
            <form id="searchForm" method="get" action="">
                <input type="hidden" name="pageSize" id="pageSizeHidden" value="<?= $pageSize ?>">
                <div style="display:flex; gap:8px; align-items:center;">
                    <div style="position:relative; flex:1;">
                        <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-3,#9ca3af);font-size:12px;"></i>
                        <input type="text" id="universalSearch" name="ClientesSearch[q]"
                            style="width:100%;padding:7px 10px 7px 30px;border:1px solid var(--border,#e5e7eb);border-radius:8px;font-size:13px;background:var(--surface-2,#f9fafb);color:var(--text,#111827);outline:none;"
                            placeholder="Buscar en todos los clientes..."
                            value="" autocomplete="off">
                        <button type="button" id="clearSearchBtn"
                            onclick="clearCliSearch()"
                            style="display:none;position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-3,#9ca3af);font-size:13px;padding:0;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;white-space:nowrap;">
                        <span style="font-size:12px;color:var(--text-3,#6b7280);">Mostrar</span>
                        <select id="pageSizeSelect"
                            style="padding:6px 8px;border:1px solid var(--border,#e5e7eb);border-radius:7px;font-size:12.5px;background:var(--surface,#fff);color:var(--text,#111827);cursor:pointer;">
                            <?php foreach ([10, 20, 50, 100, 500] as $size): ?>
                                <option value="<?= $size ?>" <?= $size === $pageSize ? 'selected' : '' ?>><?= $size ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Resultados AJAX (visibles solo cuando hay búsqueda activa) -->
        <div id="ajax-search-results" style="display:none;"></div>

        <!-- Contenido normal (paginado) — se oculta durante búsqueda AJAX -->
        <div id="clientes-normal-content">

        <!-- Tabla desktop -->
        <div class="d-none d-md-block" style="overflow-x:auto;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout'       => '{items}{pager}',
                'rowOptions'   => function ($model) {
                    $search = strtolower(implode(' ', array_filter([
                        $model->Nombre,
                        $model->Razon_social,
                        $model->RFC,
                        $model->getPrimerCorreo(),
                    ])));
                    return [
                        'onclick'     => "openModal({$model->id})",
                        'data-search' => $search,
                    ];
                },
                'tableOptions' => ['class' => 'clientes-table'],
                'headerRowOptions' => [],
                'columns' => [
                    [
                        'class'          => 'yii\grid\SerialColumn',
                        'headerOptions'  => ['style' => 'width:40px;'],
                    ],
                    [
                        'attribute'     => 'id',
                        'headerOptions' => ['style' => 'width:55px;'],
                        'contentOptions'=> ['style' => 'color:var(--text-3,#6b7280);font-size:12px;'],
                    ],
                    [
                        'attribute' => 'Nombre',
                        'value'     => function ($m) { return $m->Nombre; },
                        'contentOptions' => ['style' => 'font-weight:600;'],
                    ],
                    [
                        'attribute' => 'Razon_social',
                        'label'     => 'Razón Social',
                        'contentOptions' => ['style' => 'max-width:160px;white-space:normal;color:var(--text-2,#374151);'],
                    ],
                    [
                        'attribute'      => 'Tiempo',
                        'label'          => 'Tiempo Restante',
                        'format'         => 'raw',
                        'headerOptions'  => ['style' => 'text-align:center;'],
                        'contentOptions' => ['style' => 'text-align:center;'],
                        'value'          => function ($m) {
                            $n = floatval($m->Tiempo);
                            $cls = $n <= 0 ? 'sin' : 'ok';
                            $txt = $n <= 0 ? Html::encode($m->Tiempo) . ' SIN HORAS' : Html::encode($m->Tiempo) . ' h';
                            return "<span class='badge-tiempo {$cls}'>{$txt}</span>";
                        },
                    ],
                    [
                        'attribute'     => 'RFC',
                        'contentOptions'=> ['style' => 'font-family:monospace;font-size:12px;color:var(--text-2,#374151);'],
                    ],
                    [
                        'attribute'      => 'Correo',
                        'label'          => 'Correo',
                        'format'         => 'raw',
                        'headerOptions'  => ['class' => 'col-correo'],
                        'contentOptions' => ['class' => 'col-correo'],
                        'value'          => function ($m) {
                            $correo = $m->getPrimerCorreo();
                            if (!$correo) return '<span style="color:var(--text-3,#9ca3af);font-size:12px;">—</span>';
                            return Html::a(Html::encode($correo), '#', [
                                'onclick' => "copyToClipboard(event, '" . Html::encode($correo) . "')",
                                'style'   => 'color:var(--accent,#3b82f6);text-decoration:none;',
                            ]);
                        },
                    ],
                ],
                'pager' => [
                    'options'           => ['style' => 'display:flex;justify-content:center;padding:12px 0 6px;list-style:none;'],
                    'linkOptions'       => [],
                    'pageCssClass'      => 'page-item',
                    'firstPageCssClass' => 'page-item',
                    'lastPageCssClass'  => 'page-item',
                    'prevPageCssClass'  => 'page-item',
                    'nextPageCssClass'  => 'page-item',
                    'linkContainerOptions' => ['class' => 'pagination'],
                ],
            ]); ?>
        </div>

        <!-- Cards mobile -->
        <div class="d-block d-md-none" style="padding:12px;">
            <?php foreach ($dataProvider->models as $model): ?>
                <?php $searchAttr = strtolower(implode(' ', array_filter([
                    $model->Nombre, $model->Razon_social, $model->RFC, $model->getPrimerCorreo()
                ]))); ?>
                <div class="card-mobile-cli" onclick="openModal(<?= $model->id ?>)" data-search="<?= Html::encode($searchAttr) ?>">
                    <div class="cli-nombre"><?= Html::encode($model->Nombre) ?></div>
                    <div class="cli-field">RFC: <span><?= Html::encode($model->RFC) ?></span></div>
                    <div class="cli-field">Razón Social: <span><?= Html::encode($model->Razon_social) ?></span></div>
                    <div class="cli-field">Correo:
                        <?php $c = $model->getPrimerCorreo(); ?>
                        <span><?= $c ? Html::a(Html::encode($c), '#', [
                            'onclick' => "copyToClipboard(event, '" . Html::encode($c) . "')",
                            'style'   => 'color:var(--accent,#3b82f6);',
                        ]) : '—' ?></span>
                    </div>
                    <div style="margin-top:8px;">
                        <?php $n = floatval($model->Tiempo); ?>
                        <span class="badge-tiempo <?= $n <= 0 ? 'sin' : 'ok' ?>">
                            <?= Html::encode($model->Tiempo) ?><?= $n <= 0 ? ' SIN HORAS' : ' h' ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        </div><!-- /#clientes-normal-content -->
    </div>
</div>

<!-- Modal principal del cliente -->
<div id="modal-clientes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border:1px solid var(--border,#e5e7eb);border-radius:14px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,.12);">
            <div class="modal-header py-2 px-3 border-bottom-0 bg-transparent">
                <button id="btn-historial-cliente" class="btn btn-sm d-none"
                    style="font-size:12px;gap:5px;display:inline-flex!important;align-items:center;padding:5px 12px;border-radius:7px;border:1px solid var(--border,#e5e7eb);background:var(--surface-2,#f9fafb);color:var(--text-2,#374151);"
                    title="Historial de tickets del cliente">
                    <i class="fas fa-history"></i> Historial
                </button>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body pt-0"></div>
        </div>
    </div>
</div>

<!-- Modal historial tickets -->
<div id="modal-historial" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border:1px solid var(--border,#e5e7eb);border-radius:14px;overflow:hidden;">
            <div class="modal-header" style="background:var(--surface-2,#f9fafb);border-bottom:1px solid var(--border,#e5e7eb);padding:12px 16px;">
                <h6 class="modal-title fw-semibold" id="historial-titulo" style="font-size:14px;color:var(--text,#111827);">
                    <i class="fas fa-history me-1" style="color:var(--text-3,#9ca3af);"></i> Historial de tickets
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0">
                <div id="historial-loading" class="text-center py-5" style="display:none;color:var(--text-3,#6b7280);">
                    <div class="spinner-border spinner-border-sm me-2"></div> Cargando...
                </div>
                <div id="historial-vacio" class="text-center py-5" style="display:none;color:var(--text-3,#6b7280);font-size:13px;">
                    <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.4;"></i>
                    Sin tickets registrados para este cliente.
                </div>
                <table id="historial-tabla" class="table table-hover table-sm mb-0" style="display:none;font-size:13px;">
                    <thead>
                        <tr>
                            <th class="ps-3">Folio</th>
                            <th>Fecha</th>
                            <th>Tiempo desc.</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="historial-body"></tbody>
                </table>
            </div>
            <div class="modal-footer py-2" style="font-size:11px;color:var(--text-3,#9ca3af);border-top:1px solid var(--border,#e5e7eb);">
                <i class="fas fa-hand-pointer me-1"></i> Doble clic en un ticket para abrirlo
            </div>
        </div>
    </div>
</div>

<?php
$estadoClasses = json_encode([
    'ABIERTO'    => 'dp-e-abierto',
    'EN PROCESO' => 'dp-e-proceso',
    'PROGRAMADO' => 'dp-e-programado',
    'EN ESPERA'  => 'dp-e-espera',
    'CERRADO'    => 'dp-e-cerrado',
    'CANCELADO'  => 'dp-e-cancelado',
    'CONTPAQi'   => 'dp-e-contpaqi',
]);

$this->registerJs("
    // Abrir modal del cliente
    window.openModal = function(id) {
        $.get('" . Url::to(['clientes/update']) . "', {id: id}, function(data) {
            \$('#modal-clientes .modal-body').html(data);
            \$('#btn-historial-cliente').data('cliente-id', id).removeClass('d-none');
            \$('#modal-clientes').modal('show');
        });
    };

    // Historial
    \$('#btn-historial-cliente').on('click', function() {
        var clienteId = \$(this).data('cliente-id');
        \$('#historial-loading').show();
        \$('#historial-tabla').hide();
        \$('#historial-vacio').hide();
        \$('#historial-titulo').html('<i class=\"fas fa-history me-1\" style=\"color:var(--text-3,#9ca3af)\"></i> Cargando...');
        \$('#modal-historial').modal('show');

        \$.getJSON('" . Url::to(['clientes/historial']) . "', {id: clienteId}, function(resp) {
            \$('#historial-titulo').html(
                '<i class=\"fas fa-history me-1\" style=\"color:var(--text-3,#9ca3af)\"></i> Tickets — <span style=\"color:var(--accent,#3b82f6);\">' + resp.cliente + '</span>'
            );
            \$('#historial-loading').hide();

            if (!resp.tickets || resp.tickets.length === 0) {
                \$('#historial-vacio').show(); return;
            }

            var estadoMap = " . $estadoClasses . ";
            var estadoLabel = {
                'ABIERTO':'Abierto','EN PROCESO':'En proceso','PROGRAMADO':'Programado',
                'EN ESPERA':'En espera','CERRADO':'Cerrado','CANCELADO':'Cancelado','CONTPAQi':'CONTPAQi'
            };

            var rows = resp.tickets.map(function(t) {
                var fecha = t.Fecha_creacion ? t.Fecha_creacion.substring(0,10).split('-').reverse().join('/') : '-';
                var tiempo = t.TiempoEfectivo ? parseFloat(t.TiempoEfectivo).toFixed(2) + ' h' : '<span style=\"color:var(--text-3,#9ca3af)\">—</span>';
                var cls = estadoMap[t.Estado] || '';
                var lbl = estadoLabel[t.Estado] || t.Estado;
                return '<tr class=\"historial-ticket-row\" data-ticket-id=\"' + t.id + '\" style=\"cursor:pointer\">' +
                    '<td class=\"ps-3\" style=\"font-weight:600;\">' + t.Folio + '</td>' +
                    '<td style=\"color:var(--text-2,#374151)\">' + fecha + '</td>' +
                    '<td>' + tiempo + '</td>' +
                    '<td><span class=\"dp-estado ' + cls + '\">' + lbl + '</span></td>' +
                    '</tr>';
            }).join('');

            \$('#historial-body').html(rows);
            \$('#historial-tabla').show();

            \$('#historial-body').off('dblclick','.historial-ticket-row')
                .on('dblclick','.historial-ticket-row', function() {
                    window.location.href = '" . Url::to(['tickets/view']) . "?id=' + \$(this).data('ticket-id');
                });
        }).fail(function() {
            \$('#historial-loading').hide();
            \$('#historial-titulo').html('<i class=\"fas fa-exclamation-triangle\" style=\"color:#ef4444\"></i> Error al cargar');
        });
    });

    \$('#modal-clientes').on('hidden.bs.modal', function() {
        \$('#btn-historial-cliente').addClass('d-none');
    });

    window.copyToClipboard = function(event, text) {
        event.stopPropagation();
        navigator.clipboard.writeText(text).then(function() {
            Swal.fire({ toast:true, position:'top-end', showConfirmButton:false, timer:2500,
                icon:'success', title:'Correo copiado', text: text });
        });
    };

    // ── Búsqueda AJAX global (busca en TODOS los clientes, no solo la página actual) ──
    var searchXHR  = null;
    var searchTimer = null;
    var SEARCH_URL  = '<?= Url::to(['clientes/search']) ?>';

    function escHtml(s) {
        return (s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function clearCliSearch() {
        \$('#universalSearch').val('').focus();
        \$('#clearSearchBtn').hide();
        \$('#ajax-search-results').hide().empty();
        \$('#clientes-normal-content').show();
    }
    window.clearCliSearch = clearCliSearch;

    function renderAjaxResults(results, q) {
        var $container = \$('#ajax-search-results');
        if (!results.length) {
            $container.html(
                '<div style="text-align:center;padding:40px 20px;color:var(--text-3,#9ca3af);">' +
                '<i class="fas fa-search" style="font-size:24px;opacity:.3;display:block;margin-bottom:10px;"></i>' +
                'Sin resultados para <strong>' + escHtml(q) + '</strong></div>'
            );
            return;
        }

        // Tabla desktop
        var rows = results.map(function(c) {
            var cls = c.tiempoOk ? 'ok' : 'sin';
            var txt = c.tiempoOk ? escHtml(c.Tiempo) + ' h' : escHtml(c.Tiempo) + ' SIN HORAS';
            var correoCell = c.Correo
                ? '<a href="#" onclick="copyToClipboard(event,\'' + escHtml(c.Correo) + '\')" style="color:var(--accent,#3b82f6);text-decoration:none;">' + escHtml(c.Correo) + '</a>'
                : '<span style="color:var(--text-3,#9ca3af);">—</span>';
            return '<tr onclick="openModal(' + c.id + ')" style="cursor:pointer;border-bottom:1px solid var(--border,#e5e7eb);">' +
                '<td style="color:var(--text-3,#6b7280);font-size:12px;padding:10px 12px;">' + escHtml(String(c.id)) + '</td>' +
                '<td style="font-weight:600;padding:10px 12px;">' + escHtml(c.Nombre) + '</td>' +
                '<td style="max-width:160px;white-space:normal;color:var(--text-2,#374151);padding:10px 12px;">' + escHtml(c.Razon_social) + '</td>' +
                '<td style="text-align:center;padding:10px 12px;"><span class="badge-tiempo ' + cls + '">' + txt + '</span></td>' +
                '<td style="font-family:monospace;font-size:12px;color:var(--text-2,#374151);padding:10px 12px;">' + escHtml(c.RFC) + '</td>' +
                '<td style="padding:10px 12px;">' + correoCell + '</td>' +
                '</tr>';
        }).join('');

        // Cards mobile
        var cards = results.map(function(c) {
            var cls = c.tiempoOk ? 'ok' : 'sin';
            var txt = c.tiempoOk ? escHtml(c.Tiempo) + ' h' : escHtml(c.Tiempo) + ' SIN HORAS';
            var correoCard = c.Correo
                ? '<a href="#" onclick="copyToClipboard(event,\'' + escHtml(c.Correo) + '\')" style="color:var(--accent,#3b82f6);">' + escHtml(c.Correo) + '</a>'
                : '—';
            return '<div class="card-mobile-cli" onclick="openModal(' + c.id + ')">' +
                '<div class="cli-nombre">' + escHtml(c.Nombre) + '</div>' +
                '<div class="cli-field">RFC: <span>' + escHtml(c.RFC) + '</span></div>' +
                '<div class="cli-field">Razón Social: <span>' + escHtml(c.Razon_social) + '</span></div>' +
                '<div class="cli-field">Correo: <span>' + correoCard + '</span></div>' +
                '<div style="margin-top:8px;"><span class="badge-tiempo ' + cls + '">' + txt + '</span></div>' +
                '</div>';
        }).join('');

        $container.html(
            '<div style="padding:8px 16px 6px;font-size:12px;color:var(--text-3,#6b7280);border-bottom:1px solid var(--border,#e5e7eb);">' +
            '<i class="fas fa-search" style="margin-right:5px;"></i>' +
            '<strong>' + results.length + '</strong> resultado' + (results.length !== 1 ? 's' : '') + ' en todos los clientes' +
            '</div>' +
            '<div class="d-none d-md-block" style="overflow-x:auto;">' +
            '<table class="clientes-table"><thead><tr>' +
            '<th>ID</th><th>Nombre</th><th>Razón Social</th>' +
            '<th style="text-align:center;">Tiempo Restante</th><th>RFC</th><th>Correo</th>' +
            '</tr></thead><tbody>' + rows + '</tbody></table></div>' +
            '<div class="d-block d-md-none" style="padding:12px;">' + cards + '</div>'
        );
    }

    \$('#universalSearch').on('input', function() {
        var q = \$(this).val().trim();
        \$('#clearSearchBtn').toggle(q.length > 0);
        clearTimeout(searchTimer);
        if (searchXHR) { searchXHR.abort(); searchXHR = null; }

        if (!q) {
            \$('#ajax-search-results').hide().empty();
            \$('#clientes-normal-content').show();
            return;
        }

        // Mostrar spinner mientras carga
        \$('#ajax-search-results').show().html(
            '<div style="text-align:center;padding:30px;color:var(--text-3,#9ca3af);">' +
            '<div class="spinner-border spinner-border-sm me-2"></div>Buscando...</div>'
        );
        \$('#clientes-normal-content').hide();

        searchTimer = setTimeout(function() {
            searchXHR = \$.getJSON(SEARCH_URL, {q: q}, function(data) {
                renderAjaxResults(data.results, q);
            }).fail(function(xhr) {
                if (xhr.statusText !== 'abort') {
                    \$('#ajax-search-results').html(
                        '<div style="text-align:center;padding:30px;color:#ef4444;">Error al buscar. Intenta de nuevo.</div>'
                    );
                }
            });
        }, 300);
    });

    \$('#pageSizeSelect').on('change', function() {
        \$('#pageSizeHidden').val(\$(this).val());
        \$('#searchForm').submit();
    });
", \yii\web\View::POS_END);
?>
