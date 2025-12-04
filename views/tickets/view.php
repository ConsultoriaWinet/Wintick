<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Tickets $model */

$this->title = 'Ticket #' . $model->Folio;

// Registrar SweetAlert2 y FontAwesome
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => \yii\web\View::POS_HEAD]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
?>

<style>
    .ticket-view {
        width: 90%;
        margin: auto;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .ticket-header {
        background: #A0BAA5;
        color: white;
        padding: 30px 40px;
        position: relative;
    }

    .ticket-title {
        font-size: 28px;
        font-weight: 600;
        margin: 0 0 10px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ticket-subtitle {
        font-size: 16px;
        opacity: 0.95;
        margin: 0;
        font-weight: 400;
    }

    .ticket-content {
        padding: 35px;
    }

    .ticket-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        border-left: 4px solid #A0BAA5;
    }

    .info-card h3 {
        margin: 0 0 15px 0;
        font-size: 18px;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #64748b;
        font-size: 14px;
    }

    .info-value {
        color: #1e293b;
        font-weight: 500;
        text-align: right;
        max-width: 60%;
    }

    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-abierto {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-proceso {
        background: #fef3c7;
        color: #b45309;
    }

    .status-cerrado {
        background: #d1fae5;
        color: #065f46;
    }

    .priority-badge {
        padding: 5px 10px;
        border-radius: 16px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .priority-alta {
        background: #fee2e2;
        color: #991b1b;
    }

    .priority-media {
        background: #fef3c7;
        color: #b45309;
    }

    .priority-baja {
        background: #d1fae5;
        color: #065f46;
    }

    .description-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
        border-left: 4px solid #A0BAA5;
    }

    .solution-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
        border-left: 4px solid #A0BAA5;
    }

    .times-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
        border-left: 4px solid #A0BAA5;
    }

    .times-section h3 {
        margin: 0 0 15px 0;
        font-size: 18px;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #e2e8f0;
        justify-content: center;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .btn-primary {
        background: #A0BAA5;
        color: white;
    }

    .btn-primary:hover {
        background: #8fa994;
        transform: translateY(-1px);
    }

    .btn-danger {
        background: #dc2626;
        color: white;
    }

    .btn-danger:hover {
        background: #b91c1c;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #64748b;
        color: white;
    }

    .btn-secondary:hover {
        background: #475569;
        transform: translateY(-1px);
    }

    .empty-field {
        color: #94a3b8;
        font-style: italic;
    }

    .section-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 25px 0;
    }

    @media (max-width: 768px) {
        .ticket-view {
            width: 95%;
            margin: 20px auto;
        }

        .ticket-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .action-buttons {
            flex-direction: column;
            align-items: stretch;
        }

        .ticket-content {
            padding: 25px;
        }

        .ticket-header {
            padding: 25px;
        }
    }
</style>

<div class="ticket-view">
    <div class="ticket-header">
        <div class="ticket-title">
            <i class="fas fa-ticket-alt"></i>
            <?= Html::encode($this->title) ?>
        </div>
        <p class="ticket-subtitle">
            Cliente: <?= $model->cliente ? Html::encode($model->cliente->Nombre) : 'No asignado' ?>
        </p>
    </div>

    <div class="ticket-content">
        <div class="ticket-grid">
            <!-- Información General -->
            <div class="info-card">
                <h3><i class="fas fa-info-circle" style="color: #A0BAA5;"></i> Información General</h3>

                <div class="info-item">
                    <span class="info-label">Folio</span>
                    <span class="info-value">#<?= Html::encode($model->Folio) ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Usuario Reporta</span>
                    <span class="info-value"><?= Html::encode($model->Usuario_reporta ?: 'No especificado') ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Asignado A</span>
                    <span class="info-value">
                        <?= $model->usuarioAsignado ? Html::encode($model->usuarioAsignado->email) : '<span class="empty-field">Sin asignar</span>' ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Estado</span>
                    <span class="info-value">
                        <span class="status-badge status-<?= strtolower($model->Estado) ?>">
                            <?= Html::encode($model->Estado) ?>
                        </span>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Prioridad</span>
                    <span class="info-value">
                        <span class="priority-badge priority-<?= strtolower($model->Prioridad) ?>">
                            <?= Html::encode($model->Prioridad) ?>
                        </span>
                    </span>
                </div>
            </div>

            <!-- Información del Servicio -->
            <div class="info-card">
                <h3><i class="fas fa-cogs" style="color: #A0BAA5;"></i> Información del Servicio</h3>

                <div class="info-item">
                    <span class="info-label">Cliente</span>
                    <span class="info-value">
                        <?= $model->cliente ? Html::encode($model->cliente->Nombre) : '<span class="empty-field">No asignado</span>' ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Sistema</span>
                    <span class="info-value">
                        <?= $model->sistema ? Html::encode($model->sistema->Nombre) : '<span class="empty-field">No asignado</span>' ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Servicio</span>
                    <span class="info-value">
                        <?= $model->servicio ? Html::encode($model->servicio->Nombre) : '<span class="empty-field">No asignado</span>' ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Creado Por</span>
                    <span class="info-value">
                        <?= $model->usuarioCreador ? Html::encode($model->usuarioCreador->email) : Html::encode($model->Creado_por) ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Fechas y Tiempos -->
        <div class="times-section">
            <h3><i class="fas fa-clock" style="color: #A0BAA5;"></i> Fechas y Tiempos</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px;">

                <div class="info-item">
                    <span class="info-label">Hora Programada</span>
                    <span class="info-value">
                        <?= $model->HoraProgramada ? date('d/m/Y H:i', strtotime($model->HoraProgramada)) : '<span class="empty-field">No definida</span>' ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Hora Inicio</span>
                    <span class="info-value">
                        <?= $model->HoraInicio ? date('d/m/Y H:i', strtotime($model->HoraInicio)) : '<span class="empty-field">No definida</span>' ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Hora Finalizó</span>
                    <span class="info-value">
                        <?= $model->HoraFinalizo ? date('d/m/Y H:i', strtotime($model->HoraFinalizo)) : '<span class="empty-field">No definida</span>' ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Tiempo Efectivo</span>
                    <span class="info-value">
                        <?= Html::encode($model->TiempoEfectivo ?: 'No definido') ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Fecha Creación</span>
                    <span class="info-value">
                        <?= $model->Fecha_creacion ? date('d/m/Y H:i', strtotime($model->Fecha_creacion)) : '<span class="empty-field">No definida</span>' ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Última Actualización</span>
                    <span class="info-value">
                        <?= $model->Fecha_actualizacion ? date('d/m/Y H:i', strtotime($model->Fecha_actualizacion)) : '<span class="empty-field">No definida</span>' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Descripción -->
        <div class="description-section">
            <h3
                style="margin: 0 0 15px 0; color: #2d3748; display: flex; align-items: center; gap: 10px; font-weight: 600;">
                <i class="fas fa-file-alt" style="color: #A0BAA5;"></i> Descripción del Problema
            </h3>
            <div style="background: white; padding: 15px; border-radius: 8px; line-height: 1.6; color: #334155;">
                <?= $model->Descripcion ? Html::encode($model->Descripcion) : '<span class="empty-field">No hay descripción disponible</span>' ?>
            </div>
        </div>

        <!-- Solución -->
        <?php if ($model->Solucion): ?>
            <div class="solution-section">
                <h3
                    style="margin: 0 0 15px 0; color: #2d3748; display: flex; align-items: center; gap: 10px; font-weight: 600;">
                    <i class="fas fa-lightbulb" style="color: #A0BAA5;"></i> Solución Aplicada
                </h3>
                <div style="background: white; padding: 15px; border-radius: 8px; line-height: 1.6; color: #334155;">
                    <?= Html::encode($model->Solucion) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Botones de Acción -->
        <div class="action-buttons">
            <?= Html::a('<i class="fas fa-edit"></i> Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

            <?= Html::a('<i class="fas fa-trash"></i> Eliminar', '#', [
                'class' => 'btn btn-danger',
                'onclick' => "confirmarEliminar({$model->id}, '{$model->Folio}')"
            ]) ?>

            <?= Html::a('<i class="fas fa-arrow-left"></i> Volver a Tickets', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>
</div>

<?php
$this->registerJs("
function confirmarEliminar(ticketId, folio) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Se eliminará el ticket ' + folio + ' permanentemente',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class=\"fas fa-trash\"></i> Sí, eliminar',
        cancelButtonText: '<i class=\"fas fa-times\"></i> Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '" . \yii\helpers\Url::to(['delete']) . "?id=' + ticketId;
        }
    });
}
", \yii\web\View::POS_END);
?>