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
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/icon-wintickl.ico')]);

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
$this->registerCssFile('https://unpkg.com/phosphor-icons@1.4.1/src/css/phosphor.css', ['position' => \yii\web\View::POS_HEAD]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.19/index.global.min.js'></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="https://unpkg.com/phosphor-icons"></script>
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
/* Contenedor del menú */
.navbar-nav {
    gap: 1.5rem;
}

/* Links del nav */
.navbar-nav .nav-link {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-weight: 500;
    font-size: 0.95rem;
    color: #1f2933;
    padding: 6px 12px;
    border-radius: 10px;
    transition: all 0.2s ease;
}

/* Iconos */
.navbar-nav .nav-link i {
    font-size: 1.15rem;
    opacity: 0.85;
}

/* Hover elegante */
.navbar-nav .nav-link:hover {
    background: rgba(0,0,0,0.04);
    transform: translateY(-0.5px);
}

/* Activo (muy importante para UX) */
.navbar-nav .nav-link.active {
    background: rgba(0,0,0,0.07);
    font-weight: 600;
}
    /* Ajusta el padding del body según la altura de tu navbar */
    body {
        padding-top: 8px;
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
                        'brandLabel' => Html::img(
                            Yii::getAlias('@web/icon-wintickl.ico'),
                            [
                                'alt' => 'Wintick',
                                'style' => 'height:32px; margin-right:8px; border-radius:6px;',
                            ]
                        ) . ' Wintick',
                        'brandUrl' => Yii::$app->homeUrl,
                        'options' => [
                            'class' => 'navbar navbar-expand-lg navbar-light shadow-sm elpepe',
                        ],
                    ]);
                    
                    
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav m-auto'],
            'items' => array_filter([

                // -------------------------
                // MENÚ: TICKETS
                // -------------------------
                Yii::$app->user->can('verTickets') ? [
                    'label' => '<i class="ph-duotone ph-ticket"></i> Tickets',
                    'url' => ['/tickets/index'],
                    'encode' => false,
                ] : null,

                // -------------------------
                // MENÚ: CLIENTES
                // -------------------------
                Yii::$app->user->can('verClientes') ? [
                    'label' => '<i class="ph-duotone ph-users"></i> Clientes',
                    'url' => ['/clientes/index'],
                    'encode' => false,
                ] : null,

                // -------------------------
                // MENÚ: USUARIOS
                // -------------------------
                Yii::$app->user->can('administrarUsuarios') ? [
                    'label' => '<i class="ph-duotone ph-user-gear"></i> Usuarios',
                    'url' => ['/usuarios/index'],
                    'encode' => false,
                ] : null,

            ])
        ]);
            ?>

            <!-- Notificaciones y Usuario -->
            <div style="display: flex; align-items: center; gap: 15px; margin-left: auto;">
                <?php if (!Yii::$app->user->isGuest): ?>
                    <!-- Notificaciones -->
                    <div class="notification-bell" onclick="toggleNotifications()" style="position: relative;">
                        <i class="ph ph-bell"></i>
                        <span class="notification-badge" id="notificationCount" style="display: none;">0</span>

                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="notification-header">
                                <span><i class="ph ph-bell"></i> Notificaciones</span>
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
                            <i class="ph ph-user-circle"></i>
                            <span><?= Html::encode(Yii::$app->user->identity->email) ?></span>
                            <i class="ph ph-chevron-down" style="font-size: 12px;"></i>
                        </div>

                        <div class="user-dropdown-menu" id="userDropdownMenu">
                            <div class="user-dropdown-header">
                                <i class="ph ph-user">
                                    <strong><?= Html::encode(Yii::$app->user->identity->email) ?></strong>
                                </i>
                                <small>Usuario Activo</small>
                            </div>

                            <?= Html::beginForm(['/site/logout'], 'post') ?>
                            <button type="submit" class="user-dropdown-item logout-btn">
                                <i class="ph ph-sign-out"></i>
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

    <script>
  // ==== SONIDO NOTIFICACIONES ====
  const NOTIF_SOUND_URL = <?= json_encode(Yii::getAlias('@web/sounds/notify.mp3')) ?>;
  let notifAudio = null;
  let audioArmed = false;

  function armNotificationAudio() {
    if (audioArmed) return;
    audioArmed = true;

    notifAudio = new Audio(NOTIF_SOUND_URL);
    notifAudio.volume = 0.55;

    // “warmup” para que Brave permita el play
    const p = notifAudio.play();
    if (p && p.then) {
      p.then(() => {
        notifAudio.pause();
        notifAudio.currentTime = 0;
      }).catch(() => {
        audioArmed = false;
      });
    }
  }

  function playNotifSound() {
    if (!notifAudio) return;
    notifAudio.currentTime = 0;
    const p = notifAudio.play();
    if (p && p.catch) p.catch(()=>{});
  }

  // Armar con el primer click del usuario
  document.addEventListener('click', armNotificationAudio, { once: true });


</script>
   <script>
  // =========================================
  //  NOTIFICACIONES (Dropdown + Windows Web Notifications)
  // =========================================
  let notificationCheckInterval = null;
  let lastNotifsSeen = new Set();
  let firstLoad = true;

  // URLs desde Yii
  const NOTIFS_URL = <?= json_encode(Url::to(['/tickets/obtener-notificaciones'])) ?>;
  const MARK_ONE_URL = <?= json_encode(Url::to(['/tickets/marcar-notificacion'])) ?>;
  const MARK_ALL_URL = <?= json_encode(Url::to(['/tickets/marcar-todas-leidas'])) ?>;
  const TICKET_INDEX_URL = <?= json_encode(Url::to(['/tickets/index'])) ?>;

  // Icono (ruta web)
  const NOTIF_ICON = <?= json_encode(Yii::getAlias('@web/icon-wintickl.ico')) ?>;

  async function ensureNotificationPermission() {
    if (!("Notification" in window)) return;
    if (Notification.permission !== "default") return;

    try {
      await Notification.requestPermission();
    } catch (e) {
      console.warn("No se pudo solicitar permiso de notificación:", e);
    }
  }

  function inicializarNotificaciones() {
    // NO rompe nada si el usuario no da permiso
    ensureNotificationPermission();

    if (notificationCheckInterval) clearInterval(notificationCheckInterval);
    notificationCheckInterval = setInterval(cargarNotificaciones, 8000);
    cargarNotificaciones();
  }

  function cargarNotificaciones() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    fetch(NOTIFS_URL, {
      method: 'POST',
      headers: {
        'X-CSRF-Token': token,
        'Content-Type': 'application/json'
      }
    })
      .then(r => r.json())
      .then(data => {
        if (data && data.success) {
          mostrarNotificaciones(Array.isArray(data.notificaciones) ? data.notificaciones : []);
        }
      })
      .catch(err => console.error('Error cargando notificaciones:', err));
  }

  function mostrarNotificaciones(notificaciones) {
    const notifList = document.getElementById('notificationList');
    const badge = document.getElementById('notificationCount');

    if (!notifList || !badge) return;

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
      const ticketId = notif.ticket_id ? Number(notif.ticket_id) : null;

      // OJO: aquí mandamos tipo también
      return `
        <div class="notification-item ${!notif.leida ? 'unread' : ''}"
             onclick="abrirNotificacion(event, ${notif.id}, ${ticketId ?? 'null'}, ${JSON.stringify(notif.tipo || '')})">
          <div class="notification-icon ${iconClass}">
            <i class="fas fa-${icono}"></i>
          </div>
          <div class="notification-content">
            <div class="notification-title">${escapeHtml(notif.titulo || '')}</div>
            <div class="notification-message">${escapeHtml(notif.mensaje || '')}</div>
            <div class="notification-time">${escapeHtml(notif.fecha || '')}</div>
          </div>
        </div>
      `;
    }).join('');

    // ==========================
    // Windows / Web Notification
    // ==========================
    if (!("Notification" in window)) return;
    if (Notification.permission !== "granted") return;

    // Primera carga: NO spamear, solo marcar como vistas
    if (firstLoad) {
      notificaciones.forEach(n => lastNotifsSeen.add(String(n.id)));
      firstLoad = false;
      return;
    }

    // Disparar solo para NO leídas y NO repetidas
        notificaciones
        .filter(n => !n.leida)
        .filter(n => !lastNotifsSeen.has(String(n.id)))
        .forEach(n => {
            lastNotifsSeen.add(String(n.id));

            //  Solo si es ticket nuevo (ajusta el string si tu tipo es otro)
            if ((n.tipo || '') === 'nuevo_ticket') {
            playNotifSound();
            }

            const sysNotif = new Notification(n.titulo || "WinTick", {
            body: n.mensaje || "",
            icon: NOTIF_ICON,
            tag: "wintick-" + n.id,
            renotify: false
            });

            sysNotif.onclick = () => {
            window.focus();
            if (n.ticket_id) {
                window.location.href = `${TICKET_VIEW_URL}?id=${encodeURIComponent(n.ticket_id)}`;
            }
            };
        });
  }

  function abrirNotificacion(event, notifId, ticketId, tipo) {
    event.stopPropagation();

    // Si no hay ticket, solo marcar leída
    if (!ticketId) {
      marcarNotificacion(notifId);
      return;
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    fetch(MARK_ONE_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token },
      body: JSON.stringify({ notif_id: notifId })
    })
      .catch(() => {})
      .finally(() => {
        // Si quieres casos especiales
        if (tipo === 'mencion') {
          window.location.href = `${TICKET_INDEX_URL}?openComments=1&ticket_id=${encodeURIComponent(ticketId)}&notif_id=${encodeURIComponent(notifId)}`;
          return;
        }

        // Default: ir a view
        window.location.href = `${TICKET_VIEW_URL}?id=${encodeURIComponent(ticketId)}`;
      });
  }

  function toggleNotifications() {
    // de paso: pedir permiso con un gesto del usuario (más confiable en browsers)
    ensureNotificationPermission();

    const dropdown = document.getElementById('notificationDropdown');
    if (!dropdown) return;

    dropdown.classList.toggle('show');
    document.getElementById('userDropdownMenu')?.classList.remove('show');
  }

  function toggleUserMenu() {
    const menu = document.getElementById('userDropdownMenu');
    if (!menu) return;

    menu.classList.toggle('show');
    document.getElementById('notificationDropdown')?.classList.remove('show');
  }

  function marcarNotificacion(notifId) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    fetch(MARK_ONE_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token },
      body: JSON.stringify({ notif_id: notifId })
    })
      .then(r => r.json())
      .then(data => {
        if (data && data.success) cargarNotificaciones();
      })
      .catch(err => console.error('❌ Error:', err));
  }

  function marcarTodasLeidas(event) {
    event.stopPropagation();

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    fetch(MARK_ALL_URL, {
      method: 'POST',
      headers: { 'X-CSRF-Token': token, 'Content-Type': 'application/json' }
    })
      .then(r => r.json())
      .then(data => {
        if (data && data.success) cargarNotificaciones();
      })
      .catch(err => console.error('❌ Error:', err));
  }

  function getIconoNotificacion(tipo) {
    const iconos = {
      'asignado': 'user-check',
      'comentario': 'comment',
      'estado_cambio': 'sync-alt',
      'mencion': 'at'
    };
    return iconos[tipo] || 'bell';
  }

  // Cerrar dropdowns al hacer click fuera
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

  // Util: evitar que te rompa el HTML si llegan caracteres raros
  function escapeHtml(str) {
    return String(str)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

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
