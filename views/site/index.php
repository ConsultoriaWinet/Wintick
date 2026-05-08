<?php
/** @var yii\web\View $this */
/** @var app\models\Usuarios[] $consultores */
/** @var bool $esMonitor */

use yii\helpers\Url;
use yii\helpers\Html;

$esMonitor = $esMonitor ?? false;

$this->title = 'Dashboard - Tickets por Consultor';
$this->params['fullWidth'] = true;
?>

<!-- FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.19/index.global.min.js'></script>

<style>
/* ─── Página completa sin container ─────────────────────── */
.site-index { margin: 0; }

/* ─── Wrapper principal: sidebar + área central ──────────── */
.dashboard-wrap {
    display: flex;
    height: calc(100vh - 62px);
    min-height: 520px;
    overflow: hidden;
    margin-top: 54px; /* navbar fijo ~62px - body padding-top 8px */
}

/* ─── Sidebar consultores ────────────────────────────────── */
.consultores-sidebar {
    width: 210px;
    min-width: 190px;
    flex-shrink: 0;
    padding: 14px 8px;
    border-right: 1px solid var(--border, #e5e7eb);
    overflow-y: auto;
    background: var(--surface, #fff);
}
.consultores-sidebar h3 {
    font-size: 10.5px; font-weight: 700;
    color: var(--text-3, #9ca3af);
    letter-spacing: .07em; text-transform: uppercase;
    margin: 0 0 10px 6px;
}
.consultor-item {
    padding: 6px 9px; margin-bottom: 2px; border-radius: 8px; cursor: pointer;
    display: flex; align-items: center; gap: 9px;
    color: var(--text, #111827); font-size: 13px;
    transition: background .15s; user-select: none;
}
.consultor-item:hover  { background: var(--surface-2, #f9fafb); }
.consultor-item.active { background: var(--accent-light, #eff6ff); font-weight: 600; color: var(--accent-dark, #1d4ed8); }

/* Avatar con anillo de color */
.consultor-av {
    position: relative; flex-shrink: 0;
    width: 32px; height: 32px;
}
.consultor-av img,
.consultor-av .av-initials {
    width: 28px; height: 28px;
    border-radius: 50%;
    position: absolute; top: 2px; left: 2px;
    object-fit: cover;
}
.consultor-av .av-initials {
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: #fff;
}
.consultor-av::before {
    content: '';
    position: absolute; inset: 0;
    border-radius: 50%;
    border: 2.5px solid var(--av-ring, #6b7280);
}

/* ─── Área central (calendario + encabezado) ─────────────── */
#cal-area {
    flex: 1; min-width: 0;
    display: flex; flex-direction: column;
    overflow: hidden;
}
#cal-topbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 16px 6px;
    flex-shrink: 0;
}
#cal-topbar h1 { margin: 0; font-size: 18px; font-weight: 700; }
#cal-topbar .hint { font-size: 11px; color: var(--text-3, #9ca3af); }
#calendar-container {
    flex: 1; min-height: 0; padding: 4px 14px 10px;
    overflow: hidden;
}

/* FullCalendar overrides */
.fc .fc-toolbar-title { font-size: 16px !important; font-weight: 700 !important; color: var(--text, #111827); }
.fc .fc-button {
    background: var(--surface-2, #f3f4f6) !important;
    border: 1px solid var(--border, #e5e7eb) !important;
    color: var(--text, #374151) !important;
    box-shadow: none !important; font-size: 12px !important;
    padding: 4px 10px !important; border-radius: 6px !important;
    font-weight: 500 !important; transition: background .15s !important;
}
.fc .fc-button:hover { background: var(--accent-light, #eff6ff) !important; color: var(--accent-dark, #1d4ed8) !important; }
.fc .fc-button-active,
.fc .fc-button-primary:not(:disabled).fc-button-active {
    background: var(--accent, #3b82f6) !important; color: #fff !important;
    border-color: var(--accent, #3b82f6) !important;
}
.fc .fc-today-button { opacity: 1 !important; }
.fc th { font-size: 11px !important; font-weight: 700 !important; text-transform: uppercase; color: var(--text-3, #6b7280); letter-spacing: .05em; }
.fc .fc-daygrid-day-number { font-size: 12px !important; font-weight: 600; color: var(--text-2, #374151); padding: 3px 6px !important; }
.fc .fc-day-today { background: #eff6ff !important; }
.fc .fc-day-today .fc-daygrid-day-number { color: var(--accent, #3b82f6) !important; font-weight: 800; }
.fc .fc-daygrid-event { border-radius: 4px !important; font-size: 10.5px !important; padding: 1px 4px !important; font-weight: 500 !important; border: none !important; }
.fc .fc-event-title { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.fc .fc-list-event:hover td { background: var(--accent-light, #eff6ff) !important; }
.fc .fc-scrollgrid { border-color: var(--border, #e5e7eb) !important; }
.fc .fc-scrollgrid td, .fc .fc-scrollgrid th { border-color: var(--border, #e5e7eb) !important; }
.fc .fc-daygrid-day-frame { cursor: pointer; }
.fc .fc-daygrid-day-frame:active { background: rgba(59,130,246,.05); }

/* ─── Panel día — FUERA del calendario, posición fija ──────── */
#day-panel {
    position: fixed;
    top: 62px; right: 0;
    width: 0;
    height: calc(100vh - 62px);
    overflow: hidden;
    transition: width .25s ease;
    background: var(--surface, #fff);
    border-left: 1px solid var(--border, #e5e7eb);
    box-shadow: -4px 0 20px rgba(0,0,0,.08);
    display: flex; flex-direction: column;
    z-index: 200;
}
#day-panel.open { width: 360px; }

.dp-header {
    padding: 14px 16px 11px;
    border-bottom: 1px solid var(--border, #e5e7eb);
    display: flex; align-items: flex-start; gap: 10px; flex-shrink: 0;
}
.dp-date-box {
    background: var(--accent, #3b82f6); color: #fff;
    border-radius: 10px; width: 44px; height: 44px;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    flex-shrink: 0; line-height: 1;
}
.dp-date-box .dp-day { font-size: 20px; font-weight: 800; }
.dp-date-box .dp-mon { font-size: 9.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; margin-top: 1px; }
.dp-header-info { flex: 1; min-width: 0; }
.dp-header-info h4 { margin: 0; font-size: 13.5px; font-weight: 700; color: var(--text, #111827); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dp-header-info .dp-count { margin-top: 2px; font-size: 11.5px; color: var(--text-3, #6b7280); }
.dp-close {
    width: 26px; height: 26px; border: none; background: none; cursor: pointer;
    color: var(--text-3, #9ca3af); display: flex; align-items: center; justify-content: center;
    border-radius: 6px; flex-shrink: 0; font-size: 13px; transition: background .15s;
}
.dp-close:hover { background: var(--surface-2, #f3f4f6); color: var(--text, #374151); }
#day-panel-body { flex: 1; overflow-y: auto; padding: 10px 12px; display: flex; flex-direction: column; gap: 8px; }

.dp-ticket {
    border: 1px solid var(--border, #e5e7eb); border-radius: 10px; padding: 9px 11px;
    cursor: pointer; transition: box-shadow .15s, border-color .15s;
    background: var(--surface, #fff); text-decoration: none; color: inherit; display: block;
}
.dp-ticket:hover { box-shadow: 0 2px 10px rgba(0,0,0,.08); border-color: var(--accent, #3b82f6); color: inherit; text-decoration: none; }
.dp-ticket-top { display: flex; align-items: center; gap: 6px; margin-bottom: 5px; }
.dp-folio { font-size: 11.5px; font-weight: 700; color: var(--text, #111827); flex: 1; }
.dp-hora { font-size: 10.5px; color: var(--text-3, #9ca3af); white-space: nowrap; }
.dp-desc { font-size: 11.5px; color: var(--text-2, #374151); line-height: 1.4; margin-bottom: 6px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.dp-meta { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.dp-cliente { font-size: 10.5px; color: var(--text-3, #6b7280); font-weight: 500; flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dp-asignado { display: flex; align-items: center; gap: 4px; }
.dp-av { width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 700; color: #fff; flex-shrink: 0; }
.dp-avname { font-size: 10.5px; color: var(--text-3, #6b7280); }

.dp-estado { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 600; padding: 2px 6px; border-radius: 20px; white-space: nowrap; }
.dp-estado::before { content:''; width:5px; height:5px; border-radius:50%; flex-shrink:0; }
.dp-e-abierto    { background:#EFF6FF; color:#2563EB; } .dp-e-abierto::before    { background:#2563EB; }
.dp-e-programado { background:#F0FDF4; color:#16A34A; } .dp-e-programado::before { background:#16A34A; }
.dp-e-proceso    { background:#FFFBEB; color:#D97706; } .dp-e-proceso::before    { background:#D97706; }
.dp-e-espera     { background:#F5F3FF; color:#7C3AED; } .dp-e-espera::before     { background:#7C3AED; }
.dp-e-contpaqi   { background:#FFFBEB; color:#B45309; } .dp-e-contpaqi::before   { background:#B45309; }
.dp-e-cerrado    { background:#F1F5F9; color:#475569; } .dp-e-cerrado::before    { background:#475569; }
.dp-e-cancelado  { background:#F3F4F6; color:#6B7280; } .dp-e-cancelado::before  { background:#6B7280; }

.dp-prio { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.dp-prio-alta { background:#ef4444; } .dp-prio-media { background:#f59e0b; }
.dp-prio-baja { background:#22c55e; } .dp-prio-def   { background:#9ca3af; }

.dp-empty { text-align:center; padding:36px 16px; color:var(--text-3,#9ca3af); font-size:12.5px; }
.dp-empty i { font-size:26px; margin-bottom:8px; display:block; }
.dp-loading { text-align:center; padding:36px 16px; color:var(--text-3,#9ca3af); font-size:12.5px; }

/* ─── Responsive ─────────────────────────────────────────── */
.sidebar-toggle-btn {
    display: none; width: 100%; padding: 8px 12px;
    background: var(--surface-2,#f3f4f6); border: 1px solid var(--border,#e5e7eb);
    border-radius: 8px; font-size: 13px; font-weight: 500; color: var(--text,#374151);
    cursor: pointer; margin-bottom: 8px; text-align: left;
}
.sidebar-toggle-btn i { margin-right: 7px; }
.sidebar-lista { display: flex; flex-direction: column; }

@media (max-width: 900px) {
    .dashboard-wrap { flex-direction: column; height: auto; }
    .consultores-sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--border,#e5e7eb); }
    #day-panel { top: 0; height: 100vh; }
    #day-panel.open { width: min(360px, 100vw); }
}
@media (max-width: 767px) {
    .sidebar-toggle-btn { display: block; }
    .sidebar-lista.collapsed { display: none; }
    .fc .fc-button { padding: 3px 6px !important; font-size: 11px !important; }
    .fc .fc-toolbar-title { font-size: 13px !important; }
    .fc .fc-toolbar { flex-wrap: wrap; gap: 4px; }
}
</style>

<div class="site-index"> 
    
    <br>
    <div class="dashboard-wrap">
        <!-- Sidebar consultores -->
        <div class="consultores-sidebar">
            <button class="sidebar-toggle-btn" onclick="toggleSidebar(this)">
                <i class="fas fa-filter"></i> Filtrar
                <i class="fas fa-chevron-down" style="float:right;margin-top:2px;" id="sidebarChevron"></i>
            </button>

            <div class="sidebar-lista collapsed" id="sidebarLista">
                <h3 class="d-none d-md-block">Consultores</h3>

                <div class="consultor-item active" onclick="<?= $esMonitor ? 'void(0)' : 'filtrarPorConsultor(null)' ?>" id="consultor-todos">
                    <div class="consultor-av" style="--av-ring:#6b7280;">
                        <span class="av-initials" style="background:#6b7280;">
                            <i class="fas fa-users" style="font-size:10px;"></i>
                        </span>
                    </div>
                    <span>Todos</span>
                </div>

                <?php foreach ($consultores as $consultor):
                    $color   = $consultor->color ?? '#6c757d';
                    $avatar  = $consultor->avatar ?? null;
                    $nombre  = $consultor->Nombre ?? $consultor->email;
                    $inicial = mb_strtoupper(mb_substr($nombre, 0, 1, 'UTF-8'), 'UTF-8');
                    $esFoto  = $avatar && str_starts_with($avatar, '/uploads/');
                    $fotoUrl = $esFoto ? Yii::getAlias('@web') . $avatar : null;
                ?>
                    <div class="consultor-item" onclick="<?= $esMonitor ? 'void(0)' : 'filtrarPorConsultor(' . $consultor->id . ')' ?>"
                         id="consultor-<?= $consultor->id ?>">
                        <div class="consultor-av" style="--av-ring:<?= Html::encode($color) ?>;">
                            <?php if ($fotoUrl): ?>
                                <img src="<?= Html::encode($fotoUrl) ?>" alt="<?= Html::encode($nombre) ?>">
                            <?php else: ?>
                                <span class="av-initials" style="background:<?= Html::encode($color) ?>;">
                                    <?= Html::encode($inicial) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <span><?= Html::encode($nombre) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Área calendario -->
        <div id="cal-area">
            <div id="cal-topbar">
                <h1>Calendario de Tickets</h1>
                <span class="hint"><i class="fas fa-hand-pointer"></i> Doble clic en un día · clic en ticket para ver resumen</span>
            </div>
            <div id="calendar-container">
                <div id="calendar"></div>
            </div>
        </div>

    </div><!-- /.dashboard-wrap -->

    <!-- Panel día — FUERA del calendario, posición fija -->
    <div id="day-panel">
        <div class="dp-header">
            <div class="dp-date-box">
                <span class="dp-day" id="dp-day-num">—</span>
                <span class="dp-mon" id="dp-mon-str">—</span>
            </div>
            <div class="dp-header-info">
                <h4 id="dp-title">Día</h4>
                <div class="dp-count" id="dp-count"></div>
            </div>
            <button class="dp-close" onclick="closeDayPanel()" title="Cerrar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="day-panel-body">
            <div class="dp-empty"><i class="fas fa-calendar-day"></i>Selecciona un día</div>
        </div>
    </div>

</div>

<script>
const ES_MONITOR     = <?= $esMonitor ? 'true' : 'false' ?>;
const URL_TICKETS_DIA = '<?= Url::to(['site/get-tickets-dia']) ?>';
const URL_VER_TICKET  = '<?= Url::to(['tickets/view']) ?>';

let calendar;
let consultorActual = null;
let dayPanelOpen    = false;

/* ── abrir panel ── */
function openDayPanel(dateStr) {
    const panel = document.getElementById('day-panel');
    const body  = document.getElementById('day-panel-body');

    // Actualizar cabecera
    const d   = new Date(dateStr + 'T12:00:00');
    const day = d.getDate();
    const mon = d.toLocaleDateString('es-MX', { month: 'short' }).replace('.','').toUpperCase();
    const full= d.toLocaleDateString('es-MX', { weekday:'long', day:'numeric', month:'long' });
    document.getElementById('dp-day-num').textContent = day;
    document.getElementById('dp-mon-str').textContent = mon;
    document.getElementById('dp-title').textContent   = full.charAt(0).toUpperCase() + full.slice(1);
    document.getElementById('dp-count').textContent   = 'Cargando…';

    body.innerHTML = '<div class="dp-loading"><i class="fas fa-circle-notch fa-spin" style="font-size:22px;margin-bottom:10px;display:block;"></i>Cargando tickets…</div>';

    panel.classList.add('open');
    dayPanelOpen = true;

    // Fetch
    fetch(URL_TICKETS_DIA + '?fecha=' + dateStr)
        .then(r => r.json())
        .then(tickets => renderDayPanel(tickets, dateStr))
        .catch(() => {
            body.innerHTML = '<div class="dp-empty"><i class="fas fa-exclamation-circle"></i>Error al cargar tickets.</div>';
            document.getElementById('dp-count').textContent = '';
        });
}

function closeDayPanel() {
    document.getElementById('day-panel').classList.remove('open');
    dayPanelOpen = false;
}

/* ── panel para un solo ticket (desde eventClick) ── */
function openTicketPanel(event) {
    const panel = document.getElementById('day-panel');
    const body  = document.getElementById('day-panel-body');
    const props = event.extendedProps;

    const d   = event.start;
    const day = d.getDate();
    const mon = d.toLocaleDateString('es-MX', { month: 'short' }).replace('.','').toUpperCase();
    const hora = d.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
    const full = d.toLocaleDateString('es-MX', { weekday:'long', day:'numeric', month:'long' });

    document.getElementById('dp-day-num').textContent = day;
    document.getElementById('dp-mon-str').textContent = mon;
    document.getElementById('dp-title').textContent   = full.charAt(0).toUpperCase() + full.slice(1);
    document.getElementById('dp-count').textContent   = hora;

    const estadoClass = dpEstadoClass(props.estado);
    const estadoLabel = dpEstadoLabel(props.estado);
    const prioClass   = dpPrioClass(props.prioridad);

    body.innerHTML = `
        <a class="dp-ticket" href="${URL_VER_TICKET}?id=${event.id}" style="border-left:3px solid ${esc(event.backgroundColor||'#3b82f6')};">
            <div class="dp-ticket-top">
                <span class="dp-prio ${prioClass}"></span>
                <span class="dp-folio">${esc(event.title)}</span>
                <span class="${estadoClass} dp-estado">${estadoLabel}</span>
                <span class="dp-hora">${esc(hora)}</span>
            </div>
            <div class="dp-desc" style="-webkit-line-clamp:unset;">${esc(props.description || '—')}</div>
            <div style="margin:8px 0 6px;border-top:1px solid var(--border,#e5e7eb);"></div>
            <div style="display:flex;flex-direction:column;gap:6px;font-size:12px;color:var(--text-2,#374151);">
                <div><i class="fas fa-building" style="width:14px;opacity:.5;"></i> <strong>Cliente:</strong> ${esc(props.cliente)}</div>
                <div><i class="fas fa-desktop" style="width:14px;opacity:.5;"></i> <strong>Sistema:</strong> ${esc(props.sistema)}</div>
                <div><i class="fas fa-tools" style="width:14px;opacity:.5;"></i> <strong>Servicio:</strong> ${esc(props.servicio)}</div>
                <div><i class="fas fa-user" style="width:14px;opacity:.5;"></i> <strong>Consultor:</strong> ${esc(props.consultorNombre)}</div>
            </div>
            <div style="margin-top:10px;">
                <span style="display:inline-flex;align-items:center;gap:5px;font-size:11.5px;color:var(--accent,#3b82f6);font-weight:600;">
                    <i class="fas fa-external-link-alt"></i> Ver ticket completo
                </span>
            </div>
        </a>`;

    panel.classList.add('open');
    dayPanelOpen = true;
}

/* ── render panel ── */
function renderDayPanel(tickets, dateStr) {
    const body = document.getElementById('day-panel-body');
    const cnt  = document.getElementById('dp-count');

    cnt.textContent = tickets.length === 0 ? 'Sin tickets este día'
        : tickets.length === 1 ? '1 ticket' : tickets.length + ' tickets';

    if (tickets.length === 0) {
        body.innerHTML = '<div class="dp-empty"><i class="fas fa-check-circle" style="color:#22c55e;"></i>Sin tickets programados.</div>';
        return;
    }

    body.innerHTML = '';

    tickets.forEach(t => {
        const estadoClass = dpEstadoClass(t.estado);
        const estadoLabel = dpEstadoLabel(t.estado);
        const prioClass   = dpPrioClass(t.prioridad);

        const avHtml = t.asignado.avatar
            ? `<img src="${esc(t.asignado.avatar)}" class="dp-av" style="object-fit:cover;padding:0;">`
            : `<span class="dp-av" style="background:${esc(t.asignado.color)};">${esc(t.asignado.ini)}</span>`;

        const card = document.createElement('a');
        card.className = 'dp-ticket';
        card.href      = URL_VER_TICKET + '?id=' + t.id;
        card.innerHTML = `
            <div class="dp-ticket-top">
                <span class="dp-prio ${prioClass}"></span>
                <span class="dp-folio">${esc(t.folio)}</span>
                <span class="${estadoClass} dp-estado">${estadoLabel}</span>
                ${t.hora ? `<span class="dp-hora">${esc(t.hora)}</span>` : ''}
            </div>
            <div class="dp-desc">${esc(t.descripcion || '—')}</div>
            <div class="dp-meta">
                <span class="dp-cliente"><i class="fas fa-building" style="opacity:.5;margin-right:3px;"></i>${esc(t.cliente)}</span>
                <span class="dp-asignado">${avHtml}<span class="dp-avname">${esc(t.asignado.nombre)}</span></span>
            </div>`;

        body.appendChild(card);
    });
}

function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function dpEstadoClass(e) {
    const m = { 'ABIERTO':'dp-e-abierto','PROGRAMADO':'dp-e-programado','EN PROCESO':'dp-e-proceso',
        'EN ESPERA':'dp-e-espera','CONTPAQi':'dp-e-contpaqi','CERRADO':'dp-e-cerrado','CANCELADO':'dp-e-cancelado' };
    return m[e] || 'dp-e-cancelado';
}
function dpEstadoLabel(e) {
    const m = { 'ABIERTO':'Abierto','PROGRAMADO':'Programado','EN PROCESO':'En proceso',
        'EN ESPERA':'En espera','CONTPAQi':'CONTPAQi','CERRADO':'Cerrado','CANCELADO':'Cancelado' };
    return m[e] || e;
}
function dpPrioClass(p) {
    return p === 'ALTA' ? 'dp-prio-alta' : p === 'MEDIA' ? 'dp-prio-media' : p === 'BAJA' ? 'dp-prio-baja' : 'dp-prio-def';
}

/* ── init calendario ── */
function toggleSidebar(btn) {
    const lista   = document.getElementById('sidebarLista');
    const chevron = document.getElementById('sidebarChevron');
    const col     = lista.classList.toggle('collapsed');
    chevron.style.transform = col ? 'rotate(0deg)' : 'rotate(180deg)';
}

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale:      'es',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: { today:'Hoy', month:'Mes', week:'Semana', day:'Día', list:'Lista' },

        slotMinTime:  '08:00:00',
        slotMaxTime:  '20:00:00',
        allDaySlot:   true,
        height:       '100%',
        expandRows:   true,
        dayMaxEvents: false,

        editable:     !ES_MONITOR,
        selectable:   !ES_MONITOR,
        selectMirror: !ES_MONITOR,

        events: function (info, successCallback, failureCallback) {
            let url = '<?= Url::to(['site/get-tickets']) ?>';
            if (consultorActual) url += '?consultorId=' + consultorActual;
            fetch(url)
                .then(r => r.json())
                .then(data => successCallback(data))
                .catch(err  => failureCallback(err));
        },

        /* click en evento → muestra solo ese ticket en el panel */
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            openTicketPanel(info.event);
        },

        /* select rango */
        select: function (info) {
            if (ES_MONITOR) { calendar.unselect(); return; }
            Swal.fire({
                title: '¿Crear nuevo ticket?',
                text:  'Para la fecha: ' + info.startStr,
                icon:  'question',
                showCancelButton:   true,
                confirmButtonColor: '#28a745',
                cancelButtonColor:  '#d33',
                confirmButtonText:  'Sí, crear',
                cancelButtonText:   'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '<?= Url::to(['tickets/create']) ?>';
                    const csrf  = document.createElement('input');
                    csrf.type   = 'hidden';
                    csrf.name   = '<?= Yii::$app->request->csrfParam ?>';
                    csrf.value  = '<?= Yii::$app->request->csrfToken ?>';
                    form.appendChild(csrf);
                    const fi    = document.createElement('input');
                    fi.type     = 'hidden';
                    fi.name     = 'fecha_seleccionada';
                    fi.value    = info.startStr;
                    form.appendChild(fi);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        },

        /* drag */
        eventDrop: function (info) {
            const nuevaFecha = info.event.start;
            const folio      = info.event.title;
            const cerrado    = (info.event.extendedProps.estado || '').toUpperCase() === 'CERRADO';
            const fechaStr   = nuevaFecha.toLocaleString('es-MX', { weekday:'long',year:'numeric',month:'long',day:'numeric',hour:'2-digit',minute:'2-digit' });

            const cfg = cerrado ? {
                title: '⚠️ Ticket ya cerrado',
                html:  `<p>El ticket <strong>${folio}</strong> ya fue <strong>cerrado</strong>.</p><p style="color:#6c757d;font-size:14px;">Nueva fecha: <em>${fechaStr}</em></p>`,
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-exclamation-triangle"></i> Mover de todas formas',
                cancelButtonText: 'Cancelar', focusCancel: true,
            } : {
                title: '¿Mover ticket?',
                html:  `<p>¿Mover <strong>${folio}</strong> a:</p><p style="color:#6c757d;font-size:15px;">${fechaStr}</p>`,
                icon: 'question', showCancelButton: true,
                confirmButtonColor: '#3b82f6', cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check"></i> Sí, mover',
                cancelButtonText: 'Cancelar',
            };

            Swal.fire(cfg).then(result => {
                if (result.isConfirmed) {
                    fetch('<?= Url::to(['tickets/update-fecha']) ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: info.event.id, start: nuevaFecha.toISOString() })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Ticket movido', showConfirmButton:false, timer:2000 });
                        } else {
                            info.revert();
                            Swal.fire('Error', data.message || 'No se pudo mover', 'error');
                        }
                    })
                    .catch(() => { info.revert(); Swal.fire('Error','Error de conexión','error'); });
                } else {
                    info.revert();
                }
            });
        }
    });

    calendar.render();

    /* doble-click nativo: en celda de día → panel; en evento → ir al ticket */
    calendar.el.addEventListener('dblclick', function (e) {
        const eventEl = e.target.closest('.fc-event');
        if (eventEl) {
            // doble-click sobre un evento → navegar al ticket
            const eventObj = calendar.getEventById(eventEl.closest('[data-event-id]')?.dataset?.eventId)
                          || (() => {
                              // fallback: buscar por el folio en el title
                              const title = eventEl.querySelector('.fc-event-title')?.textContent?.trim();
                              return title ? calendar.getEvents().find(ev => ev.title === title) : null;
                          })();
            if (eventObj) {
                window.location.href = URL_VER_TICKET + '?id=' + eventObj.id;
            }
            return;
        }
        // doble-click en celda vacía → panel del día
        const cell = e.target.closest('[data-date]');
        if (cell) openDayPanel(cell.dataset.date);
    });

    /* polling Monitor */
    if (ES_MONITOR) {
        let lastUpdate = null;
        const pulse    = document.getElementById('monitor-pulse');
        setInterval(function () {
            fetch('<?= Url::to(['site/check-update']) ?>')
                .then(r => r.json())
                .then(data => {
                    if (lastUpdate === null) { lastUpdate = data.lastUpdate; }
                    else if (data.lastUpdate !== lastUpdate) {
                        lastUpdate = data.lastUpdate;
                        calendar.refetchEvents();
                        if (pulse) { pulse.style.background = '#f59e0b'; setTimeout(()=>{pulse.style.background='#22c55e';},600); }
                    }
                })
                .catch(() => { if (pulse) pulse.style.background = '#ef4444'; });
        }, 5000);
    }
});

function filtrarPorConsultor(consultorId) {
    consultorActual = consultorId;
    document.querySelectorAll('.consultor-item').forEach(i => i.classList.remove('active'));
    const el = consultorId ? document.getElementById('consultor-' + consultorId) : document.getElementById('consultor-todos');
    if (el) el.classList.add('active');
    calendar.refetchEvents();
}

window.addEventListener('resize', function () {
    if (calendar) setTimeout(() => calendar.updateSize(), 150);
});
</script>
