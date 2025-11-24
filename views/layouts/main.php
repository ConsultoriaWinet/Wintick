<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
        <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.19/index.global.min.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.19/index.global.min.js'></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
.elpepe{ 
    background-color: #A0BAA5;
}
.elnegro{ 
    color: #000000;
}
</style>

<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header id="header">
        <?php if (
            Yii::$app->controller->id !== 'site' || 
            !in_array(Yii::$app->controller->action->id, ['login', 'requestpassword', 'resetpassword'])
        ): ?>
            <?php
            NavBar::begin([
                'brandLabel' => Yii::$app->name,
                'brandUrl' => Yii::$app->homeUrl,
<<<<<<< HEAD
                'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark bg-primary fixed-top']
=======
                'options' => ['class' => 'navbar-expand-md elpepe fixed-top elnegro']
>>>>>>> 5bc250a40dae89f22a1bd30754299a159acf84c1
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'items' => [
                    ['label' => 'Clientes', 'url' => ['/clientes/index']],
                    ['label' => 'Tickets', 'url' => ['/tickets/index']],
                    ['label' => 'Usuarios', 'url' => ['/usuarios/index']],
                            ]
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav ms-auto'],
                'items' => [
                    Yii::$app->user->isGuest
                        ? ['label' => 'Login', 'url' => ['/site/login']]
                        : '<li class="nav-item elnegro">'
                        . Html::beginForm(['/site/logout'])
                        . Html::submitButton(
                            'Logout (' . Yii::$app->user->identity->email . ')',
                            ['class' => 'nav-link btn btn-link elnegro logout']
                        )
                        . Html::endForm()
                        . '</li>'
                ]
            ]);
            NavBar::end();
            ?>
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

    <footer id="footer" class="mt-auto py-3 elpepe">
        <div class="container">
            <div class="row text-white">
                <div class="col-md-6 text-center text-md-start">&copy; My Company <?= date('Y') ?></div>
                <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>