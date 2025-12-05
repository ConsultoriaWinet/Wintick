<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Clientes $model */

$this->title = 'Actualizar Cliente: ' . $model->Nombre;
$this->params['breadcrumbs'][] = ['label' => 'Clientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Nombre, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';

?>

<div class="clientes-update">

    <!-- Card principal -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><?= Html::encode($this->title) ?></h3>
            <div>
                <?= Html::a('Volver', ['index'], ['class' => 'btn btn-secondary btn-sm']) ?>
                <?= Html::a('Ver Cliente', ['view', 'id' => $model->id], ['class' => 'btn btn-info btn-sm']) ?>
            </div>
        </div>

        <div class="card-body">

            <!-- Formulario responsive -->
            <div class="row">
                <div class="col-12 col-md-8 offset-md-2">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                </div>
            </div>

        </div>
    </div>

</div>

<?php
/* CSS opcional para mejorar la apariencia */
$css = "
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}
.card-body {
    padding: 1.5rem;
}
";
$this->registerCss($css);
?>