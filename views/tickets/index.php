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
    .pagination {
        gap: 5px;
    }
    
    .pagination .page-link {
        color: #8BA590;
        border: 1px solid #ddd;
        padding: 8px 12px;
        font-size: 13px;
    }
    
    .pagination .page-link:hover {
        background-color: #f0f0f0;
        color: #7a9582;
    }
    
    .pagination .page-link.active {
        background-color: #8BA590;
        border-color: #8BA590;
    }
    
    .pagination .page-link.disabled {
        color: #ccc;
        cursor: not-allowed;
    }
    
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

');

// Obtener mes y año actual si no hay filtro
$mesActual = Yii::$app->request->get('mes', date('Y-m'));
?>


<div class="tickets-index">
    <!-- Header Principal con Buscador Universal  -->
    <div class="tickets-header">
        <h1><i class="fas fa-ticket-alt"></i> <?= Html::encode($this->title) ?></h1>
        
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
                            💡 Selecciona UNO: mes O rango de fechas (inicio-fin)
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
                                <option value="EN PROCESO" <?= ($_GET['Estado'] ?? '') == 'EN PROCESO' ? 'selected' : '' ?>>En Proceso</option>
                                <option value="EN ESPERA" <?= ($_GET['Estado'] ?? '') == 'EN ESPERA' ? 'selected' : '' ?>>En Espera</option>
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
            class="btn btn-outline-success">
                <i class="fas fa-file-csv"></i> Exportar CSV
            </a>
        </div>
    </div>

    <!-- Mes Actual Badge -->
    <?php if (!empty($_GET['mes'])): ?>
    <div class="mes-actual">
        <i class="fas fa-calendar-alt"></i>
        <strong><?= Html::encode(strftime('%B de %Y', strtotime($_GET['mes'] . '-01'))) ?></strong>
    </div>
    <?php else: ?>
   
    <?php endif; ?>

    <!-- Stats Bar -->
    <div class="stats-bar">
        <h5 class="tickets-count">
            <i class="fas fa-list"></i> Total de Tickets: <strong id="totalTickets"><?= $dataProvider->getTotalCount() ?></strong>
            <span id="filteredCount" style="display: none; margin-left: 10px; color: #667eea;"></span>
        </h5>
    </div>

    <!-- Tabla -->
    <div class="table-container">
        <div style="margin-bottom: 15px; font-size: 12px; color: #666;">
            <strong>Mostrando:</strong> <?= count($dataProvider->getModels()) ?> de <?= $dataProvider->getTotalCount() ?> tickets
        </div>
        
        <table class="table table-hover table-sm" id="ticketsTable">
            <thead>
                <tr>
                    <th class="text-primary-emphasis">Folio</th>
                    <th class="text-primary-emphasis">Cliente</th>
                    <th class="text-primary-emphasis">Sistema</th>
                    <th class="text-primary-emphasis">Servicio</th>
                    <th class="text-primary-emphasis">Usuario Reporta</th>
                    <th class="text-primary-emphasis">Asignado A</th>
                    <th class="text-primary-emphasis">Fecha de reporte</th>
                    <th class="text-primary-emphasis">Hora Inicio</th>
                    <th class="text-primary-emphasis">Descripción</th>
                    <th class="text-primary-emphasis">Prioridad</th>
                    <th class="text-primary-emphasis">Estado</th>
                    <th class="text-primary-emphasis">Acción</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- Fila para crear nuevo (FOLIO AUTOMÁTICO) MOVIDA ARRIBA -->
                <tr class="data-row new-row">
                    <td>
                        <input type="text" 
                               class="form-control form-control-sm folio" 
                               placeholder="Auto..." 
                               readonly 
                               style="background: #e9ecef; font-weight: bold; color: #10b981;">
                    </td>
                    <td>
                        <select class="form-select form-select-sm cliente" onchange="loadClienteData(this)">
                            <option value="">Seleccionar</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id'] ?>" data-prioridad="<?= $cliente['Prioridad'] ?>" data-tipo="<?= $cliente['Tipo_servicio'] ?>">
                                    <?= Html::encode($cliente['Nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm sistema">
                            <option value="">Seleccionar</option>
                            <?php foreach ($sistemas as $sistema): ?>
                                <option value="<?= $sistema['id'] ?>"><?= Html::encode($sistema['Nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm servicio">
                            <option value="">Seleccionar</option>
                            <?php foreach ($servicios as $servicio): ?>
                                <option value="<?= $servicio['id'] ?>"><?= Html::encode($servicio['Nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm usuario-reporta" placeholder="Quién reporta">
                    </td>
               <td>
    <select class="form-select form-select-sm asignado-a">
        <option value="">Seleccionar</option>

        <?php foreach ($Usuarios as $usuario): ?>
            <option
                value="<?= $usuario['id'] ?>"
                data-email="<?= Html::encode($usuario['email']) ?>"
            >
                <?= Html::encode($usuario['Nombre']) ?>
            </option>
        <?php endforeach; ?>

    </select>
</td>
                    <td>
                        <div class="datetime-wrapper">
                            <input type="text" class="form-control form-control-sm hora-programada flatpickr-datetime" placeholder="Seleccionar fecha">
                        </div>
                    </td>
                    <td>
                        <div class="datetime-wrapper">
                            <input type="text" class="form-control form-control-sm hora-inicio flatpickr-datetime" placeholder="Seleccionar fecha">
                        </div>
                    </td>
                    <td><textarea class="form-control form-control-sm descripcion" rows="1" placeholder="Descripción" style="font-size: 12px;"></textarea></td>
                    <td>
                        <select class="form-select form-select-sm prioridad">
                            <option value="">Seleccionar</option>
                            <option value="BAJA">Baja</option>
                            <option value="MEDIA">Media</option>
                            <option value="ALTA">Alta</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm estado">
                            <option value="ABIERTO" selected>Abierto</option>
                            <option value="EN PROCESO">En Proceso</option>
                            <option value="EN ESPERA">En Espera</option>
                            <option value="CERRADO">Cerrado</option>
                        </select>
                    </td>
                    <td style="white-space: nowrap;">
                        <button type="button" class="btn btn-sm btn-outline-success saveRow" title="Guardar">
                            <i class="fas fa-save"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deleteRow" title="Cancelar">
                            <i class="fas fa-times"></i>
                        </button>
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
                data-tiempo-efectivo="<?= Html::encode($ticket->TiempoEfectivo ?: '-') ?>" >
                    <td><strong><?= Html::encode($ticket->Folio) ?></strong></td>
                    <td><?= $ticket->cliente ? Html::encode($ticket->cliente->Nombre) : '-' ?></td>
                    <td><?= $ticket->sistema ? Html::encode($ticket->sistema->Nombre) : '-' ?></td>
                    <td><?= $ticket->servicio ? Html::encode($ticket->servicio->Nombre) : '-' ?></td>
                    <td><?= Html::encode($ticket->Usuario_reporta) ?></td>
                    <td> <?php if ($ticket->usuarioAsignado): ?>
        <span title="<?= Html::encode($ticket->usuarioAsignado->email) ?>">  
            <?= Html::encode($ticket->usuarioAsignado->Nombre) ?>            
        </span>
    <?php else: ?>
        -
    <?php endif; ?></td>
                    <td style="font-size: 12px; white-space: nowrap;">
                        <?= $ticket->HoraProgramada ? Html::encode(date('d/m H:i', strtotime($ticket->HoraProgramada))) : '-' ?>
                    </td>
                    <td style="font-size: 12px; white-space: nowrap;">
                        <?= $ticket->HoraInicio ? Html::encode(date('d/m H:i', strtotime($ticket->HoraInicio))) : '-' ?>
                    </td>
                    <td class="descripcion-cell" 
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        data-bs-html="true"
                        title="<?= Html::encode($ticket->Descripcion) ?>"
                        data-full-text="<?= Html::encode($ticket->Descripcion) ?>"
                        style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        <?= Html::encode(substr($ticket->Descripcion, 0, 30)) ?><?= strlen($ticket->Descripcion) > 30 ? '...' : '' ?>
                        <?php if (strlen($ticket->Descripcion) > 30): ?>
                            <i class="fas fa-eye text-muted ms-1" style="font-size: 11px;"></i>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $badgeClass = match($ticket->Prioridad) {
                            'ALTA' => 'badge bg-danger',
                            'MEDIA' => 'badge bg-warning text-dark',
                            'BAJA' => 'badge bg-info',
                            default => 'badge bg-secondary'
                        };
                        ?>
                        <span class="<?= $badgeClass ?>"><?= Html::encode($ticket->Prioridad) ?></span>
                    </td>
                    <td>
                        <?php
                        $estadoClass = match($ticket->Estado) {
                            'ABIERTO'    => 'bg-primary text-white',
                            'EN PROCESO' => 'bg-info text-dark',
                            'EN ESPERA'  => 'bg-warning text-dark',
                            'CERRADO'    => 'bg-danger text-white',
                            default      => 'bg-secondary'
                        };
                        $estadoIcon = match($ticket->Estado) {
                            'ABIERTO'    => 'fa-circle-notch',
                            'EN PROCESO' => 'fa-spinner',
                            'EN ESPERA'  => 'fa-pause-circle',
                            'CERRADO'    => 'fa-check-circle',
                            default      => 'fa-question-circle'
                        };
                        ?>
                        <div class="estado-clickeable <?= $estadoClass ?>" onclick="toggleEstadoSelect(this, <?= $ticket->id ?>)">
                            <i class="fas <?= $estadoIcon ?>"></i> <?= Html::encode($ticket->Estado) ?>
                        </div>
                        <select class="form-select form-select-sm estado-select estado-<?= $ticket->id ?>" onchange="updateEstado(this, <?= $ticket->id ?>)" style="display: none; font-size: 12px; margin-top: 5px;">
                            <option value="ABIERTO" <?= $ticket->Estado == 'ABIERTO' ? 'selected' : '' ?>>Abierto</option>
                            <option value="EN PROCESO" <?= $ticket->Estado == 'EN PROCESO' ? 'selected' : '' ?>>En Proceso</option>
                            <option value="EN ESPERA" <?= $ticket->Estado == 'EN ESPERA' ? 'selected' : '' ?>>En Espera</option>
                            <option value="CERRADO" <?= $ticket->Estado == 'CERRADO' ? 'selected' : '' ?>>Cerrado</option>
                        </select>
                    </td>
                    <td style="white-space: nowrap;">
                        <?php
                       

                       //cuenta los comentarios para asi mostrarlos en el front
                        $comentarioCount = count($ticket->comentarios);
                        ?>
                        <div style="position: relative; display: inline-block;">
                            <button class="btn btn-sm btn-outline-info comment-btn-<?= $ticket->id ?>" title="Ver comentarios" onclick="openComentariosModal(<?= $ticket->id ?>, '<?= Html::encode($ticket->Folio) ?>')">
                                <i class="fas fa-comments"></i>
                            </button>
                            <?php if ($comentarioCount > 0): ?>
                                <span class="badge bg-danger badge-count-<?= $ticket->id ?>" style="position: absolute; top: -8px; right: -8px; font-size: 11px; padding: 3px 6px; min-width: 24px; text-align: center; border-radius: 50%; font-weight: bold;"><?= $comentarioCount ?></span>
                            <?php endif; ?>
                        </div>
                                <button class="btn btn-sm btn-outline-primary" title="Agregar solución"
                                    onclick="openSolutionModal(<?= $ticket->id ?>, '<?= Html::encode($ticket->Folio) ?>')">
                                <i class="fas fa-lightbulb"></i>
                            </button>
                        <?= Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $ticket->id], ['class' => 'btn btn-sm btn-outline-secondary', 'title' => 'Editar']) ?>
                        <?= ''/* Html::a('<i class="fas fa-trash"></i>', '#', [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'title' => 'Eliminar',
                            'onclick' => "confirmarEliminar({$ticket->id}, '{$ticket->Folio}')",
                        ]) */?>
                    </td>
                </tr>
                <?php endforeach; ?>

                <!-- Fila "Sin resultados" -->
                <tr class="no-results-row" id="noResultsRow">
                    <td colspan="12">
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
                'pagination' => $dataProvider->pagination,
                'options' => ['class' => 'pagination justify-content-center mt-4'],
                'linkOptions' => ['class' => 'page-link'],
                'disabledListItemSubTagOptions' => ['tag' => 'span', 'class' => 'page-link disabled'],
            ]) ?>
        </nav>
    </div>

    
</div>

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
    return [
        'id' => (int)$u['id'],
        'email' => $u['email'],
        'nombre' => $u['Nombre'],
        'primerNombre' => preg_split('/\s+/', trim($u['Nombre'] ?? ''))[0] ?? ''
    ];
}, $Usuarios), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
<script>
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

function confirmarEliminar(ticketId, folio) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Se eliminará el ticket ${folio} permanentemente`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera',
                icon: 'info',
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
                            text: `Ticket ${folio} eliminado correctamente`,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => location.reload());
                    } else {
                        throw new Error('Error del servidor: ' + response.status);
                    }
                })
                .catch(error => {
                    console.error('Error completo:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo eliminar el ticket: ' + error.message,
                        confirmButtonColor: '#ef4444'
                    });
                });
        }
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
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        locale: "es",
        minuteIncrement: 15,
        defaultHour: 9,
        defaultMinute: 0,
        allowInput: true,
        clickOpens: true,
        theme: "airbnb"
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
        HoraProgramada: row.querySelector('.hora-programada').value,
        HoraInicio: row.querySelector('.hora-inicio').value,
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
                    timer: 1000,
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

    div.className = 'estado-clickeable ' + getEstadoClass(estado);
    div.innerHTML = '<i class="fas ' + getEstadoIcon(estado) + '"></i> ' + estado;
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
        'ABIERTO':    'bg-primary text-white',
        'EN PROCESO': 'bg-info text-dark',
        'EN ESPERA':  'bg-warning text-dark',
        'CERRADO':    'bg-danger text-white'
    };
    return classes[estado] || 'bg-secondary';
}

function getEstadoIcon(estado) {
    const icons = {
        'ABIERTO':    'fa-circle-notch',
        'EN PROCESO': 'fa-spinner',
        'EN ESPERA':  'fa-pause-circle',
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
                div.className = 'estado-clickeable ' + getEstadoClass(estadoRevertir);
                div.innerHTML = '<i class="fas ' + getEstadoIcon(estadoRevertir) + '"></i> ' + estadoRevertir;
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
    return safe.replace(/@\[(?:email):([^\]]+)\]/g, (m, email) => {
        email = (email || '').trim().toLowerCase();
        const user = (window.WINTICK_USERS || []).find(u => (u.email || '').toLowerCase() === email);
        const nombre = user?.primerNombre || (user?.Nombre ? user.Nombre.split(/\s+/)[0] : 'usuario');
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
                    let badge = container.querySelector('.badge-count-' + ticketId);

                    if (data.count > 0) {
                        if (!badge) {
                            badge = document.createElement('span');
                            badge.className = 'badge bg-danger badge-count-' + ticketId;
                            badge.style.position = 'absolute';
                            badge.style.top = '-8px';
                            badge.style.right = '-8px';
                            badge.style.fontSize = '11px';
                            badge.style.padding = '3px 6px';
                            badge.style.minWidth = '24px';
                            badge.style.textAlign = 'center';
                            badge.style.borderRadius = '50%';
                            badge.style.fontWeight = 'bold';
                            container.appendChild(badge);
                        }
                        badge.textContent = data.count;
                        badge.style.display = 'inline-block';
                    } else {
                        if (badge) badge.style.display = 'none';
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
    if (e === 'EN PROCESO') return 'swal-status-en-proceso';
    if (e === 'EN ESPERA')  return 'swal-status-en-espera';
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

document.querySelectorAll('tr.existing-row').forEach(row => {
    row.addEventListener('dblclick', function (e) {
        if (e.target.closest('button, a, select, input, textarea')) return;

        const d = this.dataset;

        const estadoClass = getStatusClass(d.estado);
        const prioridadClass = getPriorityClass(d.prioridad);
        const criticidadClass = getCriticidadClass(d.criticidad);

        const html = `
            <div class="swal-ticket-card">
                <div class="swal-ticket-header">
                    <div class="swal-ticket-title">
                        <i class="fas fa-ticket-alt"></i>
                        Ticket #${escapeHtml(d.folio || '')}
                    </div>
                    <p class="swal-ticket-subtitle">
                        Cliente: ${escapeHtml(d.cliente || 'No asignado')}
                    </p>
                </div>
                <div class="swal-ticket-body">
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
                                <span class="swal-info-label">Hora programada</span>
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
                </div>
            </div>
        `;

        Swal.fire({
            html: html,
            width: 800,
            showConfirmButton: true,
            confirmButtonText: 'Cerrar',
            showCloseButton: true,
            focusConfirm: false
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
</script>
<?php
$openComments = (int)Yii::$app->request->get('openComments', 0);
$ticketId     = (int)Yii::$app->request->get('ticket_id', 0);
?>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const openComments = <?= $openComments ?>;
  const ticketId = <?= $ticketId ?>;

  if (!openComments || !ticketId) return;

  // Abrir modal de comentarios del ticket
  const btn = document.querySelector(`.comment-btn-${ticketId}`);
  if (btn) { btn.click(); return; }

  // Si el ticket no está en la tabla visible (paginación), llamar directamente
  if (typeof openComentariosModal === 'function') {
    openComentariosModal(ticketId, '...');
  }
});
</script>
