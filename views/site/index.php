<?php
/** @var yii\web\View $this */
/** @var app\models\Usuarios[] $consultores */

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Dashboard - Tickets por Consultor';
?>

<!-- Cargar todos los plugins necesarios de FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.19/index.global.min.js'></script>

<style>
    .dashboard-container {
        display: flex;
        gap: 24px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    /* SIDEBAR MINIMALISTA */
    .consultores-sidebar {
        width: 240px;
        min-width: 220px;
        padding: 10px 0;
        border-right: 1px solid #e5e5e5;
    }

    .consultores-sidebar h3 {
        font-size: 15px;
        font-weight: 600;
        color: #555;
        margin-bottom: 12px;
        padding-left: 4px;
    }

    .consultor-item {
        padding: 8px 12px;
        margin-bottom: 4px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #333;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .consultor-item:hover {
        background-color: #f5f5f5;
    }

    .consultor-item.active {
        background-color: #e8eefc;
        font-weight: 500;
    }

    .color-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
        border: 2px solid rgba(0,0,0,0.08);
    }

    .color-badge.sin-avatar {
        font-size: 12px;
        font-weight: 700;
        color: white;
        text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }

    /* CONTENEDOR DEL CALENDARIO */
    #calendar-container {
        flex: 1;
        min-width: 300px;
        padding: 10px;
    }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .dashboard-container {
            flex-direction: column;
        }

        .consultores-sidebar {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 12px;
        }
    }

    /* Toggle sidebar en móvil */
    .sidebar-toggle-btn {
        display: none;
        width: 100%;
        padding: 10px 16px;
        background: #f0f4f1;
        border: 1px solid #d1dbd3;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        margin-bottom: 8px;
        text-align: left;
    }
    .sidebar-toggle-btn i { margin-right: 8px; }

    .sidebar-lista { display: flex; flex-direction: column; }

    @media (max-width: 767px) {
        .sidebar-toggle-btn { display: block; }
        .sidebar-lista.collapsed { display: none; }

        /* Calendario más alto en móvil para compensar el sidebar colapsado */
        #calendar-container { min-height: 400px; }
        #calendar { font-size: 12px; }

        /* Reducir padding del dashboard en móvil */
        .dashboard-container { gap: 12px; margin-top: 10px; }

        /* Botones del header del calendario más pequeños en móvil */
        .fc .fc-button { padding: 4px 8px !important; font-size: 12px !important; }
        .fc .fc-toolbar-title { font-size: 16px !important; }
        .fc .fc-toolbar { flex-wrap: wrap; gap: 6px; }
    }
</style>



<div class="site-index">
    <div class="body-content">
        <h1 class="text-center mb-4">Calendario de Tickets</h1>

        <div class="dashboard-container">

            <!-- Sidebar Moderno -->
            <div class="consultores-sidebar">
                <!-- Botón toggle visible solo en móvil -->
                <button class="sidebar-toggle-btn" onclick="toggleSidebar(this)">
                    <i class="fas fa-filter"></i> Filtrar por consultor
                    <i class="fas fa-chevron-down" style="float:right; margin-top:2px;" id="sidebarChevron"></i>
                </button>

                <div class="sidebar-lista collapsed" id="sidebarLista">
                <h3 class="d-none d-md-block">Consultores</h3>

                <!-- Ver todos -->
                <div class="consultor-item active" onclick="filtrarPorConsultor(null)" id="consultor-todos">
                    <span class="color-badge" style="background: #6b7280;">
                        <i class="fas fa-users" style="font-size:12px; color:white;"></i>
                    </span>
                    <span>Todos los consultores</span>
                </div>

                <!-- Lista de consultores -->
                <?php foreach ($consultores as $consultor):
                    $color   = $consultor->color ?? '#6c757d';
                    $avatar  = $consultor->avatar ?? null;
                    $nombre  = $consultor->Nombre ?? $consultor->email;
                    $inicial = mb_strtoupper(mb_substr($nombre, 0, 1, 'UTF-8'), 'UTF-8');
                    // Es foto si empieza con /uploads/
                    $esFoto  = $avatar && str_starts_with($avatar, '/uploads/');
                    $fotoUrl = $esFoto ? Yii::getAlias('@web') . $avatar : null;
                ?>
                    <div class="consultor-item" onclick="filtrarPorConsultor(<?= $consultor->id ?>)"
                        id="consultor-<?= $consultor->id ?>">
                        <?php if ($fotoUrl): ?>
                            <img src="<?= Html::encode($fotoUrl) ?>"
                                 style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:2px solid <?= Html::encode($color) ?>; flex-shrink:0;"
                                 alt="<?= Html::encode($nombre) ?>">
                        <?php else: ?>
                            <span class="color-badge sin-avatar" style="background-color: <?= Html::encode($color) ?>;">
                                <?= Html::encode($inicial) ?>
                            </span>
                        <?php endif; ?>
                        <span><?= Html::encode($nombre) ?></span>
                    </div>
                <?php endforeach; ?>
                </div><!-- /.sidebar-lista -->
            </div>

            <!-- Calendario Moderno -->
            <div id="calendar-container">
                <div id="calendar"></div>
            </div>

        </div>
    </div>
</div>


<script>
    let calendar;
    let consultorActual = null;

    function toggleSidebar(btn) {
        const lista = document.getElementById('sidebarLista');
        const chevron = document.getElementById('sidebarChevron');
        const isCollapsed = lista.classList.toggle('collapsed');
        chevron.style.transform = isCollapsed ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            // Configuración básica
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },

            // Botones personalizados
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día',
                list: 'Lista'
            },

            // Configuración de horario
            slotMinTime: '08:00:00',
            slotMaxTime: '20:00:00',
            allDaySlot: true,

            // Altura y diseño
            height: 'auto',
            expandRows: true,

            // Habilitar arrastrar y redimensionar
            editable: true,
            selectable: true,
            selectMirror: true,
            dayMaxEvents: true,

            // Cargar eventos desde el servidor (tickets)
            events: function (info, successCallback, failureCallback) {
                let url = '<?= Url::to(['site/get-tickets']) ?>';
                if (consultorActual) {
                    url += '?consultorId=' + consultorActual;
                }

            

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                       
                        successCallback(data);
                    })
                    .catch(error => {
                        console.error('Error al cargar tickets:', error);
                        failureCallback(error);
                    });
            },

            // Click en evento
            eventClick: function (info) {
                const props = info.event.extendedProps;
                const fechaInicio = info.event.start ? info.event.start.toLocaleString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }) : 'No definida';

                // ✅ COLOR PARA EL BADGE DE PRIORIDAD
                const prioridadColor = props.prioridad === 'ALTA' ? '#dc3545' :
                    props.prioridad === 'MEDIA' ? '#ffc107' :
                        props.prioridad === 'BAJA' ? '#28a745' : '#6c757d';

                Swal.fire({
                    title: `Ticket ${info.event.title}`,
                    html: `
                    <div style="text-align: left; padding: 20px;">
                        <div style="margin-bottom: 15px;">
                            <strong>📅 Hora de Inicio:</strong><br>
                            <span style="color: #666; font-size: 14px;">${fechaInicio}</span>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <strong>👤 Consultor:</strong><br>
                            <span style="color: #666; font-size: 14px;">${props.consultorNombre}</span>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <strong>🏢 Cliente:</strong><br>
                            <span style="color: #666; font-size: 14px;">${props.cliente}</span>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <strong>⚡ Prioridad:</strong><br>
                            <span class="badge" style="background: ${prioridadColor}; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;">${props.prioridad}</span>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <strong>📋 Estado:</strong><br>
                            <span style="color: #666; font-size: 14px;">${props.estado}</span>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <strong>🔧 Sistema:</strong><br>
                            <span style="color: #666; font-size: 14px;">${props.sistema}</span>
                        </div>

                        <div style="margin-bottom: 15px;">
                            <strong>🛠️ Servicio:</strong><br>
                            <span style="color: #666; font-size: 14px;">${props.servicio}</span>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <strong>📝 Descripción:</strong><br>
                            <span style="color: #666; font-size: 14px; line-height: 1.4;">${props.description}</span>
                        </div>
                    </div>
                `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: info.event.backgroundColor, // ✅ USAR COLOR DEL CONSULTOR
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-eye"></i> Ver detalles',
                    cancelButtonText: 'Cerrar',
                    width: '500px',
                    customClass: {
                        popup: 'ticket-popup'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '<?= Url::to(['tickets/view']) ?>?id=' + info.event.id;
                    }
                });
            },

            // Seleccionar rango de fechas
            select: function (info) {
                Swal.fire({
                    title: '¿Crear nuevo ticket?',
                    text: 'Para la fecha: ' + info.startStr,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, crear',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Crear formulario dinámico para enviar por POST
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '<?= Url::to(['tickets/create']) ?>';

                        // Agregar CSRF token
                        var csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
                        csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
                        form.appendChild(csrfInput);

                        // Agregar fecha seleccionada
                        var fechaInput = document.createElement('input');
                        fechaInput.type = 'hidden';
                        fechaInput.name = 'fecha_seleccionada';
                        fechaInput.value = info.startStr;
                        form.appendChild(fechaInput);

                        // Enviar formulario
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            },

            // Arrastrar evento
            eventDrop: function (info) {
                const nuevaFecha = info.event.start;
                const folio      = info.event.title;
                const estado     = (info.event.extendedProps.estado || '').toUpperCase();
                const cerrado    = estado === 'CERRADO';

                const fechaStr = nuevaFecha.toLocaleString('es-ES', {
                    weekday: 'long',
                    year:    'numeric',
                    month:   'long',
                    day:     'numeric',
                    hour:    '2-digit',
                    minute:  '2-digit'
                });

                const config = cerrado ? {
                    title:              '⚠️ Ticket ya cerrado',
                    html:               `<p>El ticket <strong>${folio}</strong> ya fue <strong>cerrado con solución registrada</strong>.</p>
                                         <p>Moverlo podría generar inconsistencias en los registros de tiempo y fechas.</p>
                                         <p style="color:#6c757d; font-size:14px;">Nueva fecha: <em>${fechaStr}</em></p>`,
                    icon:               'warning',
                    showCancelButton:    true,
                    confirmButtonColor:  '#dc3545',
                    cancelButtonColor:   '#6c757d',
                    confirmButtonText:   '<i class="fas fa-exclamation-triangle"></i> Mover de todas formas',
                    cancelButtonText:    'Cancelar',
                    focusCancel:         true,
                } : {
                    title:              '¿Mover ticket?',
                    html:               `<p>¿Deseas mover el ticket <strong>${folio}</strong> a:</p>
                                         <p style="color:#6c757d; font-size:15px;">${fechaStr}</p>`,
                    icon:               'question',
                    showCancelButton:    true,
                    confirmButtonColor:  '#A0BAA5',
                    cancelButtonColor:   '#6c757d',
                    confirmButtonText:   '<i class="fas fa-check"></i> Sí, mover',
                    cancelButtonText:    'Cancelar',
                };

                Swal.fire(config).then(result => {
                    if (result.isConfirmed) {
                        fetch('<?= Url::to(['tickets/update-fecha']) ?>', {
                            method:  'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body:    JSON.stringify({ id: info.event.id, start: nuevaFecha.toISOString() })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    toast:    true,
                                    position: 'top-end',
                                    icon:     'success',
                                    title:    'Ticket movido correctamente',
                                    showConfirmButton: false,
                                    timer:    2000
                                });
                            } else {
                                info.revert();
                                Swal.fire('Error', data.message || 'No se pudo mover el ticket', 'error');
                            }
                        })
                        .catch(() => {
                            info.revert();
                            Swal.fire('Error', 'Error de conexión al mover el ticket', 'error');
                        });
                    } else {
                        info.revert();
                    }
                });
            }
        });

        calendar.render();
    });

    // Función para filtrar por consultor
    function filtrarPorConsultor(consultorId) {
        consultorActual = consultorId;

        // Remover clase active de todos
        document.querySelectorAll('.consultor-item').forEach(item => {
            item.classList.remove('active');
        });

        // Agregar clase active al seleccionado
        if (consultorId) {
            document.getElementById('consultor-' + consultorId).classList.add('active');
        } else {
            document.getElementById('consultor-todos').classList.add('active');
        }

        // Recargar eventos del calendario
        calendar.refetchEvents();
    }

    window.addEventListener('resize', function () {
        if (calendar) {
            setTimeout(() => {
                calendar.updateSize();
            }, 150);
        }
    });
</script>