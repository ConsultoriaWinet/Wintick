<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;


$this->title = 'Generar Bitácora';
$this->registerCssFile('@web/views/tickets/styles.css');

?>

<!-- ===================== -->
<!-- CARD FILTROS -->
<!-- ===================== -->

<p> </p>
<div class="card mb-3">
    <div class="card-header">
        <h3>
            <?= Html::encode($this->title) ?>
        </h3>
    </div>

    <div class="card-body">

        <?php $form = ActiveForm::begin([
            'method' => 'get'
        ]); ?>

        <div class="row">

            <!-- CLIENTE -->
            <div class="col-md-6">

                <?= Html::label('Cliente') ?>

                <select name="Cliente_id" class="form-control">

                    <option value="">Todos</option>

                    <?php foreach ($clientes as $cliente): ?>

                        <option value="<?= $cliente['id'] ?>">

                            <?= Html::encode($cliente['Nombre']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- FOLIO INICIAL -->
            <div class="col-md-3">

                <?= Html::label('Folio Inicial') ?>

                <?= Html::textInput('folio_inicial', Yii::$app->request->get('folio_inicial'), [
                    'class' => 'form-control'
                ]) ?>

            </div>

            <!-- FOLIO FINAL -->
            <div class="col-md-3">

                <?= Html::label('Folio Final') ?>

                <?= Html::textInput('folio_final', Yii::$app->request->get('folio_final'), [
                    'class' => 'form-control'
                ]) ?>

            </div>

        </div>

        <br>

        <div class="row">

            <!-- FECHA INICIO -->
            <div class="col-md-4">

                <?= Html::label('Fecha Inicial') ?>

                <?= Html::input('date', 'fecha_inicio', Yii::$app->request->get('fecha_inicio'), [
                    'class' => 'form-control'
                ]) ?>

            </div>

            <!-- FECHA FINAL -->
            <div class="col-md-4">

                <?= Html::label('Fecha Final') ?>

                <?= Html::input('date', 'fecha_fin', Yii::$app->request->get('fecha_fin'), [
                    'class' => 'form-control'
                ]) ?>

            </div>

            <!-- MES -->
            <div class="col-md-4">

                <?= Html::label('Mes') ?>

                <?= Html::input('month', 'mes', Yii::$app->request->get('mes'), [
                    'class' => 'form-control'
                ]) ?>

            </div>

        </div>

        <br>

        <!-- BOTONES -->
        <div class="text-end">

            <?= Html::a('Limpiar', ['index'], [
                'class' => 'btn btn-outline-secondary me-2'
            ]) ?>

            <?= Html::submitButton('Generar Bitácora', [
                'class' => 'btn btn-primary'
            ]) ?>

        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>


<!-- ===================== -->
<!-- CARD RESULTADOS -->
<!-- ===================== -->

<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h3 class="mb-0">
            <?= Html::encode($tituloBitacora) ?>
        </h3>

        <div class="d-flex align-items-center">

            <span class="badge bg-success fs-6 me-3">

                Total Horas:
                <?= number_format($totalHoras, 2) ?>

            </span>

            <div class="tickets-header-actions">
                <a href="<?= yii\helpers\Url::to(array_merge(
                    ['bitacora/exportar-excel'],
                    Yii::$app->request->queryParams
                )) ?>" class="btn btn-success">
                    <i class="fas fa-file-csv"></i> Excel
                </a>
            </div>

        </div>

    </div>

    <div class="card-body p-0">

        <table class="table table-bordered table-striped mb-0">

            <thead class="table-success">

                <tr>

                    <th>FECHA</th>
                    <th>HORAS UTILIZADAS</th>
                    <th>FOLIO</th>
                    <th>SISTEMA</th>
                    <th>DETALLE DE ACTIVIDADES</th>
                    <th>USUARIO</th>

                </tr>

            </thead>

            <tbody>

                <?php if (!empty($tickets)): ?>

                    <?php foreach ($tickets as $ticket): ?>

                        <tr>

                            <td>
                                <?= $ticket['HoraProgramada'] ?? '' ?>
                            </td>

                            <td>
                                <?= $ticket['TiempoEfectivo'] ?? '0' ?>
                            </td>

                            <td>
                                <?= $ticket['Folio'] ?? '' ?>
                            </td>

                            <td>
                                <?= $ticket['SistemaNombre'] ?? '' ?>
                            </td>

                            <td>
                                <?= $ticket['Descripcion'] ?? '' ?>
                            </td>

                            <td>
                                <?= $ticket['UsuarioNombre'] ?? '' ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="6" class="text-center">

                            No se encontraron resultados

                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>
        <div class="d-flex justify-content-center mt-3">

            <?= LinkPager::widget([
                'pagination' => $pages,
            ]) ?>

        </div>

    </div>

</div>