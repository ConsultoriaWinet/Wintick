<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Clientes $model */

$this->title = 'Crear Cliente';
?>

<div style="max-width:780px; margin:16px auto 0;">
    <div style="margin-bottom:14px;">
        <?= Html::a('<i class="fas fa-arrow-left"></i> Volver a Clientes', ['index'], [
            'style' => 'display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--text-2,#374151);text-decoration:none;font-weight:500;',
        ]) ?>
    </div>
    <div style="background:var(--surface,#fff);border:1px solid var(--border,#e5e7eb);border-radius:14px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.06);">
        <?= $this->render('_form', ['model' => $model]) ?>
    </div>
</div>
