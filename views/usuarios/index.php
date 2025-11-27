<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="usuarios-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Crear Usuario', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="row g-4 justify-content-center">
        <?php foreach ($dataProvider->getModels() as $usuario): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                <div class="card usuario-card flex-fill shadow-sm" data-nombre="<?= Html::encode($usuario->Nombre) ?>"
                    data-email="<?= Html::encode($usuario->email) ?>" data-color="<?= Html::encode($usuario->color) ?>"
                    data-id="<?= $usuario->id ?>" style="cursor:pointer;">

                    <div class="card-body text-center d-flex flex-column align-items-center">
                        <div class="usuario-avatar mb-3" style="background-color: <?= Html::encode($usuario->color) ?>;">
                            <?= strtoupper(substr(Html::encode($usuario->Nombre), 0, 1)) ?>
                        </div>
                        <h5 class="card-title text-truncate w-100"><?= Html::encode($usuario->Nombre) ?></h5>
                        <p class="card-text text-muted text-truncate w-100">
                            <strong>Email:</strong> <?= Html::encode($usuario->email) ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>


<!-- MODAL -->
<div class="modal fade" id="usuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" id="usuarioModalContent">
            <div class="modal-header">
                <h5 class="modal-title" id="usuarioModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Aquí se mostrará la info o el formulario AJAX -->
                <div class="d-flex flex-column align-items-center" id="userStaticInfo">
                    <div id="usuarioModalAvatar" class="usuario-avatar mb-3"></div>
                    <p><strong>Email:</strong> <span id="usuarioModalEmail"></span></p>
                    <p><strong>Color:</strong> <span id="usuarioModalColor"></span></p>
                </div>
            </div>

            <div class="modal-footer">
                <a href="#" class="btn btn-warning" id="modalEditBtn">Editar</a>
                <a href="#" class="btn btn-danger" id="modalDeleteBtn">Eliminar</a>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<?php
/* ------------------ CSS ------------------ */
$this->registerCss("
.usuario-card {
    min-height: 260px;
    border-radius: 12px;
    transition: transform .2s, box-shadow .2s;
    cursor: pointer;
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
}
");
?>


<?php
/* ------------------ JAVASCRIPT ------------------ */

$script = <<<JS

// ---------------------
// ABRIR MODAL CON INFO
// ---------------------
document.querySelectorAll('.usuario-card').forEach(card => {
    card.addEventListener('click', function() {

        const id = this.dataset.id;
        const nombre = this.dataset.nombre;
        const email = this.dataset.email;
        const color = this.dataset.color;

        document.getElementById("usuarioModalLabel").textContent = nombre;

        const avatar = document.getElementById("usuarioModalAvatar");
        avatar.textContent = nombre[0].toUpperCase();
        avatar.style.backgroundColor = color;

        document.getElementById("usuarioModalEmail").textContent = email;
        document.getElementById("usuarioModalColor").textContent = color;

        document.getElementById("modalEditBtn").dataset.id = id;
        document.getElementById("modalDeleteBtn").href = "delete?id=" + id;

        const modal = new bootstrap.Modal(document.getElementById("usuarioModal"));
        modal.show();
    });
});



// ------------------------
// EDITAR VIA AJAX
// ------------------------
document.getElementById("modalEditBtn").addEventListener("click", function(e){
    e.preventDefault();
    const id = this.dataset.id;

    fetch("update?id=" + id)
        .then(r => r.text())
        .then(html => {
            document.querySelector("#usuarioModal .modal-body").innerHTML = html;

            const form = document.getElementById("formEditUsuario");

            // Guardar cambios AJAX
            form.addEventListener("submit", function(ev){
                ev.preventDefault();

                const formData = new FormData(form);

                fetch(form.action, {
                    method: "POST",
                    body: formData
                })
                .then(r => r.json())
                .then(resp => {

                    if(resp.success){

                        // Actualizar tarjeta sin recargar
                        const card = document.querySelector(".usuario-card[data-id='" + id + "']");
                        card.dataset.nombre = resp.data.Nombre;
                        card.dataset.email = resp.data.email;
                        card.dataset.color = resp.data.color;

                        card.querySelector(".usuario-avatar").style.backgroundColor = resp.data.color;
                        card.querySelector(".usuario-avatar").textContent = resp.data.Nombre[0].toUpperCase();
                        card.querySelector(".card-title").textContent = resp.data.Nombre;
                        card.querySelector(".card-text").innerHTML = "<strong>Email:</strong> " + resp.data.email;

                        // Restaurar vista normal del modal
                        location.reload(); // opcional, por si quieres refrescar modal
                    }
                });
            });

        });
});

JS;

$this->registerJs($script);
?>