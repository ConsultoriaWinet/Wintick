<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var int $year */
/** @var int $semana */
/** @var string $inicio */
/** @var string $fin */
/** @var array $estadisticas */
/** @var array $ticketsPorEstado */
/** @var array $ticketsPorConsultor */
/** @var array $ticketsPorServicio */

$this->title = 'Reporte Semanal';

$fechaInicio = new DateTime($inicio);
$fechaFin = new DateTime($fin);

$meses = [
    1 => 'enero',
    2 => 'febrero',
    3 => 'marzo',
    4 => 'abril',
    5 => 'mayo',
    6 => 'junio',
    7 => 'julio',
    8 => 'agosto',
    9 => 'septiembre',
    10 => 'octubre',
    11 => 'noviembre',
    12 => 'diciembre',
];

$fechaTexto =
    $fechaInicio->format('d') . ' de ' .
    $meses[(int) $fechaInicio->format('m')] . ' de ' .
    $fechaInicio->format('Y') .
    ' al ' .
    $fechaFin->format('d') . ' de ' .
    $meses[(int) $fechaFin->format('m')] . ' de ' .
    $fechaFin->format('Y');

?>

<div class="container-fluid mt-4">

    <!-- ENCABEZADO -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="mb-1">
                <i class="fas fa-chart-line"></i>
                Reporte Semanal
            </h1>

            <div class="text-muted">
                Semana
                <?= Html::encode($semana) ?> —
                <?= Html::encode($fechaTexto) ?>
            </div>
        </div>

        <div class="d-flex gap-2">

            <?= Html::a(
                '<i class="fas fa-chevron-left"></i> Semana anterior',
                [
                    'estadisticas/semanal',
                    'year' => $fechaInicio->modify('-7 days')->format('o'),
                    'semana' => $fechaInicio->format('W')
                ],
                ['class' => 'btn btn-outline-secondary']
            ) ?>

            <?= Html::a(
                'Semana actual',
                [
                    'estadisticas/semanal',
                    'year' => date('o'),
                    'semana' => date('W')
                ],
                ['class' => 'btn btn-primary']
            ) ?>

            <?= Html::a(
                '<i class="fas fa-chevron-right"></i> Semana siguiente',
                [
                    'estadisticas/semanal',
                    'year' => (new DateTime($fin))->modify('+1 day')->format('o'),
                    'semana' => (new DateTime($fin))->modify('+1 day')->format('W')
                ],
                ['class' => 'btn btn-outline-secondary']
            ) ?>

        </div>

    </div>


    <!-- SELECTOR DE SEMANA -->
    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="get" action="<?= Url::to(['estadisticas/semanal']) ?>">

                <div class="row align-items-end">

                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            Año
                        </label>

                        <input type="number" name="year" class="form-control" value="<?= Html::encode($year) ?>"
                            min="2020" max="2100">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            Semana
                        </label>

                        <input type="number" name="semana" class="form-control" value="<?= Html::encode($semana) ?>"
                            min="1" max="53">
                    </div>

                    <div class="col-md-3">

                        <button type="submit" class="btn btn-dark w-100">
                            <i class="fas fa-search"></i>
                            Consultar semana
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- RESUMEN -->
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card resumen-card">
                <div class="card-body">

                    <div class="resumen-titulo">
                        Total de Servicios
                    </div>

                    <div class="resumen-numero">
                        <?= $estadisticas['total'] ?>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-md-3">
            <div class="card resumen-card">
                <div class="card-body">

                    <div class="resumen-titulo">
                        Abiertos
                    </div>

                    <div class="resumen-numero">
                        <?= $estadisticas['abiertos'] ?>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-md-3">
            <div class="card resumen-card">
                <div class="card-body">

                    <div class="resumen-titulo">
                        En proceso
                    </div>

                    <div class="resumen-numero">
                        <?= $estadisticas['enProceso'] ?>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-md-3">
            <div class="card resumen-card">
                <div class="card-body">

                    <div class="resumen-titulo">
                        Cerrados
                    </div>

                    <div class="resumen-numero">
                        <?= $estadisticas['cerrados'] ?>
                    </div>

                </div>
            </div>
        </div>

    </div>


    <!-- GRÁFICAS -->
    <div class="row">

        <!-- ESTADO -->
        <div class="col-md-6 mb-4">

            <div class="card shadow-sm h-100">

                <div class="card-header fw-bold">
                    % de Estado de los Servicios
                </div>

                <div class="card-body">

                    <canvas id="graficaEstado"></canvas>

                </div>

            </div>

        </div>


        <!-- CONSULTORES -->
        <div class="col-md-6 mb-4">

            <div class="card shadow-sm h-100">

                <div class="card-header fw-bold">
                    % Servicios por Consultor
                </div>

                <div class="card-body">

                    <canvas id="graficaConsultores"></canvas>

                </div>

            </div>

        </div>


        <!-- SERVICIOS -->
        <div class="col-md-12 mb-4">

            <div class="card shadow-sm">

                <div class="card-header fw-bold">
                    Servicios realizados por tipo
                </div>

                <div class="card-body">

                    <canvas id="graficaServicios"></canvas>

                </div>

            </div>

        </div>

    </div>


    <!-- TABLA CONSULTORES -->
    <div class="card shadow-sm mb-5">

        <div class="card-header fw-bold">
            Detalle por Consultor
        </div>

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0">

                <thead class="table-dark">

                    <tr>
                        <th>Consultor</th>
                        <th>Total</th>
                        <th>Abiertos</th>
                        <th>En proceso</th>
                        <th>Programados</th>
                        <th>Cerrados</th>
                        <th>Cerrados cliente</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if (empty($ticketsPorConsultor)): ?>

                        <tr>
                            <td colspan="7" class="text-center">
                                No hay servicios registrados esta semana.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($ticketsPorConsultor as $consultor): ?>

                            <tr>

                                <td>
                                    <strong>
                                        <?= Html::encode($consultor['consultor']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= $consultor['total'] ?>
                                </td>

                                <td>
                                    <?= $consultor['abiertos'] ?>
                                </td>

                                <td>
                                    <?= $consultor['en_proceso'] ?>
                                </td>

                                <td>
                                    <?= $consultor['programados'] ?>
                                </td>

                                <td>
                                    <?= $consultor['cerrados'] ?>
                                </td>

                                <td>
                                    <?= $consultor['cerrados_cliente'] ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    const estados = <?= json_encode($ticketsPorEstado) ?>;
    const consultores = <?= json_encode($ticketsPorConsultor) ?>;
    const servicios = <?= json_encode($ticketsPorServicio) ?>;


    /* ==========================
       GRÁFICA ESTADOS
    ========================== */

    new Chart(document.getElementById('graficaEstado'), {

        type: 'pie',

        data: {

            labels: estados.map(item => item.Estado),

            datasets: [{

                data: estados.map(item => Number(item.total))

            }]

        },

        options: {

            responsive: true,

            plugins: {

                legend: {
                    position: 'bottom'
                }

            }

        }

    });


    /* ==========================
       GRÁFICA CONSULTORES
    ========================== */

    new Chart(document.getElementById('graficaConsultores'), {

        type: 'pie',

        data: {

            labels: consultores.map(item => item.consultor),

            datasets: [{

                data: consultores.map(item => Number(item.total))

            }]

        },

        options: {

            responsive: true,

            plugins: {

                legend: {
                    position: 'bottom'
                }

            }

        }

    });


    /* ==========================
       GRÁFICA SERVICIOS
    ========================== */

    new Chart(document.getElementById('graficaServicios'), {

        type: 'bar',

        data: {

            labels: servicios.map(item => item.servicio),

            datasets: [{

                label: 'Servicios',

                data: servicios.map(item => Number(item.total))

            }]

        },

        options: {

            responsive: true,

            plugins: {

                legend: {
                    display: false
                }

            },

            scales: {

                y: {
                    beginAtZero: true
                }

            }

        }

    });

</script>


<style>
    .resumen-card {
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
    }

    .resumen-titulo {
        color: #666;
        font-size: 14px;
    }

    .resumen-numero {
        font-size: 32px;
        font-weight: bold;
        margin-top: 5px;
    }

    .card-header {
        background: #f8f6f0;
    }

    canvas {
        max-height: 380px;
    }
</style>