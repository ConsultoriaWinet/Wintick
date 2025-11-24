<?php
use yii\helpers\Html;

/** @var $model app\models\Usuarios */
?>

<div class="p-3">

    <h3><?= Html::encode($model->Nombre) ?></h3>

    <p><strong>Email:</strong> <?= Html::encode($model->email) ?></p>
    <p><strong>Color:</strong> <?= Html::encode($model->color) ?></p>

    <hr>

    <!-- Botones -->
    <div class="mt-3">
        <?= Html::a('Editar', ['update', 'id' => $model->id], [
            'class' => 'btn btn-warning btn-update-ajax'
        ]) ?>

        <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data-method' => 'post',
            'data-confirm' => '¿Seguro que deseas eliminar este usuario?'
        ]) ?>
    </div>

</div>