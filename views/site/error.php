<?php
/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = 'Ocurrió un problema';

// Lucide + Anime (se cargan aquí, pero NO metemos html/body)
$this->registerJsFile('https://unpkg.com/lucide@latest', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<style>
    :root{
        --sage: #8BA590;
        --sage-soft: #A0BAA5;
        --dark: #1f2933;
        --muted: #64748b;
        --bg: #f7f9f8;
        --card: #ffffff;
    }

    .wintick-error-page{
        
        min-height: calc(100vh - 160px); /* deja espacio al header/footer del layout */
        display:flex;
        align-items:center;
        justify-content:center;
        padding: 30px 16px;
    }

    .error-wrapper{ width:100%; max-width:520px; }

    
    .error-card{
        background: var(--card);
        border-radius:20px;
        padding:40px 3px;
        box-shadow: 0 10px 40px rgba(0,0,0,.08), 0 2px 8px rgba(0,0,0,.04);
        text-align:center;
        position:relative;
        overflow:hidden;
        opacity:0;
    }

    .error-card::before{
        content:'';
        position:absolute;
        top:0; left:0; right:0;
        height:4px;
       
    }

    .brand{
        display:flex;
        justify-content:center;
        align-items:center;
        gap:8px;
        font-size:13px;
        font-weight:600;
        letter-spacing:.12em;
        text-transform:uppercase;
        color: var(--sage);
        margin-bottom:26px;
    }

    .icon-wrap{
        width:96px; height:96px;
        margin:0 auto 24px;
        border-radius:50%;
        background: rgba(139,165,144,.12);
        display:flex; align-items:center; justify-content:center;
        transform: scale(.85);
    }

    .icon-wrap i{ width:42px; height:42px; stroke-width:2.2; color: var(--sage); }

    h1{
        margin:0 0 12px;
        font-size:26px;
        font-weight:600;
        color: var(--dark);
        letter-spacing:-.5px;
    }

    .message{
        font-size:15px;
        color: var(--muted);
        line-height:1.6;
        margin-bottom:28px;
    }

    .hint{
        display:flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        font-size:13px;
        color: var(--muted);
        margin-bottom:30px;
    }

    .hint i{ width:16px; height:16px; color: var(--sage); }

    .actions{
        display:flex;
        gap:12px;
        justify-content:center;
        flex-wrap:wrap;
    }

    .btnx{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:12px 18px;
        border-radius:10px;
        font-size:14px;
        font-weight:600;
        text-decoration:none;
        border:0;
        cursor:pointer;
        background:#eef2f1;
        color:#334155;
        transition:all .2s ease;
        transform: translateY(10px);
        opacity:0;
    }

    .btnx i{ width:18px; height:18px; }

    .btnx:hover{ transform:translateY(-1px); background:#e5ebe9; }

    .btnx.primary{ background: var(--sage); color:white; }
    .btnx.primary:hover{ background:#7f9886; }

    @media(max-width:480px){
        .error-card{ padding:32px 24px; }
    }
</style>

<div class="wintick-error-page">
    <div class="error-wrapper">
        <div class="error-card" id="errorCard">

            <div class="brand">
                <i data-lucide="shield-alert"></i>
                Wintick
            </div>

            <div class="icon-wrap" id="iconWrap">
                <i data-lucide="alert-triangle"></i>
            </div>

            <h1><?= Html::encode($name ?: 'Algo no salió bien') ?></h1>

            <div class="message">
                <?= nl2br(Html::encode($message ?: 'Ocurrió un error inesperado.')) ?>
            </div>

            <div class="hint">
                <i data-lucide="info"></i>
                Puedes intentar regresar o volver al inicio
            </div>

            <div class="actions">
                <a href="javascript:history.back()" class="btnx primary">
                    <i data-lucide="arrow-left"></i>
                    Volver
                </a>

                <a href="<?= Yii::$app->homeUrl ?>" class="btnx">
                    <i data-lucide="home"></i>
                    Inicio
                </a>

                <button onclick="location.reload()" class="btnx" type="button">
                    <i data-lucide="refresh-cw"></i>
                    Recargar
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    // icons
    if (window.lucide) lucide.createIcons();

    // animations
    if (window.anime) {
        anime({
            targets: '#errorCard',
            opacity: [0,1],
            translateY: [20,0],
            easing: 'easeOutExpo',
            duration: 900
        });

        anime({
            targets: '#iconWrap',
            scale: [0.85,1],
            easing: 'easeOutElastic(1, .6)',
            duration: 1200,
            delay: 250
        });

        anime({
            targets: '.btnx',
            opacity: [0,1],
            translateY: [10,0],
            delay: anime.stagger(120, {start: 550}),
            easing: 'easeOutCubic'
        });
    }
</script>
