<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Tickets $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tickets-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'Folio',
            'Usuario_reporta',
            'Asignado_a',
            'Estado',
            'Descripcion:ntext',
            'Solucion:ntext',
            'HoraProgramada',
            'HoraInicio',
            'TiempoRestante',
            'HoraFinalizo',
            'TiempoEfectivo',
            'Cliente_id',
            'Sistema_id',
            'Servicio_id',
            'Creado_por',
            'Fecha_creacion',
            'Fecha_actualizacion',
        ],
    ]) ?>

</div>
