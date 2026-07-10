<?php

use yii\helpers\Html;
$this->title = 'Agenda de Pendientes';
?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h2 class="mb-0">
            <i class="fas fa-tasks"></i>
            Tickets Pendientes
        </h2>

        <div class="d-flex gap-2">

            <button class="btn btn-success" id="btnTeams">
                <i class="fas fa-camera"></i>
                Vista para Teams
            </button>

            <?= Html::a(
                '<i class="fas fa-arrow-left"></i> Tickets',
                ['tickets/index'],
                [
                    'class' => 'btn btn-primary',
                    'id' => 'btnTickets'
                ]
            ) ?>

        </div>

    </div>

    <div class="alert alert-info">
        <strong>Total de pendientes:</strong>
        <?= count($tickets) ?>
    </div>

    <table class="table table-hover table-bordered table-sm align-middle table-pendientes">

        <thead class="table-dark">
            <tr>
                <th>Folio</th>
                <th>Empresa</th>
                <th>Reportó</th>
                <th>Descripción</th>
                <th>Fecha Programada</th>
                <th>Hora</th>
                <th>Consultor</th>
                <th>Fecha Reporte</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>

            <?php if (empty($tickets)): ?>

                <tr>
                    <td colspan="6" class="text-center">
                        No hay tickets pendientes.
                    </td>
                </tr>

            <?php else: ?>

                <?php foreach ($tickets as $ticket): ?>

                    <?php

                    switch ($ticket->Estado) {
                        case 'ABIERTO':
                            $badge = 'bg-danger';
                            break;

                        case 'EN PROCESO':
                            $badge = 'bg-warning text-dark';
                            break;

                        case 'PROGRAMADO':
                            $badge = 'bg-primary';
                            break;

                        default:
                            $badge = 'bg-secondary';
                    }

                    ?>

                    <tr style="cursor:pointer" onclick="window.location='<?= yii\helpers\Url::to([
                        'tickets/index',
                        'id' => $ticket->id
                    ]) ?>'">
                        <td><?= $ticket->Folio ?></td>
                        <td><?= $ticket->cliente->Nombre ?? '-' ?></td>
                        <td><?= $ticket->Usuario_reporta ?></td>
                        <td><?= $ticket->Descripcion ?></td>
                        <td><?= $ticket->HoraInicio ? date('d/m/Y', strtotime($ticket->HoraInicio)) : '-' ?></td>
                        <td><?= $ticket->HoraInicio ? date('H:i', strtotime($ticket->HoraInicio)) : '-' ?></td>
                        <td><?= $ticket->usuarioAsignado->Nombre ?? '-' ?></td>
                        <td><?= $ticket->HoraProgramada ? date('d/m/Y', strtotime($ticket->HoraProgramada)) : '-' ?></td>
                        <td>
                            <span class="badge <?= $badge ?>">
                                <?= $ticket->Estado ?>
                            </span>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>

    </table>

</div>

<style>
    .table-pendientes {
        font-size: 11px;
    }

    .table-pendientes th,
    .table-pendientes td {
        padding: .22rem .40rem;
        vertical-align: middle;
        line-height: 1.15;
    }

    .table-pendientes th {
        white-space: nowrap;
    }

    .teams-mode header,
    .teams-mode footer,
    .teams-mode nav,
    .teams-mode .navbar {
        display: none !important;
    }

    .teams-mode .container {
        max-width: 100% !important;
        padding: 10px 20px;
    }

    .teams-mode .table-pendientes {
        font-size: 15px;
    }

    .teams-mode #btnTeams {
        position: fixed;
        top: 15px;
        right: 15px;
        z-index: 9999;
    }

    .table-pendientes .badge {
        font-size: 10px;
        padding: .20rem .45rem;
    }
</style>

<script>

    const btnTeams = document.getElementById('btnTeams');
    const btnTickets = document.getElementById('btnTickets');

    let teamsMode = false;

    btnTeams.addEventListener('click', function () {

        teamsMode = !teamsMode;

        document.body.classList.toggle('teams-mode');

        if (teamsMode) {

            btnTeams.innerHTML =
                '<i class="fas fa-arrow-left"></i> Volver';

            btnTickets.style.display = 'none';

        } else {

            btnTeams.innerHTML =
                '<i class="fas fa-camera"></i> Vista para Teams';

            btnTickets.style.display = '';

        }

    });

</script>