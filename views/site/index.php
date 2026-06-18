<?php
/** @var yii\web\View $this */
/** @var app\models\Usuarios[] $consultores */
/** @var bool $esMonitor */

use yii\helpers\Url;
use yii\helpers\Html;

$esMonitor = $esMonitor ?? false;

$this->title = 'Dashboard - Tickets por Consultor';
$this->params['fullWidth'] = true;

// CSS del dashboard movido a archivo externo para que el navegador lo cachee
$this->registerCssFile('@web/css/site-dashboard.css');
?>

<!-- Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<!-- FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.19/index.global.min.js'></script>


<div class="site-index">

    <br>
    <div class="dashboard-wrap">

        <?php if (!$esMonitor): ?>
            <!-- Sidebar consultores -->
            <div class="consultores-sidebar">
                <button class="sidebar-toggle-btn" onclick="toggleSidebar(this)">
                    <i class="fas fa-filter"></i> Filtrar
                    <i class="fas fa-chevron-down" style="float:right;margin-top:2px;" id="sidebarChevron"></i>
                </button>

                <div class="sidebar-lista collapsed" id="sidebarLista">
                    <h3 class="d-none d-md-block">Consultores</h3>

                    <div class="consultor-item active" onclick="<?= $esMonitor ? 'void(0)' : 'filtrarPorConsultor(null)' ?>"
                        id="consultor-todos">
                        <div class="consultor-av" style="--av-ring:#6b7280;">
                            <span class="av-initials" style="background:#6b7280;">
                                <i class="fas fa-users" style="font-size:10px;"></i>
                            </span>
                        </div>
                        <span>Todos</span>
                    </div>

                    <?php foreach ($consultores as $consultor):
                        $color = $consultor->color ?? '#6c757d';
                        $avatar = $consultor->avatar ?? null;
                        $nombre = $consultor->Nombre ?? $consultor->email;
                        $inicial = mb_strtoupper(mb_substr($nombre, 0, 1, 'UTF-8'), 'UTF-8');
                        $esFoto = $avatar && str_starts_with($avatar, '/uploads/');
                        $fotoUrl = $esFoto ? Yii::getAlias('@web') . $avatar : null;
                        ?>
                        <div class="consultor-item"
                            onclick="<?= $esMonitor ? 'void(0)' : 'filtrarPorConsultor(' . $consultor->id . ')' ?>"
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
        <?php endif; ?>

        <!-- Área calendario -->
        <div id="cal-area">
            <div id="cal-topbar">
                <h1>Calendario de Tickets</h1>
                <span class="hint"><i class="fas fa-hand-pointer"></i> Doble clic en un día · clic en ticket para ver
                    resumen</span>
                <button id="cheka-cal-toggle" onclick="toggleChekaView()" title="Vista por consultor día a día">
                    <i class="fas fa-stream"></i> Cheka
                </button>
            </div>
            <div id="calendar-container">
                <div id="calendar"></div>
            </div>

            <!-- ── VISTA CHEKA ───────────────────────────────────── -->
            <div id="cheka-view">
                <!-- Barra de navegación Cheka -->

                <!-- Filtro de rango de fechas -->
                <div class="stats-date-filter">
                    <div class="stats-date-wrap">
                        <i class="fas fa-calendar-alt stats-date-icon"></i>
                        <input type="text" id="stats-range-picker" class="stats-date-input" placeholder="Seleccionar rango de fechas…" readonly>
                        <button class="stats-date-clear" id="stats-range-clear" title="Limpiar — volver al mes actual">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <span class="stats-date-hint" id="stats-range-hint">Mostrando hoy</span>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary card-total">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1"><i class="fas fa-ticket-alt"></i> Total Tickets</h6>
                                        <h2 class="mb-0" id="totalTickets">
                                            <?= $estadisticasTickets['total'] ?>
                                        </h2>
                                        <small><i class="fas fa-calendar-day"></i> Tickets del día mostrado</small>
                                    </div>
                                    <div class="icon-large">
                                        <i class="fas fa-ticket-alt fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info card-abiertos">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1"><i class="fas fa-folder-open"></i> Abiertos</h6>
                                        <h2 class="mb-0" id="abiertosTickets">
                                            <?= $estadisticasTickets['abiertos'] ?>
                                        </h2>
                                        <small id="pct-abiertos">
                                            <?= $estadisticasTickets['total'] > 0 ? round(($estadisticasTickets['abiertos'] / $estadisticasTickets['total']) * 100, 1) : 0 ?>%
                                            del total
                                        </small>
                                    </div>
                                    <div class="icon-large">
                                        <i class="fas fa-folder-open fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning card-proceso">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1"><i class="fas fa-spinner"></i> En Proceso</h6>
                                        <h2 class="mb-0" id="enProcesoTickets">
                                            <?= $estadisticasTickets['enProceso'] ?>
                                        </h2>
                                        <small id="pct-enproceso">
                                            <?= $estadisticasTickets['total'] > 0 ? round(($estadisticasTickets['enProceso'] / $estadisticasTickets['total']) * 100, 1) : 0 ?>%
                                            del total
                                        </small>
                                    </div>
                                    <div class="icon-large">
                                        <i class="fas fa-spinner fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success card-cerrados">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1"><i class="fas fa-check-circle"></i> Cerrados</h6>
                                        <h2 class="mb-0" id="cerradosTickets">
                                            <?= $estadisticasTickets['cerrados'] ?>
                                        </h2>
                                        <small id="pct-cerrados">Tasa:
                                            <?= $tasaResolucion['tasa'] ?>%
                                        </small>
                                    </div>
                                    <div class="icon-large">
                                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cheka-topbar">
                    <button class="cheka-nav-btn" onclick="chekaNavDay(-1)" title="Día anterior">‹</button>
                    <button class="cheka-nav-btn" onclick="chekaNavDay(1)" title="Día siguiente">›</button>
                    <button class="cheka-hoy-btn" onclick="chekaGoToday()">Hoy</button>
                    <span class="cheka-date-label" id="cheka-date-label">—</span>
                    <div style="display:flex;align-items:center;gap:10px;">

                        <div class="dropdown">

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <?= Html::a(
                                        '<i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión',
                                        ['/site/logout'],
                                        [
                                            'class' => 'dropdown-item text-danger',
                                            'data-method' => 'post'
                                        ]
                                    ) ?>
                                </li>
                            </ul>
                        </div>

                        <div class="cheka-now-pill" id="cheka-now-pill">
                            <span class="cheka-now-dot"></span>
                            <span id="cheka-now-time">—</span>
                        </div>
                        <button class="btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                            style="
                width:42px;
                height:42px;
                border-radius:50%;
                border:1px solid #e5e7eb;
            ">
                            <i class="fas fa-cog"></i>
                        </button>

                    </div>
                </div>
                <!-- Grid container -->
                <div id="cheka-scroll">
                    <div class="cheka-grid" id="cheka-grid">
                        <div class="cheka-left-header">Técnico</div>
                        <div class="cheka-time-header" id="cheka-time-header"></div>
                        <div id="cheka-rows-left"></div>
                        <div id="cheka-rows-right" style="position:relative;"></div>
                    </div>
                </div>
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
    const ES_MONITOR = <?= $esMonitor ? 'true' : 'false' ?>;
    const URL_TICKETS_DIA = '<?= Url::to(['site/get-tickets-dia']) ?>';
    const URL_VER_TICKET = '<?= Url::to(['tickets/view']) ?>';
    const URL_CHEKA = '<?= Url::to(['site/get-cheka']) ?>';

    let calendar;
    let consultorActual = null;
    let dayPanelOpen = false;

    /* ── abrir panel ── */
    function openDayPanel(dateStr) {
        const panel = document.getElementById('day-panel');
        const body = document.getElementById('day-panel-body');

        // Actualizar cabecera
        const d = new Date(dateStr + 'T12:00:00');
        const day = d.getDate();
        const mon = d.toLocaleDateString('es-MX', { month: 'short' }).replace('.', '').toUpperCase();
        const full = d.toLocaleDateString('es-MX', { weekday: 'long', day: 'numeric', month: 'long' });
        document.getElementById('dp-day-num').textContent = day;
        document.getElementById('dp-mon-str').textContent = mon;
        document.getElementById('dp-title').textContent = full.charAt(0).toUpperCase() + full.slice(1);
        document.getElementById('dp-count').textContent = 'Cargando…';

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
        const body = document.getElementById('day-panel-body');
        const props = event.extendedProps;

        const d = event.start;
        const day = d.getDate();
        const mon = d.toLocaleDateString('es-MX', { month: 'short' }).replace('.', '').toUpperCase();
        const hora = d.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
        const full = d.toLocaleDateString('es-MX', { weekday: 'long', day: 'numeric', month: 'long' });

        document.getElementById('dp-day-num').textContent = day;
        document.getElementById('dp-mon-str').textContent = mon;
        document.getElementById('dp-title').textContent = full.charAt(0).toUpperCase() + full.slice(1);
        document.getElementById('dp-count').textContent = hora;

        const estadoClass = dpEstadoClass(props.estado);
        const estadoLabel = dpEstadoLabel(props.estado);
        const prioClass = dpPrioClass(props.prioridad);

        body.innerHTML = `
        <a class="dp-ticket" href="${URL_VER_TICKET}?id=${event.id}" style="border-left:3px solid ${esc(event.backgroundColor || '#3b82f6')};">
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
        const cnt = document.getElementById('dp-count');

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
            const prioClass = dpPrioClass(t.prioridad);

            const avHtml = t.asignado.avatar
                ? `<img src="${esc(t.asignado.avatar)}" class="dp-av" style="object-fit:cover;padding:0;">`
                : `<span class="dp-av" style="background:${esc(t.asignado.color)};">${esc(t.asignado.ini)}</span>`;

            const card = document.createElement('a');
            card.className = 'dp-ticket';
            card.href = URL_VER_TICKET + '?id=' + t.id;
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
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function dpEstadoClass(e) {
        const m = {
            'ABIERTO': 'dp-e-abierto', 'PROGRAMADO': 'dp-e-programado', 'EN PROCESO': 'dp-e-proceso',
            'EN ESPERA': 'dp-e-espera', 'CONTPAQi': 'dp-e-contpaqi', 'CERRADO': 'dp-e-cerrado', 'CANCELADO': 'dp-e-cancelado'
        };
        return m[e] || 'dp-e-cancelado';
    }
    function dpEstadoLabel(e) {
        const m = {
            'ABIERTO': 'Abierto', 'PROGRAMADO': 'Programado', 'EN PROCESO': 'En proceso',
            'EN ESPERA': 'En espera', 'CONTPAQi': 'CONTPAQi', 'CERRADO': 'Cerrado', 'CANCELADO': 'Cancelado'
        };
        return m[e] || e;
    }
    function dpPrioClass(p) {
        return p === 'ALTA' ? 'dp-prio-alta' : p === 'MEDIA' ? 'dp-prio-media' : p === 'BAJA' ? 'dp-prio-baja' : 'dp-prio-def';
    }

    /* ── init calendario ── */
    function toggleSidebar(btn) {
        const lista = document.getElementById('sidebarLista');
        const chevron = document.getElementById('sidebarChevron');
        const col = lista.classList.toggle('collapsed');
        chevron.style.transform = col ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día', list: 'Lista' },

            slotMinTime: '08:00:00',
            slotMaxTime: '20:00:00',
            allDaySlot: true,
            height: '100%',
            expandRows: true,
            dayMaxEvents: false,

            editable: !ES_MONITOR,
            selectable: !ES_MONITOR,
            selectMirror: !ES_MONITOR,

            events: function (info, successCallback, failureCallback) {
                let url = '<?= Url::to(['site/get-tickets']) ?>';
                if (consultorActual) url += '?consultorId=' + consultorActual;
                fetch(url)
                    .then(r => r.json())
                    .then(data => successCallback(data))
                    .catch(err => failureCallback(err));
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
                    text: 'Para la fecha: ' + info.startStr,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, crear',
                    cancelButtonText: 'Cancelar'
                }).then(result => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '<?= Url::to(['tickets/create']) ?>';
                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '<?= Yii::$app->request->csrfParam ?>';
                        csrf.value = '<?= Yii::$app->request->csrfToken ?>';
                        form.appendChild(csrf);
                        const fi = document.createElement('input');
                        fi.type = 'hidden';
                        fi.name = 'fecha_seleccionada';
                        fi.value = info.startStr;
                        form.appendChild(fi);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            },

            /* drag */
            eventDrop: function (info) {
                const nuevaFecha = info.event.start;
                const folio = info.event.title;
                const cerrado = (info.event.extendedProps.estado || '').toUpperCase() === 'CERRADO';
                const fechaStr = nuevaFecha.toLocaleString('es-MX', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });

                const cfg = cerrado ? {
                    title: '⚠️ Ticket ya cerrado',
                    html: `<p>El ticket <strong>${folio}</strong> ya fue <strong>cerrado</strong>.</p><p style="color:#6c757d;font-size:14px;">Nueva fecha: <em>${fechaStr}</em></p>`,
                    icon: 'warning', showCancelButton: true,
                    confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-exclamation-triangle"></i> Mover de todas formas',
                    cancelButtonText: 'Cancelar', focusCancel: true,
                } : {
                    title: '¿Mover ticket?',
                    html: `<p>¿Mover <strong>${folio}</strong> a:</p><p style="color:#6c757d;font-size:15px;">${fechaStr}</p>`,
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
                                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Ticket movido', showConfirmButton: false, timer: 2000 });
                                } else {
                                    info.revert();
                                    Swal.fire('Error', data.message || 'No se pudo mover', 'error');
                                }
                            })
                            .catch(() => { info.revert(); Swal.fire('Error', 'Error de conexión', 'error'); });
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
            const pulse = document.getElementById('monitor-pulse');
            setInterval(function () {
                fetch('<?= Url::to(['site/check-update']) ?>')
                    .then(r => r.json())
                    .then(data => {
                        if (lastUpdate === null) { lastUpdate = data.lastUpdate; }
                        else if (data.lastUpdate !== lastUpdate) {
                            lastUpdate = data.lastUpdate;
                            calendar.refetchEvents();
                            if (statsCustom) actualizarDashboard(statsDesde, statsHasta);
                            if (pulse) { pulse.style.background = '#f59e0b'; setTimeout(() => { pulse.style.background = '#22c55e'; }, 600); }
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

<!-- ═══════════════ CHEKA VIEW JS ═══════════════ -->
<script>
    /* ── Configuración ── */
    const CHEKA_START_H = 8;   // hora inicio timeline
    const CHEKA_END_H = 18;  // hora fin timeline (19:00 = última columna visible; cubre hasta las 19:59)
    const CHEKA_PX_MIN = 2.7; // píxeles por minuto
    const CHEKA_ROW_H = 70;  // altura fila px

    let chekaActive = false;
    let chekaDate = new Date();
    let chekaNowInterval = null;
    let chekaTickInterval = null;   // polling de cambios en tickets
    let chekaLastStamp = null;   // último Fecha_actualizacion conocido

    /* ── Colores de estado ── */
    function chekaEstadoClass(e) {
        const m = { 'ABIERTO': 'chk-abierto', 'PROGRAMADO': 'chk-programado', 'EN PROCESO': 'chk-proceso', 'CONTPAQi': 'chk-contpaqi', 'CERRADO': 'chk-cerrado' };
        return m[(e || '').toUpperCase()] || 'chk-default';
    }

    /* ── Alternar vista ── */
    function toggleChekaView() {
        chekaActive = !chekaActive;
        const btn = document.getElementById('cheka-cal-toggle');
        const calArea = document.getElementById('cal-area');
        const hint = document.querySelector('#cal-topbar .hint');

        btn.classList.toggle('active', chekaActive);
        calArea.classList.toggle('cheka-on', chekaActive);

        if (chekaActive) {
            if (hint) hint.style.display = 'none';
            if (calendar) chekaDate = calendar.getDate();
            chekaLoad(chekaDate);
            startNowClock();
            initStatsPicker();
        } else {
            if (hint) hint.style.display = '';
            stopNowClock();
        }
    }

    /* ── Navegación ── */
    function chekaNavDay(delta) {
        const d = new Date(chekaDate);
        d.setDate(d.getDate() + delta);
        chekaDate = d;
        chekaLoad(d);
    }
    function chekaGoToday() {
        chekaDate = new Date();
        chekaLoad(chekaDate);
    }

    /* ── Reloj en tiempo real ── */
    function startNowClock() {
        updateNowPill();
        chekaNowInterval = setInterval(() => {
            updateNowPill();
        }, 1000);

        setInterval(() => {
            updateNowLine();
        }, 60000);

        // Polling de cambios: si se crea/edita un ticket recarga el día en Cheka
        chekaLastStamp = null;
        chekaTickInterval = setInterval(() => {
            fetch('<?= Url::to(['site/check-update']) ?>')
                .then(r => r.json())
                .then(data => {
                    if (chekaLastStamp === null) {
                        chekaLastStamp = data.lastUpdate;
                    } else if (data.lastUpdate !== chekaLastStamp) {
                        chekaLastStamp = data.lastUpdate;
                        // chekaRender actualiza las cards con los mismos datos del timeline
                        chekaLoad(chekaDate);
                        // Solo si hay rango personalizado se consulta el endpoint de stats
                        if (statsCustom) actualizarDashboard(statsDesde, statsHasta);
                    }
                })
                .catch(() => {});
        }, 5000);
    }
    function stopNowClock() {
        if (chekaNowInterval) clearInterval(chekaNowInterval);
        if (chekaTickInterval) clearInterval(chekaTickInterval);
    }
    function updateNowPill() {
        const now = new Date();

        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');

        document.getElementById('cheka-now-time').textContent =
            h + ':' + m + ':' + s;
    }
    function updateNowLine() {
        const line = document.getElementById('cheka-now-line');
        if (!line) return;
        const now = new Date();
        const todayStr = now.toISOString().slice(0, 10);
        const chkStr = chekaDate.toISOString().slice(0, 10);
        if (todayStr !== chkStr) { line.style.display = 'none'; return; }
        line.style.display = '';
        const minFromStart = (now.getHours() - CHEKA_START_H) * 60 + now.getMinutes();
        line.style.left = (minFromStart * CHEKA_PX_MIN) + 'px';
    }

    /* ── Cargar datos ── */
    function chekaLoad(date) {
        const dateStr = date.toISOString().slice(0, 10);

        // Cabecera fecha
        const dias = <?= $esMonitor
            ? "['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']"
            : "['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb']"
            ?>;

        const meses = <?= $esMonitor
            ? "['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']"
            : "['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']"
            ?>;

        const dLabel = dias[date.getDay()] + ', ' +
            String(date.getDate()).padStart(2, '0') + ' de ' +
            meses[date.getMonth()] + ' de ' +
            date.getFullYear();
        document.getElementById('cheka-date-label').textContent = dLabel;

        // Loading
        document.getElementById('cheka-rows-left').innerHTML =
            '<div style="padding:30px 16px;color:var(--text-3);font-size:12px;"><i class="fas fa-circle-notch fa-spin"></i> Cargando…</div>';
        document.getElementById('cheka-rows-right').innerHTML = '';
        buildTimeHeader();

        fetch(URL_CHEKA + '?fecha=' + dateStr)
            .then(r => r.json())
            .then(data => chekaRender(data))
            .catch(() => {
                document.getElementById('cheka-rows-left').innerHTML =
                    '<div style="padding:30px 16px;color:#ef4444;font-size:12px;"><i class="fas fa-exclamation-circle"></i> Error al cargar</div>';
            });
    }

    /* ── Construir cabecera de horas ── */
    function buildTimeHeader() {
        const hdr = document.getElementById('cheka-time-header');
        hdr.innerHTML = '';
        const totalMin = (CHEKA_END_H - CHEKA_START_H) * 60;
        const totalPx = totalMin * CHEKA_PX_MIN;
        hdr.style.width = totalPx + 'px';

        for (let h = CHEKA_START_H; h <= CHEKA_END_H; h++) {
            const lbl = document.createElement('div');
            lbl.className = 'cheka-hour-label';
            lbl.style.width = (60 * CHEKA_PX_MIN) + 'px';
            lbl.textContent = String(h).padStart(2, '0') + ':00';
            hdr.appendChild(lbl);
        }
    }

    /* ── Renderizar filas ── */
    /* ── Asignación de lanes (sub-filas) para tickets solapados ── */
    function chekaAssignLanes(tickets) {
        // tickets ya ordenados por horaMin (PHP los ordena por HoraInicio)
        const laneEnds = []; // laneEnds[i] = minuto en que termina el último ticket del lane i
        tickets.forEach(t => {
            const start = t.horaMin;
            const end = start + Math.max(30, t.durMin || 60);
            let lane = laneEnds.findIndex(e => e <= start);
            if (lane === -1) lane = laneEnds.length;
            laneEnds[lane] = end;
            t._lane = lane;
        });
        return laneEnds.length; // total de lanes necesarios
    }

    /* ── Cards calculadas desde los MISMOS datos del timeline ── */
    function updateCardsFromCheka(data) {
        let total = 0, abiertos = 0, enProceso = 0, cerrados = 0;
        (data.usuarios || []).forEach(u => {
            (u.tickets || []).forEach(t => {
                total++;
                const e = (t.estado || '').toUpperCase();
                if (e === 'ABIERTO') abiertos++;
                else if (e === 'EN PROCESO') enProceso++;
                else if (e === 'CERRADO') cerrados++;
            });
        });

        document.getElementById('totalTickets').textContent     = total;
        document.getElementById('abiertosTickets').textContent  = abiertos;
        document.getElementById('enProcesoTickets').textContent = enProceso;
        document.getElementById('cerradosTickets').textContent  = cerrados;

        const pct = n => total > 0 ? Math.round((n / total) * 1000) / 10 : 0;
        const elPctAb = document.getElementById('pct-abiertos');
        const elPctEn = document.getElementById('pct-enproceso');
        const elPctCe = document.getElementById('pct-cerrados');
        if (elPctAb) elPctAb.textContent = pct(abiertos) + '% del total';
        if (elPctEn) elPctEn.textContent = pct(enProceso) + '% del total';
        if (elPctCe) elPctCe.textContent = 'Tasa: ' + pct(cerrados) + '%';
    }

    function chekaRender(data) {
        // Si no hay rango personalizado activo, las cards reflejan el día del timeline
        if (!statsCustom) updateCardsFromCheka(data);

        const leftEl = document.getElementById('cheka-rows-left');
        const rightEl = document.getElementById('cheka-rows-right');
        leftEl.innerHTML = '';
        rightEl.innerHTML = '';

        const totalMin = (CHEKA_END_H - CHEKA_START_H) * 60;
        const totalPx = totalMin * CHEKA_PX_MIN;
        rightEl.style.width = totalPx + 'px';

        if (!data.usuarios || !data.usuarios.length) {
            leftEl.innerHTML = '<div style="padding:30px 16px;color:var(--text-3);font-size:12.5px;text-align:center;"><i class="fas fa-calendar-day" style="font-size:22px;display:block;margin-bottom:8px;opacity:.3;"></i>Sin tickets programados</div>';
            return;
        }

        const LANE_H = 20; // alto de cada bloque (una sola línea)
        const LANE_GAP = 3;  // espacio entre lanes
        const LANE_PAD = 5;  // padding arriba/abajo de la fila

        data.usuarios.forEach((u, idx) => {
            // ── Asignar lanes a tickets solapados ──
            const numLanes = chekaAssignLanes(u.tickets);
            const rowH = Math.max(CHEKA_ROW_H, LANE_PAD * 2 + numLanes * (LANE_H + LANE_GAP) - LANE_GAP);

            // ─ Celda izquierda ─
            const left = document.createElement('div');
            left.className = 'cheka-user-cell';
            left.style.minHeight = rowH + 'px';
            const inits = (u.nombre || '?').split(/\s+/).slice(0, 2).map(w => w[0]).join('').toUpperCase();
            const avatarHtml = u.avatar
                ? `<img src="${u.avatar}" class="cheka-av-inner" style="object-fit:cover;" alt="${esc(u.nombre)}">`
                : `<div class="cheka-av-inner" style="background:${u.color};">${esc(inits)}</div>`;

            left.innerHTML = `
            <div class="cheka-av" style="--av-clr:${u.color};">${avatarHtml}</div>
            <div class="cheka-user-info">
                <div class="cheka-user-name">${esc(u.nombre)}</div>
                <div class="cheka-user-meta"><i class="fas fa-ticket-alt" style="opacity:.5;font-size:9px;"></i> ${u.tickets.length} ticket${u.tickets.length !== 1 ? 's' : ''}</div>
            </div>`;
            leftEl.appendChild(left);

            // ─ Fila timeline ─
            const row = document.createElement('div');
            row.className = 'cheka-row-timeline';
            row.style.height = rowH + 'px';
            row.style.width = totalPx + 'px';

            // Franjas de fondo (rayado por hora)
            const bg = document.createElement('div');
            bg.className = 'cheka-row-bg-hours';
            bg.style.width = totalPx + 'px';
            for (let h = CHEKA_START_H; h < CHEKA_END_H; h++) {
                const seg = document.createElement('div');
                seg.className = 'cheka-row-hour-seg';
                seg.style.width = (60 * CHEKA_PX_MIN) + 'px';
                bg.appendChild(seg);
            }
            row.appendChild(bg);

            // Tickets
            u.tickets.forEach(t => {
                const leftPx = (t.horaMin - CHEKA_START_H * 60) * CHEKA_PX_MIN;
                // Ancho visual: usar duración real pero con un máximo razonable (2h = 264px)
                // y un mínimo de 56px para que quepa el folio
                const durVisPx = Math.min(t.durMin * CHEKA_PX_MIN, 60 * 2 * CHEKA_PX_MIN);
                const widPx = Math.max(56, durVisPx);
                if (leftPx < 0 || leftPx > totalPx) return; // fuera de rango

                const topPx = LANE_PAD + (t._lane || 0) * (LANE_H + LANE_GAP);

                const blk = document.createElement('div');
                blk.className = 'cheka-ticket-block ' + chekaEstadoClass(t.estado);
                blk.style.left = leftPx + 'px';
                blk.style.width = Math.min(widPx, totalPx - leftPx) + 'px';
                blk.style.top = topPx + 'px';
                blk.style.height = LANE_H + 'px';
                blk.title = t.folio + ' · ' + t.cliente + ' · ' + t.hora;
                blk.onclick = () => {
                    const fakeDate = new Date(chekaDate);
                    const [hh, mm] = t.hora.split(':').map(Number);
                    fakeDate.setHours(hh, mm, 0, 0);
                    openTicketPanel({
                        id: t.id,
                        start: fakeDate,
                        title: t.folio,
                        backgroundColor: null,
                        extendedProps: {
                            estado: t.estado,
                            prioridad: t.prioridad,
                            description: t.titulo || '',
                            cliente: t.cliente || '-',
                            sistema: t.sistema || '-',
                            servicio: t.servicio || '-',
                            consultorNombre: u.nombre || '-'
                        }
                    });
                };
                /* Una sola línea: HH:mm · FOLIO  nombre cliente (se recorta si no cabe) */
                blk.innerHTML = `
                <span class="cheka-blk-time">${esc(t.hora)}</span>
                <span class="cheka-blk-sep">·</span>
                <span class="cheka-blk-folio">${esc(t.folio)}</span>
                <span class="cheka-blk-client">${esc(t.cliente)}</span>`;
                row.appendChild(blk);
            });

            rightEl.appendChild(row);
        });

        // Línea "ahora"
        let nowLine = document.getElementById('cheka-now-line');
        if (!nowLine) {
            nowLine = document.createElement('div');
            nowLine.id = 'cheka-now-line';
            rightEl.appendChild(nowLine);
        } else {
            rightEl.appendChild(nowLine);
        }
        updateNowLine();
    }

    function esc(s) {
        const d = document.createElement('div');
        d.textContent = s || '';
        return d.innerHTML;
    }

    /* ── Rango de fechas para las cards ──
       Por defecto (statsCustom=false) las cards siguen el día del timeline,
       calculadas desde los mismos datos de get-cheka en chekaRender().
       Solo al elegir un rango en el picker (statsCustom=true) se consulta
       get-dashboard-stats con ese rango. */
    let statsDesde = null;
    let statsHasta = null;
    let statsCustom = false;
    let statsPickerInited = false;

    function initStatsPicker() {
        if (statsPickerInited) return;
        statsPickerInited = true;

        flatpickr('#stats-range-picker', {
            mode: 'range',
            locale: 'es',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            disableMobile: true,
            appendTo: document.body,        // evita que el popup quede oculto dentro del div
            onChange: function (selectedDates) {
                if (selectedDates.length === 2) {
                    const fmt = d => d.toISOString().slice(0, 10);
                    statsDesde = fmt(selectedDates[0]);
                    statsHasta = fmt(selectedDates[1]);
                    statsCustom = true;
                    actualizarDashboard(statsDesde, statsHasta);
                    document.getElementById('stats-range-clear').classList.add('visible');
                    const d0 = selectedDates[0].toLocaleDateString('es-MX', { day: '2-digit', month: 'short' });
                    const d1 = selectedDates[1].toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' });
                    document.getElementById('stats-range-hint').textContent = 'Del ' + d0 + ' al ' + d1;
                }
            },
        });

        document.getElementById('stats-range-clear').addEventListener('click', function () {
            statsCustom = false;
            statsDesde = null;
            statsHasta = null;
            const fp = document.getElementById('stats-range-picker')._flatpickr;
            fp.clear();
            this.classList.remove('visible');
            document.getElementById('stats-range-hint').textContent = 'Mostrando el día del timeline';
            chekaLoad(chekaDate); // recargar: chekaRender actualiza las cards
        });
    }

    /* desde/hasta = 'YYYY-MM-DD' */
    function actualizarDashboard(desde, hasta) {
        let url = '<?= Url::to(['site/get-dashboard-stats']) ?>';
        if (desde && hasta) url += '?desde=' + desde + '&hasta=' + hasta;

        fetch(url)
            .then(r => r.json())
            .then(data => {
                document.getElementById('totalTickets').textContent    = data.total;
                document.getElementById('abiertosTickets').textContent = data.abiertos;
                document.getElementById('enProcesoTickets').textContent = data.enProceso;
                document.getElementById('cerradosTickets').textContent = data.cerrados;

                // Porcentajes de las cards secundarias
                const total = data.total || 0;
                const pctAb = total > 0 ? Math.round((data.abiertos  / total) * 100 * 10) / 10 : 0;
                const pctEn = total > 0 ? Math.round((data.enProceso / total) * 100 * 10) / 10 : 0;
                const pctCe = total > 0 ? Math.round((data.cerrados  / total) * 100 * 10) / 10 : 0;
                const elPctAb = document.getElementById('pct-abiertos');
                const elPctEn = document.getElementById('pct-enproceso');
                const elPctCe = document.getElementById('pct-cerrados');
                if (elPctAb) elPctAb.textContent = pctAb + '% del total';
                if (elPctEn) elPctEn.textContent = pctEn + '% del total';
                if (elPctCe) elPctCe.textContent = 'Tasa: ' + pctCe + '%';
            })
            .catch(() => {});
    }
</script>

<?php if ($esMonitor): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            setTimeout(function () {

                if (!chekaActive) {
                    toggleChekaView();
                }

                const btn = document.getElementById('cheka-cal-toggle');
                if (btn) {
                    btn.style.display = 'none';
                }

            }, 300);

        });
    </script>
    <style>
        #cal-topbar h1 {
            display: none !important;
        }

        /* Oculta ayuda */
        #cal-topbar .hint {
            display: none !important;
        }

        /* Oculta flechas */
        .cheka-nav-btn {
            display: none !important;
        }

        /* Oculta botón Hoy */
        .cheka-hoy-btn {
            display: none !important;
        }

        #mainHeader {
            display: none !important;
        }

        #cheka-cal-toggle {
            display: none !important;
        }

        .fc-header-toolbar {
            display: none !important;
        }

        /* ── Colores intensos SOLO para el Monitor ── */
        /* Cards */
        .card-total {
            background: linear-gradient(135deg, #b0b7bf, #8a929b) !important;
        }

        .card-abiertos {
            background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        }

        .card-proceso {
            background: linear-gradient(135deg, #fbbf24, #d97706) !important;
        }

        .card-cerrados {
            background: linear-gradient(135deg, #1e40af, #1e3a8a) !important;
        }

        /* Bloques del timeline: sólidos e intensos */
        .chk-abierto {
            background: #16a34a !important;
            color: #fff !important;
        }

        .chk-programado {
            background: #0d9488 !important;
            color: #fff !important;
        }

        .chk-proceso {
            background: #f59e0b !important;
            color: #fff !important;
        }

        .chk-contpaqi {
            background: #b45309 !important;
            color: #fff !important;
        }

        .chk-cerrado {
            background: #1e3a8a !important;
            color: #fff !important;
        }

        .chk-default {
            background: #4338ca !important;
            color: #fff !important;
        }

        .cheka-ticket-block .cheka-blk-time,
        .cheka-ticket-block .cheka-blk-client {
            opacity: .9 !important;
        }

        /* Panel del día: badges intensos */
        .dp-e-abierto {
            background: #16a34a !important;
            color: #fff !important;
        }

        .dp-e-abierto::before {
            background: #fff !important;
        }

        .dp-e-proceso {
            background: #f59e0b !important;
            color: #fff !important;
        }

        .dp-e-proceso::before {
            background: #fff !important;
        }

        .dp-e-cerrado {
            background: #1e3a8a !important;
            color: #fff !important;
        }

        .dp-e-cerrado::before {
            background: #fff !important;
        }

        .dp-e-programado {
            background: #0d9488 !important;
            color: #fff !important;
        }

        .dp-e-programado::before {
            background: #fff !important;
        }
    </style>
<?php endif; ?>