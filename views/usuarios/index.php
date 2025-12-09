<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

?>

<style>
    body {
        padding-top: 0px;
    }
</style>

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
                    data-rol="<?= Html::encode($usuario->rol) ?>" data-id="<?= $usuario->id ?>" style="cursor:pointer;">
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="usuarioModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center" id="usuarioModalBody">
                <div id="usuarioModalView">
                    <div id="usuarioModalAvatar" class="usuario-avatar mb-3"
                        style="margin:auto; width:100px; height:100px; font-size:40px;"></div>
                    <p><strong>Email:</strong> <span id="usuarioModalEmail"></span></p>
                    <p><strong>Color:</strong> <span id="usuarioModalColor"></span></p>

                    <div class="mt-3">
                        <?= Html::a('Editar', '#', ['class' => 'btn btn-warning', 'id' => 'modalEditBtn']) ?>
                        <?= Html::a('Eliminar', '#', ['class' => 'btn btn-danger', 'id' => 'modalDeleteBtn']) ?>
                    </div>
                </div>
                <div id="usuarioModalEdit" style="display:none;">
                    <form id="editUserForm" method="post" action="" class="p-3 rounded-3 bg-light shadow-sm">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <input type="hidden" id="editUserId" name="Usuarios[id]">
                        <div class="mb-3 text-start">
                            <label for="editUserNombre" class="form-label fw-semibold">
                                <i class="bi bi-person"></i> Nombre
                            </label>
                            <input type="text" class="form-control form-control-sm rounded-pill" id="editUserNombre"
                                name="Usuarios[Nombre]" required autocomplete="off">
                        </div>
                        <div class="mb-3 text-start">
                            <label for="editUserEmail" class="form-label fw-semibold">
                                <i class="bi bi-envelope"></i> Email
                            </label>
                            <input type="email" class="form-control form-control-sm rounded-pill" id="editUserEmail"
                                name="Usuarios[email]" required autocomplete="off">
                        </div>
                        <div class="mb-3 text-start">
                            <label for="editUserRol" class="form-label fw-semibold">
                                <i class="bi bi-envelope"></i> Rol
                            </label>
                            <input type="rol" class="form-control form-control-sm rounded-pill" id="editUserRol"
                                name="Usuarios[rol]" required autocomplete="off">
                        </div>
                        <div class="mb-4 text-start">
                            <label for="editUserColor" class="form-label fw-semibold">
                                <i class="bi bi-palette"></i> Color
                            </label>
                            <input type="color" class="form-control form-control-color ms-2" id="editUserColor"
                                name="Usuarios[color]" style="width: 3rem; height: 2rem; padding: 0;">
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                <i class="bi bi-check-circle"></i> Guardar
                            </button>
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                                id="cancelEditBtn">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- SWEETALERT TOAST -->
<script>
    window.addEventListener("DOMContentLoaded", function () {

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        <?php if (Yii::$app->request->get('created')): ?>
            Toast.fire({
                icon: "success",
                title: "Usuario creado correctamente"
            });
        <?php endif; ?>

        <?php if (Yii::$app->request->get('updated')): ?>
            Toast.fire({
                icon: "success",
                title: "Usuario actualizado correctamente"
            });
        <?php endif; ?>

        <?php if (Yii::$app->request->get('deleted')): ?>
            Toast.fire({
                icon: "success",
                title: "Usuario eliminado correctamente"
            });
        <?php endif; ?>

    });
</script>

<?php
/* ------------------ CSS ------------------ */
$this->registerCss("

body {
    padding-top: 0px; /* Ajusta según la altura de tu navbar */
}
    
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
.usuario-modal-edit .form-label {
    font-size: 0.95rem;
    color: #495057;
}
.usuario-modal-edit .form-control {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
}
.usuario-modal-edit .form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.1rem rgba(13,110,253,.25);
}
.usuario-modal-edit .btn {
    font-size: 0.95rem;
}
.usuario-modal-edit {
    background: #f6f8fa;
    border-radius: 1rem;
    padding: 1.5rem;
}
");
?>

<?php
$js = <<<JS
// Abrir modal al hacer clic en tarjeta
document.querySelectorAll('.usuario-card').forEach(card => {
    card.addEventListener('click', function() {
        const id = this.dataset.id;
        const nombre = this.dataset.nombre;
        const email = this.dataset.email;
        const rol = this.dataset.rol;
        const color = this.dataset.color;

        document.getElementById('usuarioModalLabel').textContent = nombre;
        const avatar = document.getElementById('usuarioModalAvatar');
        avatar.textContent = nombre[0].toUpperCase();
        avatar.style.backgroundColor = color;

        document.getElementById('usuarioModalEmail').textContent = email;
        document.getElementById('usuarioModalColor').textContent = color;

        document.getElementById('modalEditBtn').dataset.id = id;
        document.getElementById('modalEditBtn').dataset.nombre = nombre;
        document.getElementById('modalEditBtn').dataset.email = email;
        document.getElementById('modalEditBtn').dataset.rol = rol;
        document.getElementById('modalEditBtn').dataset.color = color;

        document.getElementById('modalDeleteBtn').dataset.id = id;

        document.getElementById('usuarioModalView').style.display = '';
        document.getElementById('usuarioModalEdit').style.display = 'none';

        const modal = new bootstrap.Modal(document.getElementById('usuarioModal'));
        modal.show();
    });
});

// Mostrar formulario de edición en el modal
document.getElementById('modalEditBtn').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('usuarioModalView').style.display = 'none';
    document.getElementById('usuarioModalEdit').style.display = '';

    // Llenar el formulario
    document.getElementById('editUserId').value = this.dataset.id;
    document.getElementById('editUserNombre').value = this.dataset.nombre;
    document.getElementById('editUserEmail').value = this.dataset.email;
    document.getElementById('editUserRol').value = this.dataset.rol;
    document.getElementById('editUserColor').value = this.dataset.color;

    document.getElementById('editUserForm').action = 'update?id=' + this.dataset.id;
});

// Botón cancelar
document.getElementById('cancelEditBtn').addEventListener('click', function() {
    document.getElementById('usuarioModalView').style.display = '';
    document.getElementById('usuarioModalEdit').style.display = 'none';
});

// Cerrar color picker automáticamente
const colorInput = document.getElementById('editUserColor');
colorInput.addEventListener('input', function () {
    this.blur();
});

// Confirmación al eliminar usuario
document.getElementById('modalDeleteBtn').addEventListener('click', function(e) {
    e.preventDefault();
    const id = this.dataset.id;

    Swal.fire({
        title: "¿Eliminar usuario?",
        text: "Esta acción no se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete?id=' + id;
        }
    });
});
JS;

$this->registerJs($js);
?>