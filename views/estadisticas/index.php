
<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Estadísticas de Tickets';
?><style>
:root {
    --primary: #A0BAA5;
    --primary-dark: #8BA590;
    --text: #1f2937;
    --muted: #6b7280;
    --card-bg: #f8f9fa;
}

.estadisticas-index {
    padding: 24px;
}

.page-header {
    margin-bottom: 24px;
    padding-bottom: 12px;
    border-bottom: 3px solid var(--primary);
}

.page-header h1 {
    color: var(--text);
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.page-header p {
    color: var(--muted);
    margin: 0;
}

/* Cards */
.card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    overflow: hidden;
}

.card-header {
    background: var(--card-bg);
    border-bottom: 1px solid #e5e7eb;
    padding: 14px 18px;
    font-weight: 600;
    color: var(--text);
}

.card-body {
    padding: 18px;
}

.kpi-card {
    color: #fff;
    border: none;
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(0,0,0,0.12);
}

.kpi-card .card-body {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.kpi-title {
    font-size: 14px;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kpi-value {
    font-size: 28px;
    font-weight: 700;
}

.kpi-sub {
    font-size: 13px;
    opacity: 0.9;
}

/* Colors */
.bg-primary-soft { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); }
.bg-warning-soft { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }
.bg-info-soft    { background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%); }
.bg-success-soft { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); }

/* Tables & badges */
.badge {
    font-size: 12px;
    padding: 6px 10px;
    border-radius: 10px;
}

.table {
    margin: 0;
}
.table thead {
    background: var(--primary);
    color: #fff;
}
.table thead th {
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.table tbody tr:hover {
    background: #f3f4f6;
}

/* Progress */
.progress {
    height: 10px;
    border-radius: 10px;
    background: #e5e7eb;
}
.progress-bar {
    background: var(--primary);
}

/* Responsive */
@media (max-width: 768px) {
    .estadisticas-index { padding: 16px; }
    .page-header h1 { font-size: 22px; }
}
</style>

<div class="estadisticas-index">
    <div class="page-header">
        <h1><i class="fas fa-chart-bar"></i> <?= Html::encode($this->title) ?></h1>
        <p class="text-muted">Panel completo de análisis y métricas del sistema</p>
    </div>

    <!-- Filtro por mes -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-calendar"></i> Seleccionar Mes</label>
                    <input type="month" name="mes" class="form-control" value="<?= $mesActual ?>" onchange="this.form.submit()">
                </div>
                <div class="col-md-8 text-end">
                    <span class="badge bg-info fs-6">Año: <?= $yearActual ?></span>
                </div>
            </form>
        </div>
    </div>

    <!-- Tarjetas de resumen principales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1"><i class="fas fa-ticket-alt"></i> Total Tickets</h6>
                            <h2 class="mb-0"><?= $estadisticasTickets['total'] ?></h2>
                            <small>
                                <?php if ($comparacionMes['diferencia'] > 0): ?>
                                    <i class="fas fa-arrow-up"></i> +<?= $comparacionMes['porcentaje'] ?>%
                                <?php elseif ($comparacionMes['diferencia'] < 0): ?>
                                    <i class="fas fa-arrow-down"></i> <?= $comparacionMes['porcentaje'] ?>%
                                <?php else: ?>
                                    <i class="fas fa-minus"></i> 0%
                                <?php endif; ?>
                                vs mes anterior
                            </small>
                        </div>
                        <div class="icon-large">
                            <i class="fas fa-ticket-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1"><i class="fas fa-folder-open"></i> Abiertos</h6>
                            <h2 class="mb-0"><?= $estadisticasTickets['abiertos'] ?></h2>
                            <small><?= $estadisticasTickets['total'] > 0 ? round(($estadisticasTickets['abiertos'] / $estadisticasTickets['total']) * 100, 1) : 0 ?>% del total</small>
                        </div>
                        <div class="icon-large">
                            <i class="fas fa-folder-open fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1"><i class="fas fa-spinner"></i> En Proceso</h6>
                            <h2 class="mb-0"><?= $estadisticasTickets['enProceso'] ?></h2>
                            <small><?= $estadisticasTickets['total'] > 0 ? round(($estadisticasTickets['enProceso'] / $estadisticasTickets['total']) * 100, 1) : 0 ?>% del total</small>
                        </div>
                        <div class="icon-large">
                            <i class="fas fa-spinner fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1"><i class="fas fa-check-circle"></i> Cerrados</h6>
                            <h2 class="mb-0"><?= $estadisticasTickets['cerrados'] ?></h2>
                            <small>Tasa: <?= $tasaResolucion['tasa'] ?>%</small>
                        </div>
                        <div class="icon-large">
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas adicionales -->
    <div class="row mb-4 text-center align-items-center">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-clock fa-3x text-primary mb-2"></i>
                    <h5 class="text-muted mb-1">Tiempo Promedio Resolución</h5>
                    <h2 class="mb-0 text-primary"><?= $tiempoPromedio ?> <small class="fs-5">hrs</small></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-percentage fa-3x text-success mb-2"></i>
                    <h5 class="text-muted mb-1">Tasa de Resolución</h5>
                    <h2 class="mb-0 text-success"><?= $tasaResolucion['tasa'] ?>%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos principales -->
    <div class="row mb-4">
        <!-- Gráfico de Estado -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Tickets por Estado</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartEstado" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Prioridad -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Tickets por Prioridad</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPrioridad" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de línea por día -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Tickets Creados por Día del Mes</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartDias" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico tickets por hora -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-business-time"></i> Tickets por Hora del Día</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartHoras" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN DE CONSULTORES -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-users-cog"></i> Desempeño de Consultores</h4>
                </div>
                <div class="card-body">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#consultoresMes" type="button">
                                <i class="fas fa-calendar-day"></i> Mes Actual
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#consultoresAnio" type="button">
                                <i class="fas fa-calendar-alt"></i> Año <?= $yearActual ?>
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Consultores del Mes -->
                        <div class="tab-pane fade show active" id="consultoresMes">
                            <?php if (empty($consultoresDelMes)): ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No hay datos de consultores en este mes</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th><i class="fas fa-user"></i> Consultor</th>
                                                <th class="text-center"><i class="fas fa-list"></i> Total</th>
                                                <th class="text-center"><i class="fas fa-check text-success"></i> Cerrados</th>
                                                <th class="text-center"><i class="fas fa-folder-open text-info"></i> Abiertos</th>
                                                <th class="text-center"><i class="fas fa-spinner text-warning"></i> En Proceso</th>
                                                <th class="text-center"><i class="fas fa-chart-pie"></i> Eficiencia</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $pos = 1; foreach ($consultoresDelMes as $consultor): ?>
                                                <?php 
                                                    $eficiencia = $consultor['total'] > 0 ? round(($consultor['cerrados'] / $consultor['total']) * 100, 1) : 0;
                                                    $colorEficiencia = $eficiencia >= 80 ? 'success' : ($eficiencia >= 50 ? 'warning' : 'danger');
                                                ?>
                                                <tr>
                                                    <td><strong><?= $pos++ ?></strong></td>
                                                    <td>
                                                        <i class="fas fa-user-circle text-primary me-2"></i>
                                                        <?= Html::encode($consultor['nombre']) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary rounded-pill"><?= $consultor['total'] ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success rounded-pill"><?= $consultor['cerrados'] ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info rounded-pill"><?= $consultor['abiertos'] ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-warning rounded-pill"><?= $consultor['en_proceso'] ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-<?= $colorEficiencia ?> rounded-pill"><?= $eficiencia ?>%</span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Consultores del Año -->
                        <div class="tab-pane fade" id="consultoresAnio">
                            <?php if (empty($consultoresDelAnio)): ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No hay datos de consultores en este año</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th><i class="fas fa-user"></i> Consultor</th>
                                                <th class="text-center"><i class="fas fa-list"></i> Total Tickets</th>
                                                <th class="text-center"><i class="fas fa-check text-success"></i> Cerrados</th>
                                                <th class="text-center"><i class="fas fa-clock"></i> Tiempo Prom. (hrs)</th>
                                                <th class="text-center"><i class="fas fa-chart-pie"></i> Eficiencia</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $pos = 1; foreach ($consultoresDelAnio as $consultor): ?>
                                                <?php 
                                                    $eficiencia = $consultor['total'] > 0 ? round(($consultor['cerrados'] / $consultor['total']) * 100, 1) : 0;
                                                    $colorEficiencia = $eficiencia >= 80 ? 'success' : ($eficiencia >= 50 ? 'warning' : 'danger');
                                                ?>
                                                <tr>
                                                    <td><strong><?= $pos++ ?></strong></td>
                                                    <td>
                                                        <i class="fas fa-user-circle text-primary me-2"></i>
                                                        <?= Html::encode($consultor['nombre']) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary rounded-pill fs-6"><?= $consultor['total'] ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success rounded-pill fs-6"><?= $consultor['cerrados'] ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info rounded-pill"><?= round($consultor['tiempo_promedio'] ?? 0, 2) ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-<?= $colorEficiencia ?> rounded-pill fs-6"><?= $eficiencia ?>%</span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN DE CLIENTES MÁS ATENDIDOS -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-gradient-info text-white">
                    <h4 class="mb-0"><i class="fas fa-building"></i> Empresas con Más Servicios</h4>
                </div>
                <div class="card-body">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#clientesMes" type="button">
                                <i class="fas fa-calendar-day"></i> Mes Actual
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#clientesAnio" type="button">
                                <i class="fas fa-calendar-alt"></i> Año <?= $yearActual ?>
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Clientes del Mes -->
                        <div class="tab-pane fade show active" id="clientesMes">
                            <?php if (empty($clientesMasAtendidos)): ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No hay datos de clientes en este mes</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th><i class="fas fa-building"></i> Cliente</th>
                                                <th class="text-center"><i class="fas fa-list"></i> Total Tickets</th>
                                                <th class="text-center"><i class="fas fa-check text-success"></i> Cerrados</th>
                                                <th class="text-center"><i class="fas fa-clock"></i> Tiempo Prom. (hrs)</th>
                                                <th class="text-center"><i class="fas fa-chart-bar"></i> % del Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $pos = 1; $totalGeneral = array_sum(array_column($clientesMasAtendidos, 'total')); ?>
                                            <?php foreach ($clientesMasAtendidos as $cliente): ?>
                                                <?php 
                                                    $porcentaje = $totalGeneral > 0 ? round(($cliente['total'] / $totalGeneral) * 100, 1) : 0;
                                                ?>
                                                <tr>
                                                    <td><strong><?= $pos++ ?></strong></td>
                                                    <td>
                                                        <i class="fas fa-briefcase text-info me-2"></i>
                                                        <?= Html::encode($cliente['cliente']) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary rounded-pill"><?= $cliente['total'] ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success rounded-pill"><?= $cliente['cerrados'] ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-warning"><?= round($cliente['tiempo_promedio'] ?? 0, 2) ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-info" role="progressbar" style="width: <?= $porcentaje ?>%">
                                                                <?= $porcentaje ?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Clientes del Año -->
                        <div class="tab-pane fade" id="clientesAnio">
                            <?php if (empty($clientesMasAtendidosAnio)): ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No hay datos de clientes en este año</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th><i class="fas fa-building"></i> Cliente</th>
                                                <th class="text-center"><i class="fas fa-list"></i> Total Tickets</th>
                                                <th class="text-center"><i class="fas fa-check text-success"></i> Cerrados</th>
                                                <th class="text-center"><i class="fas fa-chart-bar"></i> % del Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $pos = 1; $totalGeneralAnio = array_sum(array_column($clientesMasAtendidosAnio, 'total')); ?>
                                            <?php foreach ($clientesMasAtendidosAnio as $cliente): ?>
                                                <?php 
                                                    $porcentaje = $totalGeneralAnio > 0 ? round(($cliente['total'] / $totalGeneralAnio) * 100, 1) : 0;
                                                ?>
                                                <tr>
                                                    <td><strong><?= $pos++ ?></strong></td>
                                                    <td>
                                                        <i class="fas fa-briefcase text-info me-2"></i>
                                                        <?= Html::encode($cliente['cliente']) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary rounded-pill fs-6"><?= $cliente['total'] ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success rounded-pill fs-6"><?= $cliente['cerrados'] ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="progress" style="height: 25px;">
                                                            <div class="progress-bar bg-info" role="progressbar" style="width: <?= $porcentaje ?>%">
                                                                <strong><?= $porcentaje ?>%</strong>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets por Sistema y Servicio -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-server"></i> Top Sistemas</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartSistemas" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-tools"></i> Top Servicios</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartServicios" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.estadisticas-index {
    padding: 20px;
}

.page-header {
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 3px solid #0891b2;
}

.page-header h1 {
    color: #0891b2;
    font-weight: 600;
    margin-bottom: 5px;
}

.card {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: none;
    border-radius: 12px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.card-header {
    border-bottom: 2px solid #e5e7eb;
    background: #f8f9fa !important;
    border-radius: 12px 12px 0 0 !important;
    padding: 15px 20px;
}

.card-header h5, .card-header h4 {
    font-weight: 600;
    color: #374151;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%) !important;
}

.bg-gradient-info {
    background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%) !important;
}

.bg-primary { 
    background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%) !important;
}

.bg-info { 
    background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%) !important;
}

.bg-warning { 
    background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%) !important;
}

.bg-success { 
    background: linear-gradient(135deg, #10b981 0%, #34d399 100%) !important;
}

.icon-large {
    opacity: 0.3;
}

.table {
    font-size: 14px;
}

.table thead {
    background: #2c3e50;
    color: white;
}

.table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
    border: none !important;
}

.table-hover tbody tr:hover {
    background-color: #f1f5f9;
}

.badge {
    font-size: 13px;
    padding: 6px 12px;
}

.nav-tabs .nav-link {
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    color: #0891b2;
    font-weight: 600;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    font-weight: 600;
}

@media (max-width: 768px) {
    .row.mb-4 .col-md-3 {
        margin-bottom: 15px;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Datos para gráficos
const dataEstado = <?= json_encode($ticketsPorEstado) ?>;
const dataPrioridad = <?= json_encode($ticketsPorPrioridad) ?>;
const dataDias = <?= json_encode($ticketsPorDia) ?>;
const dataHoras = <?= json_encode($ticketsPorHora) ?>;
const dataSistemas = <?= json_encode($ticketsPorSistema) ?>;
const dataServicios = <?= json_encode($ticketsPorServicio) ?>;

// Colores
const coloresEstado = {
    'ABIERTO': '#0891b2',
    'EN PROCESO': '#f59e0b',
    'CERRADO': '#10b981',
    'CANCELADO': '#ef4444'
};

const coloresPrioridad = {
    'BAJA': '#06b6d4',
    'MEDIA': '#f59e0b',
    'ALTA': '#ef4444',
    'URGENTE': '#8b5cf6'
};

// Gráfico de Estado
if (dataEstado.length > 0) {
    new Chart(document.getElementById('chartEstado'), {
        type: 'pie',
        data: {
            labels: dataEstado.map(d => d.Estado),
            datasets: [{
                data: dataEstado.map(d => d.total),
                backgroundColor: dataEstado.map(d => coloresEstado[d.Estado] || '#6c757d'),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// Gráfico de Prioridad
if (dataPrioridad.length > 0) {
    new Chart(document.getElementById('chartPrioridad'), {
        type: 'doughnut',
        data: {
            labels: dataPrioridad.map(d => d.Prioridad),
            datasets: [{
                data: dataPrioridad.map(d => d.total),
                backgroundColor: dataPrioridad.map(d => coloresPrioridad[d.Prioridad] || '#6c757d'),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// Gráfico de Días
if (dataDias.length > 0) {
    new Chart(document.getElementById('chartDias'), {
        type: 'line',
        data: {
            labels: dataDias.map(d => new Date(d.fecha + 'T00:00:00').toLocaleDateString('es-MX', {day: '2-digit', month: '2-digit'})),
            datasets: [{
                label: 'Tickets Creados',
                data: dataDias.map(d => d.total),
                borderColor: '#0891b2',
                backgroundColor: 'rgba(8, 145, 178, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
}

// Gráfico de Horas
if (dataHoras.length > 0) {
    new Chart(document.getElementById('chartHoras'), {
        type: 'bar',
        data: {
            labels: dataHoras.map(d => d.hora + ':00'),
            datasets: [{
                label: 'Tickets por Hora',
                data: dataHoras.map(d => d.total),
                backgroundColor: '#06b6d4',
                borderColor: '#0891b2',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
}

// Gráfico de Sistemas
if (dataSistemas.length > 0) {
    new Chart(document.getElementById('chartSistemas'), {
        type: 'bar',
        data: {
            labels: dataSistemas.map(d => d.sistema),
            datasets: [{
                label: 'Tickets',
                data: dataSistemas.map(d => d.total),
                backgroundColor: '#8b5cf6',
                borderWidth: 0
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { beginAtZero: true }
            }
        }
    });
}

// Gráfico de Servicios
if (dataServicios.length > 0) {
    new Chart(document.getElementById('chartServicios'), {
        type: 'bar',
        data: {
            labels: dataServicios.map(d => d.servicio),
            datasets: [{
                label: 'Tickets',
                data: dataServicios.map(d => d.total),
                backgroundColor: '#f59e0b',
                borderWidth: 0
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { beginAtZero: true }
            }
        }
    });
}
</script>