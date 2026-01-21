<?php
/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;
use yii\web\View;

$this->title = 'Ocurrió un problema';

// Lucide + Anime
$this->registerJsFile('https://unpkg.com/lucide@latest', ['position' => View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js', ['position' => View::POS_HEAD]);
?>

<style>
  :root{
    --sage: #8BA590;
    --sage-soft: #A0BAA5;
    --dark: #1f2933;
    --muted: #64748b;
    --bg: #f7f9f8;
    --card: #ffffff;
    --ring: rgba(139,165,144,.22);
    --shadow: 0 18px 60px rgba(0,0,0,.10), 0 4px 12px rgba(0,0,0,.05);
  }

  body{ background: var(--bg); }

  .wintick-error-page{
    min-height: calc(100vh - 120px);
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 0px 16px;
    position: relative;
    overflow:hidden;
  }

  /* Background glow blobs */
  .wintick-error-page::before,
  .wintick-error-page::after{
    content:"";
    position:absolute;
    width: 520px;
    height: 520px;
    border-radius: 50%;
    filter: blur(60px);
    opacity: .35;
    z-index: 0;
    pointer-events:none;
  }
  .wintick-error-page::before{
   
    top: -220px;
    left: -220px;
  }
  .wintick-error-page::after{
   
    bottom: -240px;
    right: -240px;
  }

  .error-wrapper{
    width:100%;
    max-width: 560px;
    position: relative;
    z-index: 1;
  }

  .error-card{
    background: rgba(255,255,255,.86);
    border: 1px solid rgba(17,24,39,.06);
    border-radius: 22px;
    padding: 26px 24px 22px;
    box-shadow: var(--shadow);
    text-align:center;
    position:relative;
    overflow:hidden;
    opacity:0;
    transform: translateY(14px);
    backdrop-filter: blur(10px);
  }

  /* top animated gradient line */
  .error-card::before{
    content:'';
    position:absolute;
    top:0; left:0; right:0;
    height: 4px;
    background: linear-gradient(90deg, rgba(139,165,144,.0), rgba(139,165,144,.9), rgba(160,186,165,.9), rgba(139,165,144,.0));
    background-size: 200% 100%;
    animation: flow 3.4s ease-in-out infinite;
  }
  @keyframes flow{
    0%{ background-position: 0% 50%; }
    50%{ background-position: 100% 50%; }
    100%{ background-position: 0% 50%; }
  }

  .brand{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:10px;
    font-size:12px;
    font-weight:700;
    letter-spacing:.16em;
    text-transform:uppercase;
    color: var(--sage);
    margin: 6px 0 14px;
  }
  .brand i{ width:18px; height:18px; }

  .lottie-shell{
    display:flex;
    justify-content:center;
    margin: 6px 0 10px;
  }

  .lottie-ring{
    width: 190px;
    height: 190px;
    border-radius: 999px;
    background: radial-gradient(circle at 30% 30%, rgba(160,186,165,.20), rgba(160,186,165,.06));
    border: 1px solid rgba(139,165,144,.16);
    box-shadow: 0 0 0 8px rgba(139,165,144,.06), 0 18px 55px rgba(139,165,144,.12);
    display:flex;
    align-items:center;
    justify-content:center;
    position: relative;
    transform: scale(.96);
  }

  /* subtle orbit dots */
  .orbit{
    position:absolute;
    inset: -14px;
    border-radius: 999px;
    border: 1px dashed rgba(139,165,144,.22);
    opacity: .55;
  }
  .dot{
    position:absolute;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(139,165,144,.75);
    box-shadow: 0 0 0 6px rgba(139,165,144,.12);
    top: -3px;
    left: 50%;
    transform: translateX(-50%);
  }

  #canvas{
    width: 170px;
    height: 170px;
    border-radius: 16px;
  }

  /* Badge icon (small) */
  .status-badge{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(139,165,144,.10);
    border: 1px solid rgba(139,165,144,.18);
    color: #334155;
    font-weight: 700;
    font-size: 12px;
    margin-top: 8px;
    opacity:0;
    transform: translateY(10px);
  }
  .status-badge i{ width:16px; height:16px; color: var(--sage); }

  h1{
    margin: 14px 0 10px;
    font-size: 24px;
    font-weight: 800;
    color: var(--dark);
    letter-spacing:-.4px;
    line-height: 1.15;
  }

  .message{
    font-size: 14.6px;
    color: var(--muted);
    line-height: 1.65;
    margin: 0 auto 18px;
    max-width: 46ch;
  }

  .hint{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    font-size: 13px;
    color: var(--muted);
    margin: 0 0 18px;
    opacity:0;
    transform: translateY(10px);
  }
  .hint i{ width:16px; height:16px; color: var(--sage); }

  .actions{
    display:flex;
    gap:12px;
    justify-content:center;
    flex-wrap:wrap;
    margin-top: 6px;
  }

  .btnx{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:12px 16px;
    border-radius:12px;
    font-size:14px;
    font-weight:800;
    text-decoration:none;
    border: 1px solid rgba(15,23,42,.08);
    cursor:pointer;
    background: rgba(255,255,255,.9);
    color:#334155;
    transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
    transform: translateY(10px);
    opacity:0;
    box-shadow: 0 10px 26px rgba(0,0,0,.06);
  }
  .btnx i{ width:18px; height:18px; }

  .btnx:hover{
    transform: translateY(-1px);
    background: #ffffff;
    box-shadow: 0 16px 40px rgba(0,0,0,.10);
  }

  .btnx.primary{
    background: linear-gradient(180deg, var(--sage-soft), var(--sage));
    color:white;
    border-color: rgba(255,255,255,.15);
  }
  .btnx.primary:hover{
    filter: brightness(.98);
  }

  @media (max-width: 480px){
    .error-card{ padding: 22px 18px 18px; }
    h1{ font-size: 22px; }
    .lottie-ring{ width: 176px; height: 176px; }
    #canvas{ width: 158px; height: 158px; }
  }
</style>

<div class="wintick-error-page">
  <div class="error-wrapper">
    <div class="error-card" id="errorCard">

      <div class="brand">
        <i data-lucide="shield-alert"></i>
        Wintick
      </div>

      <div class="lottie-shell">
        <div class="lottie-ring" id="lottieRing">
          <div class="orbit"></div>
          <div class="dot" id="orbitDot"></div>

          <canvas id="canvas" width="300" height="300"></canvas>
        </div>
      </div>

      <div class="status-badge" id="statusBadge">
        <i data-lucide="alert-triangle"></i>
        Error detectado
      </div>

      <h1><?= Html::encode($name ?: 'Algo no salió bien') ?></h1>

      <div class="message">
        <?= nl2br(Html::encode($message ?: 'Ocurrió un error inesperado.')) ?>
      </div>

      <div class="hint" id="hintRow">
        <i data-lucide="info"></i>
        Puedes intentar regresar, ir al inicio o recargar
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

<!-- DotLottie (CDN ESM) -->
<script type="module">
  import { DotLottie } from "https://cdn.jsdelivr.net/npm/@lottiefiles/dotlottie-web/+esm";

  new DotLottie({
    autoplay: true,
    loop: true,
    canvas: document.getElementById("canvas"),
    src: "https://lottie.host/5cfae4e0-6fc7-48bd-9af1-12cb1668d834/KoOrcf3hfE.lottie",
  });
</script>

<script>
  // lucide
  if (window.lucide) lucide.createIcons();

  // anime entrance + micro-interactions
  if (window.anime) {
    anime({
      targets: '#errorCard',
      opacity: [0, 1],
      translateY: [14, 0],
      duration: 900,
      easing: 'easeOutExpo'
    });

    anime({
      targets: '#lottieRing',
      scale: [.92, 1],
      duration: 1100,
      delay: 120,
      easing: 'easeOutElastic(1, .55)'
    });

    // orbit dot rotation (infinite)
    anime({
      targets: '#orbitDot',
      rotate: 360,
      duration: 3800,
      easing: 'linear',
      loop: true,
      transformOrigin: '50% 95px'
    });

    anime({
      targets: '#statusBadge',
      opacity: [0, 1],
      translateY: [10, 0],
      duration: 650,
      delay: 280,
      easing: 'easeOutCubic'
    });

    anime({
      targets: '#hintRow',
      opacity: [0, 1],
      translateY: [10, 0],
      duration: 650,
      delay: 360,
      easing: 'easeOutCubic'
    });

    anime({
      targets: '.btnx',
      opacity: [0, 1],
      translateY: [10, 0],
      delay: anime.stagger(120, { start: 520 }),
      duration: 700,
      easing: 'easeOutCubic'
    });

    // Hover pulse on ring (subtle)
    const ring = document.getElementById('lottieRing');
    ring.addEventListener('mouseenter', () => {
      anime.remove(ring);
      anime({ targets: ring, scale: 1.02, duration: 260, easing: 'easeOutQuad' });
    });
    ring.addEventListener('mouseleave', () => {
      anime.remove(ring);
      anime({ targets: ring, scale: 1.0, duration: 320, easing: 'easeOutQuad' });
    });
  }
</script>
