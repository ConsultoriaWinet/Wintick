<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Tickets $model */

$this->title = 'Crear Ticket';
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tickets-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('fechaDesdeCalendario')): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-calendar-check"></i>
            <strong>Fecha seleccionada desde el calendario:</strong> 
            <?= Yii::$app->session->getFlash('fechaDesdeCalendario') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
