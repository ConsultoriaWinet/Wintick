<?php

use app\models\Clientes;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ClientesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Clientes';

/* --- CSS personalizado --- */
$css = "
/* COLUMNAS RESPONSIVE */
@media (max-width: 768px) {
    .col-correo { display: none !important; } /* ocultar columna correo */
    .wrap-sm { white-space: normal !important; }
    .nowrap-sm { white-space: nowrap !important; }
}

/* CARD MÓVIL */
.card-mobile {
    cursor: pointer;
    border-left: 4px solid #007bff;
    transition: transform .1s ease-in-out, box-shadow .1s ease-in-out;
}
.card-mobile:hover {
    transform: scale(1.01);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
";
$this->registerCss($css);

?>

<style>
    body {
        padding-top: 0px;
    }

    .grid-id {
        max-width: 60px;
    }

    /* Columna ID más compacta */
    .cursor-pointer {
        cursor: pointer;
    }
</style>

<div class="clientes-index">

    <!-- CARD -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a('Crear Cliente', ['create'], ['class' => 'btn btn-success']) ?>
        </div>

        <div class="card-body">

            <!-- ================= TABLE DESKTOP ================= -->
            <div class="d-none d-md-block table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'rowOptions' => function ($model) {
                                        return [
                                            'class' => 'cursor-pointer',
                                            'onclick' => "openModal({$model->id})"
                                        ];
                                    },
                    'tableOptions' => ['class' => 'table table-bordered table-striped table-hover'],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        [
                            'attribute' => 'id',
                            'contentOptions' => ['class' => 'grid-id'],
                        ],
                        'Nombre',
                        [
                            'attribute' => 'Razon_social',
                            'label' => 'Razón social',
                            'contentOptions' => ['style' => 'max-width:150px; white-space:normal;'],
                        ],
                        'RFC',
                        [
                            'attribute' => 'Correo',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-correo'],
                            'contentOptions' => ['class' => 'col-correo'],
                            'value' => function ($model) {
                                                return Html::a(Html::encode($model->Correo), '#', [
                                                    'onclick' => "copyToClipboard(event, '" . Html::encode($model->Correo) . "')"
                                                ]);
                                            }
                        ],
                    ],
                ]); ?>
            </div>
            <!-- ================= END TABLE DESKTOP ================= -->

            <!-- ================= MOBILE CARDS ================= -->
            <div class="d-block d-md-none">
                <?php foreach ($dataProvider->models as $model): ?>
                    <div class="card mb-2 shadow-sm card-mobile" onclick="openModal(<?= $model->id ?>)">
                        <div class="card-body p-3">
                            <h5 class="card-title mb-1"><?= Html::encode($model->Nombre) ?></h5>
                            <p class="mb-1"><strong>RFC:</strong> <?= Html::encode($model->RFC) ?></p>
                            <p class="mb-1">
                                <strong>Razón Social:</strong><br><?= Html::encode($model->Razon_social) ?>
                            </p>
                            <p class="mb-1">
                                <strong>Correo:</strong>
                                <?= Html::a(Html::encode($model->Correo), '#', [
                                    'onclick' => "copyToClipboard(event, '" . Html::encode($model->Correo) . "')"
                                ]) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- ================= END MOBILE CARDS ================= -->

        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal-clientes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<!-- Toast -->
<div aria-live="polite" aria-atomic="true"
    style="position: fixed; top: 20px; right: 20px; z-index: 2000; min-width: 250px;">
    <div id="email-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000"
        style="font-size: 1rem; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
        <div class="d-flex align-items-center">
            <span class="me-2 text-success">&#10004;</span> <!-- Check icon -->
            <div class="toast-body text-dark" style="flex: 1;"></div>
            <button type="button" class="btn-close btn-close-white ms-2 mb-1" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>


<?php
$this->registerJs("
    // Función para abrir modal
    window.openModal = function(id) {
        $.get('" . Url::to(['clientes/update']) . "', {id: id}, function(data) {
            $('#modal-clientes .modal-body').html(data);
            $('#modal-clientes').modal('show');
        });
    };

    // Copiar correo y mostrar toast
    window.copyToClipboard = function(event, text) {
        event.stopPropagation(); // Evita abrir el modal
        navigator.clipboard.writeText(text).then(function() {
            var toastEl = document.getElementById('email-toast');
            toastEl.querySelector('.toast-body').innerText = 'Correo copiado: ' + text;
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        });
    };
");
?>