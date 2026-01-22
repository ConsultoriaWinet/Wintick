<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/icon-wintick.ico')]);

// CDN Resources
$this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://npmcdn.com/flatpickr/dist/l10n/es.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => \yii\web\View::POS_HEAD]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/SplitText.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/TextPlugin.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.19/index.global.min.js'></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    :root {
        --primary-color: #A0BAA5;
        --primary-dark: #8BA590;
        --text-dark: #000000;
        --text-light: #6b7280;
        --border-color: #e5e7eb;
        --bg-light: #f9fafb;
    }

    body {
        background-color: var(--bg-light);
        padding-top: 70px;
    }

    .elpepe {
        background-color: var(--primary-color) !important;
    }

    .elnegro {
        color: var(--text-dark) !important;
    }

    #header .navbar {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    #header .navbar-brand {
        font-weight: 700;
        font-size: 1.25rem;
        color: var(--text-dark) !important;
    }

    #header .nav-link {
        color: var(--text-dark) !important;
        font-weight: 500;
        padding: 0.5rem 1rem !important;
        border-radius: 8px;
        margin: 0 0.25rem;
        transition: all 0.3s ease;
    }

    #header .nav-link:hover {
        background-color: rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    /* ========================================
       Animacion Header
       ======================================== */
    #mainHeader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1030;
        transition: top 0.3s, background-color 0.3s;

    }

    /* Ajusta el padding del body según la altura de tu navbar */
    body {
        padding-top: 80px;
    }


    /* ========================================
       DROPDOWN USUARIO
       ======================================== */
    .user-dropdown {
        position: relative;
    }

    .user-dropdown-toggle {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .user-dropdown-toggle:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
    }

    .user-dropdown-toggle i {
        font-size: 20px;
        color: var(--text-dark);
    }

    .user-dropdown-toggle span {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 14px;
    }

    .user-dropdown-menu {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        min-width: 250px;
        display: none;
        z-index: 1001;
        border: 2px solid var(--border-color);
        overflow: hidden;
        animation: slideDown 0.3s ease;
    }

    .user-dropdown-menu.show {
        display: block;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .user-dropdown-header {
        padding: 16px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        border-bottom: 2px solid var(--border-color);
        color: white;
    }

    .user-dropdown-header strong {
        display: block;
        font-size: 16px;
        margin-bottom: 4px;
    }

    .user-dropdown-header small {
        opacity: 0.9;
    }

    .user-dropdown-item {
        padding: 12px 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--text-dark);
        font-weight: 500;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
    }

    .user-dropdown-item:hover {
        background-color: var(--bg-light);
    }

    .user-dropdown-item i {
        width: 20px;
        text-align: center;
        color: var(--primary-color);
    }

    .user-dropdown-item.logout-btn {
        border-top: 2px solid var(--border-color);
        color: #ef4444;
    }

    .user-dropdown-item.logout-btn i {
        color: #ef4444;
    }

    .user-dropdown-item.logout-btn:hover {
        background-color: #fee2e2;
    }

    /* ========================================
       NOTIFICACIONES
       ======================================== */
    .notification-bell {
        position: relative;
        cursor: pointer;
        padding: 10px;
        border-radius: 50%;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
    }

    .notification-bell:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .notification-bell i {
        font-size: 20px;
        color: var(--text-dark);
    }

    .notification-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        background-color: #ef4444;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: bold;
        border: 2px solid var(--primary-color);
    }

    .notification-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        background: white;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        width: 380px;
        max-height: 450px;
        overflow-y: auto;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        display: none;
        z-index: 1001;
    }

    .notification-dropdown.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    .notification-header {
        padding: 14px 16px;
        border-bottom: 2px solid var(--border-color);
        font-weight: 700;
        color: var(--text-dark);
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notification-header button {
        font-size: 11px;
        padding: 4px 12px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 8px;
        color: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .notification-header button:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color);
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        gap: 12px;
    }

    .notification-item:hover {
        background-color: var(--bg-light);
    }

    .notification-item.unread {
        background-color: #e0f2fe;
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .notification-icon.comentario {
        background-color: #e8f5e9;
        color: #2e7d32;
    }

    .notification-icon.estado_cambio {
        background-color: #fff3e0;
        color: #e65100;
    }

    .notification-icon.asignado {
        background-color: #f3e5f5;
        color: #6a1b9a;
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-title {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 4px;
        font-size: 13px;
    }

    .notification-message {
        color: var(--text-light);
        font-size: 12px;
        margin-bottom: 4px;
        line-height: 1.4;
    }

    .notification-time {
        font-size: 11px;
        color: #bdc3c7;
    }

    .notification-empty {
        padding: 40px 10px;
        text-align: center;
        color: var(--text-light);
    }

    .notification-empty i {
        font-size: 40px;
        margin-bottom: 10px;
        opacity: 0.5;
    }

    #footer {
        background-color: var(--primary-color) !important;
    }
    .welcome-popup {
        border-radius: 24px !important;
        border: none !important;
        box-shadow: 0 20px 60px rgba(139, 165, 144, 0.2) !important;
    }
    
    .welcome-title {
        font-size: 28px !important;
        font-weight: 600 !important;
        color: #1a1a1a !important;
        margin-bottom: 10px !important;
    }
    
    .welcome-btn {
        border-radius: 12px !important;
        padding: 12px 24px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        letter-spacing: 0.3px !important;
        box-shadow: 0 4px 16px rgba(139, 165, 144, 0.3) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    
    .welcome-btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(139, 165, 144, 0.4) !important;
    }
    
    .swal2-timer-progress-bar {
        background: rgba(139, 165, 144, 0.3) !important;
    }
</style>
<script>
    const TICKET_VIEW_URL = <?= json_encode(Url::to(['/tickets/view'])) ?>;
</script>
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header id="mainHeader">
        <?php if (
            Yii::$app->controller->id !== 'site' ||
            !in_array(Yii::$app->controller->action->id, ['login', 'requestpassword', 'resetpassword'])
        ): ?>
            <?php

            NavBar::begin([
                'brandLabel' => Yii::$app->name,
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar navbar-expand-lg navbar-light shadow-sm elpepe',
                ],
            ]);

echo Nav::widget([
    'options' => ['class' => 'navbar-nav'],
    'items' => array_filter([
        
        // -------------------------
        // MENÚ: TICKETS
        // -------------------------
        Yii::$app->user->can('verTickets') ? [
            'label' => 'Tickets',
            'url' => ['/tickets/index']
        ] : null,

        // -------------------------
        // MENÚ: CLIENTES
        // -------------------------
        Yii::$app->user->can('verClientes') ? [
            'label' => 'Clientes',
            'url' => ['/clientes/index']
        ] : null,

        // -------------------------
        // MENÚ: USUARIOS
        // -------------------------
        Yii::$app->user->can('administrarUsuarios') ? [
            'label' => 'Usuarios',
            'url' => ['/usuarios/index']
        ] : null,

    ])
]);

            ?>

            <!-- Notificaciones y Usuario -->
            <div style="display: flex; align-items: center; gap: 15px; margin-left: auto;">
                <?php if (!Yii::$app->user->isGuest): ?>
                    <!-- Notificaciones -->
                    <div class="notification-bell" onclick="toggleNotifications()" style="position: relative;">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationCount" style="display: none;">0</span>

                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="notification-header">
                                <span><i class="fas fa-bell"></i> Notificaciones</span>
                                <button onclick="marcarTodasLeidas(event)">
                                    <i class="fas fa-check-double"></i> Marcar todas
                                </button>
                            </div>
                            <div id="notificationList">
                                <div class="notification-empty">
                                    <i class="fas fa-spinner fa-spin"></i><br>
                                    <small>Cargando...</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown Usuario -->
                    <div class="user-dropdown">
                        <div class="user-dropdown-toggle" onclick="toggleUserMenu()">
                            <i class="fas fa-user-circle"></i>
                            <span><?= Html::encode(Yii::$app->user->identity->email) ?></span>
                            <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                        </div>

                        <div class="user-dropdown-menu" id="userDropdownMenu">
                            <div class="user-dropdown-header">
                                <strong><?= Html::encode(Yii::$app->user->identity->email) ?></strong>
                                <small>Usuario Activo</small>
                            </div>

                            <?= Html::beginForm(['/site/logout'], 'post') ?>
                            <button type="submit" class="user-dropdown-item logout-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                Cerrar Sesión
                            </button>
                            <?= Html::endForm() ?>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= Url::to(['/site/login']) ?>" class="btn btn-sm btn-light">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                <?php endif; ?>
            </div>

            <?php NavBar::end(); ?>
        <?php endif; ?>
    </header>

    <main id="main" class="flex-shrink-0" role="main">
        <div class="container">
            <?php if (!empty($this->params['breadcrumbs'])): ?>
                <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
            <?php endif ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer id="footer" class="mt-auto py-3">
        <div class="container">
            <div class="row text-white">
                <div class="col-md-6 text-center text-md-start">&copy; Wintick <?= date('Y') ?></div>
            </div>
        </div>
    </footer>

    <?php
    $js = <<<JS
        let lastScrollTop = 0;
        const header = document.getElementById('mainHeader');

        window.addEventListener('scroll', function() {
            let st = window.pageYOffset || document.documentElement.scrollTop;

    if (st > lastScrollTop && st > 100) {
        // Scrolleando hacia abajo, ocultar
        header.style.top = "-80px"; // Ajusta según la altura de tu navbar
    } else if (st < lastScrollTop) {
        // Scrolleando hacia arriba, mostrar
        header.style.top = "0";
    }

    lastScrollTop = st <= 0 ? 0 : st;
});
JS;

    $this->registerJs($js);
    ?>

    <!-- JavaScript para notificaciones -->
    <script>
        let notificationCheckInterval;

        function inicializarNotificaciones() {
       
            notificationCheckInterval = setInterval(cargarNotificaciones, 8000);
            cargarNotificaciones();
        }

        function cargarNotificaciones() {
          

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const token = csrfToken ? csrfToken.getAttribute('content') : '';

            fetch('<?= Url::to(['/tickets/obtener-notificaciones']) ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': token,
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                 
                    if (data.success) {
                        mostrarNotificaciones(data.notificaciones);
                    }
                })
                .catch(error => console.error('Error cargando notificaciones:', error));
        }

        function mostrarNotificaciones(notificaciones) {
          
            const notifList = document.getElementById('notificationList');
            const badge = document.getElementById('notificationCount');
            const noLeidas = notificaciones.filter(n => !n.leida).length;

           

            if (noLeidas > 0) {
                badge.textContent = noLeidas;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }

            if (notificaciones.length === 0) {
                notifList.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-check-circle"></i><br>
                    <small>No hay notificaciones</small>
                </div>
            `;
                return;
            }

            notifList.innerHTML = notificaciones.map(notif => {
                const iconClass = notif.tipo || 'asignado';
                const icono = getIconoNotificacion(notif.tipo);
                return `
                <div class="notification-item ${!notif.leida ? 'unread' : ''}" 
                     onclick="abrirNotificacion(event, ${notif.id}, ${notif.ticket_id || 'null'})">
                    <div class="notification-icon ${iconClass}">
                        <i class="fas fa-${icono}"></i>
                    </div>                                          
                    <div class="notification-content">
                        <div class="notification-title">${notif.titulo}</div>
                        <div class="notification-message">${notif.mensaje}</div>
                        <div class="notification-time">${notif.fecha}</div>
                    </div>
                </div>
            `;
            }).join('');
        }

function abrirNotificacion(event, notifId, ticketId, tipo) {
  event.stopPropagation();

  if (!ticketId) {
    marcarNotificacion(notifId);
    return;
  }

  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  fetch('<?= Url::to(['/tickets/marcar-notificacion']) ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token },
    body: JSON.stringify({ notif_id: notifId })
  }).catch(() => {}).finally(() => {


    if (tipo === 'mencion') {
      window.location.href = `<?= Url::to(['/tickets/index']) ?>?openComments=1&ticket_id=${encodeURIComponent(ticketId)}&notif_id=${encodeURIComponent(notifId)}`;
      return;
    }

    // ✅ default: ir a view (como ya lo haces)
    window.location.href = `${TICKET_VIEW_URL}?id=${encodeURIComponent(ticketId)}`;
  });
}


        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');
            document.getElementById('userDropdownMenu').classList.remove('show');
        }

        function toggleUserMenu() {
            const menu = document.getElementById('userDropdownMenu');
            menu.classList.toggle('show');
            document.getElementById('notificationDropdown').classList.remove('show');
        }

        function marcarNotificacion(notifId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const token = csrfToken ? csrfToken.getAttribute('content') : '';

            fetch('<?= Url::to(['/tickets/marcar-notificacion']) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': token
                },
                body: JSON.stringify({ notif_id: notifId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cargarNotificaciones();
                    }
                })
                .catch(error => console.error('❌ Error:', error));
        }

        function marcarTodasLeidas(event) {
            event.stopPropagation();

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const token = csrfToken ? csrfToken.getAttribute('content') : '';

            fetch('<?= Url::to(['/tickets/marcar-todas-leidas']) ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': token,
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cargarNotificaciones();
                    }
                })
                .catch(error => console.error('❌ Error:', error));
        }

        function getIconoNotificacion(tipo) {
            const iconos = {
                'asignado': 'user-check',
                'comentario': 'comment',
                'estado_cambio': 'sync-alt'
            };
            return iconos[tipo] || 'bell';
        }

        document.addEventListener('click', function (e) {
            const bell = document.querySelector('.notification-bell');
            const dropdown = document.getElementById('notificationDropdown');
            const userToggle = document.querySelector('.user-dropdown-toggle');
            const userMenu = document.getElementById('userDropdownMenu');

            if (bell && dropdown && !bell.contains(e.target)) {
                dropdown.classList.remove('show');
            }

            if (userToggle && userMenu && !userToggle.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.remove('show');
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            <?php if (!Yii::$app->user->isGuest): ?>
                inicializarNotificaciones();
            <?php endif; ?>
        });
    </script>

    <?php $this->endBody() ?>

</body>

</html>
<?php $this->endPage() ?>

<!-- ✅ MOVER EL BLOQUE DE BIENVENIDA AQUÍ AL FINAL -->
<?php if (Yii::$app->session->hasFlash('welcome')): ?>
    <?php 
    $welcomeData = Yii::$app->session->getFlash('welcome');
    $nombre = $welcomeData['nombre'];
    $rol = $welcomeData['rol'];
    $email = $welcomeData['email'];
    
    // Determinar saludo según la hora
    $hora = date('H');
    $saludo = 'Buenas noches';
    if ($hora >= 6 && $hora < 12) {
        $saludo = 'Buenos días';
    } elseif ($hora >= 12 && $hora < 18) {
        $saludo = 'Buenas tardes';
    }
    
    // Determinar icono según el rol
    $iconoRol = match($rol) {
        'Administracion', 'Administracion' => '👑',
        'Consultores', 'Consultores' => '💼',
        'Cliente', 'Cliente' => '👤',
        default => '🎯'
    };
    ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pequeño delay para que se cargue completamente la página
        setTimeout(() => {
            Swal.fire({
                icon: 'success',
                title: '<?= $saludo ?>, <?= Html::encode($nombre) ?>! <?= $iconoRol ?>',
                html: `
                    <div style="text-align: center; padding: 20px 0;">
                        <div style="background: linear-gradient(135deg, #8BA590 0%, #7a9582 100%); color: white; padding: 20px; border-radius: 16px; margin: 20px 0; box-shadow: 0 8px 32px rgba(139, 165, 144, 0.3);">
                            <div style="font-size: 24px; margin-bottom: 8px;">¡Bienvenido de vuelta!</div>
                            <div style="font-size: 14px; opacity: 0.9;">
                                <strong><?= Html::encode($rol) ?></strong> • <?= Html::encode($email) ?>
                            </div>
                        </div>
                        <div style="color: #666; font-size: 14px; line-height: 1.6;">
                            <i class="fas fa-clock" style="color: #8BA590; margin-right: 6px;"></i>
                            Conectado el <?= date('d/m/Y') ?> a las <?= date('H:i') ?>
                        </div>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: '<i class="fas fa-rocket"></i> ¡Empecemos!',
                confirmButtonColor: '#8BA590',
                timer: 8000,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                },
                backdrop: `
                    rgba(139, 165, 144, 0.1)
                    left top
                    no-repeat
                `,
                customClass: {
                    popup: 'welcome-popup',
                    title: 'welcome-title',
                    confirmButton: 'welcome-btn'
                }
            });
        }, 500);
    });
    </script>
<?php endif; ?>