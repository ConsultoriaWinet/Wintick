<?php

use app\models\Tickets;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use app\models\Clientes;

/** @var yii\web\View $this */
/** @var app\models\TicketsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */


$clientes = $clientes ?? [];
$sistemas = $sistemas ?? [];
$servicios = $servicios ?? [];
$Usuarios = $Usuarios ?? [];
$asignadoFiltro = $asignadoFiltro ?? '';

$this->title = 'Tickets';
$this->registerCssFile('@web/views/tickets/styles.css');

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/js/tickets-filters.js', ['position' => \yii\web\View::POS_END]);

$this->registerCss('
    /* ── Paginación ── */
    .pagination {
        gap: 4px;
        flex-wrap: wrap;
    }
    .pagination .page-item .page-link {
        min-width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px !important;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #4b5563;
        font-size: 13px;
        font-weight: 500;
        padding: 0 10px;
        transition: background .15s, color .15s, border-color .15s;
        text-decoration: none;
    }
    .pagination .page-item .page-link:hover {
        background: #f0f4f0;
        border-color: #8BA590;
        color: #4a7c59;
    }
    /* Página activa — verde suave */
    .pagination .page-item.active .page-link {
        background: #8BA590;
        border-color: #8BA590;
        color: #fff;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(139,165,144,.35);
    }
    /* Anterior / Siguiente deshabilitados */
    .pagination .page-item.disabled .page-link {
        background: #f9fafb;
        border-color: #e5e7eb;
        color: #d1d5db;
        cursor: not-allowed;
        pointer-events: none;
    }
    /* Selector de rango */
    .page-size-selector {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #6b7280;
    }
    .page-size-selector select {
        border: 1px solid #e5e7eb;
        border-radius: 7px;
        padding: 4px 8px;
        font-size: 12px;
        color: #374151;
        background: #fff;
        cursor: pointer;
    }
    .page-size-selector select:focus {
        outline: none;
        border-color: #8BA590;
    }
    /* Sin scroll horizontal */
    body { overflow-x: hidden; }
    .table-container { overflow-x: hidden; }
    
    .table-container > div:first-child {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    
    .compact-filter-menu {
        max-height: 600px;
        overflow-y: auto;
    }
    
    .filter-section-notice {
        background: #fffbeb;
        border-left: 3px solid #f59e0b;
        padding: 10px 12px;
        margin: 10px 0;
        font-size: 12px;
        color: #92400e;
        border-radius: 4px;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .compact-filter-group input[type="date"],
    .compact-filter-group input[type="month"] {
        max-width: 100%;
    }

    .mention-box{
    position:absolute; z-index:99999; background:#fff;
    border:1px solid #e5e7eb; border-radius:12px;
    box-shadow:0 12px 30px rgba(0,0,0,.15);
    width:360px; display:none; overflow:hidden;
    }
    .mention-item{
    padding:10px 12px; cursor:pointer; font-size:13px; line-height:1.2;
    border-bottom:1px solid #f1f5f9;
    }
    .mention-item:last-child{ border-bottom:none; }
    .mention-item:hover{ background:#f8fafc; }
    .mention-top{ display:flex; justify-content:space-between; gap:10px; }
    .mention-name{ font-weight:700; }
    .mention-email{ color:#64748b; font-size:12px; }



    

   /* ========================================
         ======================================== */



/* ========================================
   ✅ Mobile: Tabla -> Cards
   ======================================== */
@media (max-width: 768px){





  /* ====== TABLA A CARDS ====== */
  #ticketsTable{
    min-width: 0; /* ya no forzamos ancho */
    border-spacing: 0;
    padding:10%;
  }

  #ticketsTable thead{
    display: none; /* escondemos encabezado */
  }

  #ticketsTable tbody,
  #ticketsTable tr,
  #ticketsTable td{
    display: block;
    width: 100%;
  }

  /* cada fila como tarjeta */
  #ticketsTable tbody tr.data-row{
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    margin: 10px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    transform: none !important; /* quita tu scale hover en móvil */
  }

  /* fila nueva diferenciada */
  #ticketsTable tbody tr.new-row{
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1px solid #10b981;
    padding: 12px;
  }

  /* Cada celda como "label: value" */
  #ticketsTable tbody tr.data-row td{
    border: none;
    padding: 10px 0;
    display: flex;
    justify-content: space-between;
    gap: 12px;
    align-items: flex-start;
  }

  /* Generamos la etiqueta (según el orden de columnas) */
  #ticketsTable tbody tr.data-row td::before{
    content: "";
    font-weight: 800;
    color: #6b7280;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .4px;
    flex: 0 0 42%;
    padding-right: 8px;
  }

  /* valores */
  #ticketsTable tbody tr.data-row td{
    font-size: 13px;
  }
  #ticketsTable tbody tr.data-row td > *{
    flex: 1;
    max-width: 58%;

  }

  /* Etiquetas por columna (12 columnas) */
  #ticketsTable tbody tr.data-row td:nth-child(1)::before { content: "Folio"; }
  #ticketsTable tbody tr.data-row td:nth-child(2)::before { content: "Cliente"; }
  #ticketsTable tbody tr.data-row td:nth-child(3)::before { content: "Sistema"; }
  #ticketsTable tbody tr.data-row td:nth-child(4)::before { content: "Servicio"; }
  #ticketsTable tbody tr.data-row td:nth-child(5)::before { content: "Usuario reporta"; }
  #ticketsTable tbody tr.data-row td:nth-child(6)::before { content: "Asignado a"; }
  #ticketsTable tbody tr.data-row td:nth-child(7)::before { content: "Hora programada"; }
  #ticketsTable tbody tr.data-row td:nth-child(8)::before { content: "Hora inicio"; }
  #ticketsTable tbody tr.data-row td:nth-child(9)::before { content: "Descripción"; }
  #ticketsTable tbody tr.data-row td:nth-child(10)::before{ content: "Prioridad"; }
  #ticketsTable tbody tr.data-row td:nth-child(11)::before{ content: "Estado"; }
  #ticketsTable tbody tr.data-row td:nth-child(12)::before{ content: "Acción"; }

  /* Descripción: que se vea completa en móvil */
  #ticketsTable tbody tr.data-row td:nth-child(9){
    flex-direction: column;
  }
  #ticketsTable tbody tr.data-row td:nth-child(9)::before{
    flex: none;
    padding-right: 0;
    margin-bottom: 6px;
  }
  #ticketsTable tbody tr.data-row td:nth-child(9) > *{
    max-width: 100%;
    width: 100%;
  }
  .descripcion-cell{
    max-width: 100% !important;
    white-space: normal !important;
    overflow: visible !important;
    text-overflow: unset !important;
  }

  /* Acciones: botones en fila, wrap */
  #ticketsTable tbody tr.data-row td:nth-child(12){
    flex-direction: column;
    align-items: stretch;
  }
  #ticketsTable tbody tr.data-row td:nth-child(12)::before{
    flex: none;
    margin-bottom: 8px;
  }
  #ticketsTable tbody tr.data-row td:nth-child(12){
    gap: 10px;
  }
  #ticketsTable tbody tr.data-row td:nth-child(12) .btn{
    width: 100%;
    justify-content: center;
  }

  /* inputs/selects en fila nueva al 100% */
  #ticketsTable .form-control,
  #ticketsTable .form-select,
  #ticketsTable textarea{
    width: 100%;
  }

  /* Oculta fila sin resultados bien en cards */
  #noResultsRow td{
    display:block;
  }

  /* Paginador no se rompa */
  .pagination{
    flex-wrap: wrap;
    gap: 6px;
  }
}

/* ─────────────────────────────────────────────────────────────
   Flatpickr: reloj visible a la DERECHA del calendario
   ───────────────────────────────────────────────────────────── */
.flatpickr-calendar.hasTime:not(.noCalendar) {
    width: auto !important;
    display: grid;
    grid-template-columns: auto 88px;
    grid-template-rows: auto auto;
}
.flatpickr-calendar.hasTime:not(.noCalendar) .flatpickr-months {
    grid-column: 1 / -1;
}
.flatpickr-calendar.hasTime:not(.noCalendar) .flatpickr-innerContainer {
    grid-column: 1;
    grid-row: 2;
}
.flatpickr-calendar.hasTime:not(.noCalendar) .flatpickr-time {
    grid-column: 2;
    grid-row: 2;
    border-top: none;
    border-left: 1px solid #e2e8f0;
    flex-direction: column;
    height: auto;
    padding: 14px 6px;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.flatpickr-calendar.hasTime:not(.noCalendar) .flatpickr-time .numInputWrapper {
    width: 62px;
    text-align: center;
}
.flatpickr-calendar.hasTime:not(.noCalendar) .flatpickr-time-separator {
    display: none;
}
/* Input visible al usuario (altInput) */
.flatpickr-input.flatpickr-alt-input {
    background: #fff !important;
    cursor: text !important;
}
.flatpickr-input.flatpickr-alt-input::placeholder {
    color: #adb5bd;
    font-size: 12px;
}

');


// Obtener mes y año actual si no hay filtro
$mesActual = Yii::$app->request->get('mes', date('Y-m'));
?>


<div class="tickets-index">
    <!-- Header unificado: título + búsqueda + stats en una sola barra -->
    <div class="tickets-header">

        <!-- Fila 1: título · buscador · acciones -->
        <div class="th-row-main">
        <h1><i class="fas fa-headset"></i> <?= Html::encode($this->title) ?></h1>

        <!-- BUSCADOR UNIVERSAL + FILTRO AVANZADO COMPACTO -->
        <div class="search-filter-wrapper">
            <!-- Buscador Universal Instantáneo -->
            <div class="global-search-container">
                <i class="fas fa-search"></i>
                <input type="text" 
                       id="globalSearch" 
                       placeholder="Buscar por cualquier cosa..."
                       autocomplete="off">
                <button class="search-clear-btn" id="clearSearch" title="Limpiar búsqueda">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Filtro Avanzado Compacto (Dropdown) -->
            <div class="compact-filter-dropdown">
                <button class="compact-filter-btn" id="compactFilterBtn" type="button">
                    <i class="fas fa-sliders-h"></i>
                    Filtros
                </button>
                
                <div class="compact-filter-menu" id="compactFilterMenu" style="display: none;">
                    <form method="get" id="compactFilterForm">
                        <!-- Sección: Fechas -->
                        <div class="filter-section-title">
                            <i class="fas fa-calendar-alt"></i> <strong>FECHAS PROGRAMADAS</strong>
                        </div>
                        
                        <div class="filter-section-notice">
                             Selecciona UN: mes O rango de fechas (inicio-fin)
                        </div>
                        <div class="compact-filter-group">
                            <label>Mes</label>
                            <input type="month" name="mes" value="<?= $_GET['mes'] ?? '' ?>" placeholder="Seleccionar mes (opcional)">
                        </div>
                        <div class="compact-filter-group">
                            <label>Desde</label>
                            <input type="date" name="fecha_inicio" value="<?= $_GET['fecha_inicio'] ?? '' ?>" placeholder="Desde (opcional)">
                        </div>
                        <div class="compact-filter-group">
                            <label>Hasta</label>
                            <input type="date" name="fecha_fin" value="<?= $_GET['fecha_fin'] ?? '' ?>" placeholder="Hasta (opcional)">
                        </div>

                        <!-- Sección: Identidad -->
                        <div class="filter-section-title">
                            <i class="fas fa-id-card"></i> IDENTIDAD
                        </div>
                        <div class="compact-filter-group">
                            <label>Cliente</label>
                            <select name="Cliente_id">
                                <option value="">Todos</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= $cliente['id'] ?>" <?= ($_GET['Cliente_id'] ?? '') == $cliente['id'] ? 'selected' : '' ?>>
                                        <?= Html::encode($cliente['Nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="compact-filter-group">
                            <label>Sistema</label>
                            <select name="Sistema_id">
                                <option value="">Todos</option>
                                <?php foreach ($sistemas as $sistema): ?>
                                    <option value="<?= $sistema['id'] ?>" <?= ($_GET['Sistema_id'] ?? '') == $sistema['id'] ? 'selected' : '' ?>>
                                        <?= Html::encode($sistema['Nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="compact-filter-group">
                            <label>Servicio</label>
                            <select name="Servicio_id">
                                <option value="">Todos</option>
                                <?php foreach ($servicios as $servicio): ?>
                                    <option value="<?= $servicio['id'] ?>" <?= ($_GET['Servicio_id'] ?? '') == $servicio['id'] ? 'selected' : '' ?>>
                                        <?= Html::encode($servicio['Nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Sección: Estado -->
                        <div class="filter-section-title">
                            <i class="fas fa-flag"></i> ESTADO Y PRIORIDAD
                        </div>
                        <div class="compact-filter-group">
                            <label>Prioridad</label>
                            <select name="Prioridad">
                                <option value="">Todas</option>
                                <option value="BAJA" <?= ($_GET['Prioridad'] ?? '') == 'BAJA' ? 'selected' : '' ?>>Baja</option>
                                <option value="MEDIA" <?= ($_GET['Prioridad'] ?? '') == 'MEDIA' ? 'selected' : '' ?>>Media</option>
                                <option value="ALTA" <?= ($_GET['Prioridad'] ?? '') == 'ALTA' ? 'selected' : '' ?>>Alta</option>
                            </select>
                        </div>

                        <div class="compact-filter-group">
                            <label>Estado</label>
                            <select name="Estado">
                                <option value="">Todos</option>
                                <option value="ABIERTO" <?= ($_GET['Estado'] ?? '') == 'ABIERTO' ? 'selected' : '' ?>>Abierto</option>
                                <option value="PROGRAMADO" <?= ($_GET['Estado'] ?? '') == 'PROGRAMADO' ? 'selected' : '' ?>>Programado</option>
                                <option value="EN PROCESO" <?= ($_GET['Estado'] ?? '') == 'EN PROCESO' ? 'selected' : '' ?>>En Proceso</option>
                                <option value="CONTPAQi" <?= ($_GET['Estado'] ?? '') == 'CONTPAQi' ? 'selected' : '' ?>>CONTPAQi </option>
                                <option value="CERRADO" <?= ($_GET['Estado'] ?? '') == 'CERRADO' ? 'selected' : '' ?>>Cerrado</option>
                            </select>
                        </div>

                        <div class="compact-filter-group">
                            <label>Asignado A</label>
                            <?php
                            $asignadoFiltroView = $asignadoFiltro
                                ?? ($_GET['asignado_a'] ?? (Yii::$app->user->id ?? ''));
                            ?>

                            <select name="asignado_a">
                                <option value="">Todos</option>
                                <?php foreach ($Usuarios as $usuario): ?>
                                    <option value="<?= $usuario['id'] ?>" <?= ($asignadoFiltroView == $usuario['id']) ? 'selected' : '' ?>>
                                        <?= Html::encode($usuario['email']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Botones -->
                        <div class="compact-filter-actions">
                            <button type="submit" class="btn-apply-filter" name="apply">
                                <i class="fas fa-check"></i> Aplicar
                            </button>
                            <a href="<?= Url::to(['index']) ?>" class="btn-clear-filter" style="text-align: center; text-decoration: none; line-height: 32px;">
                                <i class="fas fa-redo"></i> Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="tickets-header-actions">
            <a href="<?= Url::to(array_merge(['tickets/exportar'], $_GET)) ?>"
               class="btn btn-outline-success btn-sm">
                <i class="fas fa-file-csv"></i> Exportar CSV
            </a>
        </div>
        </div><!-- /.th-row-main -->

        <!-- Fila 2: total · filtro activo · mostrando · página · refresh -->
        <div class="th-row-stats">
            <span class="th-stat-total">
                <i class="fas fa-headset" style="opacity:.45;font-size:11px;"></i>
                <strong id="totalTickets"><?= $dataProvider->getTotalCount() ?></strong> tickets
                <span id="filteredCount" style="display:none;margin-left:8px;color:#667eea;font-weight:600;"></span>
            </span>
            <?php if (!empty($_GET['mes'])):
                $mesesEs=[1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic'];
                $ts=strtotime($_GET['mes'].'-01');
            ?><span class="th-stat-badge"><i class="fas fa-calendar-alt"></i> <?= Html::encode($mesesEs[(int)date('n',$ts)].' '.date('Y',$ts)) ?></span><?php endif; ?>
            <span class="th-stat-showing">Mostrando <?= count($dataProvider->getModels()) ?> de <?= $dataProvider->getTotalCount() ?></span>
            <?php $perPageActual=(int)Yii::$app->request->get('per-page',20); ?>
            <div class="th-page-size">
                <label for="perPageSelect">Por página:</label>
                <select id="perPageSelect" onchange="cambiarPorPagina(this.value)">
                    <?php foreach([10,20,30,50,100] as $op): ?>
                        <option value="<?=$op?>" <?=$perPageActual===$op?'selected':''?>><?=$op?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="th-refresh-btn" id="refreshPage" title="Actualizar" onclick="refreshPage()">
                <i class="fas fa-sync-alt" id="refreshIcon"></i>
            </button>
        </div><!-- /.th-row-stats -->

    </div><!-- /.tickets-header -->

    <!-- Tabla -->
    <div class="table-container">
        
        <table class="table table-hover table-sm" id="ticketsTable">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Asunto · Cliente</th>
                    <th>Sistema · Servicio</th>
                    <th>Prioridad</th>
                    <th>Asignado</th>
                    <th>Estado</th>
                    <th>Fechas</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- Fila para crear nuevo (FOLIO AUTOMÁTICO) MOVIDA ARRIBA -->
                <tr class="data-row new-row">
                    <td><input type="text" class="form-control form-control-sm folio" placeholder="Auto..." readonly style="background:#e9ecef;font-weight:bold;color:#10b981;min-width:80px"></td>
                    <td style="min-width:230px">
                        <!-- Searchable: cliente -->
                        <div class="ss-wrap mb-1">
                            <input type="text" class="form-control form-control-sm ss-input" placeholder="Buscar empresa/cliente...">
                            <div class="ss-dropdown"></div>
                            <select class="cliente" style="display:none">
                                <option value="">Seleccionar cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= $cliente['id'] ?>" data-prioridad="<?= $cliente['Prioridad'] ?>" data-tipo="<?= $cliente['Tipo_servicio'] ?>">
                                        <?= Html::encode($cliente['Nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="text" class="form-control form-control-sm usuario-reporta mb-1" placeholder="Nombre de quien reporta">
                        <textarea class="form-control form-control-sm descripcion" rows="1" placeholder="Descripción del problema" style="font-size:12px;resize:none;"></textarea>
                    </td>
                    <td style="min-width:170px">
                        <!-- Searchable: sistema -->
                        <div class="ss-wrap mb-1">
                            <input type="text" class="form-control form-control-sm ss-input" placeholder="Buscar sistema...">
                            <div class="ss-dropdown"></div>
                            <select class="sistema" style="display:none">
                                <option value="">Sistema</option>
                                <?php foreach ($sistemas as $sistema): ?>
                                    <option value="<?= $sistema['id'] ?>"><?= Html::encode($sistema['Nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Searchable: servicio -->
                        <div class="ss-wrap">
                            <input type="text" class="form-control form-control-sm ss-input" placeholder="Buscar servicio...">
                            <div class="ss-dropdown"></div>
                            <select class="servicio" style="display:none">
                                <option value="">Servicio</option>
                                <?php foreach ($servicios as $servicio): ?>
                                    <option value="<?= $servicio['id'] ?>"><?= Html::encode($servicio['Nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </td>
                    <td><select class="form-select form-select-sm prioridad" style="min-width:90px">
                        <option value="">Prioridad</option>
                        <option value="BAJA">Baja</option>
                        <option value="MEDIA">Media</option>
                        <option value="ALTA">Alta</option>
                    </select></td>
                    <td><select class="form-select form-select-sm asignado-a" style="min-width:120px">
                        <option value="">Asignar a</option>
                        <?php foreach ($Usuarios as $usuario): ?>
                            <option value="<?= $usuario['id'] ?>" data-email="<?= Html::encode($usuario['email']) ?>"><?= Html::encode($usuario['Nombre']) ?></option>
                        <?php endforeach; ?>
                    </select></td>
                    <td><select class="form-select form-select-sm estado" style="min-width:110px">
                        <option value="ABIERTO" selected>Abierto</option>
                        <option value="PROGRAMADO">Programado</option>
                        <option value="EN PROCESO">En Proceso</option>
                        <option value="CONTPAQi">CONTPAQi</option>
                        <option value="CERRADO">Cerrado</option>
                    </select></td>
                    <td style="min-width:165px">
                        <div style="display:flex;flex-direction:column;gap:4px;">
                            <div style="display:flex;align-items:center;gap:4px;">
                                <span style="font-size:10px;color:#888;white-space:nowrap;min-width:60px">Reporte:</span>
                                <input type="datetime-local" class="form-control form-control-sm hora-programada" style="font-size:11px;padding:3px 5px">
                            </div>
                            <div style="display:flex;align-items:center;gap:4px;">
                                <span style="font-size:10px;color:#888;white-space:nowrap;min-width:60px">Inicio:</span>
                                <input type="datetime-local" class="form-control form-control-sm hora-inicio" style="font-size:11px;padding:3px 5px">
                            </div>
                        </div>
                    </td>
                    <td style="white-space:nowrap">
                        <button type="button" class="btn btn-sm btn-success saveRow" title="Guardar"><i class="fas fa-save"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deleteRow" title="Cancelar"><i class="fas fa-times"></i></button>
                    </td>
                </tr>

                <!-- Filas existentes de BD -->
                <?php 
                $tickets = $dataProvider->getModels();
                foreach ($tickets as $ticket): 
                ?>
                <tr class="data-row existing-row" 
                 data-ticket-id="<?= $ticket->id ?>"
                data-folio="<?= Html::encode($ticket->Folio) ?>"
                data-cliente="<?= Html::encode($ticket->cliente ? $ticket->cliente->Nombre : '-') ?>"
                data-criticidad="<?= Html::encode($ticket->cliente ? $ticket->cliente->Criticidad : '-') ?>"
                data-sistema="<?= Html::encode($ticket->sistema ? $ticket->sistema->Nombre : '-') ?>"
                data-servicio="<?= Html::encode($ticket->servicio ? $ticket->servicio->Nombre : '-') ?>"
                data-usuario-reporta="<?= Html::encode($ticket->Usuario_reporta) ?>"
                data-asignado-a="<?= Html::encode($ticket->usuarioAsignado ? $ticket->usuarioAsignado->email : '-') ?>"
                data-hora-programada="<?= Html::encode($ticket->HoraProgramada ? date('d/m/Y H:i', strtotime($ticket->HoraProgramada)) : '-') ?>"
                data-hora-inicio="<?= Html::encode($ticket->HoraInicio ? date('d/m/Y H:i', strtotime($ticket->HoraInicio)) : '-') ?>"
                data-prioridad="<?= Html::encode($ticket->Prioridad) ?>"
                data-estado="<?= Html::encode($ticket->Estado) ?>"
                data-descripcion="<?= Html::encode($ticket->Descripcion) ?>"
                data-hora-finalizo="<?= Html::encode($ticket->HoraFinalizo ? date('d/m/Y H:i', strtotime($ticket->HoraFinalizo)) : '-') ?>"
                data-hora-inicio-raw="<?= Html::encode($ticket->HoraInicio ? date('Y-m-d\TH:i', strtotime($ticket->HoraInicio)) : '') ?>"
                data-hora-programada-raw="<?= Html::encode($ticket->HoraProgramada ? date('Y-m-d\TH:i', strtotime($ticket->HoraProgramada)) : '') ?>"
                data-cliente-id="<?= (int)$ticket->Cliente_id ?>"
                data-sistema-id="<?= (int)$ticket->Sistema_id ?>"
                data-servicio-id="<?= (int)$ticket->Servicio_id ?>"
                data-asignado-id="<?= (int)$ticket->Asignado_a ?>"
                data-solucion="<?= Html::encode($ticket->Solucion ?: '') ?>"
                data-tiene-solucion="<?= $ticket->Solucion ? '1' : '0' ?>"
                data-tiempo-efectivo="<?= Html::encode($ticket->TiempoEfectivo ?: '-') ?>"
                data-id="<?= $ticket->id ?>" >
                    <?php
                    // Avatar del asignado
                    $asigNombre = $ticket->usuarioAsignado ? $ticket->usuarioAsignado->Nombre : '';
                    $asigColor  = $ticket->usuarioAsignado ? ($ticket->usuarioAsignado->color ?: '#6B7280') : '#6B7280';
                    $asigIni    = '';
                    if ($asigNombre) {
                        $parts   = preg_split('/\s+/', trim($asigNombre));
                        $asigIni = mb_strtoupper(mb_substr($parts[0], 0, 1, 'UTF-8'), 'UTF-8');
                        if (isset($parts[1])) $asigIni .= mb_strtoupper(mb_substr($parts[1], 0, 1, 'UTF-8'), 'UTF-8');
                    }
                    // Badges
                    $estadoBadge = match($ticket->Estado) {
                        'ABIERTO'    => 'tkt-estado-abierto',
                        'PROGRAMADO' => 'tkt-estado-programado',
                        'EN PROCESO' => 'tkt-estado-proceso',
                        'EN ESPERA'  => 'tkt-estado-espera',
                        'CONTPAQi'   => 'tkt-estado-contpaqi',
                        'CERRADO'    => 'tkt-estado-cerrado',
                        'CANCELADO'  => 'tkt-estado-cancelado',
                        default      => 'tkt-estado-default',
                    };
                    $prioBadge = match($ticket->Prioridad) {
                        'ALTA'  => 'tkt-prio-alta',
                        'MEDIA' => 'tkt-prio-media',
                        'BAJA'  => 'tkt-prio-baja',
                        default => 'tkt-prio-default',
                    };
                    $estadoLabel = match($ticket->Estado) {
                        'ABIERTO'    => 'Abierto',
                        'PROGRAMADO' => 'Programado',
                        'EN PROCESO' => 'En proceso',
                        'EN ESPERA'  => 'En espera',
                        'CONTPAQi'   => 'CONTPAQi',
                        'CERRADO'    => 'Cerrado',
                        'CANCELADO'  => 'Cancelado',
                        default      => Html::encode($ticket->Estado),
                    };
                    $comentarioCount = count($ticket->comentarios);
                    $descCorta = mb_strlen($ticket->Descripcion ?? '') > 50
                        ? mb_substr($ticket->Descripcion, 0, 50, 'UTF-8') . '…'
                        : ($ticket->Descripcion ?? '');
                    $fechaActualizado = $ticket->HoraInicio
                        ? date('d M', strtotime($ticket->HoraInicio))
                        : ($ticket->HoraProgramada ? date('d M', strtotime($ticket->HoraProgramada)) : '—');
                    ?>
                    <?php
                    $avUrl = null;
                    if ($ticket->usuarioAsignado && $ticket->usuarioAsignado->avatar && str_starts_with($ticket->usuarioAsignado->avatar, '/uploads/')) {
                        $avUrl = \yii\helpers\Url::to('@web' . $ticket->usuarioAsignado->avatar, true);
                    }
                    $fechaProg = $ticket->HoraProgramada ? Html::encode(date('d/m/Y H:i', strtotime($ticket->HoraProgramada))) : '—';
                    ?>
                    <td class="tkt-folio-cell"><?= Html::encode($ticket->Folio) ?></td>
                    <td class="tkt-asunto-cell">
                        <div class="tkt-asunto-title"><?= Html::encode($descCorta) ?></div>
                        <div class="tkt-asunto-sub">
                            <?= Html::encode($ticket->cliente ? $ticket->cliente->Nombre : '—') ?>
                            <?php if ($comentarioCount > 0): ?>
                                <span class="tkt-cmnt-badge badge-count-<?= $ticket->id ?>"><i class="fas fa-comment-dots"></i> <?= $comentarioCount ?></span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="tkt-sis-cell">
                        <div class="tkt-sis-name"><?= Html::encode($ticket->sistema ? $ticket->sistema->Nombre : '—') ?></div>
                        <div class="tkt-sis-svc"><?= Html::encode($ticket->servicio ? $ticket->servicio->Nombre : '—') ?></div>
                    </td>
                    <td><span class="tkt-prio <?= $prioBadge ?>"><?= Html::encode(ucfirst(strtolower($ticket->Prioridad ?: 'Media'))) ?></span></td>
                    <td class="tkt-asignado-cell">
                        <?php if ($ticket->usuarioAsignado): ?>
                        <div class="tkt-assignee">
                            <?php if ($avUrl): ?>
                                <img src="<?= Html::encode($avUrl) ?>" class="tkt-av" style="object-fit:cover;" alt="<?= Html::encode($asigIni) ?>">
                            <?php else: ?>
                                <div class="tkt-av" style="background:<?= Html::encode($asigColor) ?>"><?= Html::encode($asigIni ?: '?') ?></div>
                            <?php endif; ?>
                            <span class="tkt-av-name"><?= Html::encode($asigNombre) ?></span>
                        </div>
                        <?php else: ?><span class="tkt-empty">—</span><?php endif; ?>
                    </td>
                    <td>
                        <span class="tkt-estado <?= $estadoBadge ?> estado-clickeable" onclick="toggleEstadoSelect(this, <?= $ticket->id ?>)"><?= $estadoLabel ?></span>
                        <select class="form-select form-select-sm estado-select estado-<?= $ticket->id ?>" onchange="updateEstado(this, <?= $ticket->id ?>)" style="display:none;font-size:12px;margin-top:4px;">
                            <option value="ABIERTO"    <?= $ticket->Estado == 'ABIERTO'    ? 'selected' : '' ?>>Abierto</option>
                            <option value="PROGRAMADO" <?= $ticket->Estado == 'PROGRAMADO' ? 'selected' : '' ?>>Programado</option>
                            <option value="EN PROCESO" <?= $ticket->Estado == 'EN PROCESO' ? 'selected' : '' ?>>En Proceso</option>
                            <option value="CONTPAQi"   <?= $ticket->Estado == 'CONTPAQi'   ? 'selected' : '' ?>>CONTPAQi</option>
                            <option value="CERRADO"    <?= $ticket->Estado == 'CERRADO'    ? 'selected' : '' ?>>Cerrado</option>
                        </select>
                    </td>
                    <td class="tkt-fecha-cell">
                        <?php if ($ticket->HoraProgramada): ?>
                            <div class="tkt-fecha-row"><span class="tkt-fecha-label">Reporte</span><?= Html::encode(date('d/m/y H:i', strtotime($ticket->HoraProgramada))) ?></div>
                        <?php endif; ?>
                        <?php if ($ticket->HoraInicio): ?>
                            <div class="tkt-fecha-row"><span class="tkt-fecha-label">Inicio</span><?= Html::encode(date('d/m/y H:i', strtotime($ticket->HoraInicio))) ?></div>
                        <?php endif; ?>
                        <?php if (!$ticket->HoraProgramada && !$ticket->HoraInicio): ?>—<?php endif; ?>
                    </td>
                    <td style="white-space:nowrap;">
                        <button class="tkt-btn-del" title="Eliminar ticket"
                            onclick="confirmarEliminar(<?= $ticket->id ?>, '<?= Html::encode($ticket->Folio) ?>', <?= $ticket->Estado === 'CERRADO' && $ticket->Solucion ? 'true' : 'false' ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>

                <!-- Fila "Sin resultados" -->
                <tr class="no-results-row" id="noResultsRow">
                    <td colspan="8">
                        <i class="fas fa-search" style="font-size: 40px; opacity: 0.3; margin-bottom: 10px;"></i>
                        <div><strong>No se encontraron resultados</strong></div>
                        <small>Intenta con otros términos de búsqueda</small>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- ✅ PAGINADOR -->
        <nav aria-label="Paginación">
            <?= LinkPager::widget([
                'pagination'                    => $dataProvider->pagination,
                'options'                       => ['class' => 'pagination justify-content-center mt-3 mb-1'],
                'linkOptions'                   => ['class' => 'page-link'],
                'activePageCssClass'            => 'active',
                'disabledPageCssClass'          => 'disabled',
                'disabledListItemSubTagOptions' => ['tag' => 'span', 'class' => 'page-link'],
                'firstPageLabel'                => '«',
                'lastPageLabel'                 => '»',
                'prevPageLabel'                 => '‹',
                'nextPageLabel'                 => '›',
                'maxButtonCount'                => 7,
            ]) ?>
        </nav>
    </div>

    
</div>

<!-- ═══════════════════════════════════════════════
     TICKET DRAWER — panel lateral deslizante
     ═══════════════════════════════════════════════ -->
<style>
/* ── Drawer base ─────────────────────────────── */
.ticket-drawer {
    position: fixed;
    top: 0; right: 0;
    width: 460px;
    height: 100vh;
    z-index: 1040;
    pointer-events: none;
    display: flex;
    flex-direction: column;
}

.drawer-panel {
    position: absolute;
    top: 0; right: 0;
    width: 100%;
    height: 100%;
    background: var(--surface, #fff);
    border-left: 1px solid var(--border, #E8E2D2);
    box-shadow: -6px 0 32px rgba(0,0,0,0.10);
    display: flex;
    flex-direction: column;
    transform: translateX(100%);
    transition: transform 0.28s cubic-bezier(.4,0,.2,1);
    pointer-events: all;
    overflow: hidden;
}

.ticket-drawer.open .drawer-panel {
    transform: translateX(0);
}

/* ── Header ──────────────────────────────────── */
.drawer-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 14px 16px 10px;
    border-bottom: 1px solid var(--border, #E8E2D2);
    background: var(--surface, #fff);
    flex-shrink: 0;
}

.drawer-header-left { display: flex; flex-direction: column; gap: 6px; min-width: 0; }

.drawer-folio-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

.drawer-folio {
    font-size: 15px;
    font-weight: 700;
    color: var(--text, #1A1814);
}

.d-estado-badge, .d-prioridad-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: .4px;
}

/* reutiliza los colores del theme */
.d-estado-badge.abierto      { background: var(--state-abierto-bg, #EFF6FF);   color: var(--state-abierto, #2563EB); }
.d-estado-badge.en-proceso   { background: var(--state-proceso-bg, #FFFBEB);   color: var(--state-proceso, #D97706); }
.d-estado-badge.en-espera    { background: var(--state-espera-bg, #F5F3FF);    color: var(--state-espera, #7C3AED); }
.d-estado-badge.cerrado      { background: var(--state-cerrado-bg, #F0FDF4);   color: var(--state-cerrado, #16A34A); }
.d-estado-badge.cancelado    { background: var(--state-cancelado-bg, #F3F4F6); color: var(--state-cancelado, #6B7280); }
.d-estado-badge.programado   { background: #F0FDF4; color: #16A34A; }
.d-estado-badge.contpaqi     { background: #FFFBEB; color: #D97706; }

.d-prioridad-badge.alta  { background: var(--priority-alta-bg, #FEF2F2);  color: var(--priority-alta, #DC2626); }
.d-prioridad-badge.media { background: var(--priority-media-bg, #FFFBEB); color: var(--priority-media, #D97706); }
.d-prioridad-badge.baja  { background: var(--priority-baja-bg, #EFF6FF);  color: var(--priority-baja, #2563EB); }

.drawer-assignee {
    font-size: 12px;
    color: var(--text-3, #807868);
    display: flex;
    align-items: center;
    gap: 5px;
}

.drawer-header-actions { display: flex; gap: 6px; flex-shrink: 0; margin-left: 10px; }

.d-icon-btn {
    width: 30px; height: 30px;
    border: 1px solid var(--border, #E8E2D2);
    border-radius: 7px;
    background: var(--surface-2, #F5F1E8);
    color: var(--text-2, #4D483F);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px;
    transition: all 0.15s;
}

.d-icon-btn:hover { background: var(--border, #E8E2D2); }
.d-icon-btn-sol   { color: var(--accent, oklch(0.60 0.13 38)); border-color: var(--accent-light, #f5ede8); }
.d-icon-btn-close { color: var(--priority-alta, #DC2626); }

/* ── Meta grid ───────────────────────────────── */
.drawer-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    padding: 0 16px;
    border-bottom: 1px solid var(--border, #E8E2D2);
    flex-shrink: 0;
    background: var(--surface-2, #F5F1E8);
}

.dm-item {
    padding: 8px 4px;
    border-bottom: 1px solid var(--border, #E8E2D2);
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.dm-item:nth-child(odd)  { border-right: 1px solid var(--border, #E8E2D2); padding-right: 12px; }
.dm-item:nth-child(even) { padding-left: 12px; }
.dm-item:nth-last-child(-n+2) { border-bottom: none; }

.dm-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--text-3, #807868);
}

.dm-val {
    font-size: 12px;
    font-weight: 500;
    color: var(--text, #1A1814);
    word-break: break-word;
}

/* ── Tabs ────────────────────────────────────── */
.drawer-tabs {
    display: flex;
    border-bottom: 1px solid var(--border, #E8E2D2);
    flex-shrink: 0;
    background: var(--surface, #fff);
    padding: 0 8px;
}

.d-tab {
    padding: 9px 13px;
    font-size: 12px;
    font-weight: 500;
    border: none;
    background: none;
    color: var(--text-3, #807868);
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.15s;
    white-space: nowrap;
}

.d-tab:hover  { color: var(--text, #1A1814); }
.d-tab.active { color: var(--accent, oklch(0.60 0.13 38)); border-bottom-color: var(--accent, oklch(0.60 0.13 38)); font-weight: 600; }

/* ── Content area ────────────────────────────── */
.drawer-content-area {
    flex: 1;
    overflow-y: auto;
    min-height: 0;
}

.drawer-content-area::-webkit-scrollbar { width: 5px; }
.drawer-content-area::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

.dtab-pane { display: none; }
.dtab-pane.active { display: block; }

/* ── Comment items in drawer ─────────────────── */
.d-cmnt-list {
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.d-cmnt-item {
    display: flex;
    gap: 9px;
    animation: dCmntIn .2s ease;
}

@keyframes dCmntIn { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:translateY(0); } }

.d-cmnt-avatar {
    width: 30px; height: 30px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: #fff;
    flex-shrink: 0;
}

.d-cmnt-bubble {
    flex: 1;
    background: var(--surface-2, #F5F1E8);
    border-radius: 0 10px 10px 10px;
    padding: 8px 12px;
    font-size: 12.5px;
    line-height: 1.5;
}

.d-cmnt-bubble.nota { background: #fff8e1; border-left: 3px solid #f59e0b; }
.d-cmnt-bubble.nota.p2p { background: #fdf2f8; border-left: 3px solid #a855f7; }
.d-cmnt-bubble.solucion { background: #f0fdf4; border-left: 3px solid #16a34a; }

.d-cmnt-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 4px;
    flex-wrap: wrap;
}

.d-cmnt-autor { font-weight: 600; font-size: 12px; color: var(--text, #1A1814); }
.d-cmnt-fecha { font-size: 10px; color: var(--text-3, #807868); }
.d-cmnt-tipo-tag {
    font-size: 9px; font-weight: 700; padding: 1px 6px;
    border-radius: 8px; text-transform: uppercase; letter-spacing: .3px;
}
.d-cmnt-tipo-tag.nota    { background: #fef3c7; color: #b45309; }
.d-cmnt-tipo-tag.nota.p2p { background: #f3e8ff; color: #7c3aed; }
.d-cmnt-tipo-tag.solucion { background: #d1fae5; color: #065f46; }

.d-cmnt-text { color: var(--text-2, #4D483F); font-size: 12.5px; line-height: 1.5; white-space: pre-wrap; }

.d-cmnt-empty {
    text-align: center;
    padding: 30px 20px;
    color: var(--text-3, #807868);
    font-size: 13px;
}

.d-cmnt-loading { text-align: center; padding: 30px; color: var(--text-3); font-size: 18px; }

/* ── Description section ─────────────────────── */
.drawer-desc-section {
    padding: 10px 16px;
    border-bottom: 1px solid var(--border, #E8E2D2);
    flex-shrink: 0;
    background: var(--surface, #fff);
}

.drawer-desc-text {
    font-size: 12.5px;
    color: var(--text-2, #4D483F);
    line-height: 1.55;
}

/* ── Solución tab ────────────────────────────── */
.d-sol-content {
    padding: 16px;
    font-size: 13px;
    color: var(--text-2);
    line-height: 1.6;
}

.d-sol-empty {
    padding: 24px 16px;
    text-align: center;
    color: var(--text-3);
    font-size: 13px;
}

/* ── Edit / Solution panels ──────────────────── */
.drawer-inner-panel {
    padding: 14px 16px;
    overflow-y: auto;
    flex: 1;
    min-height: 0;
}

.d-edit-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.d-field-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.d-field-group.full { grid-column: 1/-1; }

.d-field-group label {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-3);
    text-transform: uppercase;
    letter-spacing: .3px;
}

.d-field-group select,
.d-field-group input,
.d-field-group textarea {
    font-size: 12.5px;
    padding: 6px 9px;
    border: 1px solid var(--border);
    border-radius: 7px;
    background: var(--surface);
    color: var(--text);
    transition: border-color 0.15s;
}

.d-field-group select:focus,
.d-field-group input:focus,
.d-field-group textarea:focus {
    outline: none;
    border-color: var(--accent);
}

.d-panel-bar {
    display: flex;
    gap: 8px;
    padding: 10px 16px;
    border-top: 1px solid var(--border);
    justify-content: flex-end;
    flex-shrink: 0;
    background: var(--surface);
}

.d-btn-cancel {
    padding: 7px 16px; border-radius: 7px; border: 1px solid var(--border);
    background: var(--surface-2); color: var(--text-2); font-size: 12px;
    font-weight: 600; cursor: pointer; transition: all 0.15s;
}
.d-btn-cancel:hover { background: var(--border); }

.d-btn-save {
    padding: 7px 18px; border-radius: 7px; border: none;
    background: var(--accent); color: #fff; font-size: 12px;
    font-weight: 600; cursor: pointer; transition: all 0.15s;
    display: flex; align-items: center; gap: 6px;
}
.d-btn-save:hover { background: var(--accent-dark); }
.d-btn-save:disabled { opacity: 0.55; cursor: not-allowed; }

/* ── Compose area ────────────────────────────── */
.drawer-compose {
    border-top: 1px solid var(--border);
    padding: 10px 14px 12px;
    flex-shrink: 0;
    background: var(--surface);
}

.dc-tipo-tabs {
    display: flex;
    gap: 4px;
    margin-bottom: 8px;
}

.dc-tipo-tab {
    padding: 4px 12px;
    font-size: 11px;
    font-weight: 500;
    border: 1px solid var(--border);
    border-radius: 20px;
    background: var(--surface-2);
    color: var(--text-3);
    cursor: pointer;
    transition: all 0.15s;
}

.dc-tipo-tab.active, .dc-tipo-tab:hover {
    background: var(--accent);
    color: #fff;
    border-color: var(--accent);
}

.dc-textarea {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 8px 10px;
    font-size: 13px;
    color: var(--text);
    background: var(--surface);
    resize: none;
    transition: border-color 0.15s;
    font-family: 'IBM Plex Sans', sans-serif;
}

.dc-textarea:focus { outline: none; border-color: var(--accent); }

.dc-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 8px;
}

.dc-btn-send {
    padding: 7px 18px;
    border: none;
    border-radius: 8px;
    background: var(--accent);
    color: #fff;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: background 0.15s;
}

.dc-btn-send:hover    { background: var(--accent-dark); }
.dc-btn-send:disabled { opacity: 0.55; cursor: not-allowed; }

.dc-btn-attach {
    padding: 7px 11px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--surface);
    color: var(--text-muted);
    font-size: 13px;
    cursor: pointer;
    transition: all 0.15s;
}
.dc-btn-attach:hover { background: var(--accent); color: #fff; border-color: var(--accent); }

/* Drag-over en el textarea */
.dc-textarea.drag-over {
    border-color: var(--accent);
    background: color-mix(in srgb, var(--accent) 7%, var(--surface));
    outline: 2px dashed var(--accent);
    outline-offset: -2px;
}

/* Preview de archivo pendiente */
#dc-attach-preview {
    margin: 6px 0 2px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
#dc-attach-preview:empty { display: none; }

.dc-attach-item {
    position: relative;
    border-radius: 8px;
    border: 1px solid var(--border);
    overflow: hidden;
    max-width: 100%;
    background: var(--bg);
}
.dc-attach-img {
    display: block;
    max-height: 110px;
    max-width: 100%;
    border-radius: 7px;
    cursor: zoom-in;
}
.dc-attach-file {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 7px 10px;
    font-size: 12px;
    color: var(--text);
}
.dc-attach-remove {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: rgba(0,0,0,0.55);
    color: #fff;
    border: none;
    cursor: pointer;
    font-size: 10px;
    line-height: 20px;
    text-align: center;
    padding: 0;
}

/* ── Shift table when drawer opens ──────────────*/
#main > .container {
    transition: padding-right 0.28s cubic-bezier(.4,0,.2,1);
}

body.drawer-open #main > .container {
    padding-right: 470px;
}

@media (max-width: 900px) {
    .ticket-drawer { width: 100vw; }
    body.drawer-open #main > .container { padding-right: 0; }
}
</style>

<div id="ticketDrawer" class="ticket-drawer closed">
    <div class="drawer-panel">

        <!-- ── Header ── -->
        <div class="drawer-header">
            <div class="drawer-header-left">
                <div class="drawer-folio-row">
                    <span class="drawer-folio" id="d-folio"></span>
                    <span class="d-estado-badge" id="d-estado-badge"></span>
                    <span class="d-prioridad-badge" id="d-prioridad-badge"></span>
                </div>
                <div class="drawer-assignee">
                    <i class="fas fa-user-circle"></i>
                    <span id="d-asignado-label"></span>
                    <span style="margin:0 4px;opacity:.4">·</span>
                    <span id="d-reporta-label" style="opacity:.7"></span>
                </div>
            </div>
            <div class="drawer-header-actions">
                <button class="d-icon-btn" id="d-btn-edit" title="Editar ticket"><i class="fas fa-pen"></i></button>
                <button class="d-icon-btn d-icon-btn-sol" id="d-btn-sol" title="Registrar solución"><i class="fas fa-check-double"></i></button>
                <button class="d-icon-btn d-icon-btn-close" onclick="closeDrawer()" title="Cerrar"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <!-- ── Meta grid ── -->
        <div class="drawer-meta">
            <div class="dm-item"><span class="dm-label">Cliente</span><span class="dm-val" id="d-cliente"></span></div>
            <div class="dm-item"><span class="dm-label">Sistema</span><span class="dm-val" id="d-sistema"></span></div>
            <div class="dm-item"><span class="dm-label">Servicio</span><span class="dm-val" id="d-servicio"></span></div>
            <div class="dm-item"><span class="dm-label">Tiempo efectivo</span><span class="dm-val" id="d-tiempo-efectivo"></span></div>
            <div class="dm-item"><span class="dm-label">Reporte</span><span class="dm-val" id="d-programada"></span></div>
            <div class="dm-item"><span class="dm-label">Inicio</span><span class="dm-val" id="d-inicio"></span></div>
        </div>

        <!-- ── Descripción ── -->
        <div class="drawer-desc-section">
            <div class="drawer-desc-text" id="d-descripcion-text"></div>
            <button class="drawer-desc-more" id="d-desc-more-btn" onclick="toggleDescMore()" style="display:none">Ver más</button>
        </div>

        <!-- ── VIEW MODE ── -->
        <div id="drawer-view-mode" style="display:flex;flex-direction:column;flex:1;min-height:0;overflow:hidden;">

            <!-- Tabs -->
            <div class="drawer-tabs">
                <button class="d-tab active" onclick="switchDTab(this,'conv')" data-dtab="conv">Conversación</button>
                <button class="d-tab" onclick="switchDTab(this,'notas')" data-dtab="notas">Notas internas</button>
                <button class="d-tab" onclick="switchDTab(this,'sol')" data-dtab="sol">Solución</button>
            </div>

            <!-- Tab content -->
            <div class="drawer-content-area" id="drawerContentArea">
                <div class="dtab-pane active" id="dtab-conv">
                    <div class="d-cmnt-list" id="d-cmnt-conv"><div class="d-cmnt-loading"><i class="fas fa-spinner fa-spin"></i></div></div>
                </div>
                <div class="dtab-pane" id="dtab-notas">
                    <div class="d-cmnt-list" id="d-cmnt-notas"></div>
                </div>
                <div class="dtab-pane" id="dtab-sol">
                    <div id="d-sol-content"></div>
                </div>
            </div>

            <!-- Compose -->
            <div class="drawer-compose" id="drawerCompose">
                <div class="dc-tipo-tabs">
                    <button class="dc-tipo-tab active" onclick="setDTipo(this,'comentario')">Comentario</button>
                    <button class="dc-tipo-tab" onclick="setDTipo(this,'nota_interna')">🔒 Nota privada</button>
                    <button class="dc-tipo-tab" onclick="setDTipo(this,'solucion')">Solución</button>
                </div>
                <input type="hidden" id="d-tipo-val" value="comentario">
                <!-- Selector de destinatario P2P (solo visible en nota_interna) -->
                <div id="d-destinatario-wrap" style="display:none;margin-bottom:6px;">
                    <select id="d-destinatario-id" style="width:100%;font-size:12px;padding:5px 8px;border:1px solid var(--border);border-radius:7px;background:var(--surface);color:var(--text);">
                        <option value="">— Enviar a (privado) —</option>
                        <?php foreach ($Usuarios as $u): ?>
                            <option value="<?= (int)$u['id'] ?>"><?= Html::encode($u['Nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="file" id="dc-file-input" style="display:none" accept="*/*">
                <textarea id="d-nuevo-cmnt" class="dc-textarea" placeholder="Escribe aquí..." rows="2"></textarea>
                <div id="dc-attach-preview"></div>
                <div class="dc-actions">
                    <button class="dc-btn-attach" id="dc-btn-attach" type="button" onclick="document.getElementById('dc-file-input').click()" title="Adjuntar archivo">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <button class="dc-btn-send" id="d-btn-send" onclick="enviarDComentario()">
                        <i class="fas fa-paper-plane"></i> Enviar
                    </button>
                </div>
            </div>
        </div>

        <!-- ── EDIT MODE ── -->
        <div id="drawer-edit-mode" style="display:none;flex-direction:column;flex:1;min-height:0;overflow:hidden;">
            <div class="drawer-inner-panel">
                <div class="d-edit-grid">
                    <div class="d-field-group">
                        <label><i class="fas fa-circle-notch"></i> Estado</label>
                        <select id="de-Estado">
                            <option value="ABIERTO">Abierto</option>
                            <option value="PROGRAMADO">Programado</option>
                            <option value="EN PROCESO">En Proceso</option>
                            <option value="CONTPAQi">CONTPAQi</option>
                        </select>
                    </div>
                    <div class="d-field-group">
                        <label><i class="fas fa-exclamation-circle"></i> Prioridad</label>
                        <select id="de-Prioridad">
                            <option value="ALTA">Alta</option>
                            <option value="MEDIA">Media</option>
                            <option value="BAJA">Baja</option>
                        </select>
                    </div>
                    <div class="d-field-group">
                        <label><i class="fas fa-user"></i> Asignado a</label>
                        <select id="de-Asignado_a">
                            <option value="">Sin asignar</option>
                        </select>
                    </div>
                    <div class="d-field-group">
                        <label><i class="fas fa-user-tag"></i> Usuario reporta</label>
                        <input type="text" id="de-Usuario_reporta">
                    </div>
                    <div class="d-field-group">
                        <label><i class="fas fa-building"></i> Cliente</label>
                        <select id="de-Cliente_id"><option value="">Sin cliente</option></select>
                    </div>
                    <div class="d-field-group">
                        <label><i class="fas fa-desktop"></i> Sistema</label>
                        <select id="de-Sistema_id"><option value="">Sin sistema</option></select>
                    </div>
                    <div class="d-field-group">
                        <label><i class="fas fa-cogs"></i> Servicio</label>
                        <select id="de-Servicio_id"><option value="">Sin servicio</option></select>
                    </div>
                    <div class="d-field-group">
                        <label><i class="fas fa-calendar-alt"></i> Hora reporte</label>
                        <input type="datetime-local" id="de-HoraProgramada">
                    </div>
                    <div class="d-field-group">
                        <label><i class="fas fa-play-circle"></i> Hora inicio</label>
                        <input type="datetime-local" id="de-HoraInicio">
                    </div>
                    <div class="d-field-group full">
                        <label><i class="fas fa-file-alt"></i> Descripción</label>
                        <textarea id="de-Descripcion" rows="4"></textarea>
                    </div>
                </div>
            </div>
            <div class="d-panel-bar">
                <button class="d-btn-cancel" onclick="drawerShowView()"><i class="fas fa-arrow-left"></i> Cancelar</button>
                <button class="d-btn-save" id="de-save-btn" onclick="saveDrawerEdit()"><i class="fas fa-save"></i> Guardar cambios</button>
            </div>
        </div>

        <!-- ── SOLUTION MODE ── -->
        <div id="drawer-sol-mode" style="display:none;flex-direction:column;flex:1;min-height:0;overflow:hidden;">
            <div class="drawer-inner-panel">
                <div style="background:var(--surface-2);border-radius:10px;padding:12px;margin-bottom:14px;font-size:12px;display:flex;flex-direction:column;gap:6px;">
                    <div><span style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--text-3)">Hora de inicio</span><br><strong id="ds-label-inicio">—</strong></div>
                    <div><span style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--text-3)">Hora de finalización</span><br><strong id="ds-label-fin">—</strong></div>
                    <div><span style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--text-3)">Tiempo efectivo</span><br>
                        <span style="background:var(--state-cerrado-bg);color:var(--state-cerrado);padding:2px 10px;border-radius:20px;font-weight:700;font-size:11px;" id="ds-badge-tiempo">Sin calcular</span>
                    </div>
                </div>
                <div class="d-field-group" style="margin-bottom:12px;">
                    <label><i class="fas fa-clock"></i> Hora de finalización</label>
                    <input type="datetime-local" id="ds-HoraFinalizo">
                </div>
                <div class="d-field-group" style="margin-bottom:12px;">
                    <label><i class="fas fa-hourglass-end"></i> Tiempo efectivo</label>
                    <input type="text" id="ds-TiempoEfectivo" placeholder="Auto-calculado...">
                </div>
                <div class="d-field-group">
                    <label><i class="fas fa-lightbulb"></i> Solución aplicada</label>
                    <textarea id="ds-Solucion" rows="5" placeholder="Describe la causa y lo que hiciste para resolverlo..."></textarea>
                </div>
            </div>
            <div class="d-panel-bar">
                <button class="d-btn-cancel" onclick="drawerShowView()"><i class="fas fa-arrow-left"></i> Cancelar</button>
                <button class="d-btn-save" id="ds-save-btn" onclick="saveDrawerSolucion()"><i class="fas fa-check-circle"></i> Guardar solución</button>
            </div>
        </div>

    </div><!-- /.drawer-panel -->
</div><!-- /#ticketDrawer -->

<!-- Modal para Solución -->
<div class="modal fade" id="solutionModal" tabindex="-1" aria-labelledby="solutionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content solution-modal">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="solutionModalLabel">
                        <i class="fas fa-wrench"></i> Cierre de ticket
                    </h5>
                    <small class="text-light-50 d-block mt-1">
                        <i class="fas fa-info-circle"></i>
                        Registra la solución y el tiempo real invertido.
                    </small>
                </div>
                <div class="ms-3">
                    <span id="solutionTicketFolio" class="badge bg-light text-dark" style="font-size: 0.8rem;">
                        <!-- Se llena por JS -->
                    </span>
                </div>
                <button type="button" class="btn-close btn-close-white ms-3" data-bs-dismiss="modal" aria-label="Close" onclick="closeModal()"></button>
            </div>

            <div class="modal-body">
                <!-- Hidden fields -->
                <input type="hidden" id="ticketId" value="">
                <input type="hidden" id="horaInicioTicket" value="">

                <div class="row g-3">
                    <!-- Columna izquierda: resumen de tiempo -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0 solution-summary-card">
                            <div class="card-body">
                                <h6 class="card-title text-muted mb-2">
                                    <i class="fas fa-stopwatch"></i> Resumen de tiempo
                                </h6>

                                <div class="mb-2 small">
                                    <div class="text-muted">Hora de inicio</div>
                                    <div id="labelHoraInicio" class="fw-semibold">
                                        -
                                    </div>
                                </div>

                                <div class="mb-2 small">
                                    <div class="text-muted">Hora de finalización</div>
                                    <div id="labelHoraFinalizo" class="fw-semibold">
                                        -
                                    </div>
                                </div>

                                <hr class="my-2">

                                <div class="small text-muted mb-1">
                                    Tiempo efectivo calculado
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success-subtle text-success-emphasis" id="badgeTiempoEfectivo" style="font-size: 0.8rem;">
                                        Sin calcular
                                    </span>
                                </div>

                                <small class="text-muted d-block mt-2">
                                    Se calcula automáticamente al elegir la hora de finalización.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha: formulario -->
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-clock"></i> Hora de finalización
                            </label>
                            <input type="datetime-local" id="horaFinalizo" class="form-control">
                            <small class="text-muted">
                                Selecciona la fecha y hora en que se terminó realmente la atención.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-align-left"></i> Solución aplicada
                            </label>
                            <textarea id="solucion" class="form-control" rows="4"
                                      placeholder="Describe brevemente la causa del problema y lo que hiciste para resolverlo..."></textarea>
                        </div>

                        <div class="mb-1">
                            <label class="form-label">
                                <i class="fas fa-hourglass-end"></i> Tiempo efectivo invertido
                            </label>
                            <input type="text" id="tiempoEfectivo" class="form-control" 
                                   placeholder="Se calculará automáticamente a partir de la hora de inicio y finalización">
                           
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="saveSolution()">
                    <i class="fas fa-save"></i> Guardar solución
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Modal para Comentarios -->
<style>
/* ── Modal comentarios ─────────────────────────────────── */
#comentariosModal .modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,.18);
    overflow: hidden;
}
#comentariosModal .modal-header {
    background: linear-gradient(135deg, #8BA590 0%, #6d8f73 100%);
    color: #fff;
    padding: 18px 24px;
    border-bottom: none;
}
#comentariosModal .modal-title { font-size: 16px; font-weight: 600; }
#comentariosModal .modal-body  { padding: 0; background: #f4f6f4; }
#comentariosModal .modal-footer {
    background: #fff;
    border-top: 1px solid #e8ede9;
    padding: 14px 20px;
    gap: 10px;
}

/* ── Lista de comentarios ──────────────────────────────── */
#listaComentarios {
    max-height: 360px;
    overflow-y: auto;
    padding: 16px 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    scroll-behavior: smooth;
}
#listaComentarios::-webkit-scrollbar { width: 5px; }
#listaComentarios::-webkit-scrollbar-track { background: #f0f0f0; border-radius: 10px; }
#listaComentarios::-webkit-scrollbar-thumb { background: #b5c9b8; border-radius: 10px; }

.cmnt-item {
    display: flex;
    gap: 10px;
    animation: cmntFadeIn .25s ease;
}
@keyframes cmntFadeIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }

.cmnt-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
    margin-top: 2px;
}
.cmnt-bubble {
    flex: 1;
    background: #fff;
    border-radius: 0 12px 12px 12px;
    padding: 10px 14px;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
    position: relative;
}
.cmnt-bubble.tipo-nota_interna  { background: #fffbeb; border-left: 3px solid #f59e0b; }
.cmnt-bubble.tipo-solucion       { background: #f0fdf4; border-left: 3px solid #22c55e; }
.cmnt-bubble.tipo-comentario     { border-left: 3px solid #8BA590; }

.cmnt-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 5px;
    flex-wrap: wrap;
}
.cmnt-author { font-size: 12px; font-weight: 700; color: #374151; }
.cmnt-fecha  { font-size: 11px; color: #9ca3af; }
.cmnt-badge  {
    font-size: 10px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.badge-comentario   { background: #e0f0e3; color: #2d6a2d; }
.badge-nota_interna { background: #fef3c7; color: #92400e; }
.badge-solucion     { background: #dcfce7; color: #166534; }

.cmnt-texto {
    font-size: 13px;
    color: #374151;
    line-height: 1.55;
    word-break: break-word;
    margin: 0;
}
.cmnt-mention-tag {
    display: inline-block;
    background: #dbeafe;
    color: #1d4ed8;
    border-radius: 4px;
    padding: 0 5px;
    font-size: 12px;
    font-weight: 600;
}

/* ── Imagen / Archivo adjunto ──────────────────────────── */
.cmnt-archivo { margin-top: 8px; }
.cmnt-img-preview {
    max-width: 100%;
    max-height: 260px;
    border-radius: 8px;
    cursor: pointer;
    border: 1px solid #e5e7eb;
    transition: opacity .2s;
    display: block;
}
.cmnt-img-preview:hover { opacity: .88; }
.cmnt-file-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #4b5563;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 5px 10px;
    text-decoration: none;
    transition: background .15s;
}
.cmnt-file-link:hover { background: #e5e7eb; color: #111; }

.cmnt-empty {
    text-align: center;
    padding: 32px 16px;
    color: #9ca3af;
}
.cmnt-empty i { font-size: 36px; margin-bottom: 10px; display: block; }

/* ── Formulario nuevo comentario ───────────────────────── */
.cmnt-form-wrapper {
    background: #fff;
    padding: 16px 20px;
    border-top: 2px solid #e8ede9;
}
.cmnt-tipo-tabs {
    display: flex;
    gap: 6px;
    margin-bottom: 12px;
}
.cmnt-tab {
    flex: 1;
    padding: 6px 4px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #f9fafb;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    transition: all .15s;
    color: #6b7280;
}
.cmnt-tab:hover { border-color: #8BA590; color: #4a7c59; }
.cmnt-tab.active-comentario   { background: #e0f0e3; border-color: #8BA590; color: #2d6a2d; }
.cmnt-tab.active-nota_interna { background: #fef3c7; border-color: #f59e0b; color: #92400e; }
.cmnt-tab.active-solucion     { background: #dcfce7; border-color: #22c55e; color: #166534; }

#nuevoComentario {
    border-radius: 10px;
    border: 1px solid #d1d5db;
    font-size: 13px;
    resize: none;
    transition: border-color .2s;
}
#nuevoComentario:focus { border-color: #8BA590; box-shadow: 0 0 0 3px rgba(139,165,144,.2); }

/* ── Upload area ───────────────────────────────────────── */
.cmnt-upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 10px;
    padding: 12px 14px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    font-size: 12px;
    color: #6b7280;
    position: relative;
    overflow: hidden;
    margin-top: 10px;
}
.cmnt-upload-area:hover, .cmnt-upload-area.drag-over {
    border-color: #8BA590;
    background: #f0f7f1;
    color: #4a7c59;
}
.cmnt-upload-area input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
}
.cmnt-upload-preview {
    display: none;
    align-items: center;
    gap: 10px;
    background: #f9fafb;
    border-radius: 8px;
    padding: 8px 12px;
    margin-top: 8px;
    border: 1px solid #e5e7eb;
}
.cmnt-upload-preview img {
    height: 52px;
    width: 52px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
}
.cmnt-upload-preview .file-info { flex: 1; font-size: 12px; color: #374151; }
.cmnt-upload-preview .file-name { font-weight: 600; }
.cmnt-upload-preview .file-size { color: #9ca3af; }
.cmnt-remove-file {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    font-size: 16px;
    padding: 0 4px;
    line-height: 1;
}

/* ── Lightbox simple ───────────────────────────────────── */
#imgLightbox {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.85);
    z-index: 99999;
    align-items: center;
    justify-content: center;
    cursor: zoom-out;
}
#imgLightbox.open { display: flex; }
#imgLightbox img {
    max-width: 90vw;
    max-height: 90vh;
    border-radius: 8px;
    box-shadow: 0 8px 40px rgba(0,0,0,.5);
}
</style>

<div class="modal fade" id="comentariosModal" tabindex="-1" aria-labelledby="comentariosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="comentariosModalLabel">
                    <i class="fas fa-comments me-2"></i>
                    Ticket <span id="ticketFolioComentarios" style="font-weight:800;"></span>
                    <span id="cmntCountBadge" style="font-size:11px; background:rgba(255,255,255,.25); border-radius:20px; padding:2px 9px; margin-left:8px;"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeComentariosModal()"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="ticketIdComentarios" value="">

                <!-- Lista -->
                <div id="listaComentarios">
                    <div class="cmnt-empty">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Cargando comentarios...</p>
                    </div>
                </div>

                <!-- Formulario -->
                <div class="cmnt-form-wrapper">
                    <!-- Tabs de tipo -->
                    <div class="cmnt-tipo-tabs" id="cmntTipoTabs">
                        <button class="cmnt-tab active-comentario" data-tipo="comentario" onclick="setCmntTipo('comentario', this)">
                            💬 Comentario
                        </button>
                        <button class="cmnt-tab" data-tipo="nota_interna" onclick="setCmntTipo('nota_interna', this)">
                            📝 Nota interna
                        </button>
                        <button class="cmnt-tab" data-tipo="solucion" onclick="setCmntTipo('solucion', this)">
                            ✅ Solución
                        </button>
                    </div>
                    <input type="hidden" id="tipoComentario" value="comentario">

                    <!-- Textarea -->
                    <div style="position:relative;">
                        <textarea id="nuevoComentario" class="form-control" rows="3"
                            placeholder="Escribe tu comentario... Usa @ para mencionar a alguien"></textarea>
                        <div id="mentionBox" class="mention-box"></div>
                    </div>

                    <!-- Zona de carga de archivo -->
                    <div class="cmnt-upload-area" id="cmntUploadArea">
                        <input type="file" id="cmntArchivoInput" accept="image/*,.pdf,.docx,.xlsx"
                               onchange="handleCmntFileSelect(this)">
                        <i class="fas fa-paperclip me-1"></i>
                        Adjuntar imagen o archivo <span style="color:#9ca3af;">(jpg, png, gif, webp, pdf — máx. 8 MB)</span>
                    </div>

                    <!-- Preview del archivo seleccionado -->
                    <div class="cmnt-upload-preview" id="cmntUploadPreview">
                        <img id="cmntPreviewImg" src="" alt="preview" style="display:none;">
                        <div id="cmntFileIconPreview" style="display:none; font-size:32px;">📄</div>
                        <div class="file-info">
                            <div class="file-name" id="cmntFileName"></div>
                            <div class="file-size" id="cmntFileSize"></div>
                        </div>
                        <button class="cmnt-remove-file" onclick="removeCmntFile()" title="Quitar archivo">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="closeComentariosModal()">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
                <button type="button" class="btn btn-success btn-sm" id="btnEnviarComentario" onclick="agregarComentario()">
                    <i class="fas fa-paper-plane me-1"></i> Enviar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox para imágenes -->
<div id="imgLightbox" onclick="closeLightbox()">
    <img id="imgLightboxSrc" src="" alt="imagen ampliada">
</div>
<script>
window.WINTICK_USERS = <?= json_encode(array_map(function($u){
    $avatar = $u['avatar'] ?? null;
    $avatarUrl = ($avatar && str_starts_with($avatar, '/uploads/'))
        ? \yii\helpers\Url::to('@web' . $avatar, true)
        : null;
    return [
        'id'          => (int)$u['id'],
        'email'       => $u['email'],
        'nombre'      => $u['Nombre'],
        'primerNombre'=> preg_split('/\s+/', trim($u['Nombre'] ?? ''))[0] ?? '',
        'avatar'      => $avatarUrl,
    ];
}, $Usuarios), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
window.WINTICK_CLIENTES = <?= json_encode(array_map(fn($c) => ['id' => (int)$c['id'], 'nombre' => $c['Nombre']], $clientes), JSON_UNESCAPED_UNICODE) ?>;
window.WINTICK_SISTEMAS = <?= json_encode(array_map(fn($s) => ['id' => (int)$s['id'], 'nombre' => $s['Nombre']], $sistemas), JSON_UNESCAPED_UNICODE) ?>;
window.WINTICK_SERVICIOS = <?= json_encode(array_map(fn($s) => ['id' => (int)$s['id'], 'nombre' => $s['Nombre']], $servicios), JSON_UNESCAPED_UNICODE) ?>;
window.URL_QUICK_UPDATE = '<?= \yii\helpers\Url::to(['/tickets/quick-update']) ?>';
</script>
<script>

/**
 * Funcion para poder actualizar toda la pagina sin perder el estado de busqueda, filtros, ordenamiento, etc.
 * 
 */
function refreshPage() {
    // Animación de spin mientras carga
    const icon = document.getElementById('refreshIcon');
    const btn  = document.getElementById('refreshPage');
    if (icon) icon.classList.add('fa-spin');
    if (btn)  btn.disabled = true;

    // Recargar conservando los filtros actuales pero sin acumular el param ?refresh
    const url = new URL(window.location.href);
    url.searchParams.delete('refresh');          // limpiar si ya existía
    url.searchParams.set('_r', Date.now());      // forzar petición nueva al servidor
    window.location.href = url.toString();
}

/**
 * Cambia el número de tickets por página preservando todos los filtros activos.
 * Siempre resetea a la primera página para evitar páginas vacías.
 */
function cambiarPorPagina(valor) {
    const url = new URL(window.location.href);
    url.searchParams.set('per-page', valor);
    url.searchParams.delete('page');   // volver a la página 1
    url.searchParams.delete('_r');     // limpiar param de refresh si existía
    window.location.href = url.toString();
}

/**
 * =========================================================
 *
 * - En el textarea se ve bonito: @Nombre
 * - En el backend se guarda token: @[email:correo@dominio.com]
 * - Al renderizar comentarios se ve @Nombre con badge
 * =========================================================
 */

let rowsCache = [];
const totalTicketsOriginal = <?= $dataProvider->getTotalCount() ?>;
let tieneTiempoGuardado = false;
let tiempoEditadoManualmente = false;
let solutionOpenedFromEstadoChange = false;
let lastTicketIdSolution = null;
let lastEstadoAnterior = null;

// ========================================
// FOLIO
// ========================================
function loadNextFolio(inputElement) {
    if (!inputElement) return;

    inputElement.value = 'Generando...';
    inputElement.style.color = '#f59e0b';

    fetch('<?= Url::to(['/tickets/get-next-folio']) ?>', {
        credentials: 'same-origin'
    })
        .then(response => response.json())
        .then(data => {
            if (data.nextFolio) {
                inputElement.value = data.nextFolio;
                inputElement.style.color = '#10b981';
            } else {
                inputElement.value = '❌ Error';
                inputElement.style.color = '#ef4444';
                console.error('No se recibió el siguiente folio');
            }
        })
        .catch(error => {
            console.error('Error obteniendo folio:', error);
            inputElement.value = '❌ Error';
            inputElement.style.color = '#ef4444';
            inputElement.readOnly = false;
        });
}

// ========================================
// CLIENTE -> PRIORIDAD
// ========================================
function loadClienteData(selectElement) {
    const row = selectElement.closest('tr');
    const selectedOption = selectElement.options[selectElement.selectedIndex];

    if (selectedOption.value === '') return;

    const prioridad = selectedOption.getAttribute('data-prioridad');
    if (prioridad) row.querySelector('.prioridad').value = prioridad;

    row.querySelector('.estado').value = 'ABIERTO';
}

// ========================================
// SEARCH
// ========================================
function normalizeText(text) {
    return (text || '').toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^\w\s]/gi, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}

function buildRowsCache() {
    rowsCache = [];
    const rows = document.querySelectorAll('#tableBody tr.existing-row');

    rows.forEach(row => {
        const visibleText = row.innerText;
        const descripcionCell = row.querySelector('.descripcion-cell');
        const fullDescription = descripcionCell ? descripcionCell.getAttribute('data-full-text') : '';

        const searchText = normalizeText(visibleText + ' ' + fullDescription);

        rowsCache.push({
            element: row,
            searchText: searchText
        });
    });
}

function confirmarEliminar(ticketId, folio, tieneDevolucion = false) {
    const extraText = tieneDevolucion
        ? '<br><span style="color:#15803d;font-size:13px;"><i class="fas fa-undo"></i> El tiempo efectivo será devuelto al saldo del cliente.</span>'
        : '';

    Swal.fire({
        title: '¿Eliminar ticket ' + folio + '?',
        html: `<span style="color:#64748b;">Esta acción no se puede deshacer.</span>${extraText}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'Eliminando...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        fetch('<?= Url::to(['delete']) ?>?id=' + ticketId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
            },
            body: '<?= Yii::$app->request->csrfParam ?>=<?= Yii::$app->request->getCsrfToken() ?>'
        })
        .then(response => {
            if (response.ok || response.status === 302) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Eliminado!',
                    text: tieneDevolucion
                        ? `Ticket ${folio} eliminado y tiempo devuelto al cliente.`
                        : `Ticket ${folio} eliminado correctamente.`,
                    showConfirmButton: false,
                    timer: 2200,
                    timerProgressBar: true
                }).then(() => location.reload());
            } else {
                throw new Error('Error del servidor: ' + response.status);
            }
        })
        .catch(error => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar: ' + error.message, confirmButtonColor: '#ef4444' });
        });
    });
}

function performSearch(query) {
    const normalizedQuery = normalizeText(query);

    if (!normalizedQuery) {
        rowsCache.forEach(item => item.element.style.display = '');
        updateSearchStats(totalTicketsOriginal, false);
        return;
    }

    let visibleCount = 0;
    rowsCache.forEach(item => {
        if (item.searchText.includes(normalizedQuery)) {
            item.element.style.display = '';
            visibleCount++;
        } else {
            item.element.style.display = 'none';
        }
    });

    updateSearchStats(visibleCount, true);
}

function updateSearchStats(count, isFiltered) {
    const filteredCount = document.getElementById('filteredCount');
    const noResultsRow = document.getElementById('noResultsRow');

    if (isFiltered) {
        if (count === 0) {
            noResultsRow.classList.add('active');
            filteredCount.style.display = 'none';
        } else {
            noResultsRow.classList.remove('active');
            filteredCount.style.display = 'inline';
            filteredCount.innerHTML = `(mostrando <strong>${count}</strong> de <strong>${totalTicketsOriginal}</strong>)`;
        }
    } else {
        noResultsRow.classList.remove('active');
        filteredCount.style.display = 'none';
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => { clearTimeout(timeout); func(...args); };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
const debouncedSearch = debounce(performSearch, 150);

// ========================================
// FLATPICKR
// ========================================
function initializeFlatpickr(element) {
    flatpickr(element, {
        enableTime: true,
        dateFormat: "Y-m-d H:i",   // formato que va al servidor (oculto)
        altInput: true,             // muestra un input amigable al usuario
        altFormat: "d/m/Y H:i",    // formato visible: DD/MM/AAAA HH:MM
        time_24hr: true,
        locale: "es",
        minuteIncrement: 15,
        defaultHour: 9,
        defaultMinute: 0,
        allowInput: true,           // permite escribir directo sin abrir el picker
        clickOpens: true,
        onReady: function(_, __, instance) {
            if (!instance.altInput) return;
            const inp = instance.altInput;
            inp.placeholder = 'DD/MM/AAAA  HH:MM';
            inp.classList.add('form-control', 'form-control-sm');

            // ── Máscara: inserta / espacio : automáticamente al escribir ──
            inp.addEventListener('keydown', function(e) {
                // Backspace: si el último carácter es separador, bórralo también
                if (e.key === 'Backspace') {
                    const v = inp.value;
                    if (v.endsWith('/') || v.endsWith(' ') || v.endsWith(':')) {
                        inp.value = v.slice(0, -1);
                        e.preventDefault();
                    }
                }
            });

            inp.addEventListener('input', function() {
                // Solo dígitos, máx 12 (DDMMAAAAhhmm)
                let d = inp.value.replace(/\D/g, '').substring(0, 12);
                let out = '';
                if (d.length > 0)  out  = d.substring(0, Math.min(2, d.length));
                if (d.length > 2)  out += '/' + d.substring(2, Math.min(4,  d.length));
                if (d.length > 4)  out += '/' + d.substring(4, Math.min(8,  d.length));
                if (d.length > 8)  out += ' ' + d.substring(8, Math.min(10, d.length));
                if (d.length > 10) out += ':' + d.substring(10, 12);
                inp.value = out;
            });
        }
    });
}

// ========================================
// SAVE TICKET
// ========================================
function saveTicket(row) {
    const ticket = {
        Folio: row.querySelector('.folio').value,
        Cliente_id: row.querySelector('.cliente').value,
        Sistema_id: row.querySelector('.sistema').value,
        Servicio_id: row.querySelector('.servicio').value,
        Usuario_reporta: row.querySelector('.usuario-reporta').value,
        Asignado_a: row.querySelector('.asignado-a').value,
        Descripcion: row.querySelector('.descripcion').value,
        Prioridad: row.querySelector('.prioridad').value,
        Estado: row.querySelector('.estado').value,
        HoraProgramada: (row.querySelector('.hora-programada')?.value || '').replace('T', ' ') || null,
        HoraInicio:     (row.querySelector('.hora-inicio')?.value     || '').replace('T', ' ') || null,
    };

    if (!ticket.Folio || !ticket.Cliente_id || !ticket.Usuario_reporta || !ticket.Asignado_a) {
        Swal.fire({
            icon: 'warning',
            title: 'Faltan datos',
            text: '⚠️ Por favor completa todos los campos obligatorios',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }

    if (!ticket.Descripcion || ticket.Descripcion.trim() === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Descripción vacía',
            text: '⚠️ La descripción no puede estar vacía',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }

    const saveBtn = row.querySelector('.saveRow');
    const originalHtml = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('<?= Url::to(['save-bulk']) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
        },
        body: JSON.stringify({ tickets: [ticket] })
    })
        .then(response => {
            if (!response.ok) throw new Error('HTTP error! status: ' + response.status);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Ticket guardado: ' + ticket.Folio,
                    showConfirmButton: false,
                    timer: 100,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end'
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al guardar',
                    text: data.message || JSON.stringify(data.errors || 'Error desconocido'),
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#ef4444'
                });
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalHtml;
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '❌ Error: ' + error.message,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#ef4444'
            });
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalHtml;
        });
}

// ========================================
// ESTADO
// ========================================
function updateEstado(selectElement, ticketId) {
    const estado = selectElement.value;
    const div = selectElement.previousElementSibling;

    // Capturar estado anterior ANTES de modificar el div
    const estadoAnterior = div.textContent.trim();

    div.className = 'tkt-estado estado-clickeable ' + getEstadoClass(estado);
    div.textContent = getEstadoLabel(estado);
    div.style.display = 'inline-flex';
    selectElement.style.display = 'none';

    // Si se selecciona CERRADO, abrir el modal de solución SIN llamar al backend todavía.
    // La notificación y el cierre real ocurren solo cuando se guarda la solución.
    if (estado === 'CERRADO') {
        lastEstadoAnterior        = estadoAnterior;
        solutionOpenedFromEstadoChange = true;
        lastTicketIdSolution      = ticketId;

        const folio = selectElement.closest('tr')?.dataset.folio || '';
        openSolutionModal(ticketId, folio, true);
        return;
    }

    fetch('<?= Url::to(['update-estado']) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
        },
        body: JSON.stringify({ id: ticketId, estado: estado })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) buildRowsCache();
        })
        .catch(error => console.error('Error:', error));
}

function toggleEstadoSelect(element, ticketId) {
    const select = document.querySelector('.estado-' + ticketId);
    if (!select) return;

    if (select.style.display === 'none' || select.style.display === '') {
        element.style.display = 'none';
        select.style.display = 'block';
        select.focus();
    } else {
        element.style.display = 'inline-flex';
        select.style.display = 'none';
    }
}

function getEstadoClass(estado) {
    const classes = {
        'ABIERTO':    'tkt-estado-abierto',
        'PROGRAMADO': 'tkt-estado-programado',
        'EN PROCESO': 'tkt-estado-proceso',
        'EN ESPERA':  'tkt-estado-espera',
        'CONTPAQi':   'tkt-estado-contpaqi',
        'CERRADO':    'tkt-estado-cerrado',
        'CANCELADO':  'tkt-estado-cancelado',
    };
    return classes[estado] || 'tkt-estado-default';
}

function getEstadoLabel(estado) {
    const labels = {
        'ABIERTO':    'Abierto',
        'PROGRAMADO': 'Programado',
        'EN PROCESO': 'En proceso',
        'EN ESPERA':  'En espera',
        'CONTPAQi':   'CONTPAQi',
        'CERRADO':    'Cerrado',
        'CANCELADO':  'Cancelado',
    };
    return labels[estado] || estado;
}

function getEstadoIcon(estado) {
    const icons = {
        'ABIERTO':    'fa-circle-notch',
        'PROGRAMADO': 'fa-calendar-check',
        'EN PROCESO': 'fa-spinner',
        'CONTPAQi':  'fa-pause-circle',
        'CERRADO':    'fa-check-circle'
    };
    return icons[estado] || 'fa-question-circle';
}

// ========================================
// TIEMPO EFECTIVO
// ========================================
function formatearFechaBonita(fechaStr) {
    const d = new Date(fechaStr);
    if (isNaN(d.getTime())) return fechaStr || '-';

    const opciones = {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return d.toLocaleString('es-MX', opciones);
}
function roundUpTo15Minutes(totalMin) {
  if (!Number.isFinite(totalMin) || totalMin <= 0) return 0;
  return Math.ceil(totalMin / 15) * 15; 
}

function formatHoursDecimal(hours) {
  // 2 decimales máximo pero sin ceros innecesarios (1.50 -> 1.5, 2.00 -> 2)
  return String(parseFloat(hours.toFixed(2)));
}

function calcularTiempoEfectivo() {
  if (tiempoEditadoManualmente) return;

  const inicioStr = document.getElementById('horaInicioTicket').value;
  const finInput  = document.getElementById('horaFinalizo');
  const finStr    = finInput.value;

  const salidaInput = document.getElementById('tiempoEfectivo');
  const badge       = document.getElementById('badgeTiempoEfectivo');
  const labelFin    = document.getElementById('labelHoraFinalizo');

  if (!inicioStr || !finStr) {
    salidaInput.value = '';
    badge.textContent = 'Sin calcular';
    labelFin.textContent = '-';
    return;
  }

  const inicio = new Date(inicioStr);
  const fin    = new Date(finStr);

  if (isNaN(inicio.getTime()) || isNaN(fin.getTime()) || fin < inicio) {
    salidaInput.value = '';
    badge.textContent = 'Revisa las fechas';
    labelFin.textContent = formatearFechaBonita(finStr);
    return;
  }

  const diffMs = fin - inicio;
  const rawMin = Math.floor(diffMs / 60000);

  if (rawMin <= 0) {
    salidaInput.value = '';
    badge.textContent = 'Menos de 1 minuto';
    labelFin.textContent = formatearFechaBonita(finStr);
    return;
  }

  const roundedMin = roundUpTo15Minutes(rawMin);

    const h = Math.floor(roundedMin / 60);
    const m = roundedMin % 60;
    const textoHoras = `${h}.${String(m).padStart(2,'0')}`;
    salidaInput.value = textoHoras;
    badge.textContent = textoHoras + ' h';

  salidaInput.value = textoHoras;
  badge.textContent = `${textoHoras} h`; 
  labelFin.textContent = formatearFechaBonita(finStr);
}

// ========================================
// SOLUTION MODAL
// ========================================
function openSolutionModal(ticketId, folio, openedFromEstadoChange = false) {
    const selectElement = document.querySelector('.estado-' + ticketId);
    if (!selectElement) {
        console.error('No se encontró el select de estado para el ticket', ticketId);
        return;
    }

    const estado = selectElement.value;

    solutionOpenedFromEstadoChange = openedFromEstadoChange;
    lastTicketIdSolution = ticketId;

    if (estado !== 'CERRADO') {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Solo puedes agregar una solución a un ticket que esté en estado CERRADO. Cambia el estado e inténtalo de nuevo.',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }

    document.getElementById('ticketId').value = ticketId;

    const folioBadge = document.getElementById('solutionTicketFolio');
    if (folioBadge) folioBadge.textContent = 'Ticket ' + folio;

    document.getElementById('horaFinalizo').value = '';
    document.getElementById('solucion').value = '';
    document.getElementById('tiempoEfectivo').value = '';
    document.getElementById('horaInicioTicket').value = '';
    document.getElementById('labelHoraInicio').textContent = '-';
    document.getElementById('labelHoraFinalizo').textContent = '-';
    document.getElementById('badgeTiempoEfectivo').textContent = 'Sin calcular';

    tieneTiempoGuardado = false;
    tiempoEditadoManualmente = false;

    fetch('<?= Url::to(['get-ticket-data']) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
        },
        body: JSON.stringify({ id: ticketId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.ticket) {
                const t = data.ticket;

                if (t.HoraInicio) {
                    document.getElementById('horaInicioTicket').value = t.HoraInicio;
                    document.getElementById('labelHoraInicio').textContent = formatearFechaBonita(t.HoraInicio);
                }

                if (t.HoraFinalizo) {
                    document.getElementById('horaFinalizo').value = t.HoraFinalizo;
                    document.getElementById('labelHoraFinalizo').textContent = formatearFechaBonita(t.HoraFinalizo);
                }

                if (t.Solucion) document.getElementById('solucion').value = t.Solucion;

                if (t.TiempoEfectivo) {
                    tieneTiempoGuardado = true;
                    document.getElementById('tiempoEfectivo').value = t.TiempoEfectivo;
                    document.getElementById('badgeTiempoEfectivo').textContent = t.TiempoEfectivo;
                } else {
                    tieneTiempoGuardado = false;
                    if (t.HoraInicio && t.HoraFinalizo) calcularTiempoEfectivo();
                }
            }
        });

    const modal = document.getElementById('solutionModal');
    modal.classList.add('show');
    modal.style.display = 'block';
    document.body.classList.add('modal-open');

    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    backdrop.id = 'solutionBackdrop';
    document.body.appendChild(backdrop);
}

function saveSolution() {
    const ticketId = document.getElementById('ticketId').value;
    const solucion = document.getElementById('solucion').value;
    const horaFinalizo = document.getElementById('horaFinalizo').value;
    const tiempoEfectivo = document.getElementById('tiempoEfectivo').value;

    if (!solucion || !horaFinalizo || !tiempoEfectivo) {
       Swal.fire({
            icon: 'warning',
            title: 'Faltan datos',
            text: 'Por favor completa todos los campos antes de guardar la solución.',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }

    fetch('<?= Url::to(['save-solution']) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
        },
        body: JSON.stringify({
            id: ticketId,
            solucion: solucion,
            horaFinalizo: horaFinalizo,
            tiempoEfectivo: tiempoEfectivo
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                solutionOpenedFromEstadoChange = false;
                lastTicketIdSolution = null;

              Swal.fire({ 
                    icon: 'success',
                    title: '¡Solución guardada!',
                    text: 'La solución del ticket se ha guardado correctamente.',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end'
              })
                closeModal();
                sessionStorage.setItem('notifNoSuprimir', '1');
                location.reload();
            } else {
                let msg = data.message || 'Error desconocido';
                if (data.errors) msg += '\n\nDetalles:\n' + JSON.stringify(data.errors, null, 2);
                alert(msg);
            }
        })
        .catch(error => {
            console.error('Error saving solution:', error);
            alert('Error de comunicación con el servidor: ' + error.message);
        });
}

function closeModal() {
    const modal = document.getElementById('solutionModal');
    modal.classList.remove('show');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');

    const backdrop = document.getElementById('solutionBackdrop');
    if (backdrop) backdrop.remove();

    document.getElementById('horaFinalizo').value = '';
    document.getElementById('solucion').value = '';
    document.getElementById('tiempoEfectivo').value = '';

    if (solutionOpenedFromEstadoChange && lastTicketIdSolution) {
        const select = document.querySelector('.estado-' + lastTicketIdSolution);
        if (select) {
            // Revertir al estado que tenía antes de seleccionar CERRADO.
            // No se llama al backend porque nunca se guardó CERRADO — solo se cambió la UI.
            const estadoRevertir = lastEstadoAnterior || 'ABIERTO';
            select.value = estadoRevertir;

            const div = select.previousElementSibling;
            if (div) {
                div.className = 'tkt-estado estado-clickeable ' + getEstadoClass(estadoRevertir);
                div.textContent = getEstadoLabel(estadoRevertir);
                div.style.display = 'inline-flex';
                select.style.display = 'none';
            }
        }
    }

    solutionOpenedFromEstadoChange = false;
    lastTicketIdSolution = null;
    lastEstadoAnterior   = null;
}

// ========================================
// COMENTARIOS MODAL
// ========================================
function openComentariosModal(ticketId, folio) {
    document.getElementById('ticketIdComentarios').value = ticketId;
    document.getElementById('ticketFolioComentarios').textContent = '#' + folio;

    const ta = document.getElementById('nuevoComentario');
    if (ta) { ta.value = ''; ta._mentions = []; }

    setCmntTipo('comentario', document.querySelector('.cmnt-tab[data-tipo="comentario"]'));
    removeCmntFile();
    cargarComentarios(ticketId);

    const modal = document.getElementById('comentariosModal');
    modal.classList.add('show');
    modal.style.display = 'block';
    document.body.classList.add('modal-open');

    if (!document.getElementById('comentariosBackdrop')) {
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'comentariosBackdrop';
        document.body.appendChild(backdrop);
    }
}

function cargarComentarios(ticketId) {
    const lista = document.getElementById('listaComentarios');
    lista.innerHTML = `<div class="cmnt-empty"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Cargando...</p></div>`;

    fetch('<?= Url::to(['/tickets/obtener-comentarios']) ?>?ticket_id=' + ticketId)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                mostrarComentarios(data.comentarios);
                const badge = document.getElementById('cmntCountBadge');
                if (badge) badge.textContent = data.comentarios.length + (data.comentarios.length === 1 ? ' comentario' : ' comentarios');
            } else {
                lista.innerHTML = `<div class="alert alert-danger m-3">Error al cargar comentarios</div>`;
            }
        })
        .catch(() => {
            lista.innerHTML = `<div class="alert alert-danger m-3">Error de conexión</div>`;
        });
}

function getAvatarColor(email) {
    const colors = ['#8BA590','#6d8f73','#5b7a61','#4a6950','#3a5840',
                    '#a8c4ad','#7fb08a','#6a9975','#5d8868','#4f7659'];
    let hash = 0;
    for (let i = 0; i < (email||'').length; i++) hash = email.charCodeAt(i) + ((hash << 5) - hash);
    return colors[Math.abs(hash) % colors.length];
}

function getInitials(nombre, email) {
    if (nombre && nombre.trim()) {
        const parts = nombre.trim().split(/\s+/);
        return (parts[0][0] + (parts[1] ? parts[1][0] : '')).toUpperCase();
    }
    return (email || '?')[0].toUpperCase();
}

function mostrarComentarios(comentarios) {
    const lista = document.getElementById('listaComentarios');

    if (!comentarios.length) {
        lista.innerHTML = `<div class="cmnt-empty"><i class="fas fa-comment-slash"></i><p>Sin comentarios aún. ¡Sé el primero!</p></div>`;
        return;
    }

    lista.innerHTML = comentarios.map(c => {
        const initials  = getInitials(c.nombre, c.usuario);
        const avatarClr = getAvatarColor(c.usuario);
        const badgeMap  = { comentario:'badge-comentario', nota_interna:'badge-nota_interna', solucion:'badge-solucion' };
        const labelMap  = { comentario:'💬 Comentario', nota_interna:'📝 Nota interna', solucion:'✅ Solución' };

        let archivoHtml = '';
        if (c.archivo) {
            if (c.esImagen) {
                archivoHtml = `<div class="cmnt-archivo">
                    <img src="${escapeHtml(c.archivo)}" class="cmnt-img-preview"
                         onclick="openLightbox('${escapeHtml(c.archivo)}')"
                         alt="imagen adjunta" loading="lazy">
                </div>`;
            } else {
                const filename = c.archivo.split('/').pop();
                archivoHtml = `<div class="cmnt-archivo">
                    <a href="${escapeHtml(c.archivo)}" target="_blank" class="cmnt-file-link">
                        <i class="fas fa-file-download"></i> ${escapeHtml(filename)}
                    </a>
                </div>`;
            }
        }

        return `<div class="cmnt-item">
            <div class="cmnt-avatar" style="background:${avatarClr};">${initials}</div>
            <div class="cmnt-bubble tipo-${c.tipo}">
                <div class="cmnt-meta">
                    <span class="cmnt-author">${escapeHtml(c.nombre || c.usuario)}</span>
                    <span class="cmnt-badge ${badgeMap[c.tipo]||'badge-comentario'}">${labelMap[c.tipo]||c.tipo}</span>
                    <span class="cmnt-fecha"><i class="far fa-clock"></i> ${escapeHtml(c.fecha)}</span>
                </div>
                <p class="cmnt-texto">${renderMentions(c.comentario)}</p>
                ${archivoHtml}
            </div>
        </div>`;
    }).join('');

    lista.scrollTop = lista.scrollHeight;
}

function renderMentions(text) {
    const safe = escapeHtml(text || '');
    return safe.replace(/@\[email:([^\]]+)\]/gi, (m, email) => {
        email = (email || '').trim().toLowerCase();
        const user = (window.WINTICK_USERS || []).find(u => (u.email || '').toLowerCase() === email);
        const nombre = user?.primerNombre || (user?.nombre ? user.nombre.split(/\s+/)[0] : email.split('@')[0]);
        return `<span class="cmnt-mention-tag">@${escapeHtml(nombre)}</span>`;
    });
}

function setCmntTipo(tipo, btn) {
    document.getElementById('tipoComentario').value = tipo;
    document.querySelectorAll('.cmnt-tab').forEach(t => {
        t.className = 'cmnt-tab';
    });
    if (btn) btn.classList.add('active-' + tipo);
}

// ── Upload helpers ──────────────────────────────────────────────────────────
function handleCmntFileSelect(input) {
    const file = input.files[0];
    if (!file) return;

    const preview   = document.getElementById('cmntUploadPreview');
    const previewImg = document.getElementById('cmntPreviewImg');
    const fileIcon  = document.getElementById('cmntFileIconPreview');
    const fileName  = document.getElementById('cmntFileName');
    const fileSize  = document.getElementById('cmntFileSize');

    fileName.textContent = file.name;
    fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
    preview.style.display = 'flex';

    const imageTypes = ['image/jpeg','image/png','image/gif','image/webp'];
    if (imageTypes.includes(file.type)) {
        const reader = new FileReader();
        reader.onload = e => { previewImg.src = e.target.result; previewImg.style.display = 'block'; fileIcon.style.display = 'none'; };
        reader.readAsDataURL(file);
    } else {
        previewImg.style.display = 'none';
        fileIcon.style.display = 'block';
    }
}

function removeCmntFile() {
    const input = document.getElementById('cmntArchivoInput');
    if (input) input.value = '';
    document.getElementById('cmntUploadPreview').style.display = 'none';
    document.getElementById('cmntPreviewImg').src = '';
}

// ── Lightbox ────────────────────────────────────────────────────────────────
function openLightbox(src) {
    document.getElementById('imgLightboxSrc').src = src;
    document.getElementById('imgLightbox').classList.add('open');
}
function closeLightbox() {
    document.getElementById('imgLightbox').classList.remove('open');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

// ── Enviar comentario (multipart para soportar archivo) ─────────────────────
function agregarComentario() {
    const ticketId        = document.getElementById('ticketIdComentarios').value;
    const comentarioVis   = document.getElementById('nuevoComentario').value;
    const comentario      = (window.buildCommentPayload ? window.buildCommentPayload(comentarioVis) : comentarioVis.trim());
    const tipo            = document.getElementById('tipoComentario').value;
    const archivoInput    = document.getElementById('cmntArchivoInput');
    const tieneArchivo    = archivoInput && archivoInput.files.length > 0;

    if (!comentario || comentario.trim() === '') {
        alert('Escribe un comentario antes de enviar.');
        return;
    }

    const btn = document.getElementById('btnEnviarComentario');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enviando...'; }

    const formData = new FormData();
    formData.append('ticket_id', ticketId);
    formData.append('comentario', comentario);
    formData.append('tipo', tipo);
    if (tieneArchivo) formData.append('archivo', archivoInput.files[0]);

    fetch('<?= Url::to(['/tickets/agregar-comentario']) ?>', {
        method: 'POST',
        body: formData
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const ta = document.getElementById('nuevoComentario');
                if (ta) { ta.value = ''; ta._mentions = []; }
                removeCmntFile();
                cargarComentarios(ticketId);
                updateCommentBadge(ticketId);
            } else {
                alert('Error: ' + (data.message || JSON.stringify(data.errors)));
            }
        })
        .catch(err => alert('Error de conexión: ' + err.message))
        .finally(() => {
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Enviar'; }
        });
}

function closeComentariosModal() {
    const modal = document.getElementById('comentariosModal');
    modal.classList.remove('show');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
    const backdrop = document.getElementById('comentariosBackdrop');
    if (backdrop) backdrop.remove();
    removeCmntFile();
}

function getTipoLabel(tipo) {
    const labels = { comentario:'💬 Comentario', nota_interna:'📝 Nota Interna', solucion:'✅ Solución' };
    return labels[tipo] || tipo;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
}

function updateCommentBadge(ticketId) {
    fetch('<?= Url::to(['contar-comentarios']) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
        },
        body: JSON.stringify({ ticket_id: ticketId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const button = document.querySelector('.comment-btn-' + ticketId);
                if (button) {
                    const container = button.parentElement;
                    let dot = container.querySelector('.badge-count-' + ticketId);

                    if (data.count > 0) {
                        if (!dot) {
                            dot = document.createElement('span');
                            dot.className = 'tkt-cmnt-dot badge-count-' + ticketId;
                            container.appendChild(dot);
                        }
                        dot.style.display = 'block';
                        // También actualizar el badge de texto en la celda de asunto
                        const row = button.closest('tr');
                        if (row) {
                            let textBadge = row.querySelector('.badge-count-txt-' + ticketId);
                            if (!textBadge) {
                                textBadge = document.createElement('span');
                                textBadge.className = 'tkt-cmnt-badge badge-count-txt-' + ticketId;
                                const sub = row.querySelector('.tkt-asunto-sub');
                                if (sub) sub.appendChild(textBadge);
                            }
                            textBadge.innerHTML = '<i class="fas fa-comment-dots"></i> ' + data.count;
                            textBadge.style.display = 'inline-flex';
                        }
                    } else {
                        if (dot) dot.style.display = 'none';
                        const row = button.closest('tr');
                        if (row) {
                            const textBadge = row.querySelector('.badge-count-txt-' + ticketId);
                            if (textBadge) textBadge.style.display = 'none';
                        }
                    }
                }
            }
        })
        .catch(error => console.error('Error actualizando comentarios:', error));
}

// ========================================
// DOM READY
// ========================================
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('globalSearch');
    const clearButton = document.getElementById('clearSearch');
    const filterBtn = document.getElementById('compactFilterBtn');
    const filterMenu = document.getElementById('compactFilterMenu');
    const horaFinalizoInput = document.getElementById('horaFinalizo');
    const addRowsBtn = document.getElementById('addMoreRows');

    buildRowsCache();
    document.querySelectorAll('.flatpickr-datetime').forEach(initializeFlatpickr);

    const initialSaveBtn = document.querySelector('.new-row .saveRow');
    if (initialSaveBtn) initialSaveBtn.addEventListener('click', function () {
        saveTicket(this.closest('tr'));
    });

    const initialDeleteBtn = document.querySelector('.new-row .deleteRow');
    if (initialDeleteBtn) initialDeleteBtn.addEventListener('click', function () {
        const row = this.closest('tr');
        row.querySelectorAll('input, textarea, select').forEach(field => {
            if (!field.classList.contains('folio')) field.value = '';
        });
        loadNextFolio(row.querySelector('.folio'));
    });

    if (searchInput) searchInput.addEventListener('input', function (e) {
        const query = e.target.value.trim();
        clearButton?.classList.toggle('active', !!query);
        debouncedSearch(query);
    });

    if (clearButton) clearButton.addEventListener('click', function () {
        searchInput.value = '';
        clearButton.classList.remove('active');
        performSearch('');
        searchInput.focus();
    });

    if (filterBtn && filterMenu) {
        filterBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            filterMenu.classList.toggle('show');
            filterBtn.classList.toggle('active');
        });

        document.addEventListener('click', function (e) {
            if (!filterMenu.contains(e.target) && !filterBtn.contains(e.target)) {
                filterMenu.classList.remove('show');
                filterBtn.classList.remove('active');
            }
        });
    }

    if (horaFinalizoInput) {
        horaFinalizoInput.addEventListener('change', calcularTiempoEfectivo);
        horaFinalizoInput.addEventListener('input', calcularTiempoEfectivo);
    }

    if (addRowsBtn) {
        addRowsBtn.addEventListener('click', function () {
            const tableBody = document.getElementById('tableBody');
            const templateRow = document.querySelector('.new-row');
            const newRow = templateRow.cloneNode(true);

            newRow.querySelectorAll('input, textarea, select').forEach(field => {
                if (!field.classList.contains('folio')) field.value = '';
                if (field.tagName === 'SELECT' && !field.classList.contains('estado')) field.selectedIndex = 0;
            });

            loadNextFolio(newRow.querySelector('.folio'));

            newRow.querySelectorAll('.flatpickr-datetime').forEach(function (element) {
                if (element._flatpickr) element._flatpickr.destroy();
                initializeFlatpickr(element);
            });

            const saveBtn = newRow.querySelector('.saveRow');
            const deleteBtn = newRow.querySelector('.deleteRow');
            const clienteSelect = newRow.querySelector('.cliente');

            saveBtn.addEventListener('click', function () { saveTicket(newRow); });
            deleteBtn.addEventListener('click', function () { if (confirm('¿Eliminar fila?')) newRow.remove(); });
            clienteSelect.addEventListener('change', function () { loadClienteData(this); });

            tableBody.appendChild(newRow);
        });
    }

    loadNextFolio(document.querySelector('.new-row .folio'));

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl, {
        trigger: 'hover',
        delay: { show: 300, hide: 100 }
    }));
});

// ========================================
// DOBLE CLICK EN FILA -> SWEETALERT
// ========================================
function getStatusClass(estado) {
    const e = (estado || '').toUpperCase();
    if (e === 'ABIERTO')    return 'swal-status-abierto';
    if (e === 'PROGRAMADO') return 'swal-status-programado';
    if (e === 'EN PROCESO') return 'swal-status-en-proceso';
    if (e === 'CONTPAQi')  return 'swal-status-contpaqi';
    if (e === 'CERRADO')    return 'swal-status-cerrado';
    return '';
}

function getPriorityClass(prioridad) {
    const p = (prioridad || '').toUpperCase();
    if (p === 'ALTA') return 'swal-priority-alta';
    if (p === 'MEDIA') return 'swal-priority-media';
    if (p === 'BAJA') return 'swal-priority-baja';
    return '';
}

function getCriticidadClass(criticidad) {
    const c = (criticidad || '').toUpperCase().trim();
    if (c === 'URGENTE') return 'swal-criticidad-urgente';
    if (c === 'MEDIA') return 'swal-criticidad-media';
    if (c === 'BAJA' || c === 'BAJO') return 'swal-criticidad-baja';
    return '';
}

// ── Helpers para el popup de doble-click ──────────────────────────────────────
function buildSelectOptions(list, selectedId) {
    return list.map(item =>
        `<option value="${item.id}" ${item.id == selectedId ? 'selected' : ''}>${escapeHtml(item.nombre)}</option>`
    ).join('');
}
function buildUserOptions(selectedId) {
    return window.WINTICK_USERS.map(u =>
        `<option value="${u.id}" ${u.id == selectedId ? 'selected' : ''}>${escapeHtml(u.nombre)} (${escapeHtml(u.email)})</option>`
    ).join('');
}

// ═══════════════════════════════════════════
// TICKET DRAWER
// ═══════════════════════════════════════════

const DRAWER_CMNT_URL   = '<?= Url::to(['/tickets/obtener-comentarios']) ?>';
const DRAWER_SEND_URL   = '<?= Url::to(['/tickets/agregar-comentario']) ?>';
const DRAWER_SOL_URL    = '<?= \yii\helpers\Url::to(['/tickets/save-solution']) ?>';
const DRAWER_CSRF       = '<?= Yii::$app->request->getCsrfToken() ?>';

let _drawerTicketId  = null;
let _drawerData      = {};
let _drawerCmntTipo  = 'comentario';
let _drawerTab       = 'conv';
let _allComments     = [];
let _drawerPendingFile = null;

/* ── Abrir drawer ────────────────────────── */
function openDrawer(d) {
    _drawerTicketId = d.id || d.ticketId;
    _drawerData     = d;

    // Header
    document.getElementById('d-folio').textContent       = '#' + (d.folio || '');
    document.getElementById('d-asignado-label').textContent = d.asignadoA || 'Sin asignar';
    document.getElementById('d-reporta-label').textContent  = d.usuarioReporta ? 'reporte ' + d.usuarioReporta : '';

    // Estado badge
    const eBadge = document.getElementById('d-estado-badge');
    const eKey   = (d.estado || '').toLowerCase().replace(/\s+/g, '-');
    eBadge.className  = 'd-estado-badge ' + eKey;
    eBadge.textContent = d.estado || '';

    // Prioridad badge
    const pBadge = document.getElementById('d-prioridad-badge');
    pBadge.className  = 'd-prioridad-badge ' + (d.prioridad || '').toLowerCase();
    pBadge.textContent = d.prioridad || '';

    // Botón solución: solo si no tiene solución
    document.getElementById('d-btn-sol').style.display = (d.tieneSolucion !== '1') ? '' : 'none';

    // Meta
    document.getElementById('d-cliente').textContent          = d.cliente || '-';
    document.getElementById('d-sistema').textContent          = d.sistema || '-';
    document.getElementById('d-servicio').textContent         = d.servicio || '-';
    document.getElementById('d-tiempo-efectivo').textContent  = d.tiempoEfectivo || '-';
    document.getElementById('d-programada').textContent       = d.horaProgramada || '-';
    document.getElementById('d-inicio').textContent           = d.horaInicio || '-';

    // Descripción — siempre completa
    const descEl = document.getElementById('d-descripcion-text');
    descEl.textContent = d.descripcion || '';
    document.getElementById('d-desc-more-btn').style.display = 'none';

    // Pestaña Solución
    renderDrawerSolucion(d.solucion || '');

    // Modo vista
    drawerShowView();

    // Cargar comentarios
    loadDrawerComments(_drawerTicketId);

    // Mostrar drawer
    const drawer = document.getElementById('ticketDrawer');
    drawer.classList.remove('closed');
    drawer.classList.add('open');
    document.body.classList.add('drawer-open');
}

/* ── Cerrar drawer ───────────────────────── */
function closeDrawer() {
    const drawer = document.getElementById('ticketDrawer');
    drawer.classList.remove('open');
    drawer.classList.add('closed');
    document.body.classList.remove('drawer-open');
    _drawerTicketId = null;
}

/* ── Modos del panel ─────────────────────── */
function drawerShowView() {
    document.getElementById('drawer-view-mode').style.display = 'flex';
    document.getElementById('drawer-edit-mode').style.display = 'none';
    document.getElementById('drawer-sol-mode').style.display  = 'none';
}

function drawerShowEdit() {
    const d = _drawerData;
    document.getElementById('de-Estado').value          = d.estado || '';
    document.getElementById('de-Prioridad').value       = d.prioridad || '';
    document.getElementById('de-Usuario_reporta').value = d.usuarioReporta || '';
    document.getElementById('de-HoraProgramada').value  = d.horaProgramadaRaw || '';
    document.getElementById('de-HoraInicio').value      = d.horaInicioRaw || '';
    document.getElementById('de-Descripcion').value     = d.descripcion || '';
    document.getElementById('de-Asignado_a').innerHTML  = '<option value="">Sin asignar</option>' + buildUserOptions(d.asignadoId);
    document.getElementById('de-Cliente_id').innerHTML  = '<option value="">Sin cliente</option>'  + buildSelectOptions(window.WINTICK_CLIENTES, d.clienteId);
    document.getElementById('de-Sistema_id').innerHTML  = '<option value="">Sin sistema</option>'  + buildSelectOptions(window.WINTICK_SISTEMAS, d.sistemaId);
    document.getElementById('de-Servicio_id').innerHTML = '<option value="">Sin servicio</option>' + buildSelectOptions(window.WINTICK_SERVICIOS, d.servicioId);

    document.getElementById('drawer-view-mode').style.display = 'none';
    document.getElementById('drawer-edit-mode').style.display = 'flex';
    document.getElementById('drawer-sol-mode').style.display  = 'none';
}

function drawerShowSol() {
    const d = _drawerData;
    const iniRawForSol = d.horaInicioRaw || d.horaProgramadaRaw || '';
    const iniLabelForSol = d.horaInicio && d.horaInicio !== '-' ? d.horaInicio : (d.horaProgramada || '-');
    document.getElementById('ds-label-inicio').textContent = iniLabelForSol;
    document.getElementById('ds-label-fin').textContent    = '—';
    document.getElementById('ds-badge-tiempo').textContent = 'Sin calcular';
    document.getElementById('ds-HoraFinalizo').value       = '';
    document.getElementById('ds-TiempoEfectivo').value     = '';
    document.getElementById('ds-Solucion').value           = '';

    document.getElementById('ds-HoraFinalizo').oninput = function() {
        const fin = new Date(this.value);
        document.getElementById('ds-label-fin').textContent = this.value ? fin.toLocaleString('es-MX') : '—';
        if (iniRawForSol && this.value) {
            const ini = new Date(iniRawForSol);
            const diffMin = Math.round((fin - ini) / 60000);
            if (diffMin > 0) {
                const h = Math.floor(diffMin / 60), m = diffMin % 60;
                document.getElementById('ds-TiempoEfectivo').value = h + '.' + String(m).padStart(2,'0');
                document.getElementById('ds-badge-tiempo').textContent = h + 'h ' + m + 'min';
            }
        }
    };

    document.getElementById('drawer-view-mode').style.display = 'none';
    document.getElementById('drawer-edit-mode').style.display = 'none';
    document.getElementById('drawer-sol-mode').style.display  = 'flex';
}

/* ── Tab switching ───────────────────────── */
function switchDTab(btn, tab) {
    document.querySelectorAll('.d-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    _drawerTab = tab;
    document.querySelectorAll('.dtab-pane').forEach(p => p.classList.remove('active'));
    const pane = document.getElementById('dtab-' + tab);
    if (pane) pane.classList.add('active');
    if (tab === 'conv' || tab === 'notas') renderDrawerCommentsByType();
}

/* ── Cargar comentarios ──────────────────── */
function loadDrawerComments(ticketId) {
    ['d-cmnt-conv','d-cmnt-notas'].forEach(id => {
        document.getElementById(id).innerHTML = '<div class="d-cmnt-loading"><i class="fas fa-spinner fa-spin"></i></div>';
    });
    fetch(DRAWER_CMNT_URL + '?ticket_id=' + ticketId)
        .then(r => r.json())
        .then(data => {
            _allComments = data.success ? data.comentarios : [];
            renderDrawerCommentsByType();
        })
        .catch(() => {
            document.getElementById('d-cmnt-conv').innerHTML = '<div class="d-cmnt-empty">Error al cargar comentarios</div>';
        });
}

function renderDrawerCommentsByType() {
    renderDCmntList('d-cmnt-conv',   _allComments.filter(c => c.tipo === 'comentario'));
    renderDCmntList('d-cmnt-notas',  _allComments.filter(c => c.tipo === 'nota_interna'));
}

function renderDCmntList(containerId, items) {
    const el = document.getElementById(containerId);
    if (!el) return;
    if (!items.length) {
        el.innerHTML = '<div class="d-cmnt-empty"><i class="fas fa-comment-slash" style="font-size:20px;opacity:.3;display:block;margin-bottom:8px;"></i>Sin registros</div>';
        return;
    }
    el.innerHTML = items.map(c => {
        const nombre = (c.nombre || c.usuario || 'Usuario');
        const email  = (c.usuario || '');
        const col    = getAvatarColor(email);
        const ini    = nombre.charAt(0).toUpperCase();

        let tipoTag = '';
        if (c.tipo === 'nota_interna') {
            if (c.destinatarioId) {
                const destNombre = c.destinatarioNombre || 'alguien';
                tipoTag = `<span class="d-cmnt-tipo-tag nota p2p" style="display:inline-flex;align-items:center;gap:3px;"><i class="fas fa-lock" style="font-size:9px;"></i> Privado con ${escapeHtml(destNombre)}</span>`;
            } else {
                tipoTag = `<span class="d-cmnt-tipo-tag nota">Nota interna</span>`;
            }
        } else if (c.tipo === 'solucion') {
            tipoTag = `<span class="d-cmnt-tipo-tag solucion">Solución</span>`;
        }

        const avatarHtml = c.avatar
            ? `<img src="${escapeHtml(c.avatar)}" class="d-cmnt-avatar" style="object-fit:cover;padding:0;" alt="${escapeHtml(ini)}">`
            : `<div class="d-cmnt-avatar" style="background:${escapeHtml(col)}">${escapeHtml(ini)}</div>`;

        const isP2P = c.tipo === 'nota_interna' && c.destinatarioId;
        // Archivo adjunto
        let adjuntoHtml = '';
        if (c.archivo) {
            const imageExts = ['jpg','jpeg','png','gif','webp'];
            const ext = c.archivo.split('.').pop().toLowerCase();
            if (c.esImagen || imageExts.includes(ext)) {
                adjuntoHtml = `<div class="cmnt-archivo">
                    <img src="${escapeHtml(c.archivo)}" class="cmnt-img-preview"
                         onclick="openLightbox('${escapeHtml(c.archivo)}')"
                         alt="imagen adjunta" loading="lazy">
                </div>`;
            } else {
                const fname = decodeURIComponent(c.archivo.split('/').pop());
                adjuntoHtml = `<div class="cmnt-archivo">
                    <a href="${escapeHtml(c.archivo)}" download class="cmnt-file-link" target="_blank">
                        <i class="fas fa-file-download"></i> ${escapeHtml(fname)}
                    </a>
                </div>`;
            }
        }

        // Ocultar el texto si es solo el nombre del archivo (enviado sin texto)
        const textoMostrar = (c.archivo && c.comentario && c.comentario === decodeURIComponent(c.archivo.split('/').pop().replace(/^cmnt_[a-f0-9.]+\./, '')))
            ? '' : (c.comentario || '');

        return `
            <div class="d-cmnt-item">
                ${avatarHtml}
                <div class="d-cmnt-bubble${c.tipo === 'nota_interna' ? ' nota' : c.tipo === 'solucion' ? ' solucion' : ''}${isP2P ? ' p2p' : ''}">
                    <div class="d-cmnt-meta">
                        <span class="d-cmnt-autor">${escapeHtml(nombre)}</span>
                        ${tipoTag}
                        <span class="d-cmnt-fecha">${escapeHtml(c.fecha || '')}</span>
                    </div>
                    ${textoMostrar ? `<div class="d-cmnt-text">${renderMentions(textoMostrar)}</div>` : ''}
                    ${adjuntoHtml}
                </div>
            </div>`;
    }).join('');
}

/* ── Renderizar solución ─────────────────── */
function renderDrawerSolucion(solucion) {
    const el = document.getElementById('d-sol-content');
    if (!el) return;
    el.innerHTML = solucion
        ? `<div class="d-sol-content" style="padding:16px;font-size:13px;color:var(--text-2);line-height:1.6;white-space:pre-wrap;">${escapeHtml(solucion)}</div>`
        : '<div class="d-sol-empty"><i class="fas fa-lightbulb" style="font-size:20px;opacity:.3;display:block;margin-bottom:8px;"></i>Sin solución registrada aún.</div>';
}

/* ── Toggle descripción larga ────────────── */
function toggleDescMore() {
    const el  = document.getElementById('d-descripcion-text');
    const btn = document.getElementById('d-desc-more-btn');
    el.classList.toggle('expanded');
    btn.textContent = el.classList.contains('expanded') ? 'Ver menos' : 'Ver más';
}

/* ── Tipo de comentario (compose) ────────── */
function setDTipo(btn, tipo) {
    document.querySelectorAll('.dc-tipo-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    _drawerCmntTipo = tipo;
    document.getElementById('d-tipo-val').value = tipo;
    const ta = document.getElementById('d-nuevo-cmnt');
    const hints = { comentario:'Responder al cliente...', nota_interna:'Mensaje privado para alguien del equipo...', solucion:'Describe la solución...' };
    ta.placeholder = hints[tipo] || 'Escribe aquí...';
    const destWrap = document.getElementById('d-destinatario-wrap');
    if (destWrap) destWrap.style.display = tipo === 'nota_interna' ? 'block' : 'none';
    if (tipo !== 'nota_interna') {
        const sel = document.getElementById('d-destinatario-id');
        if (sel) sel.value = '';
    }
}

/* ── Enviar comentario desde drawer ─────── */
function enviarDComentario() {
    const texto = (document.getElementById('d-nuevo-cmnt').value || '').trim();
    if (!texto && !_drawerPendingFile) return;
    if (!_drawerTicketId) return;

    const btn = document.getElementById('d-btn-send');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    const textoFinal = texto
        ? (window.buildDrawerPayload ? window.buildDrawerPayload(texto) : texto)
        : (_drawerPendingFile ? _drawerPendingFile.name : '');

    const fd = new FormData();
    fd.append('ticket_id', _drawerTicketId);
    fd.append('comentario', textoFinal);
    fd.append('tipo', _drawerCmntTipo);
    if (_drawerCmntTipo === 'nota_interna') {
        const destSel = document.getElementById('d-destinatario-id');
        if (destSel && destSel.value) fd.append('destinatario_id', destSel.value);
    }
    if (_drawerPendingFile) fd.append('archivo', _drawerPendingFile);

    fetch(DRAWER_SEND_URL, { method:'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('d-nuevo-cmnt').value = '';
                const destSel = document.getElementById('d-destinatario-id');
                if (destSel) destSel.value = '';
                clearDrawerAttach();
                loadDrawerComments(_drawerTicketId);
                updateCommentBadge(_drawerTicketId);
            } else {
                alert('Error: ' + (data.message || 'No se pudo enviar.'));
            }
        })
        .catch(e => alert('Error de conexión'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar';
        });
}

/* ── Drag & Drop / Paste / Clip para el compose del drawer ─────── */
(function initDrawerAttach() {
    const ta        = document.getElementById('d-nuevo-cmnt');
    const fileInput = document.getElementById('dc-file-input');
    const preview   = document.getElementById('dc-attach-preview');
    if (!ta || !fileInput || !preview) return;

    /* Selección por botón clip */
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) setDrawerAttach(fileInput.files[0]);
        fileInput.value = '';
    });

    /* Drag over */
    ['dragenter','dragover'].forEach(ev => {
        ta.addEventListener(ev, e => { e.preventDefault(); ta.classList.add('drag-over'); });
    });
    ['dragleave','dragend'].forEach(ev => {
        ta.addEventListener(ev, () => ta.classList.remove('drag-over'));
    });
    ta.addEventListener('drop', e => {
        e.preventDefault();
        ta.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file) setDrawerAttach(file);
    });

    /* Ctrl+V / paste */
    ta.addEventListener('paste', e => {
        const items = e.clipboardData?.items;
        if (!items) return;
        for (const item of items) {
            if (item.kind === 'file') {
                e.preventDefault();
                setDrawerAttach(item.getAsFile());
                return;
            }
        }
    });
})();

function setDrawerAttach(file) {
    _drawerPendingFile = file;
    const preview = document.getElementById('dc-attach-preview');
    preview.innerHTML = '';

    const item = document.createElement('div');
    item.className = 'dc-attach-item';

    const isImage = file.type.startsWith('image/');
    if (isImage) {
        const img = document.createElement('img');
        img.className = 'dc-attach-img';
        img.src = URL.createObjectURL(file);
        img.onclick = () => window.open(img.src, '_blank');
        item.appendChild(img);
    } else {
        const info = document.createElement('div');
        info.className = 'dc-attach-file';
        const ext = file.name.split('.').pop().toUpperCase();
        info.innerHTML = `<i class="fas fa-file"></i><span>${file.name}</span><small style="color:var(--text-muted)">${(file.size/1024).toFixed(0)} KB</small>`;
        item.appendChild(info);
    }

    const removeBtn = document.createElement('button');
    removeBtn.className = 'dc-attach-remove';
    removeBtn.innerHTML = '×';
    removeBtn.onclick = clearDrawerAttach;
    item.appendChild(removeBtn);

    preview.appendChild(item);
}

function clearDrawerAttach() {
    _drawerPendingFile = null;
    const preview = document.getElementById('dc-attach-preview');
    if (preview) preview.innerHTML = '';
}

/* ── Guardar edición desde drawer ───────── */
async function saveDrawerEdit() {
    const btn = document.getElementById('de-save-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    const payload = {
        id:              _drawerTicketId,
        Estado:          document.getElementById('de-Estado').value,
        Prioridad:       document.getElementById('de-Prioridad').value,
        Asignado_a:      document.getElementById('de-Asignado_a').value || null,
        Usuario_reporta: document.getElementById('de-Usuario_reporta').value,
        Cliente_id:      document.getElementById('de-Cliente_id').value || null,
        Sistema_id:      document.getElementById('de-Sistema_id').value || null,
        Servicio_id:     document.getElementById('de-Servicio_id').value || null,
        HoraProgramada:  document.getElementById('de-HoraProgramada').value || null,
        HoraInicio:      document.getElementById('de-HoraInicio').value || null,
        Descripcion:     document.getElementById('de-Descripcion').value,
    };
    try {
        const res  = await fetch(window.URL_QUICK_UPDATE, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':DRAWER_CSRF}, body: JSON.stringify(payload) });
        const data = await res.json();
        if (data.success) {
            Swal.fire({ icon:'success', title:'¡Guardado!', toast:true, position:'top-end', showConfirmButton:false, timer:1800, timerProgressBar:true });
            setTimeout(() => location.reload(), 1200);
        } else {
            Swal.fire({ icon:'error', title:'Error al guardar', text: JSON.stringify(data.errors || data.message), confirmButtonColor: 'var(--accent)' });
            btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Guardar cambios';
        }
    } catch { Swal.fire({ icon:'error', title:'Error de conexión', confirmButtonColor:'var(--accent)' }); btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Guardar cambios'; }
}

/* ── Guardar solución desde drawer ──────── */
async function saveDrawerSolucion() {
    const solucion   = (document.getElementById('ds-Solucion').value || '').trim();
    const horaFin    = document.getElementById('ds-HoraFinalizo').value;
    const tiempoEf   = (document.getElementById('ds-TiempoEfectivo').value || '').trim();
    if (!solucion || !horaFin || !tiempoEf) {
        Swal.fire({ icon:'warning', title:'Faltan datos', text:'Completa todos los campos.', confirmButtonColor:'var(--accent)' });
        return;
    }
    const btn = document.getElementById('ds-save-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    try {
        const res  = await fetch(DRAWER_SOL_URL, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':DRAWER_CSRF}, body: JSON.stringify({ id:_drawerTicketId, solucion, horaFinalizo:horaFin, tiempoEfectivo:tiempoEf }) });
        const data = await res.json();
        if (data.success) {
            Swal.fire({ icon:'success', title:'¡Solución guardada!', toast:true, position:'top-end', showConfirmButton:false, timer:1800, timerProgressBar:true });
            setTimeout(() => location.reload(), 1200);
        } else {
            Swal.fire({ icon:'error', title:'Error', text: data.message || 'No se pudo guardar.', confirmButtonColor:'var(--accent)' });
            btn.disabled = false; btn.innerHTML = '<i class="fas fa-check-circle"></i> Guardar solución';
        }
    } catch { Swal.fire({ icon:'error', title:'Error de conexión', confirmButtonColor:'var(--accent)' }); btn.disabled = false; btn.innerHTML = '<i class="fas fa-check-circle"></i> Guardar solución'; }
}

/* ── Botones del header del drawer ──────── */
document.getElementById('d-btn-edit').addEventListener('click', drawerShowEdit);
document.getElementById('d-btn-sol').addEventListener('click',  drawerShowSol);

/* ── Cerrar con Escape ───────────────────── */
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });

/* ── Dblclick en filas ───────────────────── */
document.querySelectorAll('tr.existing-row').forEach(row => {
    row.addEventListener('dblclick', function (e) {
        if (e.target.closest('button, a, select, input, textarea')) return;
        openDrawer(this.dataset);
        return; // ── todo lo demás es código legacy de SweetAlert, se conserva abajo pero no se ejecuta ──

        const d = this.dataset;

        const estadoClass = getStatusClass(d.estado);
        const prioridadClass = getPriorityClass(d.prioridad);
        const criticidadClass = getCriticidadClass(d.criticidad);
        const sinSolucion = d.tieneSolucion !== '1';

        const html = `
<style>
.swal-header-actions { position:absolute; top:12px; right:62px; display:flex; gap:10px; z-index:10; }
.swal-icon-btn {
    width:34px; height:34px; border-radius:8px; border:none;
    background:rgba(255,255,255,0.22); color:rgba(255,255,255,0.9); cursor:pointer; display:flex;
    align-items:center; justify-content:center; font-size:14px; transition:all 0.2s;
}
.swal-icon-btn:hover { background:rgba(255,255,255,0.38); transform:translateY(-1px); }
.swal2-actions { display:none!important; }
.swal2-close { display:none!important; }
.swal-icon-btn-sol { border-color:rgba(255,220,100,0.8); color:#fde68a; }
.swal-icon-btn-sol:hover { background:rgba(253,230,138,0.25); }
.swal-mode-bar {
    display:flex; gap:10px; align-items:center; justify-content:flex-end;
    padding:14px 20px 0; border-top:1px solid #e2e8f0; margin-top:16px;
}
.swal-mode-bar .btn-cancel-mode {
    padding:8px 18px; border-radius:7px; border:1px solid #cbd5e0; background:#fff;
    color:#475569; cursor:pointer; font-size:13px; font-weight:600; transition:all 0.2s;
}
.swal-mode-bar .btn-cancel-mode:hover { background:#f1f5f9; }
.swal-mode-bar .btn-save-mode {
    padding:8px 20px; border-radius:7px; border:none;
    background:linear-gradient(135deg,#A0BAA5,#8BA590); color:#fff;
    cursor:pointer; font-size:13px; font-weight:600; transition:all 0.2s;
    display:flex; align-items:center; gap:6px;
}
.swal-mode-bar .btn-save-mode:hover { opacity:0.88; transform:translateY(-1px); }
.swal-mode-bar .btn-save-mode:disabled { opacity:0.55; cursor:not-allowed; transform:none; }
.edit-field-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; padding:4px 0 8px; }
.edit-field-group { display:flex; flex-direction:column; gap:5px; }
.edit-field-group.full { grid-column:1/-1; }
.edit-field-group label { font-size:12px; font-weight:600; color:#64748b; }
.edit-field-group select,
.edit-field-group input,
.edit-field-group textarea {
    font-size:13px; padding:7px 10px; border:1px solid #dce8de; border-radius:7px;
    background:#f7fbf8; color:#1e293b; transition:border-color 0.2s;
}
.edit-field-group select:focus,
.edit-field-group input:focus,
.edit-field-group textarea:focus { outline:none; border-color:#A0BAA5; background:#fff; }
.sol-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.sol-tiempo-card {
    background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px;
    font-size:13px; display:flex; flex-direction:column; gap:6px;
}
.sol-tiempo-card .sol-tiempo-label { color:#64748b; font-size:11px; font-weight:600; }
.sol-tiempo-card .sol-tiempo-val { font-weight:700; color:#1e293b; }
.sol-badge-tiempo {
    display:inline-block; background:#dcfce7; color:#15803d; padding:4px 10px;
    border-radius:20px; font-size:12px; font-weight:700; margin-top:4px;
}
</style>
            <div class="swal-ticket-card">
                <div class="swal-ticket-header" style="position:relative">
                    <div class="swal-header-actions">
                        <button class="swal-icon-btn" id="swal-btn-edit" title="Editar ticket">
                            <i class="fas fa-pen"></i>
                        </button>
                        ${sinSolucion ? `<button class="swal-icon-btn swal-icon-btn-sol" id="swal-btn-sol" title="Registrar solución">
                            <i class="fas fa-check-double"></i>
                        </button>` : ''}
                    </div>
                    <div class="swal-ticket-title">
                        <i class="fas fa-ticket-alt"></i>
                        Ticket #${escapeHtml(d.folio || '')}
                    </div>
                    <p class="swal-ticket-subtitle">
                        Cliente: ${escapeHtml(d.cliente || 'No asignado')}
                    </p>
                </div>

                <!-- ── MODO VISTA ────────────────────────────── -->
                <div id="swal-mode-view" class="swal-ticket-body">
                    <div class="swal-ticket-grid">
                        <div class="swal-info-card">
                            <h3><i class="fas fa-info-circle"></i> Información General</h3>

                            <div class="swal-info-item">
                                <span class="swal-info-label">Usuario reporta</span>
                                <span class="swal-info-value">${escapeHtml(d.usuarioReporta || '-')}</span>
                            </div>

                            <div class="swal-info-item">
                                <span class="swal-info-label">Asignado a</span>
                                <span class="swal-info-value">${escapeHtml(d.asignadoA || '-')}</span>
                            </div>

                            <div class="swal-info-item">
                                <span class="swal-info-label">Estado</span>
                                <span class="swal-info-value">
                                    <span class="swal-status-badge ${estadoClass}">
                                        ${escapeHtml(d.estado || '-')}
                                    </span>
                                </span>
                            </div>

                            <div class="swal-info-item">
                                <span class="swal-info-label">Prioridad</span>
                                <span class="swal-info-value">
                                    <span class="swal-priority-badge ${prioridadClass}">
                                        ${escapeHtml(d.prioridad || '-')}
                                    </span>
                                </span>
                            </div>

                            <div class="swal-info-item">
                                <span class="swal-info-label">Criticidad</span>
                                <span class="swal-info-value">
                                    <span class="swal-criticidad-badge ${criticidadClass}">
                                        ${escapeHtml(d.criticidad || '-')}
                                    </span>
                                </span>
                            </div>
                        </div>

                        <div class="swal-info-card">
                            <h3><i class="fas fa-cogs"></i> Información del servicio</h3>

                            <div class="swal-info-item">
                                <span class="swal-info-label">Cliente</span>
                                <span class="swal-info-value">${escapeHtml(d.cliente || 'No asignado')}</span>
                            </div>

                            <div class="swal-info-item">
                                <span class="swal-info-label">Sistema</span>
                                <span class="swal-info-value">${escapeHtml(d.sistema || 'No asignado')}</span>
                            </div>

                            <div class="swal-info-item">
                                <span class="swal-info-label">Servicio</span>
                                <span class="swal-info-value">${escapeHtml(d.servicio || 'No asignado')}</span>
                            </div>
                        </div>
                    </div>

                    <div class="swal-times">
                        <div class="swal-section-title">
                            <i class="fas fa-clock"></i> Fechas y tiempos
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:8px;">
                            <div class="swal-info-item">
                                <span class="swal-info-label">Hora reporte</span>
                                <span class="swal-info-value">${escapeHtml(d.horaProgramada || '-')}</span>
                            </div>
                            <div class="swal-info-item">
                                <span class="swal-info-label">Hora inicio</span>
                                <span class="swal-info-value">${escapeHtml(d.horaInicio || '-')}</span>
                            </div>
                            <div class="swal-info-item">
                                <span class="swal-info-label">Hora finalizó</span>
                                <span class="swal-info-value">${escapeHtml(d.horaFinalizo || '-')}</span>
                            </div>
                            <div class="swal-info-item">
                                <span class="swal-info-label">Tiempo efectivo</span>
                                <span class="swal-info-value">${escapeHtml(d.tiempoEfectivo || '-')}</span>
                            </div>
                        </div>
                    </div>

                    <div class="swal-description">
                        <div class="swal-section-title">
                            <i class="fas fa-file-alt"></i> Descripción del problema
                        </div>
                        <div class="swal-description-text">
                            ${d.descripcion ? escapeHtml(d.descripcion) : '<span class="swal-empty">No hay descripción disponible</span>'}
                        </div>
                    </div>
                    ${d.solucion ? `<div class="swal-description" style="border-left:4px solid #A0BAA5; margin-top:10px;">
                        <div class="swal-section-title"><i class="fas fa-lightbulb"></i> Solución aplicada</div>
                        <div class="swal-description-text">${escapeHtml(d.solucion)}</div>
                    </div>` : ''}
                    <div style="display:flex;justify-content:center;padding:18px 0 4px;">
                        <button id="swal-view-close" style="padding:9px 32px;border-radius:8px;border:1px solid #dce8de;background:#f7fbf8;color:#5a7a60;font-weight:600;font-size:14px;cursor:pointer;transition:all 0.2s;">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>
                </div>

                <!-- ── MODO EDICIÓN ─────────────────────────── -->
                <div id="swal-mode-edit" style="display:none" class="swal-ticket-body">
                    <div class="edit-field-grid">
                        <div class="edit-field-group">
                            <label><i class="fas fa-circle-notch"></i> Estado</label>
                            <select id="edit-Estado">
                                <option value="ABIERTO" ${d.estado==='ABIERTO'?'selected':''}>Abierto</option>
                                <option value="PROGRAMADO" ${d.estado==='PROGRAMADO'?'selected':''}>Programado</option>
                                <option value="EN PROCESO" ${d.estado==='EN PROCESO'?'selected':''}>En Proceso</option>
                                <option value="CONTPAQi" ${d.estado==='CONTPAQi'?'selected':''}>CONTPAQi</option>
                            </select>
                        </div>
                        <div class="edit-field-group">
                            <label><i class="fas fa-exclamation-circle"></i> Prioridad</label>
                            <select id="edit-Prioridad">
                                <option value="ALTA" ${d.prioridad==='ALTA'?'selected':''}>Alta</option>
                                <option value="MEDIA" ${d.prioridad==='MEDIA'?'selected':''}>Media</option>
                                <option value="BAJA" ${d.prioridad==='BAJA'?'selected':''}>Baja</option>
                            </select>
                        </div>
                        <div class="edit-field-group">
                            <label><i class="fas fa-user"></i> Asignado a</label>
                            <select id="edit-Asignado_a">
                                <option value="">Sin asignar</option>
                                ${buildUserOptions(d.asignadoId)}
                            </select>
                        </div>
                        <div class="edit-field-group">
                            <label><i class="fas fa-user-tag"></i> Usuario reporta</label>
                            <input type="text" id="edit-Usuario_reporta" value="${escapeHtml(d.usuarioReporta||'')}">
                        </div>
                        <div class="edit-field-group">
                            <label><i class="fas fa-building"></i> Cliente</label>
                            <select id="edit-Cliente_id">
                                <option value="">Sin cliente</option>
                                ${buildSelectOptions(window.WINTICK_CLIENTES, d.clienteId)}
                            </select>
                        </div>
                        <div class="edit-field-group">
                            <label><i class="fas fa-desktop"></i> Sistema</label>
                            <select id="edit-Sistema_id">
                                <option value="">Sin sistema</option>
                                ${buildSelectOptions(window.WINTICK_SISTEMAS, d.sistemaId)}
                            </select>
                        </div>
                        <div class="edit-field-group">
                            <label><i class="fas fa-cogs"></i> Servicio</label>
                            <select id="edit-Servicio_id">
                                <option value="">Sin servicio</option>
                                ${buildSelectOptions(window.WINTICK_SERVICIOS, d.servicioId)}
                            </select>
                        </div>
                        <div class="edit-field-group">
                            <label><i class="fas fa-calendar-alt"></i> Hora reporte</label>
                            <input type="datetime-local" id="edit-HoraProgramada" value="${d.horaProgramadaRaw||''}">
                        </div>
                        <div class="edit-field-group">
                            <label><i class="fas fa-play-circle"></i> Hora inicio</label>
                            <input type="datetime-local" id="edit-HoraInicio" value="${d.horaInicioRaw||''}">
                        </div>
                        <div class="edit-field-group full">
                            <label><i class="fas fa-file-alt"></i> Descripción</label>
                            <textarea id="edit-Descripcion" rows="3">${escapeHtml(d.descripcion||'')}</textarea>
                        </div>
                    </div>
                    <div class="swal-mode-bar">
                        <button class="btn-cancel-mode" id="edit-cancel"><i class="fas fa-times"></i> Cancelar</button>
                        <button class="btn-save-mode" id="edit-save"><i class="fas fa-save"></i> Guardar cambios</button>
                    </div>
                </div>

                <!-- ── MODO SOLUCIÓN ─────────────────────────── -->
                <div id="swal-mode-sol" style="display:none" class="swal-ticket-body">
                    <div class="sol-grid">
                        <div class="sol-tiempo-card">
                            <div>
                                <div class="sol-tiempo-label">Hora de inicio</div>
                                <div class="sol-tiempo-val" id="sol-label-inicio">${escapeHtml(d.horaInicio||'-')}</div>
                            </div>
                            <div>
                                <div class="sol-tiempo-label">Hora de finalización</div>
                                <div class="sol-tiempo-val" id="sol-label-fin">—</div>
                            </div>
                            <div>
                                <div class="sol-tiempo-label">Tiempo efectivo</div>
                                <span class="sol-badge-tiempo" id="sol-badge-tiempo">Sin calcular</span>
                            </div>
                            <small style="color:#94a3b8;font-size:11px;">Se calcula automáticamente al elegir hora de finalización.</small>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <div class="edit-field-group">
                                <label><i class="fas fa-clock"></i> Hora de finalización</label>
                                <input type="datetime-local" id="sol-HoraFinalizo">
                            </div>
                            <div class="edit-field-group">
                                <label><i class="fas fa-hourglass-end"></i> Tiempo efectivo</label>
                                <input type="text" id="sol-TiempoEfectivo" placeholder="Auto-calculado...">
                            </div>
                        </div>
                    </div>
                    <div class="edit-field-group" style="margin-top:12px;">
                        <label><i class="fas fa-lightbulb"></i> Solución aplicada</label>
                        <textarea id="sol-Solucion" rows="4" placeholder="Describe la causa del problema y lo que hiciste para resolverlo..."></textarea>
                    </div>
                    <div class="swal-mode-bar">
                        <button class="btn-cancel-mode" id="sol-cancel"><i class="fas fa-times"></i> Cancelar</button>
                        <button class="btn-save-mode" id="sol-save"><i class="fas fa-check-circle"></i> Guardar solución</button>
                    </div>
                </div>

            </div>
        `;

        Swal.fire({
            html: html,
            width: 860,
            showConfirmButton: false,
            showCloseButton: false,
            focusConfirm: false,
            padding: 0,
            didOpen: (popup) => {
                const closeBtn = popup.querySelector('#swal-view-close');
                if (closeBtn) closeBtn.addEventListener('click', () => Swal.close());
                closeBtn && (closeBtn.onmouseenter = () => { closeBtn.style.background='#edf7ee'; closeBtn.style.borderColor='#A0BAA5'; });
                closeBtn && (closeBtn.onmouseleave = () => { closeBtn.style.background='#f7fbf8'; closeBtn.style.borderColor='#dce8de'; });
                const ticketId = d.id;

                // — Botón Editar —
                const btnEdit = popup.querySelector('#swal-btn-edit');
                const modeView = popup.querySelector('#swal-mode-view');
                const modeEdit = popup.querySelector('#swal-mode-edit');
                const modeSol  = popup.querySelector('#swal-mode-sol');

                function showView()  { modeView.style.display=''; modeEdit.style.display='none'; if(modeSol) modeSol.style.display='none'; }
                function showEdit()  { modeView.style.display='none'; modeEdit.style.display=''; if(modeSol) modeSol.style.display='none'; }
                function showSol()   { modeView.style.display='none'; if(modeEdit) modeEdit.style.display='none'; modeSol.style.display=''; }

                if (btnEdit) btnEdit.addEventListener('click', showEdit);

                popup.querySelector('#edit-cancel').addEventListener('click', showView);
                popup.querySelector('#edit-save').addEventListener('click', async function() {
                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

                    const payload = {
                        id: ticketId,
                        Estado:          popup.querySelector('#edit-Estado').value,
                        Prioridad:       popup.querySelector('#edit-Prioridad').value,
                        Asignado_a:      popup.querySelector('#edit-Asignado_a').value || null,
                        Usuario_reporta: popup.querySelector('#edit-Usuario_reporta').value,
                        Cliente_id:      popup.querySelector('#edit-Cliente_id').value || null,
                        Sistema_id:      popup.querySelector('#edit-Sistema_id').value || null,
                        Servicio_id:     popup.querySelector('#edit-Servicio_id').value || null,
                        HoraProgramada:  popup.querySelector('#edit-HoraProgramada').value || null,
                        HoraInicio:      popup.querySelector('#edit-HoraInicio').value || null,
                        Descripcion:     popup.querySelector('#edit-Descripcion').value,
                    };

                    try {
                        const res = await fetch(window.URL_QUICK_UPDATE, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>' },
                            body: JSON.stringify(payload)
                        });
                        const data = await res.json();
                        if (data.success) {
                            Swal.fire({ icon:'success', title:'¡Guardado!', toast:true, position:'top-end', showConfirmButton:false, timer:1800, timerProgressBar:true });
                            setTimeout(() => location.reload(), 1200);
                        } else {
                            Swal.fire({ icon:'error', title:'Error al guardar', text: JSON.stringify(data.errors || data.message), confirmButtonColor:'#8BA590' });
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-save"></i> Guardar cambios';
                        }
                    } catch(err) {
                        Swal.fire({ icon:'error', title:'Error de conexión', confirmButtonColor:'#8BA590' });
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-save"></i> Guardar cambios';
                    }
                });

                // — Botón Solución —
                const btnSol = popup.querySelector('#swal-btn-sol');
                if (btnSol && modeSol) {
                    btnSol.addEventListener('click', showSol);

                    // Calcular tiempo al cambiar hora finalización
                    popup.querySelector('#sol-HoraFinalizo').addEventListener('change', function() {
                        const fin = new Date(this.value);
                        const iniRaw = d.horaInicioRaw;
                        popup.querySelector('#sol-label-fin').textContent = this.value ? fin.toLocaleString('es-MX') : '—';
                        if (iniRaw && this.value) {
                            const ini = new Date(iniRaw);
                            const diffMin = Math.round((fin - ini) / 60000);
                            if (diffMin > 0) {
                                const h = Math.floor(diffMin / 60);
                                const m = diffMin % 60;
                                const te = h + '.' + String(m).padStart(2,'0');
                                popup.querySelector('#sol-TiempoEfectivo').value = te;
                                popup.querySelector('#sol-badge-tiempo').textContent = h + 'h ' + m + 'min';
                            }
                        }
                    });

                    popup.querySelector('#sol-cancel').addEventListener('click', showView);
                    popup.querySelector('#sol-save').addEventListener('click', async function() {
                        const btn = this;
                        const solucion    = popup.querySelector('#sol-Solucion').value.trim();
                        const horaFin     = popup.querySelector('#sol-HoraFinalizo').value;
                        const tiempoEf    = popup.querySelector('#sol-TiempoEfectivo').value.trim();

                        if (!solucion || !horaFin || !tiempoEf) {
                            Swal.fire({ icon:'warning', title:'Faltan datos', text:'Completa todos los campos antes de guardar.', confirmButtonColor:'#8BA590' });
                            return;
                        }

                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

                        try {
                            const res = await fetch('<?= \yii\helpers\Url::to(['/tickets/save-solution']) ?>', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>' },
                                body: JSON.stringify({ id: ticketId, solucion, horaFinalizo: horaFin, tiempoEfectivo: tiempoEf })
                            });
                            const data = await res.json();
                            if (data.success) {
                                Swal.fire({ icon:'success', title:'¡Solución guardada!', toast:true, position:'top-end', showConfirmButton:false, timer:1800, timerProgressBar:true });
                                setTimeout(() => location.reload(), 1200);
                            } else {
                                Swal.fire({ icon:'error', title:'Error', text: data.message || 'No se pudo guardar.', confirmButtonColor:'#8BA590' });
                                btn.disabled = false;
                                btn.innerHTML = '<i class="fas fa-check-circle"></i> Guardar solución';
                            }
                        } catch(err) {
                            Swal.fire({ icon:'error', title:'Error de conexión', confirmButtonColor:'#8BA590' });
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-check-circle"></i> Guardar solución';
                        }
                    });
                }
            }
        });
    });
});

// ========================================
// ✅ MENCIONES PRO (textarea bonito, token al enviar)
// ========================================
(function initMentionsPro() {
    const ta = document.getElementById('nuevoComentario');
    if (!ta) return;

    // Crear mentionBox si no existe
    let box = document.getElementById('mentionBox');
    if (!box) {
        box = document.createElement('div');
        box.id = 'mentionBox';
        document.body.appendChild(box);
    }
    const modal = ta.closest('.modal');
    const modalBody = ta.closest('.modal-body');

    if (modalBody) {
    modalBody.addEventListener('scroll', () => {
        if (box.style.display === 'block') positionBox();
    }, { passive: true });
    }

    if (modal) {
    modal.addEventListener('shown.bs.modal', () => {
        if (box.style.display === 'block') positionBox();
    });
    }


    // Estilos 
    if (!document.getElementById('mentionProStyles')) {
        const st = document.createElement('style');
        st.id = 'mentionProStyles';
        st.textContent = `
        #mentionBox{
            position: fixed;              /* clave */
            z-index: 20000;               /* arriba del modal/backdrop */
            display: none;

            width: min(380px, calc(100vw - 24px));
            max-height: 320px;
            overflow: auto;

            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 18px 50px rgba(0,0,0,.18);
            backdrop-filter: blur(6px);
            }

            #mentionBox .m-head{
            position: sticky;
            top: 0;
            padding: 10px 12px;
            background: #f8fafc;
            border-bottom: 1px solid #eef2f7;
            font-size: 12px;
            color: #64748b;
            display: flex;
            gap: 8px;
            align-items: center;
            }

            #mentionBox .m-item{
            padding: 10px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            }

            #mentionBox .m-item:last-child{ border-bottom: none; }
            #mentionBox .m-item:hover{ background: #f8fafc; }

            #mentionBox .m-left{ display:flex; flex-direction:column; gap:2px; }
            #mentionBox .m-name{ font-weight: 800; font-size: 13px; color: #0f172a; }
            #mentionBox .m-email{ font-size: 12px; color: #64748b; }

            #mentionBox .m-tag{
            font-size: 12px;
            font-weight: 800;
            padding: 6px 10px;
            border-radius: 999px;
            background: #e0f2fe;
            color: #075985;
            white-space: nowrap;
            }
        `;
        document.head.appendChild(st);
    }

    // Normalizar usuarios
    const users = (window.WINTICK_USERS || []).map(u => ({
        id: u.id,
        email: (u.email || '').trim(),
        nombre: (u.nombre || u.Nombre || '').trim(),
        primerNombre: (u.primerNombre || (u.Nombre || '').trim().split(/\s+/)[0] || '').trim(),
    })).filter(u => u.email);

    ta._mentions = ta._mentions || [];

    function firstNameFromUser(u) {
        return u.primerNombre || (u.nombre ? u.nombre.split(/\s+/)[0] : '') || u.email.split('@')[0];
    }

    function positionBox() {
    const r = ta.getBoundingClientRect();

    // base: debajo del textarea
    let left = r.left;
    let top  = r.bottom + 6;

    
    const boxW = box.offsetWidth || 360;
    const boxH = box.offsetHeight || 240;

   
    const maxLeft = window.innerWidth - boxW - 12;
    left = Math.max(12, Math.min(left, maxLeft));

   
    if (top + boxH > window.innerHeight - 12) {
        top = r.top - boxH - 6;
    }

    
    top = Math.max(12, Math.min(top, window.innerHeight - boxH - 12));

    box.style.left = `${left}px`;
    box.style.top  = `${top}px`;
    }

    function hide() { box.style.display = 'none'; box.innerHTML = ''; }

    function getMentionQuery(text, caret) {
        const left = text.slice(0, caret);
        const at = left.lastIndexOf('@');
        if (at === -1) return null;
        if (at > 0 && /\w/.test(left[at - 1])) return null;

        const q = left.slice(at + 1);
        if (q.includes(' ') || q.includes('\n')) return null;

        return { atIndex: at, q };
    }

    function filterUsers(q) {
        const query = (q || '').toLowerCase();
        if (!query) return users.slice(0, 8);

        return users.filter(u => {
            const fn = firstNameFromUser(u).toLowerCase();
            return u.email.toLowerCase().includes(query)
                || (u.nombre || '').toLowerCase().includes(query)
                || fn.includes(query);
        }).slice(0, 8);
    }

    function insertPrettyMention(user, atIndex, caret) {
        const label = firstNameFromUser(user);
        const pretty = `@${label}`;

        const text = ta.value;
        const before = text.slice(0, atIndex);
        const after = text.slice(caret);

        const inserted = pretty + ' ';
        ta.value = before + inserted + after;

        const start = before.length;
        const end = before.length + pretty.length;
        ta._mentions.push({ start, end, email: user.email, label });

        const newPos = (before + inserted).length;
        ta.focus();
        ta.setSelectionRange(newPos, newPos);
        hide();
    }

    function cleanupMentions() {
        const text = ta.value;
        ta._mentions = (ta._mentions || []).filter(m => text.slice(m.start, m.end) === `@${m.label}`);
    }

    ta.addEventListener('input', () => {
        cleanupMentions();

        const caret = ta.selectionStart;
        const info = getMentionQuery(ta.value, caret);
        if (!info) return hide();

        const list = filterUsers(info.q);
        if (!list.length) return hide();

        positionBox();
        box.innerHTML = `
          <div class="m-head">Menciona a alguien · clic para seleccionar</div>
          ${list.map(u => {
            const label = firstNameFromUser(u);
            return `
              <div class="m-item" data-email="${u.email}">
                <div class="m-left">
                  <div class="m-name">@${label}</div>
                  <div class="m-email">${u.email}</div>
                </div>
                <div class="m-tag">${label}</div>
              </div>
            `;
          }).join('')}
        `;
        box.style.display = 'block';

        [...box.querySelectorAll('.m-item')].forEach(el => {
            el.addEventListener('click', () => {
                const email = el.dataset.email;
                const user = users.find(x => x.email === email);
                if (!user) return;
                insertPrettyMention(user, info.atIndex, caret);
            });
        });
    });

    ta.addEventListener('blur', () => setTimeout(hide, 160));
    window.addEventListener('resize', () => { if (box.style.display === 'block') positionBox(); });
    window.addEventListener('scroll', () => { if (box.style.display === 'block') positionBox(); });

    // ✅ función global para convertir texto visible -> token guardado
    window.buildCommentPayload = function (commentVisible) {
        let text = commentVisible || '';
        const ms = (ta._mentions || []).slice().sort((a, b) => b.start - a.start);

        ms.forEach(m => {
            const current = text.slice(m.start, m.end);
            const expected = `@${m.label}`;
            if (current === expected) {
                const token = `@[email:${m.email}]`;
                text = text.slice(0, m.start) + token + text.slice(m.end);
            }
        });

        return text.trim();
    };
})();

// ── MENCIONES PRO para el drawer (misma lógica que el modal) ─────────
(function initDrawerMentionsPro() {
    const ta = document.getElementById('d-nuevo-cmnt');
    if (!ta) return;

    // Caja propia en body (el #mentionBox original vive dentro del modal y queda oculto cuando el modal está cerrado)
    let box = document.getElementById('drawerMentionBox');
    if (!box) {
        box = document.createElement('div');
        box.id = 'drawerMentionBox';
        document.body.appendChild(box);
        const st = document.createElement('style');
        st.textContent = `
        #drawerMentionBox {
            position: fixed;
            z-index: 20000;
            display: none;
            width: min(360px, calc(100vw - 24px));
            max-height: 300px;
            overflow: auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 18px 50px rgba(0,0,0,.18);
        }
        #drawerMentionBox .m-head {
            position: sticky; top: 0;
            padding: 10px 12px;
            background: #f8fafc;
            border-bottom: 1px solid #eef2f7;
            font-size: 12px; color: #64748b;
        }
        #drawerMentionBox .m-item {
            padding: 10px 12px; cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            display: flex; justify-content: space-between; gap: 10px; align-items: center;
        }
        #drawerMentionBox .m-item:last-child { border-bottom: none; }
        #drawerMentionBox .m-item:hover, #drawerMentionBox .m-item.kbd-active { background: #f8fafc; }
        #drawerMentionBox .m-left { display: flex; flex-direction: column; gap: 2px; }
        #drawerMentionBox .m-name { font-weight: 800; font-size: 13px; color: #0f172a; }
        #drawerMentionBox .m-email { font-size: 12px; color: #64748b; }
        #drawerMentionBox .m-tag {
            font-size: 12px; font-weight: 800; padding: 6px 10px;
            border-radius: 999px; background: #e0f2fe; color: #075985; white-space: nowrap;
        }`;
        document.head.appendChild(st);
    }

    const users = (window.WINTICK_USERS || []).map(u => ({
        id          : u.id,
        email       : (u.email || '').trim(),
        nombre      : (u.nombre || u.Nombre || '').trim(),
        primerNombre: (u.primerNombre || (u.Nombre || '').trim().split(/\s+/)[0] || '').trim(),
    })).filter(u => u.email);

    ta._mentions = ta._mentions || [];

    function firstNameFromUser(u) {
        return u.primerNombre || (u.nombre ? u.nombre.split(/\s+/)[0] : '') || u.email.split('@')[0];
    }

    function positionBox() {
        const r    = ta.getBoundingClientRect();
        let left   = r.left;
        let top    = r.bottom + 6;
        const boxW = box.offsetWidth  || 360;
        const boxH = box.offsetHeight || 240;
        left = Math.max(12, Math.min(left, window.innerWidth - boxW - 12));
        if (top + boxH > window.innerHeight - 12) top = r.top - boxH - 6;
        top = Math.max(12, Math.min(top, window.innerHeight - boxH - 12));
        box.style.left = `${left}px`;
        box.style.top  = `${top}px`;
    }

    function hide() { box.style.display = 'none'; box.innerHTML = ''; }

    function getMentionQuery(text, caret) {
        const left = text.slice(0, caret);
        const at   = left.lastIndexOf('@');
        if (at === -1) return null;
        if (at > 0 && /\w/.test(left[at - 1])) return null;
        const q = left.slice(at + 1);
        if (q.includes(' ') || q.includes('\n')) return null;
        return { atIndex: at, q };
    }

    function filterUsers(q) {
        const query = (q || '').toLowerCase();
        if (!query) return users.slice(0, 8);
        return users.filter(u => {
            const fn = firstNameFromUser(u).toLowerCase();
            return u.email.toLowerCase().includes(query)
                || (u.nombre || '').toLowerCase().includes(query)
                || fn.includes(query);
        }).slice(0, 8);
    }

    function insertPrettyMention(user, atIndex, caret) {
        const label    = firstNameFromUser(user);
        const pretty   = `@${label}`;
        const text     = ta.value;
        const before   = text.slice(0, atIndex);
        const after    = text.slice(caret);
        const inserted = pretty + ' ';
        ta.value = before + inserted + after;
        const start = before.length;
        const end   = before.length + pretty.length;
        ta._mentions.push({ start, end, email: user.email, label });
        ta.focus();
        ta.setSelectionRange((before + inserted).length, (before + inserted).length);
        hide();
    }

    function cleanupMentions() {
        const text = ta.value;
        ta._mentions = (ta._mentions || []).filter(m => text.slice(m.start, m.end) === `@${m.label}`);
    }

    ta.addEventListener('input', () => {
        cleanupMentions();
        const caret = ta.selectionStart;
        const info  = getMentionQuery(ta.value, caret);
        if (!info) return hide();
        const list = filterUsers(info.q);
        if (!list.length) return hide();
        positionBox();
        box.innerHTML = `
          <div class="m-head">Menciona a alguien · clic para seleccionar</div>
          ${list.map(u => {
            const label = firstNameFromUser(u);
            return `<div class="m-item" data-email="${u.email}">
                <div class="m-left">
                  <div class="m-name">@${label}</div>
                  <div class="m-email">${u.email}</div>
                </div>
                <div class="m-tag">${label}</div>
              </div>`;
          }).join('')}
        `;
        box.style.display = 'block';
        [...box.querySelectorAll('.m-item')].forEach(el => {
            el.addEventListener('click', () => {
                const user = users.find(x => x.email === el.dataset.email);
                if (user) insertPrettyMention(user, info.atIndex, caret);
            });
        });
    });

    ta.addEventListener('keydown', e => {
        if (box.style.display === 'none') return;
        const items = [...box.querySelectorAll('.m-item')];
        if (!items.length) return;
        const active = box.querySelector('.m-item.kbd-active') || items[0];
        const idx    = items.indexOf(active);
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            items.forEach(el => el.classList.remove('kbd-active'));
            (items[Math.min(idx + 1, items.length - 1)] || items[0]).classList.add('kbd-active');
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            items.forEach(el => el.classList.remove('kbd-active'));
            (items[Math.max(idx - 1, 0)]).classList.add('kbd-active');
        } else if (e.key === 'Enter' || e.key === 'Tab') {
            const target = box.querySelector('.m-item.kbd-active') || items[0];
            if (target) {
                e.preventDefault();
                const caret = ta.selectionStart;
                const info  = getMentionQuery(ta.value, caret);
                const user  = users.find(x => x.email === target.dataset.email);
                if (info && user) insertPrettyMention(user, info.atIndex, caret);
            }
        } else if (e.key === 'Escape') {
            hide();
        }
    });

    ta.addEventListener('blur', () => setTimeout(hide, 160));
    window.addEventListener('resize', () => { if (box.style.display === 'block') positionBox(); });

    window.buildDrawerPayload = function (commentVisible) {
        let text = commentVisible || '';
        const ms = (ta._mentions || []).slice().sort((a, b) => b.start - a.start);
        ms.forEach(m => {
            const current  = text.slice(m.start, m.end);
            if (current === `@${m.label}`) {
                text = text.slice(0, m.start) + `@[email:${m.email}]` + text.slice(m.end);
            }
        });
        return text.trim();
    };

    const closeBtn = document.getElementById('d-btn-close');
    if (closeBtn) closeBtn.addEventListener('click', () => { ta._mentions = []; ta.value = ''; });
})();

// ── SELECTS BUSCABLES (cliente / sistema / servicio) ──────────────────
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
        let _guardFocus  = false; // evita loop al restaurar focus

        function render(items) {
            if (!items.length) {
                // Nodo simple: no requiere innerHTML extenso
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
                    el.className    = 'ss-item';
                    el.dataset.value = String(opt.value);
                    // Resaltar coincidencia sin innerHTML masivo
                    if (re) {
                        el.innerHTML = opt.text.replace(re, '<strong>$1</strong>');
                    } else {
                        el.textContent = opt.text;
                    }
                    el.addEventListener('mousedown', e => {
                        e.preventDefault();
                        input.value  = opt.text;
                        select.value = opt.value;
                        dropdown.style.display = 'none';
                        if (onSelect) onSelect(select);
                    });
                    frag.appendChild(el);
                });
                // replaceChildren no toca el input (sibling) → no pierde focus
                dropdown.replaceChildren(frag);
            }
            dropdown.style.display = 'block';
        }

        function filterAndShow() {
            const q = normalize(input.value);
            const filtered = q
                ? allOptions.filter(o => normalize(o.text).includes(q))
                : allOptions;
            render(filtered.slice(0, 12));
            // Guardia: si el render causó pérdida de focus, lo restauramos una sola vez
            if (!_guardFocus && document.activeElement !== input) {
                _guardFocus = true;
                const ss = input.selectionStart ?? input.value.length;
                const se = input.selectionEnd   ?? input.value.length;
                input.focus();
                try { input.setSelectionRange(ss, se); } catch (_) {}
                _guardFocus = false;
            }
        }

        input.addEventListener('focus', () => { if (!_guardFocus) filterAndShow(); });
        input.addEventListener('input', filterAndShow);
        input.addEventListener('blur', () => setTimeout(() => { dropdown.style.display = 'none'; }, 160));
        input.addEventListener('keydown', e => {
            if (dropdown.style.display === 'none') return;
            const items = [...dropdown.querySelectorAll('.ss-item')];
            const active = dropdown.querySelector('.ss-active');
            const idx = items.indexOf(active);
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

    // Inicializar todos los .ss-wrap en la fila nueva cuando se cree
    function initRow(row) {
        const wraps = row.querySelectorAll('.ss-wrap');
        wraps.forEach(wrap => {
            const sel = wrap.querySelector('select');
            if (!sel) return;
            const cls = sel.className; // 'cliente', 'sistema', or 'servicio'
            const cb  = cls === 'cliente' ? (s) => loadClienteData(s) : null;
            initWrap(wrap, cb);
        });
    }

    // Inicializar la fila nueva existente en el DOM
    const newRow = document.querySelector('tr.new-row');
    if (newRow) initRow(newRow);

    // Re-inicializar si se resetea la fila (cuando se guarda y se limpia)
    window.reinitSearchableSelects = () => {
        const r = document.querySelector('tr.new-row');
        if (r) initRow(r);
    };
})();
</script>
<?php
$openComments = (int)Yii::$app->request->get('openComments', 0);
$ticketId     = (int)Yii::$app->request->get('ticket_id', 0);

// Pre-cargar datos del ticket si viene de una notificación y no está en la página actual
$drawerPreload = null;
if ($openComments && $ticketId) {
    $tkt = \app\models\Tickets::findOne($ticketId);
    if ($tkt) {
        $drawerPreload = [
            'id'              => $tkt->id,
            'folio'           => $tkt->Folio,
            'estado'          => $tkt->Estado,
            'prioridad'       => $tkt->Prioridad,
            'cliente'         => $tkt->cliente ? $tkt->cliente->Nombre : '-',
            'criticidad'      => $tkt->cliente ? $tkt->cliente->Criticidad : '-',
            'sistema'         => $tkt->sistema ? $tkt->sistema->Nombre : '-',
            'servicio'        => $tkt->servicio ? $tkt->servicio->Nombre : '-',
            'asignadoA'       => $tkt->usuarioAsignado ? $tkt->usuarioAsignado->email : '-',
            'asignadoId'      => (int)$tkt->Asignado_a,
            'usuarioReporta'  => $tkt->Usuario_reporta,
            'horaProgramada'  => $tkt->HoraProgramada ? date('d/m/Y H:i', strtotime($tkt->HoraProgramada)) : '-',
            'horaInicio'      => $tkt->HoraInicio ? date('d/m/Y H:i', strtotime($tkt->HoraInicio)) : '-',
            'horaFinalizo'    => $tkt->HoraFinalizo ? date('d/m/Y H:i', strtotime($tkt->HoraFinalizo)) : '-',
            'horaProgramadaRaw' => $tkt->HoraProgramada ? date('Y-m-d\TH:i', strtotime($tkt->HoraProgramada)) : '',
            'horaInicioRaw'   => $tkt->HoraInicio ? date('Y-m-d\TH:i', strtotime($tkt->HoraInicio)) : '',
            'tiempoEfectivo'  => $tkt->TiempoEfectivo ?: '-',
            'descripcion'     => $tkt->Descripcion,
            'solucion'        => $tkt->Solucion ?: '',
            'tieneSolucion'   => $tkt->Solucion ? '1' : '0',
            'clienteId'       => (int)$tkt->Cliente_id,
            'sistemaId'       => (int)$tkt->Sistema_id,
            'servicioId'      => (int)$tkt->Servicio_id,
        ];
    }
}
?>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const openComments = <?= $openComments ?>;
  const ticketId     = <?= $ticketId ?>;

  if (!openComments || !ticketId) return;

  // Primero buscar la fila en la tabla y abrir el drawer con sus datos
  const row = document.querySelector(`tr[data-ticket-id="${ticketId}"]`);
  if (row) { openDrawer(row.dataset); return; }

  // Ticket no está en la página actual → usar datos pre-cargados desde PHP
  const preload = <?= $drawerPreload ? json_encode($drawerPreload, JSON_UNESCAPED_UNICODE) : 'null' ?>;
  if (preload && typeof openDrawer === 'function') {
    openDrawer(preload);
  }
});

// ─── Auto-refresh cuando llega notificación de ticket vía SSE ─────────────────
(function () {
    // IDs de tickets ya notificados en esta sesión (evita duplicar el banner)
    const ticketsNotificados = new Set();
    let bannerEl = null;

    function mostrarBanner(notifs) {
        // Filtrar solo tickets realmente nuevos no mostrados aún
        const nuevos = notifs.filter(n => n.ticket_id && !ticketsNotificados.has(n.ticket_id));
        if (nuevos.length === 0) return;
        nuevos.forEach(n => ticketsNotificados.add(n.ticket_id));

        // Crear contenedor banner si no existe
        if (!bannerEl) {
            bannerEl = document.createElement('div');
            bannerEl.id = 'tickets-nuevos-banner';
            bannerEl.style.cssText = [
                'position:fixed', 'bottom:24px', 'right:24px', 'z-index:9999',
                'display:flex', 'flex-direction:column', 'gap:8px',
                'max-width:320px'
            ].join(';');
            document.body.appendChild(bannerEl);
        }

        nuevos.forEach(function(n) {
            const pill = document.createElement('div');
            pill.style.cssText = [
                'background:#0f172a', 'color:#f1f5f9', 'border-radius:12px',
                'padding:11px 16px', 'font-size:13px',
                'box-shadow:0 4px 20px rgba(0,0,0,.4)',
                'display:flex', 'align-items:center', 'gap:10px',
                'animation:slideInRight .3s ease'
            ].join(';');

            const ticketUrl = <?= json_encode(\yii\helpers\Url::to(['tickets/view'])) ?> + '?id=' + n.ticket_id;
            pill.innerHTML =
                '<i class="fas fa-ticket-alt" style="color:#38bdf8;font-size:15px;flex-shrink:0"></i>' +
                '<div style="flex:1;line-height:1.4">' +
                  '<div style="font-weight:600;margin-bottom:2px">Ticket nuevo asignado</div>' +
                  '<div style="font-size:11px;color:#94a3b8">' + (n.mensaje || n.titulo || '') + '</div>' +
                '</div>' +
                '<a href="' + ticketUrl + '" style="' +
                  'background:#38bdf8;color:#0f172a;border:none;border-radius:7px;' +
                  'padding:5px 10px;font-size:11px;font-weight:700;cursor:pointer;' +
                  'text-decoration:none;white-space:nowrap' +
                '">Ver ticket</a>' +
                '<button onclick="this.closest(\'div[style]\').remove()" style="' +
                  'background:none;border:none;color:#64748b;cursor:pointer;' +
                  'font-size:16px;line-height:1;padding:0 2px' +
                '">×</button>';

            bannerEl.appendChild(pill);

            // Auto-quitar después de 12s si el usuario no interactuó
            setTimeout(function() { if (pill.parentNode) pill.remove(); }, 12000);
        });
    }

    // Agregar keyframe de animación
    const style = document.createElement('style');
    style.textContent = '@keyframes slideInRight{from{opacity:0;transform:translateX(30px)}to{opacity:1;transform:translateX(0)}}';
    document.head.appendChild(style);

    window.addEventListener('wintick:tickets-updated', function (e) {
        mostrarBanner(e.detail || []);
    });
})();
</script>
