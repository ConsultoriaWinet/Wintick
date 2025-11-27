<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Usuarios $model */

$this->title = 'Editar Usuario: ' . $model->Nombre;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Editar';
?>

<div class="update-form-container" style="max-width:600px; margin:40px auto;">

    <h4 class="fw-bold mb-3 text-center">Editar Usuario</h4>

    <div class="card shadow-sm p-4">

        <?php $form = ActiveForm::begin([
            'id' => 'formEditUsuario',
        ]); ?>

        <?= $form->field($model, 'Nombre')->textInput([
            'class' => 'form-control form-control-lg',
            'placeholder' => 'Nombre completo'
        ]) ?>

        <?= $form->field($model, 'email')->input('email', [
            'class' => 'form-control form-control-lg',
            'placeholder' => 'Correo electrónico'
        ]) ?>

        <?= $form->field($model, 'color')->input('color', [
            'class' => 'form-control form-control-color',
            'style' => 'width:80px; height:45px; padding:4px;'
        ]) ?>

        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

        <div class="text-end mt-4">
            <?= Html::submitButton('Guardar Cambios', ['class' => 'btn btn-primary btn-lg']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<?php
// Si el formulario se envía, guardar y redirigir con parámetros
if ($model->load(Yii::$app->request->post()) && $model->save()) {
    return $this->redirect([
        'index',
        'updated' => 1,
        'id' => $model->id,
        'nombre' => $model->Nombre,
        'email' => $model->email,
        'color' => $model->color
    ]);
}
?>