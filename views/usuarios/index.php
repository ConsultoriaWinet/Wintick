<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\UsuariosSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuarios-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Usuarios', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="row g-4 justify-content-center">
        <?php foreach ($dataProvider->getModels() as $usuario): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                <div class="card usuario-card flex-fill shadow-sm" data-nombre="<?= Html::encode($usuario->Nombre) ?>"
                    data-email="<?= Html::encode($usuario->email) ?>" data-color="<?= Html::encode($usuario->color) ?>"
                    data-id="<?= $usuario->id ?>" style="cursor: pointer;">

                    <div class="card-body text-center d-flex flex-column align-items-center">

                        <!-- Avatar dinámico -->
                        <div class="usuario-avatar mb-3" style="background-color: <?= Html::encode($usuario->color) ?>;">
                            <?= strtoupper(substr(Html::encode($usuario->Nombre), 0, 1)) ?>
                        </div>

                        <h5 class="card-title text-truncate w-100">
                            <?= Html::encode($usuario->Nombre) ?>
                        </h5>

                        <p class="card-text text-muted text-truncate w-100">
                            <strong>Email:</strong> <?= Html::encode($usuario->email) ?>
                        </p>

                        <div class="mt-auto"></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>



    <!-- Modal dinámico -->
    <div class="modal fade" id="usuarioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="usuarioModalContent">
                <!-- Aquí se cargará AJAX -->
            </div>
        </div>
    </div>

    <?php
    $script = <<<JS

// Cuando se hace clic en una tarjeta
document.querySelectorAll('.usuario-card').forEach(card => {
    card.addEventListener('click', function() {

        const id = this.getAttribute('data-id');

        // Cargar vista del CRUD con AJAX dentro del modal
        fetch('view?id=' + id)
            .then(response => response.text())
            .then(html => {

                // Insertar HTML dentro del modal
                document.getElementById('usuarioModalContent').innerHTML = html;

                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('usuarioModal'));
                modal.show();
            });
    });
});


// --- Manejar clicks dentro del modal (para AJAX Update) ---
document.addEventListener("click", function(e) {

    // Botón para abrir edición dentro del modal
    if (e.target.matches('.btn-update-ajax')) {
        e.preventDefault();

        fetch(e.target.href)
            .then(response => response.text())
            .then(html => {
                document.getElementById('usuarioModalContent').innerHTML = html;
            });
    }

});

JS;

    $this->registerJs($script);
    ?>

    <!-- Modal 
    <div class="modal fade" id="usuarioModal" tabindex="-1" aria-labelledby="usuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="usuarioModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Email:</strong> <span id="modalEmail"></span></p>
                    <p><strong>Color:</strong> <span id="modalColor"></span></p>
                </div>
                <div class="modal-footer">
                    <a href="#" id="modalView" class="btn btn-primary">View</a>
                    <a href="#" id="modalUpdate" class="btn btn-warning">Update</a>
                    <a href="#" id="modalDelete" class="btn btn-danger"
                        data-confirm="Are you sure you want to delete this item?" data-method="post">Delete</a>
                </div>
            </div>
        </div>
    </div>
-->
    <?php
    $this->registerCss("
.usuario-card {
    min-height: 260px;
    max-height: 260px;
    width: 100%;
    border-radius: 12px;
    transition: transform .2s, box-shadow .2s;
    border: none;
}

.usuario-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.usuario-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    font-size: 32px;
    color: white;
    font-weight: bold;
    display: flex;
    justify-content: center;
    align-items: center;
    text-transform: uppercase;
}

.usuario-card .card-title,
.usuario-card .card-text {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

@media (max-width: 576px) {
    .usuario-card {
        min-height: 300px;
        max-height: 300px;
    }
}
");
    ?>

    <?php
    $script = <<<JS
    // Manejar clic en las tarjetas
    document.querySelectorAll('.usuario-card').forEach(card => {
        card.addEventListener('click', function() {
            // Obtener datos de la tarjeta
            const nombre = this.getAttribute('data-nombre');
            const email = this.getAttribute('data-email');
            const color = this.getAttribute('data-color');
            const id = this.getAttribute('data-id');

            // Actualizar contenido del modal
            document.getElementById('usuarioModalLabel').textContent = nombre;
            document.getElementById('modalEmail').textContent = email;
            document.getElementById('modalColor').textContent = color;
            document.getElementById('modalView').setAttribute('href', 'view?id=' + id);
            document.getElementById('modalUpdate').setAttribute('href', 'update?id=' + id);
            document.getElementById('modalDelete').setAttribute('href', 'delete?id=' + id);

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('usuarioModal'));
            modal.show();
        });
    });
JS;
    $this->registerJs($script);
    ?>