<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Usuarios $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<?= Html::a('Editar', ['update', 'id' => $model->id], [
    'class' => 'btn btn-warning btn-update-ajax'
]) ?>

<?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
    'class' => 'btn btn-danger',
    'data-method' => 'post',
    'data-confirm' => '¿Seguro que deseas eliminar este usuario?'
]) ?>

<!--<div class="usuarios-view">

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
            'Nombre',
            'password_hash',
            'password_reset_token',
            'email:email',
            'status',
            'rol',
            'created_at',
            'updated_at',
            'color',
        ],
    ]) ?>

</div>-->