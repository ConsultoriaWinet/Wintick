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
/** @var array $ticketsDetalleConsultor */

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
                    <div class="d-flex justify-content-between align-items-center">

                        <span>
                            % Servicios por Consultor
                        </span>

                        <div class="consultor-filter">

                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnConsultores">
                                <i class="fas fa-users"></i>
                                Consultores
                                <i class="fas fa-chevron-down"></i>
                            </button>

                            <div id="consultoresMenu" class="consultores-menu">

                                <label class="consultor-option todos-option">
                                    <input type="checkbox" id="checkTodosConsultores" checked>
                                    <strong>Todos</strong>
                                </label>

                                <hr>

                                <?php foreach ($ticketsPorConsultor as $index => $consultor): ?>

                                    <label class="consultor-option">

                                        <input type="checkbox" class="check-consultor"
                                            value="<?= Html::encode($consultor['consultor']) ?>" checked>

                                        <?= Html::encode($consultor['consultor']) ?>

                                    </label>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    </div>
                </div>

                <div class="card-body">

                    <canvas id="graficaConsultores"></canvas>

                </div>

                <div id="resumenConsultores" class="alert alert-info py-2 mb-3 text-center">
                    Todos los consultores realizaron el 100% de los servicios de la semana.
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

                            <?php
                            // Buscar los tickets correspondientes a este consultor
                            $ticketsConsultor = array_filter(
                                $ticketsDetalleConsultor,
                                function ($ticket) use ($consultor) {
                                    return $ticket['consultor'] === $consultor['consultor'];
                                }
                            );

                            // ID seguro para usar en HTML/JavaScript
                            $consultorId = 'consultor-' . md5($consultor['consultor']);
                            ?>

                            <!-- FILA DEL CONSULTOR -->
                            <tr class="consultor-row" onclick="toggleTickets('<?= $consultorId ?>')" style="cursor:pointer;">

                                <td>
                                    <strong>
                                        <i class="fas fa-chevron-right toggle-icon"></i>
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


                            <!-- TICKETS DEL CONSULTOR -->
                            <tr id="<?= $consultorId ?>" class="tickets-consultor" style="display:none;">

                                <td colspan="7">

                                    <div class="p-3 bg-light">

                                        <?php if (empty($ticketsConsultor)): ?>

                                            <div class="text-muted text-center">
                                                No hay tickets para este consultor.
                                            </div>

                                        <?php else: ?>

                                            <table class="table table-sm table-bordered mb-0">

                                                <thead class="table-secondary">

                                                    <tr>
                                                        <th>Folio</th>
                                                        <th>Cliente</th>
                                                        <th>Servicio</th>
                                                        <th>Estado</th>
                                                        <th>Fecha / Hora Inicio</th>
                                                        <th>Descripción</th>
                                                    </tr>

                                                </thead>

                                                <tbody>

                                                    <?php foreach ($ticketsConsultor as $ticket): ?>

                                                        <tr>

                                                            <td>
                                                                <?= Html::a(
                                                                    Html::encode($ticket['Folio']),
                                                                    ['tickets/view', 'id' => $ticket['id']],
                                                                    [
                                                                        'class' => 'fw-bold text-decoration-none',
                                                                        'target' => '_blank'
                                                                    ]
                                                                ) ?>
                                                            </td>

                                                            <td>
                                                                <?= Html::encode($ticket['cliente'] ?? '-') ?>
                                                            </td>

                                                            <td>
                                                                <?= Html::encode($ticket['servicio'] ?? '-') ?>
                                                            </td>

                                                            <td>
                                                                <?= Html::encode($ticket['Estado'] ?? '-') ?>
                                                            </td>

                                                            <td>
                                                                <?= !empty($ticket['HoraInicio'])
                                                                    ? date('d/m/Y H:i', strtotime($ticket['HoraInicio']))
                                                                    : '-' ?>
                                                            </td>

                                                            <td>
                                                                <?= Html::encode($ticket['Descripcion'] ?? '-') ?>
                                                            </td>

                                                        </tr>

                                                    <?php endforeach; ?>

                                                </tbody>

                                            </table>

                                        <?php endif; ?>

                                    </div>

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

    let graficaConsultores;


    /* =================================
       OBTENER CONSULTORES SELECCIONADOS
    ================================= */

    function obtenerConsultoresSeleccionados() {

        const checks = document.querySelectorAll('.check-consultor');

        return Array.from(checks)
            .filter(check => check.checked)
            .map(check => check.value);

    }


    /* =================================
       ACTUALIZAR GRÁFICA
    ================================= */

    function actualizarGraficaConsultores() {

        const seleccionados = obtenerConsultoresSeleccionados();

        const datosFiltrados = consultores.filter(item =>
            seleccionados.includes(item.consultor)
        );

        const labels = datosFiltrados.map(
            item => item.consultor
        );

        const datos = datosFiltrados.map(
            item => Number(item.total)
        );

        // Total de servicios de toda la semana
        const totalSemana = Number(
            <?= json_encode($estadisticas['total']) ?>
        );

        // Total de servicios de los consultores seleccionados
        const totalSeleccionados = datos.reduce(
            (a, b) => a + b,
            0
        );

        // Porcentaje respecto al total de la semana
        const porcentajeSemana = totalSemana > 0
            ? ((totalSeleccionados / totalSemana) * 100).toFixed(1)
            : 0;

        // Nombres seleccionados
        const nombres = datosFiltrados.map(
            item => item.consultor
        );

        // Actualizar mensaje
        const resumen = document.getElementById(
            'resumenConsultores'
        );

        if (nombres.length === 0) {

            resumen.innerHTML =
                '<strong>No hay consultores seleccionados.</strong>';

        } else if (nombres.length === consultores.length) {

            resumen.innerHTML =
                `<strong>Todos los consultores</strong> ` +
                `realizaron el <strong>100%</strong> ` +
                `de los servicios de la semana ` +
                `(${totalSeleccionados} de ${totalSemana}).`;

        } else {

            let textoNombres;

            if (nombres.length === 1) {

                textoNombres = nombres[0];

            } else if (nombres.length === 2) {

                textoNombres =
                    nombres[0] + ' y ' + nombres[1];

            } else {

                textoNombres =
                    nombres.slice(0, -1).join(', ') +
                    ' y ' +
                    nombres[nombres.length - 1];
            }

            const verbo =
                nombres.length === 1
                    ? 'realizó'
                    : 'realizaron';

            resumen.innerHTML =
                `<strong>${textoNombres}</strong> ` +
                `${verbo} el <strong>${porcentajeSemana}%</strong> ` +
                `de los servicios de la semana ` +
                `(${totalSeleccionados} de ${totalSemana}).`;
        }

        // Destruir gráfica anterior
        if (graficaConsultores) {
            graficaConsultores.destroy();
        }

        // Crear gráfica
        graficaConsultores = new Chart(
            document.getElementById('graficaConsultores'),
            {
                type: 'pie',

                data: {
                    labels: labels,

                    datasets: [{
                        data: datos
                    }]
                },

                options: {

                    responsive: true,

                    plugins: {

                        legend: {
                            position: 'bottom'
                        },

                        tooltip: {

                            callbacks: {

                                label: function (context) {

                                    const total =
                                        datos.reduce(
                                            (a, b) => a + b,
                                            0
                                        );

                                    const valor = context.raw;

                                    const porcentaje = total > 0
                                        ? ((valor / total) * 100).toFixed(1)
                                        : 0;

                                    return `${context.label}: ${valor} (${porcentaje}%)`;
                                }

                            }

                        }

                    }

                }

            }
        );
    }


    /* =================================
       ABRIR / CERRAR MENU
    ================================= */

    const btnConsultores =
        document.getElementById('btnConsultores');

    const consultoresMenu =
        document.getElementById('consultoresMenu');


    btnConsultores.addEventListener('click', function (e) {

        e.stopPropagation();

        consultoresMenu.classList.toggle('show');

    });


    document.addEventListener('click', function (e) {

        if (
            !consultoresMenu.contains(e.target) &&
            !btnConsultores.contains(e.target)
        ) {

            consultoresMenu.classList.remove('show');

        }

    });


    /* =================================
       CHECK "TODOS"
    ================================= */

    const checkTodos =
        document.getElementById('checkTodosConsultores');


    checkTodos.addEventListener('change', function () {

        document
            .querySelectorAll('.check-consultor')
            .forEach(check => {

                check.checked = this.checked;

            });

        actualizarGraficaConsultores();

    });


    /* =================================
       CHECK INDIVIDUAL
    ================================= */

    document
        .querySelectorAll('.check-consultor')
        .forEach(check => {

            check.addEventListener('change', function () {

                const todos =
                    document.querySelectorAll('.check-consultor');

                const seleccionados =
                    document.querySelectorAll(
                        '.check-consultor:checked'
                    );

                checkTodos.checked =
                    todos.length === seleccionados.length;

                actualizarGraficaConsultores();

            });

        });


    /* =================================
       INICIALIZAR
    ================================= */

    actualizarGraficaConsultores();


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

    function toggleTickets(id) {

        const fila = document.getElementById(id);

        if (!fila) {
            return;
        }

        const consultorRow = fila.previousElementSibling;
        const icon = consultorRow.querySelector('.toggle-icon');

        if (fila.style.display === 'none' || fila.style.display === '') {

            fila.style.display = 'table-row';

            if (icon) {
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-down');
            }

        } else {

            fila.style.display = 'none';

            if (icon) {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-right');
            }
        }
    }

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

    .consultor-row {
        transition: background-color .15s ease;
    }

    .consultor-row:hover {
        background-color: #f3f3f3;
    }

    .toggle-icon {
        width: 16px;
        margin-right: 6px;
        font-size: 12px;
    }

    .tickets-consultor td {
        background-color: #fafafa;
    }

    .tickets-consultor table {
        font-size: 13px;
    }

    .consultor-filter {
        position: relative;
    }

    .consultores-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 38px;
        z-index: 1000;

        width: 220px;
        max-height: 300px;
        overflow-y: auto;

        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;

        padding: 10px;

        box-shadow: 0 4px 15px rgba(0, 0, 0, .15);
    }

    .consultores-menu.show {
        display: block;
    }

    .consultor-option {
        display: flex;
        align-items: center;

        gap: 8px;

        padding: 7px 5px;

        margin: 0;

        cursor: pointer;

        font-size: 13px;
    }

    .consultor-option:hover {
        background: #f5f5f5;
        border-radius: 5px;
    }

    .consultor-option input {
        cursor: pointer;
    }

    .todos-option {
        padding-bottom: 8px;
    }
</style>