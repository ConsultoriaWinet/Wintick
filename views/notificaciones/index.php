<?php

use app\models\Notificaciones;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\NotificacionesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Notificaciones';

$tipoIcono = [
    'asignado'   => ['icon' => 'bi-ticket-detailed',  'bg' => '#3b82f6'],
    'comentario' => ['icon' => 'bi-chat-dots',         'bg' => '#8BA590'],
    'solucion'   => ['icon' => 'bi-check-circle',      'bg' => '#22c55e'],
    'alerta'     => ['icon' => 'bi-exclamation-triangle','bg' => '#f59e0b'],
];
?>

<style>
.notif-card {
    border-radius: 10px;
    border: 1px solid #e8f0e9;
    transition: box-shadow .15s, border-color .15s;
    cursor: default;
}
.notif-card:hover {
    box-shadow: 0 3px 12px rgba(0,0,0,.09);
    border-color: #A0BAA5;
}
.notif-card.no-leida {
    border-left: 4px solid #A0BAA5;
    background: #f7fbf8;
}
.notif-card.leida {
    border-left: 4px solid #e5e7eb;
    opacity: .85;
}
.notif-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.1rem;
    color: #fff;
}
.notif-titulo {
    font-weight: 600;
    font-size: .93rem;
    color: #1f2933;
}
.notif-mensaje {
    font-size: .83rem;
    color: #6b7280;
    margin: 0;
}
.notif-fecha {
    font-size: .72rem;
    color: #9ca3af;
    white-space: nowrap;
}
.badge-leida {
    font-size: .68rem;
    padding: .2rem .55rem;
    border-radius: 50px;
    font-weight: 600;
}
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #9ca3af;
}
.empty-state i { font-size: 3rem; display: block; margin-bottom: .75rem; }
</style>

<div class="notificaciones-index">
    <div class="card shadow-sm border-0">

        <!-- HEADER -->
        <div class="card-header d-flex justify-content-between align-items-center text-white py-3"
             style="background: linear-gradient(135deg, #A0BAA5 0%, #8BA590 100%); border-bottom: none; border-radius: 8px 8px 0 0;">
            <h4 class="mb-0 fw-semibold"><i class="bi bi-bell-fill me-2"></i><?= Html::encode($this->title) ?></h4>
            <span class="badge" style="background:rgba(255,255,255,.25); font-size:.8rem; padding:.35rem .7rem; border-radius:50px;">
                <?= $dataProvider->getTotalCount() ?> total
            </span>
        </div>

        <div class="card-body p-3">

            <?php if ($dataProvider->getTotalCount() === 0): ?>
                <div class="empty-state">
                    <i class="bi bi-bell-slash"></i>
                    <p class="mb-0 fw-semibold">Sin notificaciones</p>
                    <small>No hay notificaciones registradas</small>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-2">
                    <?php foreach ($dataProvider->models as $notif): ?>
                        <?php
                            $tipo = strtolower($notif->tipo ?? 'alerta');
                            $cfg  = $tipoIcono[$tipo] ?? ['icon' => 'bi-bell', 'bg' => '#6b7280'];
                            $leida = (int)$notif->leida === 1;
                        ?>
                        <div class="notif-card p-3 d-flex align-items-start gap-3 <?= $leida ? 'leida' : 'no-leida' ?>">

                            <!-- Icono -->
                            <div class="notif-icon" style="background:<?= $cfg['bg'] ?>">
                                <i class="bi <?= $cfg['icon'] ?>"></i>
                            </div>

                            <!-- Contenido -->
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                    <span class="notif-titulo"><?= Html::encode($notif->titulo) ?></span>
                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                        <?php if ($leida): ?>
                                            <span class="badge-leida" style="background:#e5e7eb; color:#6b7280;">Leída</span>
                                        <?php else: ?>
                                            <span class="badge-leida" style="background:#dcfce7; color:#16a34a;">Nueva</span>
                                        <?php endif; ?>
                                        <span class="notif-fecha">
                                            <i class="bi bi-clock me-1"></i><?= Html::encode($notif->fecha_creacion) ?>
                                        </span>
                                    </div>
                                </div>
                                <p class="notif-mensaje mt-1"><?= Html::encode($notif->mensaje) ?></p>
                                <?php if ($notif->ticket_id): ?>
                                    <small class="text-muted">
                                        <i class="bi bi-ticket me-1"></i>
                                        <?= Html::a('Ver ticket #' . $notif->ticket_id, ['/tickets/view', 'id' => $notif->ticket_id], ['class' => 'text-decoration-none', 'style' => 'color:#8BA590; font-weight:600;']) ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <!-- Eliminar -->
                            <?= Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $notif->id], [
                                'class' => 'btn btn-sm btn-outline-danger flex-shrink-0',
                                'style' => 'padding:.25rem .5rem; border-radius:6px;',
                                'data'  => ['confirm' => '¿Eliminar esta notificación?', 'method' => 'post'],
                            ]) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
