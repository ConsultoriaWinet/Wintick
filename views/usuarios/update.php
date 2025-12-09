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

        <?= $form->field($model, 'Rol')->textInput([
            'maxlength' => true,
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
// Script para cerrar el selector de color automáticamente
$script = <<<JS
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.querySelector('#formEditUsuario input[type="color"]');

    if (colorInput) {
        // Cerrar al seleccionar color (arrastrando)
        colorInput.addEventListener('input', function() {
            this.blur();
        });

        // Cerrar al soltar el mouse
        colorInput.addEventListener('mouseup', function() {
            this.blur();
        });

        // Cerrar después de un cambio final (también en móviles)
        colorInput.addEventListener('change', function() {
            this.blur();
        });
    }
});
JS;

$this->registerJs($script);
?>