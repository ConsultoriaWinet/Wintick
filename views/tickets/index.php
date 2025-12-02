<?php

use app\models\Tickets;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;


/** @var yii\web\View $this */
/** @var app\models\TicketsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tickets';

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
// Estilos de Flatpickr (Tema Airbnb)
$this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css');
// Scripts de Flatpickr + Idioma Español
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js', ['position' => \yii\web\View::POS_HEAD]);
// Obtener mes y año actual si no hay filtro
$mesActual = Yii::$app->request->get('mes', date('Y-m'));
?>

<style>
    /* ========================================
       REMOVER CONTAINER DE YII2 (SOLO ESTA PÁGINA)
       ======================================== */
    body {
        margin: 0 !important;
        padding: 0 !important;
    }

    .tickets-index {
        width: 100vw !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Sobrescribir containers de Yii2 en esta página */
    .container:has(.tickets-index),
    .container-fluid:has(.tickets-index) {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Si el container está arriba en la jerarquía */
    .wrap:has(.tickets-index) > .container,
    .wrap:has(.tickets-index) > .container-fluid {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* ========================================
       CONTENEDOR PRINCIPAL
       ======================================== */
    .tickets-index {
        
        min-height: 100vh;
        padding: 30px 20px;
        
        
    }

    /* ========================================
       HEADER PRINCIPAL
       ======================================== */
    .tickets-header {
        margin: 50px;
        margin-top: 3%;
        background: white;
        color: black;
        padding: 25px 30px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .tickets-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .tickets-header h1 i {
        font-size: 32px;
    }

    .tickets-header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    /* ========================================
       BUSCADOR UNIVERSAL + FILTRO AVANZADO
       ======================================== */
    .search-filter-wrapper {
        display: flex;
        gap: 10px;
        align-items: center;
        flex: 1;
        max-width: 700px;
    }

    .global-search-container {
        position: relative;
        flex: 1;
        min-width: 280px;
    }

    .global-search-container i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: black;
        font-size: 16px;
        pointer-events: none;
    }

    #globalSearch {
        width: 100%;
        padding: 12px 45px 12px 45px;
        background: rgba(0, 0, 0, 0.15);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        color: white;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    #globalSearch::placeholder {
        color: black;
    }

    #globalSearch:focus {
        outline: none;
        background: rgba(0, 0, 0, 0.25);
        border-color: rgba(0, 0, 0, 0.6);
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    }

    .search-clear-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.2);
        border: none;
        color: black;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .search-clear-btn:hover {
        background: black;
        transform: translateY(-50%) scale(1.1);
    }

    .search-clear-btn.active {
        display: flex;
    }

    /* Dropdown filtro avanzado compacto */
    .compact-filter-dropdown {
        position: relative;
    }

    .compact-filter-btn {
        background: rgba(255, 255, 255, 0.15);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: black;
        padding: 10px 16px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .compact-filter-btn:hover {
        background: rgba(255, 255, 255, 0.25);
        border-color: rgba(255, 255, 255, 0.6);
    }

    .compact-filter-btn.active {
        background: rgba(255, 255, 255, 0.3);
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
    }

    .compact-filter-menu {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        padding: 20px;
        min-width: 350px;
        max-width: 450px;
        display: none;
        z-index: 1000;
        animation: slideDownFade 0.3s ease;
        margin: 30px;
        padding: 30px;
    }

    .compact-filter-menu.show {
        display: block;
    }

    @keyframes slideDownFade {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .filter-section-title {
        font-size: 12px;
        font-weight: 700;
        color: #1a1a1a;
        text-transform: uppercase;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .compact-filter-group {
        margin-bottom: 15px;
    }

    .compact-filter-group label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 5px;
        text-transform: uppercase;
    }

    .compact-filter-group select,
    .compact-filter-group input {
        width: 100%;
        padding: 8px 12px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        transition: all 0.2s ease;
    }

    .compact-filter-group select:focus,
    .compact-filter-group input:focus {
        outline: none;
        border-color: #1a1a1a;
        box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.1);
    }

    .compact-filter-actions {
        display: flex;
        gap: 8px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px solid #e5e7eb;
    }

    .compact-filter-actions button {
        flex: 1;
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-apply-filter {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: white;
    }

    .btn-apply-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .btn-clear-filter {
        background: #f3f4f6;
        color: #6b7280;
    }

    .btn-clear-filter:hover {
        background: #e5e7eb;
    }

    /* ========================================
       MES ACTUAL BADGE
       ======================================== */
    .mes-actual {
        background: gray;
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        font-weight: 600;
        margin-bottom: 20px;
        font-size: 14px;
        box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-left: 3%;
    }

    .mes-actual i {
        font-size: 18px;
    }

    /* ========================================
       STATS BAR
       ======================================== */
    .stats-bar {
        background: white;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin: 0 50px 20px 50px; 
    }

    .tickets-count {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 8px;
        padding:10px;
    }

    .tickets-count strong {
        color: #1a1a1a;
        font-size: 20px;
    }

    /* ========================================
       BOTONES DE ACCIÓN
       ======================================== */
    .btn-outline-success {
        background: white;
        color: #10b981;
        border: 2px solid #10b981;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-outline-success:hover {
        background: #10b981;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    #addMoreRows {
        background: white;
        color: #6b7280;
        border: 2px solid #d1d5db;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    #addMoreRows:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
        transform: translateY(-2px);
    }

    /* ========================================
       TABLA
       ======================================== */
    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin: 15px; 
    }

    .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    }

    .table thead th {
        font-weight: 600;
        color: white;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        padding: 15px 12px;
        border: none;
    }

    .table tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f9fafb;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .table tbody td {
        padding: 12px;
        vertical-align: middle;
        font-size: 13px;
        color: #374151;
    }

    .new-row {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-top: 3px solid #10b981;
        border-bottom: 3px solid #10b981;
    }

    .new-row td {
        padding: 12px;
    }

    /* Fila "Sin resultados" */
    .no-results-row {
        display: none;
    }

    .no-results-row td {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
        font-size: 15px;
    }

    .no-results-row.active {
        display: table-row;
    }

    /* ========================================
       BADGES Y ESTADOS
       ======================================== */
    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bg-danger { background: #9f8241;}
    .bg-warning { background: #9f8241; }
    .bg-info { background: #9f8241; }
    .bg-primary { background:#9f8241; }

    /* Estados clickeables */
    .estado-clickeable {
        padding: 8px 15px;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .estado-clickeable:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    /* ========================================
       BOTONES DE LA TABLA
       ======================================== */
    .btn-sm {
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 12px;
        transition: all 0.2s ease;
        border: 2px solid;
    }

    .btn-outline-info {
        color: #0891b2;
        border-color: #0891b2;
    }

    .btn-outline-info:hover {
        background: #0891b2;
        color: white;
        transform: scale(1.1);
    }

    .btn-outline-primary {
        color: #667eea;
        border-color: #667eea;
    }

    .btn-outline-primary:hover {
        background: #667eea;
        color: white;
        transform: scale(1.1);
    }

    .btn-outline-secondary {
        color: #6b7280;
        border-color: #6b7280;
    }

    .btn-outline-secondary:hover {
        background: #6b7280;
        color: white;
        transform: scale(1.1);
    }

    .btn-outline-danger {
        color: #ef4444;
        border-color: #ef4444;
    }

    .btn-outline-danger:hover {
        background: #ef4444;
        color: white;
        transform: scale(1.1);
    }

    .btn-outline-success.saveRow {
        color: #10b981;
        border-color: #10b981;
    }

    .btn-outline-success.saveRow:hover {
        background: #10b981;
        color: white;
    }

    /* ========================================
       MODAL STYLES
       ======================================== */
    .modal-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: white;
        border-bottom: none;
        padding: 20px 25px;
    }

    .modal-title {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-body {
        padding: 25px;
    }

    .modal-footer {
        border-top: 2px solid #e5e7eb;
        padding: 20px 25px;
    }

    /* ========================================
       TOOLTIP DESCRIPCIÓN
       ======================================== */
    .descripcion-cell {
        position: relative;
        transition: all 0.2s ease;
        cursor: help;
    }

    .descripcion-cell:hover {
        background-color: #f1f5f9 !important;
    }

    .tooltip {
        font-size: 13px !important;
    }

    .tooltip-inner {
        max-width: 400px !important;
        text-align: left !important;
        padding: 12px 16px !important;
        background-color: #1f2937 !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
        border-radius: 8px !important;
        line-height: 1.6 !important;
    }

    .tooltip-arrow::before {
        border-top-color: #1f2937 !important;
    }

    /* ========================================
       COMENTARIOS
       ======================================== */
    .comentario-item {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 15px 18px;
        margin-bottom: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .comentario-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        transform: translateX(5px);
    }

    .comentario-item.nota_interna {
        border-left-color: #f59e0b;
        background: #fef3c7;
    }

    .comentario-item.solucion {
        border-left-color: #10b981;
        background: #d1fae5;
    }

    .comentario-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .comentario-usuario {
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .comentario-tipo {
        font-size: 10px;
        padding: 3px 10px;
        border-radius: 15px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .comentario-tipo.comentario {
        background: #dbeafe;
        color: #1e40af;
    }

    .comentario-tipo.nota_interna {
        background: #fef3c7;
        color: #92400e;
    }

    .comentario-tipo.solucion {
        background: #d1fae5;
        color: #065f46;
    }

    .comentario-fecha {
        font-size: 11px;
        color: #9ca3af;
        font-weight: 600;
    }

    .comentario-texto {
        color: #374151;
        line-height: 1.6;
        margin: 0;
        font-size: 13px;
    }

    .comentarios-empty {
        text-align: center;
        padding: 50px 20px;
        color: #9ca3af;
    }

    .comentarios-empty i {
        font-size: 60px;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    /* ========================================
       RESPONSIVE
       ======================================== */
    @media (max-width: 768px) {
        .tickets-header {
            flex-direction: column;
            align-items: stretch;
        }

        .tickets-header h1 {
            font-size: 22px;
            text-align: center;
        }

        .search-filter-wrapper {
            flex-direction: column;
            max-width: 100%;
        }

        .global-search-container {
            min-width: 100%;
            max-width: 100%;
        }

        .compact-filter-menu {
            right: auto;
            left: 0;
            min-width: 100%;
        }

        .tickets-header-actions {
            width: 100%;
            flex-direction: column;
        }

        .stats-bar {
            flex-direction: column;
            gap: 15px;
        }
    }

    /* ========================================
       FLATPICKR CUSTOM STYLES
       ======================================== */
    .flatpickr-input {
        background: white !important;
        border: 2px solid #e5e7eb !important;
        border-radius: 8px !important;
        padding: 8px 35px 8px 12px !important;
        font-size: 12px !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
    }

    .flatpickr-input:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
        outline: none !important;
    }

    .flatpickr-input:hover {
        border-color: #667eea !important;
    }

    /* Icono de calendario */
    .datetime-wrapper {
        position: relative;
    }

    .datetime-wrapper::after {
        content: '\f073';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #667eea;
        pointer-events: none;
        font-size: 14px;
    }
</style>

<div class="tickets-index">
    <!-- Header Principal con Buscador Universal + Filtro Compacto -->
    <div class="tickets-header">
        <h1><i class="fas fa-ticket-alt"></i> <?= Html::encode($this->title) ?></h1>
        
        <!-- BUSCADOR UNIVERSAL + FILTRO AVANZADO COMPACTO -->
        <div class="search-filter-wrapper">
            <!-- Buscador Universal Instantáneo -->
            <div class="global-search-container">
                <i class="fas fa-search"></i>
                <input type="text" 
                       id="globalSearch" 
                       placeholder="🔍 Buscar por cualquier cosa..."
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
                
                <div class="compact-filter-menu" id="compactFilterMenu">
                    <form method="get" id="compactFilterForm">
                        <!-- Sección: Fechas -->
                        <div class="filter-section-title">
                            <i class="fas fa-calendar"></i> FECHAS
                        </div>
                        <div class="compact-filter-group">
                            <label>Mes</label>
                            <input type="month" name="mes" value="<?= $_GET['mes'] ?? '' ?>" placeholder="Seleccionar mes (opcional)">
                        </div>

                        <!-- Sección: Identidad -->
                        <div class="filter-section-title">
                            <i class="fas fa-id-card"></i> IDENTIDAD
                        </div>
                        <div class="compact-filter-group">
                            <label>Cliente</label>
                            <select name="cliente_id">
                                <option value="">Todos</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= $cliente['id'] ?>" <?= ($_GET['cliente_id'] ?? '') == $cliente['id'] ? 'selected' : '' ?>>
                                        <?= Html::encode($cliente['Nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="compact-filter-group">
                            <label>Sistema</label>
                            <select name="sistema_id">
                                <option value="">Todos</option>
                                <?php foreach ($sistemas as $sistema): ?>
                                    <option value="<?= $sistema['id'] ?>" <?= ($_GET['sistema_id'] ?? '') == $sistema['id'] ? 'selected' : '' ?>>
                                        <?= Html::encode($sistema['Nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="compact-filter-group">
                            <label>Servicio</label>
                            <select name="servicio_id">
                                <option value="">Todos</option>
                                <?php foreach ($servicios as $servicio): ?>
                                    <option value="<?= $servicio['id'] ?>" <?= ($_GET['servicio_id'] ?? '') == $servicio['id'] ? 'selected' : '' ?>>
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
                            <select name="prioridad">
                                <option value="">Todas</option>
                                <option value="BAJA" <?= ($_GET['prioridad'] ?? '') == 'BAJA' ? 'selected' : '' ?>>Baja</option>
                                <option value="MEDIA" <?= ($_GET['prioridad'] ?? '') == 'MEDIA' ? 'selected' : '' ?>>Media</option>
                                <option value="ALTA" <?= ($_GET['prioridad'] ?? '') == 'ALTA' ? 'selected' : '' ?>>Alta</option>
                            </select>
                        </div>

                        <div class="compact-filter-group">
                            <label>Estado</label>
                            <select name="estado">
                                <option value="">Todos</option>
                                <option value="ABIERTO" <?= ($_GET['estado'] ?? '') == 'ABIERTO' ? 'selected' : '' ?>>Abierto</option>
                                <option value="EN PROCESO" <?= ($_GET['estado'] ?? '') == 'EN PROCESO' ? 'selected' : '' ?>>En Proceso</option>
                                <option value="CERRADO" <?= ($_GET['estado'] ?? '') == 'CERRADO' ? 'selected' : '' ?>>Cerrado</option>
                            </select>
                        </div>

                        <div class="compact-filter-group">
                            <label>Asignado A</label>
                            <select name="asignado_a">
                                <option value="">Todos</option>
                                <?php foreach ($Usuarios as $usuario): ?>
                                    <option value="<?= $usuario['id'] ?>" <?= ($_GET['asignado_a'] ?? '') == $usuario['id'] ? 'selected' : '' ?>>
                                        <?= Html::encode($usuario['email']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Botones -->
                        <div class="compact-filter-actions">
                            <button type="submit" class="btn-apply-filter">
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
    <div class="mes-actual" style="background: #10b981;">
        <i class="fas fa-calendar-alt"></i>
        <strong>Todos los tickets</strong>
    </div>
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
        <table class="table table-hover table-sm" id="ticketsTable">
            <thead>
                <tr>
                    <th class="text-primary-emphasis">Folio</th>
                    <th class="text-primary-emphasis">Cliente</th>
                    <th class="text-primary-emphasis">Sistema</th>
                    <th class="text-primary-emphasis">Servicio</th>
                    <th class="text-primary-emphasis">Usuario Reporta</th>
                    <th class="text-primary-emphasis">Asignado A</th>
                    <th class="text-primary-emphasis">Hora Programada</th>
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
                                <option value="<?= $usuario['id'] ?>"><?= Html::encode($usuario['email']) ?></option>
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
                <tr class="data-row existing-row" data-ticket-id="<?= $ticket->id ?>">
                    <td><strong><?= Html::encode($ticket->Folio) ?></strong></td>
                    <td><?= $ticket->cliente ? Html::encode($ticket->cliente->Nombre) : '-' ?></td>
                    <td><?= $ticket->sistema ? Html::encode($ticket->sistema->Nombre) : '-' ?></td>
                    <td><?= $ticket->servicio ? Html::encode($ticket->servicio->Nombre) : '-' ?></td>
                    <td><?= Html::encode($ticket->Usuario_reporta) ?></td>
                    <td><?= $ticket->usuarioAsignado ? Html::encode($ticket->usuarioAsignado->email) : '-' ?></td>
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
                            'ABIERTO' => 'bg-primary text-white',
                            'EN PROCESO' => 'bg-info text-dark',
                            'CERRADO' => 'bg-danger text-white',
                            default => 'bg-secondary'
                        };
                        $estadoIcon = match($ticket->Estado) {
                            'ABIERTO' => 'fa-circle-notch',
                            'EN PROCESO' => 'fa-spinner',
                            'CERRADO' => 'fa-check-circle',
                            default => 'fa-question-circle'
                        };
                        ?>
                        <div class="estado-clickeable <?= $estadoClass ?>" onclick="toggleEstadoSelect(this, <?= $ticket->id ?>)">
                            <i class="fas <?= $estadoIcon ?>"></i> <?= Html::encode($ticket->Estado) ?>
                        </div>
                        <select class="form-select form-select-sm estado-select estado-<?= $ticket->id ?>" onchange="updateEstado(this, <?= $ticket->id ?>)" style="display: none; font-size: 12px; margin-top: 5px;">
                            <option value="ABIERTO" <?= $ticket->Estado == 'ABIERTO' ? 'selected' : '' ?>>Abierto</option>
                            <option value="EN PROCESO" <?= $ticket->Estado == 'EN PROCESO' ? 'selected' : '' ?>>En Proceso</option>
                            <option value="CERRADO" <?= $ticket->Estado == 'CERRADO' ? 'selected' : '' ?>>Cerrado</option>
                        </select>
                    </td>
                    <td style="white-space: nowrap;">
                        <button class="btn btn-sm btn-outline-info" title="Ver comentarios" onclick="openComentariosModal(<?= $ticket->id ?>, '<?= Html::encode($ticket->Folio) ?>')">
                            <i class="fas fa-comments"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary" title="Agregar solución" onclick="openSolutionModal(<?= $ticket->id ?>, '<?= Html::encode($ticket->Folio) ?>')">
                            <i class="fas fa-lightbulb"></i>
                        </button>
                        <?= Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $ticket->id], ['class' => 'btn btn-sm btn-outline-secondary', 'title' => 'Editar']) ?>
                        <?= Html::a('<i class="fas fa-trash"></i>', '#', [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'title' => 'Eliminar',
                            'onclick' => "confirmarEliminar({$ticket->id}, '{$ticket->Folio}')",
                        ]) ?>
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
    </div>

    <button type="button" class="mt-3 m-3" id="addMoreRows">
        <i class="fas fa-plus"></i> Agregar más filas
    </button>
</div>

<!-- Modal para Solución -->
<div class="modal fade" id="solutionModal" tabindex="-1" aria-labelledby="solutionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="solutionModalLabel">
                    <i class="fas fa-wrench"></i> Solución del Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ticketId" value="">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-clock"></i> Hora Finalización</label>
                    <input type="datetime-local" id="horaFinalizo" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-align-left"></i> Solución</label>
                    <textarea id="solucion" class="form-control" rows="5" placeholder="Describe la solución aplicada..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-hourglass-end"></i> Tiempo efectivo invertido</label>
                    <input type="text" id="tiempoEfectivo" class="form-control" placeholder="Ejemplo: 2 horas, 30 minutos">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="saveSolution()">
                    <i class="fas fa-save"></i> Guardar Solución
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Comentarios -->
<div class="modal fade" id="comentariosModal" tabindex="-1" aria-labelledby="comentariosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="comentariosModalLabel">
                    <i class="fas fa-comments"></i> Comentarios del Ticket <span id="ticketFolioComentarios"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ticketIdComentarios" value="">
                
                <!-- Lista de comentarios -->
                <div id="listaComentarios" style="max-height: 400px; overflow-y: auto; margin-bottom: 20px;">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-spinner fa-spin"></i> Cargando comentarios...
                    </div>
                </div>
                
                <!-- Formulario para nuevo comentario -->
                <div style="border-top: 2px solid #e5e7eb; padding-top: 15px;">
                    <h6 style="color: #495057; margin-bottom: 10px;">
                        <i class="fas fa-plus-circle"></i> Agregar nuevo comentario
                    </h6>
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-tag"></i> Tipo de comentario</label>
                        <select id="tipoComentario" class="form-select">
                            <option value="comentario">💬 Comentario general</option>
                            <option value="nota_interna">📝 Nota interna</option>
                            <option value="solucion">✅ Solución propuesta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-comment"></i> Comentario</label>
                        <textarea id="nuevoComentario" class="form-control" rows="3" placeholder="Escribe tu comentario aquí..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeComentariosModal()">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <button type="button" class="btn btn-primary" onclick="agregarComentario()">
                    <i class="fas fa-paper-plane"></i> Enviar Comentario
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ========================================
// VARIABLES GLOBALES
// ========================================
let rowsCache = [];
const totalTicketsOriginal = <?= $dataProvider->getTotalCount() ?>;

// ========================================
// FOLIO AUTOINCREMENTAL
// ========================================
function loadNextFolio(inputElement) {
    if (!inputElement) return;
    
    inputElement.value = '⏳ Generando...';
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
// CARGAR DATOS DE CLIENTE
// ========================================
function loadClienteData(selectElement) {
    const row = selectElement.closest('tr');
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    if (selectedOption.value === '') {
        return;
    }
    
    const prioridad = selectedOption.getAttribute('data-prioridad');
    
    if (prioridad) {
        row.querySelector('.prioridad').value = prioridad;
    }
    row.querySelector('.estado').value = 'ABIERTO';
}

// ========================================
// BÚSQUEDA UNIVERSAL
// ========================================
function normalizeText(text) {
    return text.toLowerCase()
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
            // Crear formulario para enviar DELETE
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= Url::to(['index']) ?>/' ;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
            csrfInput.value = '<?= Yii::$app->request->getCsrfToken() ?>';
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            
            form.submit();
        }
    });
}

function performSearch(query) {
    const normalizedQuery = normalizeText(query);
    
    if (!normalizedQuery) {
        rowsCache.forEach(item => {
            item.element.style.display = '';
        });
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
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

const debouncedSearch = debounce(performSearch, 150);

// ========================================
// INICIALIZAR FLATPICKR
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
// GUARDAR TICKET
// ========================================
function saveTicket(row) {
    console.log('🚀 Guardando ticket...');
    
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

    console.log('📋 Datos del ticket:', ticket);

    if (!ticket.Folio || !ticket.Cliente_id || !ticket.Usuario_reporta || !ticket.Asignado_a) {
        alert('⚠️ Por favor completa los campos requeridos');
        return;
    }

    if (!ticket.Descripcion || ticket.Descripcion.trim() === '') {
        alert('⚠️ Por favor escribe una descripción');
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
        console.log('📡 Respuesta:', response.status);
        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('📦 Datos:', data);
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Ticket guardado: ' + ticket.Folio,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end'
            }).then(() => {
                location.reload();
            });
        } else {
            console.error('❌ Error:', data);
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
        console.error('💥 Error:', error);
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
// ESTADOS
// ========================================
function updateEstado(selectElement, ticketId) {
    const estado = selectElement.value;
    const div = selectElement.previousElementSibling;
    
    div.className = 'estado-clickeable ' + getEstadoClass(estado);
    div.innerHTML = '<i class="fas ' + getEstadoIcon(estado) + '"></i> ' + estado;
    div.style.display = 'inline-flex';
    selectElement.style.display = 'none';
    
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
        if (data.success) {
            buildRowsCache();
        }
    })
    .catch(error => console.error('Error:', error));
}

function toggleEstadoSelect(element, ticketId) {
    const select = document.querySelector('.estado-' + ticketId);
    if (select.style.display === 'none') {
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
        'ABIERTO': 'bg-primary text-white',
        'EN PROCESO': 'bg-info text-dark',
        'CERRADO': 'bg-danger text-white'
    };
    return classes[estado] || 'bg-secondary';
}

function getEstadoIcon(estado) {
    const icons = {
        'ABIERTO': 'fa-circle-notch',
        'EN PROCESO': 'fa-spinner',
        'CERRADO': 'fa-check-circle'
    };
    return icons[estado] || 'fa-question-circle';
}

// ========================================
// MODALES
// ========================================
function openSolutionModal(ticketId, folio) {
    const selectElement = document.querySelector('.estado-' + ticketId);
    const estado = selectElement.value;
    
    if (estado !== 'CERRADO') {
        alert('⚠️ El ticket debe estar CERRADO');
        return;
    }
    
    document.getElementById('ticketId').value = ticketId;
    
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
        if (data.success) {
            document.getElementById('horaFinalizo').value = data.ticket.HoraFinalizo || '';
            document.getElementById('solucion').value = data.ticket.Solucion || '';
            document.getElementById('tiempoEfectivo').value = data.ticket.TiempoEfectivo || '';
        }
    });
    
    const modal = document.getElementById('solutionModal');
    modal.classList.add('show');
    modal.style.display = 'block';
    document.body.classList.add('modal-open');
    
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    document.body.appendChild(backdrop);
}

function saveSolution() {
    const ticketId = document.getElementById('ticketId').value;
    const solucion = document.getElementById('solucion').value;
    const horaFinalizo = document.getElementById('horaFinalizo').value;
    const tiempoEfectivo = document.getElementById('tiempoEfectivo').value;
    
    if (!solucion || !horaFinalizo || !tiempoEfectivo) {
        alert('⚠️ Completa todos los campos');
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
            alert('✅ Solución guardada');
            closeModal();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Desconocido'));
        }
    });
}

function closeModal() {
    const modal = document.getElementById('solutionModal');
    modal.classList.remove('show');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
    
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) backdrop.remove();
    
    document.getElementById('horaFinalizo').value = '';
    document.getElementById('solucion').value = '';
    document.getElementById('tiempoEfectivo').value = '';
}

function openComentariosModal(ticketId, folio) {
    document.getElementById('ticketIdComentarios').value = ticketId;
    document.getElementById('ticketFolioComentarios').textContent = folio;
    document.getElementById('nuevoComentario').value = '';
    document.getElementById('tipoComentario').value = 'comentario';
    
    cargarComentarios(ticketId);
    
    const modal = document.getElementById('comentariosModal');
    modal.classList.add('show');
    modal.style.display = 'block';
    document.body.classList.add('modal-open');
    
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    backdrop.id = 'comentariosBackdrop';
    document.body.appendChild(backdrop);
}

function cargarComentarios(ticketId) {
    const lista = document.getElementById('listaComentarios');
    lista.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
    
    fetch('<?= Url::to(['/tickets/obtener-comentarios']) ?>?ticket_id=' + ticketId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarComentarios(data.comentarios);
        } else {
            lista.innerHTML = '<div class="alert alert-danger">Error al cargar</div>';
        }
    });
}

function mostrarComentarios(comentarios) {
    const lista = document.getElementById('listaComentarios');
    
    if (comentarios.length === 0) {
        lista.innerHTML = `
            <div class="comentarios-empty">
                <i class="fas fa-comments"></i>
                <p>No hay comentarios</p>
            </div>
        `;
        return;
    }
    
    lista.innerHTML = comentarios.map(c => `
        <div class="comentario-item ${c.tipo}">
            <div class="comentario-header">
                <div class="comentario-usuario">
                    <i class="fas fa-user-circle"></i>
                    ${c.usuario}
                    <span class="comentario-tipo ${c.tipo}">${getTipoLabel(c.tipo)}</span>
                </div>
                <span class="comentario-fecha">
                    <i class="fas fa-clock"></i> ${c.fecha}
                </span>
            </div>
            <p class="comentario-texto">${escapeHtml(c.comentario)}</p>
        </div>
    `).join('');
}

function agregarComentario() {
    const ticketId = document.getElementById('ticketIdComentarios').value;
    const comentario = document.getElementById('nuevoComentario').value.trim();
    const tipo = document.getElementById('tipoComentario').value;
    
    if (!comentario) {
        alert('Escribe un comentario');
        return;
    }
    
    fetch('<?= Url::to(['/tickets/agregar-comentario']) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
        },
        body: JSON.stringify({
            ticket_id: ticketId,
            comentario: comentario,
            tipo: tipo
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('nuevoComentario').value = '';
            cargarComentarios(ticketId);
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function closeComentariosModal() {
    const modal = document.getElementById('comentariosModal');
    modal.classList.remove('show');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
    
    const backdrop = document.getElementById('comentariosBackdrop');
    if (backdrop) backdrop.remove();
}

function getTipoLabel(tipo) {
    const labels = {
        'comentario': '💬 Comentario',
        'nota_interna': '📝 Nota Interna',
        'solucion': '✅ Solución'
    };
    return labels[tipo] || tipo;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ========================================
// DOCUMENT READY
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('globalSearch');
    const clearButton = document.getElementById('clearSearch');
    const filterBtn = document.getElementById('compactFilterBtn');
    const filterMenu = document.getElementById('compactFilterMenu');
    
    buildRowsCache();
    
    document.querySelectorAll('.flatpickr-datetime').forEach(initializeFlatpickr);
    
    const initialSaveBtn = document.querySelector('.new-row .saveRow');
    if (initialSaveBtn) {
        initialSaveBtn.addEventListener('click', function() {
            saveTicket(this.closest('tr'));
        });
    }
    
    const initialDeleteBtn = document.querySelector('.new-row .deleteRow');
    if (initialDeleteBtn) {
        initialDeleteBtn.addEventListener('click', function() {
            const row = this.closest('tr');
            row.querySelectorAll('input, textarea, select').forEach(field => {
                if (!field.classList.contains('folio')) {
                    field.value = '';
                }
            });
            loadNextFolio(row.querySelector('.folio'));
        });
    }
    
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        clearButton.classList.toggle('active', query);
        debouncedSearch(query);
    });
    
    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        clearButton.classList.remove('active');
        performSearch('');
        searchInput.focus();
    });
    
    filterBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        filterMenu.classList.toggle('show');
        filterBtn.classList.toggle('active');
    });
    
    document.addEventListener('click', function(e) {
        if (!filterMenu.contains(e.target) && !filterBtn.contains(e.target)) {
            filterMenu.classList.remove('show');
            filterBtn.classList.remove('active');
        }
    });
    
    loadNextFolio(document.querySelector('.new-row .folio'));
    
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl, {
        trigger: 'hover',
        delay: { show: 300, hide: 100 }
    }));
});

// ========================================
// AGREGAR FILAS
// ========================================
document.getElementById('addMoreRows').addEventListener('click', function() {
    const tableBody = document.getElementById('tableBody');
    const templateRow = document.querySelector('.new-row');
    const newRow = templateRow.cloneNode(true);
    
    newRow.querySelectorAll('input, textarea, select').forEach(field => {
        if (!field.classList.contains('folio')) {
            field.value = '';
        }
        if (field.tagName === 'SELECT' && !field.classList.contains('estado')) {
            field.selectedIndex = 0;
        }
    });
    
    loadNextFolio(newRow.querySelector('.folio'));
    
    newRow.querySelectorAll('.flatpickr-datetime').forEach(function(element) {
        if (element._flatpickr) {
            element._flatpickr.destroy();
        }
        initializeFlatpickr(element);
    });
    
    const saveBtn = newRow.querySelector('.saveRow');
    const deleteBtn = newRow.querySelector('.deleteRow');
    const clienteSelect = newRow.querySelector('.cliente');
    
    saveBtn.removeAttribute('onclick');
    deleteBtn.removeAttribute('onclick');
    clienteSelect.removeAttribute('onchange');
    
    saveBtn.addEventListener('click', function() {
        saveTicket(newRow);
    });
    
    deleteBtn.addEventListener('click', function() {
        if (confirm('¿Eliminar fila?')) {
            newRow.remove();
        }
    });
    
    clienteSelect.addEventListener('change', function() {
        loadClienteData(this);
    });
    
    tableBody.appendChild(newRow);
});
</script>