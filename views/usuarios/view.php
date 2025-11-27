<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
?>

<div class="usuario-modal-view p-4">

    <!-- Encabezado con nombre -->
    <div class="d-flex align-items-center mb-4">
        <div class="usuario-avatar me-3" style="background-color: <?= Html::encode($model->color) ?>;">
            <?= strtoupper(substr(Html::encode($model->Nombre), 0, 1)) ?>
        </div>
        <div>
            <h4 class="mb-0"><?= Html::encode($model->Nombre) ?></h4>
            <small class="text-muted"><?= Html::encode($model->email) ?></small>
        </div>
    </div>

    <!-- Botones -->
    <div class="mb-3 d-flex gap-2">
        <?= Html::a(
            '<i class="bi bi-pencil-square"></i> Editar',
            ['update', 'id' => $model->id],
            ['class' => 'btn btn-warning btn-update-ajax']
        ) ?>

        <?= Html::a(
            '<i class="bi bi-trash"></i> Eliminar',
            ['delete', 'id' => $model->id],
            [
                'class' => 'btn btn-danger',
                'data-method' => 'post',
                'data-confirm' => '¿Seguro que deseas eliminar este usuario?'
            ]
        ) ?>
    </div>

    <!-- Información detallada -->
    <div class="card shadow-sm">
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Nombre',
                        'value' => $model->Nombre,
                    ],
                    [
                        'label' => 'Email',
                        'value' => $model->email,
                    ],
                    [
                        'label' => 'Color',
                        'value' => $model->color,
                    ],
                    [
                        'label' => 'Fecha de creación',
                        'value' => date('d/m/Y H:i', $model->created_at),
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>

<style>
    .usuario-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #fff;
        font-size: 26px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .usuario-modal-view h4 {
        font-weight: 600;
    }

    .card-body th {
        width: 180px;
    }
</style>


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