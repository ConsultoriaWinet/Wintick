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
    .container-fluid {
        display: flex;
        gap: 20px;
        margin-top: 20px;
    }
    
    .consultores-sidebar {
        width: 250px;
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        height: fit-content;
        position: sticky;
        top: 80px;
    }
    
    .consultor-item {
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .consultor-item:hover {
        transform: translateX(5px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .consultor-item.active {
        font-weight: bold;
        box-shadow: 0 3px 8px rgba(0,0,0,0.2);
    }
    
    .color-badge {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }
    
    #calendar {
        flex: 1;
        max-width: 1100px;
        padding: 0 10px;
    }
    
    .fc-event {
        cursor: pointer;
        border-radius: 4px;
    }
    
    .fc-event-title {
        font-weight: bold;
    }
    
    h3 {
        margin-top: 0;
        margin-bottom: 20px;
        color: #333;
    }
</style>

<div class="site-index">
    <div class="body-content">
        <h1 class="text-center">Calendario de Tickets</h1>
        
        <div class="container-fluid">
            <!-- Sidebar de consultores -->
            <div class="consultores-sidebar">
                <h3>Consultores</h3>
                
                <!-- Opción para ver todos -->
                <div class="consultor-item active" 
                     onclick="filtrarPorConsultor(null)"
                     id="consultor-todos"
                     style="background-color: #e9ecef;">
                    <span class="color-badge" style="background: gray;"></span>
                    <span>Todos los consultores</span>
                </div>
                
                <!-- Lista de consultores -->
                <?php foreach ($consultores as $consultor): ?>
                    <div class="consultor-item" 
                         onclick="filtrarPorConsultor(<?= $consultor->id ?>)"
                         id="consultor-<?= $consultor->id ?>"
                         style="background-color: <?= $consultor->color ?? '#6c757d' ?>20;">
                        <span class="color-badge" style="background-color: <?= $consultor->color ?? '#6c757d' ?>;"></span>
                        <span><?= Html::encode($consultor->Nombre ?? $consultor->email ?? 'Consultor #' . $consultor->id) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Calendario -->
            <div id='calendar'></div>
        </div>
    </div>
</div>

<script>
let calendar;
let consultorActual = null;

document.addEventListener('DOMContentLoaded', function() {
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
        events: function(info, successCallback, failureCallback) {
            let url = '<?= Url::to(['site/get-tickets']) ?>';
            if (consultorActual) {
                url += '?consultorId=' + consultorActual;
            }
            
            console.log('Cargando tickets desde:', url);
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    console.log('Tickets recibidos:', data);
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Error al cargar tickets:', error);
                    failureCallback(error);
                });
        },
        
        // Click en evento
        eventClick: function(info) {
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
        select: function(info) {
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
        eventDrop: function(info) {
            console.log('Evento movido: ' + info.event.id + ' a ' + info.event.start);
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
</script>