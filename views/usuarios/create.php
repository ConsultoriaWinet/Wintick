<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Usuarios $model */

$this->title = 'Nuevo Usuario';

?>
<div class="usuarios-create d-flex justify-content-center mt-5">

    <div class="col-lg-7 col-md-9">
        <div class="card border-0 shadow-lg">

            <div class="card-body p-4">
                <h3 class="text-center text-primary mb-3"><?= Html::encode($this->title) ?></h3>
                <p class="text-center text-muted">Rellena la información para registrar un nuevo usuario.</p>

                <?= $this->render('_form', ['model' => $model]) ?>


            </div>
        </div>
    </div>

</div>

<?php
$this->registerCss("
    body {
        padding-top: 0px; /* Ajusta según la altura de tu navbar */
    }");
?>