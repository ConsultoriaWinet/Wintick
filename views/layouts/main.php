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
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/LOGOWINTICKICO.ico')]);

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
$this->registerJsFile('https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js', ['position' => \yii\web\View::POS_HEAD]);

$this->registerJsFile(
    'https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js',
    ['position' => \yii\web\View::POS_HEAD]
);



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

    .elpepe { background-color: var(--primary-color) !important; }
    .elnegro { color: var(--text-dark) !important; }

    #header .navbar { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }

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

    .navbar-nav { gap: 1.5rem; }

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

    .navbar-nav .nav-link i {
        font-size: 1.15rem;
        opacity: 0.85;
    }

    .navbar-nav .nav-link:hover {
        background: rgba(0,0,0,0.04);
        transform: translateY(-0.5px);
    }

    .navbar-nav .nav-link.active {
        background: rgba(0,0,0,0.07);
        font-weight: 600;
    }

    /* Ajusta el padding del body según la altura de tu navbar */
    body { padding-top: 8px; }

    /* ========================================
       DROPDOWN USUARIO
       ======================================== */
    .user-dropdown { position: relative; }

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

    .user-dropdown-menu.show { display: block; }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
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

    .user-dropdown-header small { opacity: 0.9; }

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

    .user-dropdown-item:hover { background-color: var(--bg-light); }

    .user-dropdown-item i {
        width: 20px;
        text-align: center;
        color: var(--primary-color);
    }

    .user-dropdown-item.logout-btn {
        border-top: 2px solid var(--border-color);
        color: #ef4444;
    }

    .user-dropdown-item.logout-btn i { color: #ef4444; }

    .user-dropdown-item.logout-btn:hover { background-color: #fee2e2; }

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

    .notification-header button:hover { background: rgba(255, 255, 255, 0.3); }

    #notifPermissionBanner {
        padding: 8px 14px;
        font-size: 11.5px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 1px solid var(--border-color);
    }
    #notifPermissionBanner.perm-default {
        background: #fff8e1;
        color: #7a5800;
    }
    #notifPermissionBanner.perm-denied {
        background: #fdecea;
        color: #8b0000;
    }
    #notifPermissionBanner button {
        margin-left: auto;
        padding: 3px 10px;
        border-radius: 20px;
        border: 1px solid currentColor;
        background: transparent;
        color: inherit;
        cursor: pointer;
        font-size: 11px;
        white-space: nowrap;
    }

    .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color);
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        gap: 12px;
    }

    .notification-item:hover { background-color: var(--bg-light); }
    .notification-item.unread { background-color: #e0f2fe; }

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

    .notification-icon.comentario { background-color: #e8f5e9; color: #2e7d32; }
    .notification-icon.estado_cambio { background-color: #fff3e0; color: #e65100; }
    .notification-icon.asignado { background-color: #f3e5f5; color: #6a1b9a; }

    .notification-content { flex: 1; min-width: 0; }

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

    #footer { background-color: var(--primary-color) !important; }

    .welcome-btn {
        background-color: var(--primary-color) !important;
    }

    /* ========================================
       MOBILE RESPONSIVE
       ======================================== */

    /* Ocultar nombre de usuario en pantallas muy pequeñas para ahorrar espacio */
    @media (max-width: 480px) {
        .user-dropdown-toggle span { display: none; }
        .user-dropdown-toggle { padding: 6px 10px; gap: 6px; }
    }

    /* Notification dropdown: ancho completo en móvil para que no se salga de pantalla */
    @media (max-width: 600px) {
        .notification-dropdown {
            width: calc(100vw - 24px);
            right: auto;
            left: 50%;
            transform: translateX(-50%);
        }
        .notification-dropdown.show {
            animation: slideDown 0.3s ease;
        }
    }

    /* User dropdown: ajustar para que no se salga de pantalla */
    @media (max-width: 400px) {
        .user-dropdown-menu {
            right: -30px;
            min-width: 230px;
        }
    }

    /* Navbar: reducir gap en tablets */
    @media (max-width: 992px) {
        .navbar-nav { gap: 0.5rem; }

        /* El contenido principal necesita padding cuando el navbar está colapsado */
        #main { padding-top: 0.5rem; }
    }

    /* Footer legible en móvil */
    @media (max-width: 576px) {
        #footer .row { text-align: center !important; }
        #footer .col-md-6 { text-align: center !important; }
    }

    /* Container con padding lateral adecuado en móvil */
    @media (max-width: 576px) {
        #main .container { padding-left: 12px; padding-right: 12px; }
    }
</style>

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
                    Yii::getAlias('@web/LOGOWINTICKICO.ico'),
                    [
                        'alt' => 'Wintick',
                        'style' => 'height:32px; margin-right:8px; border-radius:6px;',
                    ]
                ) . ' ',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-expand-lg navbar-light shadow-sm elpepe',
            ],
        ]);

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav m-auto'],
            'items' => array_filter([

                Yii::$app->user->can('verTickets') ? [
                    'label' => '<i class="ph-duotone ph-ticket"></i> Tickets',
                    'url' => ['/tickets/index'],
                    'encode' => false,
                ] : null,

                Yii::$app->user->can('verClientes') ? [
                    'label' => '<i class="ph-duotone ph-users"></i> Clientes',
                    'url' => ['/clientes/index'],
                    'encode' => false,
                ] : null,

                Yii::$app->user->can('administrarUsuarios') ? [
                    'label' => '<i class="ph-duotone ph-user-gear"></i> Usuarios',
                    'url' => ['/usuarios/index'],
                    'encode' => false,
                ] : null,

            ])
        ]);
        ?>

        <div style="display: flex; align-items: center; gap: 15px; margin-left: auto;">
            <?php if (!Yii::$app->user->isGuest): ?>
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
                        <div id="notifPermissionBanner" style="display:none;"></div>
                        <div id="notificationList">
                            <div class="notification-empty">
                                <i class="fas fa-spinner fa-spin"></i><br>
                                <small>Cargando...</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="user-dropdown">
                    <?php
                        $currentUser   = Yii::$app->user->identity;
                        $currentAvatar = $currentUser->avatar ?? null;
                        $currentColor  = $currentUser->color ?? '#A0BAA5';
                        $currentNombre = $currentUser->Nombre ?? $currentUser->email;
                        $currentRol    = $currentUser->rol ?? '';
                        $inicialUser   = mb_strtoupper(mb_substr($currentNombre, 0, 1, 'UTF-8'), 'UTF-8');
                        $avatarUrl     = $currentAvatar ? Yii::getAlias('@web') . $currentAvatar : null;
                    ?>
                    <div class="user-dropdown-toggle" onclick="toggleUserMenu()" id="userDropdownToggle">
                        <?php if ($avatarUrl): ?>
                            <img id="navUserAvatar" src="<?= Html::encode($avatarUrl) ?>"
                                 style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:2px solid rgba(255,255,255,0.6); flex-shrink:0;"
                                 alt="foto">
                        <?php else: ?>
                            <span id="navUserAvatar" style="
                                width:32px; height:32px; border-radius:50%;
                                background-color:<?= Html::encode($currentColor) ?>;
                                display:inline-flex; align-items:center; justify-content:center;
                                font-size:14px; font-weight:700; color:white; flex-shrink:0;
                                border:2px solid rgba(255,255,255,0.5);
                                text-shadow:0 1px 2px rgba(0,0,0,0.3);
                            "><?= Html::encode($inicialUser) ?></span>
                        <?php endif; ?>
                        <span><?= Html::encode($currentNombre) ?></span>
                        <i class="ph ph-chevron-down" style="font-size: 12px;"></i>
                    </div>

                    <div class="user-dropdown-menu" id="userDropdownMenu">
                        <!-- Header con foto grande -->
                        <div class="user-dropdown-header" style="display:flex; align-items:center; gap:14px;">
                            <div style="position:relative; cursor:pointer;" onclick="document.getElementById('avatarFileInput').click();" title="Cambiar foto">
                                <?php if ($avatarUrl): ?>
                                    <img id="navAvatarPreview" src="<?= Html::encode($avatarUrl) ?>"
                                         style="width:52px; height:52px; border-radius:50%; object-fit:cover; border:3px solid rgba(255,255,255,0.7);"
                                         alt="foto">
                                <?php else: ?>
                                    <div id="navAvatarPreview" style="
                                        width:52px; height:52px; border-radius:50%;
                                        background:rgba(255,255,255,0.25);
                                        display:flex; align-items:center; justify-content:center;
                                        font-size:22px; font-weight:700; color:white;
                                        border:3px solid rgba(255,255,255,0.5);
                                    "><?= Html::encode($inicialUser) ?></div>
                                <?php endif; ?>
                                <!-- Overlay cámara -->
                                <div style="
                                    position:absolute; bottom:0; right:0;
                                    width:20px; height:20px; border-radius:50%;
                                    background:#1f2937; border:2px solid white;
                                    display:flex; align-items:center; justify-content:center;
                                ">
                                    <i class="fas fa-camera" style="font-size:9px; color:white;"></i>
                                </div>
                            </div>
                            <div>
                                <strong style="display:block; font-size:15px;"><?= Html::encode($currentNombre) ?></strong>
                                <small style="opacity:0.85;"><?= Html::encode($currentRol) ?></small>
                            </div>
                        </div>

                        <!-- Input oculto para subir foto -->
                        <input type="file" id="avatarFileInput" accept="image/jpeg,image/png,image/webp,image/gif"
                               style="display:none;" onchange="subirFotoPerfil(this)">

                        <!-- Indicador de carga -->
                        <div id="avatarUploadStatus" style="display:none; padding:8px 16px; font-size:12px; color:#6b7280; background:#f9fafb; border-bottom:1px solid #e5e7eb;">
                            <i class="fas fa-spinner fa-spin"></i> Subiendo foto...
                        </div>

                        <!-- Opción: cambiar foto -->
                        <div class="user-dropdown-item" onclick="document.getElementById('avatarFileInput').click();" style="cursor:pointer;">
                            <i class="fas fa-camera" style="color:#A0BAA5;"></i>
                            <span>Cambiar foto de perfil</span>
                        </div>

                        <!-- Color picker (solo Consultores) -->
                        <?php if ($currentRol === 'Consultores'): ?>
                        <div class="user-dropdown-item" onclick="toggleColorPicker(event)" style="flex-direction:column; align-items:flex-start; gap:8px;">
                            <div style="display:flex; align-items:center; gap:12px; width:100%;">
                                <span style="width:16px; height:16px; border-radius:50%; background:<?= Html::encode($currentColor) ?>; border:2px solid #e5e7eb; flex-shrink:0;" id="colorPreviewIcon"></span>
                                <span>Mi Color en calendario</span>
                            </div>
                            <div id="colorPickerWrap" style="display:none; width:100%; padding:4px 0;">
                                <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:8px;">
                                    <?php
                                    $coloresRapidos = ['#e74c3c','#e67e22','#f1c40f','#2ecc71','#1abc9c','#3498db','#9b59b6','#e91e8c','#607d8b','#795548','#A0BAA5','#ff6b6b','#4ecdc4','#45b7d1','#96ceb4'];
                                    foreach ($coloresRapidos as $c): ?>
                                        <span onclick="seleccionarColor(event, '<?= $c ?>')"
                                              style="width:24px; height:24px; border-radius:50%; background:<?= $c ?>; cursor:pointer; border:2px solid transparent; transition:border 0.15s;"
                                              onmouseover="this.style.border='2px solid #1f2937'" onmouseout="this.style.border='2px solid transparent'">
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <input type="color" id="colorPickerInput" value="<?= Html::encode($currentColor) ?>"
                                           style="width:36px; height:28px; padding:0; border:none; cursor:pointer; border-radius:4px;"
                                           oninput="seleccionarColor(event, this.value)">
                                    <span style="font-size:12px; color:#6b7280;">Color personalizado</span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

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
        header.style.top = "-80px";
    } else if (st < lastScrollTop) {
        header.style.top = "0";
    }

    lastScrollTop = st <= 0 ? 0 : st;
});
JS;

$this->registerJs($js);
?>

<script>


  // ==========================================================
  //  TODO EN UNO: SONIDO + DROPDOWN + WEB NOTIFICATIONS
  // ==========================================================

  // URLs desde Yii
  const NOTIF_SOUND_URL  = <?= json_encode(Yii::getAlias('@web/sounds/notify.mp3')) ?>;

  const NOTIFS_URL       = <?= json_encode(Url::to(['/tickets/obtener-notificaciones'])) ?>;
  const NOTIFS_STREAM_URL= <?= json_encode(Url::to(['/tickets/notificaciones-stream'])) ?>;
  const MARK_ONE_URL     = <?= json_encode(Url::to(['/tickets/marcar-notificacion'])) ?>;
  const MARK_ALL_URL     = <?= json_encode(Url::to(['/tickets/marcar-todas-leidas'])) ?>;
  const TICKET_INDEX_URL = <?= json_encode(Url::to(['/tickets/index'])) ?>;
  const TICKET_VIEW_URL  = <?= json_encode(Url::to(['/tickets/view'])) ?>;

  const NOTIF_ICON      = <?= json_encode(Yii::getAlias('@web/LOGOWINTICKICO.ico')) ?>;

  // ===== SONIDO =====
  // Crear el objeto Audio de inmediato — el navegador permite cargar sin gesto del usuario,
  // solo bloquea el play() automático. Lo intentamos en cada notificación nueva.
  let notifAudio = null;
  try {
    notifAudio = new Audio(NOTIF_SOUND_URL);
    notifAudio.volume = 0.6;
    notifAudio.preload = 'auto';
  } catch(e) {}

  function playNotifSound() {
    if (!notifAudio) return;
    // Rebobinar y reproducir; si el navegador bloquea, falla silenciosamente
    notifAudio.currentTime = 0;
    const p = notifAudio.play();
    if (p && p.catch) p.catch(() => {});
  }

  // Exponer función para test manual desde consola
  window.testSound = () => { playNotifSound(); console.log('🔊 Probando sonido:', NOTIF_SOUND_URL); };

  // ===== WEB NOTIFICATIONS =====
  let sseSource = null;
  let lastNotifsSeen = new Set();
  // Si el reload fue tras guardar una solución, mostrar las nuevas notificaciones sin suprimirlas
  let firstLoad = !sessionStorage.getItem('notifNoSuprimir');
  sessionStorage.removeItem('notifNoSuprimir');

  async function ensureNotificationPermission() {
    if (!("Notification" in window)) return;
    if (Notification.permission !== "default") return;
    try { await Notification.requestPermission(); } catch(e) {}
  }

  function inicializarNotificaciones() {
    ensureNotificationPermission();
    // Cargar notificaciones existentes al abrir la pagina
    cargarNotificacionesIniciales();
    // Conectar SSE para recibir nuevas en tiempo real
    conectarSSE();
  }

  function cargarNotificacionesIniciales() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    fetch(NOTIFS_URL, {
      method: 'POST',
      headers: { 'X-CSRF-Token': token, 'Content-Type': 'application/json' }
    })
      .then(r => r.json())
      .then(data => {
        if (data && data.success) {
          mostrarNotificaciones(Array.isArray(data.notificaciones) ? data.notificaciones : []);
        }
      })
      .catch(err => console.error('Error cargando notificaciones iniciales:', err));
  }

  function conectarSSE() {
    if (sseSource) {
      sseSource.close();
    }

    sseSource = new EventSource(NOTIFS_STREAM_URL);

    sseSource.addEventListener('notificacion', function(e) {
      try {
        const nuevas = JSON.parse(e.data);
        if (Array.isArray(nuevas) && nuevas.length > 0) {
          // Recargar lista completa para mostrar el estado actualizado
          cargarNotificacionesIniciales();
          // Disparar alertas solo de las realmente nuevas
          dispararAlertas(nuevas);
        }
      } catch(err) {
        console.error('SSE parse error:', err);
      }
    });

    sseSource.onerror = function() {
      // Si la conexion cae, esperar 5s y reconectar automaticamente
      sseSource.close();
      setTimeout(conectarSSE, 5000);
    };
  }

  function dispararAlertas(notificaciones) {
    if (!("Notification" in window) || Notification.permission !== "granted") return;
    if (firstLoad) {
      notificaciones.forEach(n => lastNotifsSeen.add(String(n.id)));
      firstLoad = false;
      return;
    }
    notificaciones
      .filter(n => !lastNotifsSeen.has(String(n.id)))
      .forEach(n => {
        lastNotifsSeen.add(String(n.id));
        playNotifSound();
        const sysNotif = new Notification(n.titulo || 'WinTick', {
          body: n.mensaje || '',
          icon: NOTIF_ICON,
          tag: 'wintick-' + n.id,
          renotify: false
        });
        sysNotif.onclick = () => {
          window.focus();
          if (n.ticket_id) {
            if (n.tipo === 'mencion') {
              window.location.href = `${TICKET_INDEX_URL}?openComments=1&ticket_id=${encodeURIComponent(n.ticket_id)}&notif_id=${encodeURIComponent(n.id)}`;
            } else {
              window.location.href = `${TICKET_VIEW_URL}?id=${encodeURIComponent(n.ticket_id)}`;
            }
          }
        };
      });
  }

  function actualizarBannerPermiso() {
    const banner = document.getElementById('notifPermissionBanner');
    if (!banner) return;
    if (!("Notification" in window)) {
      banner.style.display = 'none';
      return;
    }
    const perm = Notification.permission;
    if (perm === 'granted') {
      banner.style.display = 'none';
    } else if (perm === 'default') {
      banner.className = 'perm-default';
      banner.style.display = 'flex';
      banner.innerHTML = `<i class="fas fa-bell-slash"></i> Notificaciones del navegador desactivadas
        <button onclick="ensureNotificationPermission().then(actualizarBannerPermiso)">Activar</button>`;
    } else {
      banner.className = 'perm-denied';
      banner.style.display = 'flex';
      banner.innerHTML = `<i class="fas fa-ban"></i> Bloqueaste las notificaciones del navegador. Actívalas en la configuración del sitio.`;
    }
  }

  function mostrarNotificaciones(notificaciones) {
    actualizarBannerPermiso();
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

      // tipo seguro para onclick
      const tipoSafe = String(notif.tipo || '').replace(/'/g, "\\'");

      return `
        <div class="notification-item ${!notif.leida ? 'unread' : ''}"
             onclick="abrirNotificacion(event, ${notif.id}, ${ticketId ?? 'null'}, '${tipoSafe}')">
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
  }

  function abrirNotificacion(event, notifId, ticketId, tipo) {
    event.stopPropagation();

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
        if (tipo === 'mencion') {
          window.location.href =
            `${TICKET_INDEX_URL}?openComments=1&ticket_id=${encodeURIComponent(ticketId)}&notif_id=${encodeURIComponent(notifId)}`;
          return;
        }
        window.location.href = `${TICKET_VIEW_URL}?id=${encodeURIComponent(ticketId)}`;
      });
  }

  function toggleNotifications() {
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
        if (data && data.success) cargarNotificacionesIniciales();
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
        if (data && data.success) cargarNotificacionesIniciales();
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

  document.addEventListener('click', function (e) {
    const bell = document.querySelector('.notification-bell');
    const dropdown = document.getElementById('notificationDropdown');

    const userToggle = document.querySelector('.user-dropdown-toggle');
    const userMenu = document.getElementById('userDropdownMenu');

    if (bell && dropdown && !bell.contains(e.target)) dropdown.classList.remove('show');
    if (userToggle && userMenu && !userToggle.contains(e.target) && !userMenu.contains(e.target)) userMenu.classList.remove('show');
  });

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

  // ============================================================
  //  FOTO DE PERFIL
  // ============================================================
  const AVATAR_URL = <?= json_encode(Url::to(['/usuarios/update-avatar'])) ?>;
  const COLOR_URL  = <?= json_encode(Url::to(['/usuarios/update-color'])) ?>;
  const CSRF_META  = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  function subirFotoPerfil(input) {
    const file = input.files[0];
    if (!file) return;

    const status = document.getElementById('avatarUploadStatus');
    if (status) status.style.display = 'block';

    const formData = new FormData();
    formData.append('avatar', file);
    formData.append('_csrf', CSRF_META());

    fetch(AVATAR_URL, {
      method: 'POST',
      headers: { 'X-CSRF-Token': CSRF_META() },
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (status) status.style.display = 'none';
      if (data.success) {
        // Actualizar foto en el toggle del navbar
        const toggle = document.getElementById('navUserAvatar');
        if (toggle) {
          const img = document.createElement('img');
          img.src = data.url + '?t=' + Date.now();
          img.style.cssText = 'width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.6);flex-shrink:0;';
          img.alt = 'foto';
          toggle.replaceWith(img);
          img.id = 'navUserAvatar';
        }
        // Actualizar preview grande en el header del dropdown
        const preview = document.getElementById('navAvatarPreview');
        if (preview) {
          const img2 = document.createElement('img');
          img2.src = data.url + '?t=' + Date.now();
          img2.style.cssText = 'width:52px;height:52px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,0.7);';
          img2.alt = 'foto';
          preview.replaceWith(img2);
          img2.id = 'navAvatarPreview';
        }
      } else {
        alert(data.message || 'Error al subir la foto.');
      }
    })
    .catch(() => {
      if (status) status.style.display = 'none';
      alert('Error de red al subir la foto.');
    });

    // Reset input para poder subir la misma foto de nuevo si hace falta
    input.value = '';
  }

  // ============================================================
  //  COLOR PICKER (solo Consultores)
  // ============================================================
  function toggleColorPicker(e) {
    e.stopPropagation();
    const p = document.getElementById('colorPickerWrap');
    if (p) p.style.display = p.style.display === 'none' ? 'block' : 'none';
  }

  function seleccionarColor(e, color) {
    e.stopPropagation();
    // Actualizar previsualización inmediata
    const icon = document.getElementById('colorPreviewIcon');
    if (icon) icon.style.background = color;
    const navAvatar = document.getElementById('navUserAvatar');
    if (navAvatar) navAvatar.style.backgroundColor = color;
    const picker = document.getElementById('colorPickerInput');
    if (picker) picker.value = color;

    fetch(COLOR_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-Token': CSRF_META() },
      body: new URLSearchParams({ color: color, '_csrf': CSRF_META() })
    })
    .then(r => r.json())
    .then(data => {
      if (!data.success) {
        console.warn('No se pudo guardar el color:', data.message);
      }
    })
    .catch(() => {});
  }


</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
