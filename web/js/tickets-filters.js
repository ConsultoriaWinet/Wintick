/**
 * TicketsFilters - Manejo avanzado de filtros para la vista de tickets
 * Los filtros se persisten en sessionStorage para sobrevivir navegación entre módulos.
 * El export CSV sigue usando los params de URL ($_GET) — no se toca nada del servidor.
 */

const FILTER_STORAGE_KEY = 'wintick_ticket_filters';
const FILTER_PARAMS = ['mes', 'fecha_inicio', 'fecha_fin', 'Cliente_id', 'Sistema_id', 'Servicio_id', 'Prioridad', 'Estado', 'asignado_a'];

class TicketsFilters {
    constructor() {
        this.filterBtn  = document.getElementById('compactFilterBtn');
        this.filterMenu = document.getElementById('compactFilterMenu');
        this.filterForm = document.getElementById('compactFilterForm');
        this.init();
    }

    init() {
        this.restoreOrRedirect();
        this.setupEventListeners();
    }

    /**
     * Al cargar la página:
     * - Si hay params de filtro en la URL → guardarlos en sessionStorage (estado fresco).
     * - Si NO hay params en la URL → revisar sessionStorage y redirigir con esos params.
     *   Excepción: si llegamos aquí por "Limpiar" (flag en sessionStorage) → no restaurar.
     */
    restoreOrRedirect() {
        const urlParams  = new URLSearchParams(window.location.search);
        const hasFilters = FILTER_PARAMS.some(k => urlParams.has(k) && urlParams.get(k) !== '');

        // Llegamos desde una notificación — no redirigir, dejar que el ticket se abra
        if (urlParams.has('ticket_id') || urlParams.has('openComments')) return;

        if (hasFilters) {
            // Hay filtros activos en URL → persistirlos
            const saved = {};
            FILTER_PARAMS.forEach(k => { if (urlParams.get(k)) saved[k] = urlParams.get(k); });
            sessionStorage.setItem(FILTER_STORAGE_KEY, JSON.stringify(saved));
            return;
        }

        // Sin filtros en URL: verificar si el usuario limpió explícitamente
        if (sessionStorage.getItem(FILTER_STORAGE_KEY + '_cleared') === '1') {
            sessionStorage.removeItem(FILTER_STORAGE_KEY + '_cleared');
            return;
        }

        // Intentar restaurar desde sessionStorage
        const raw = sessionStorage.getItem(FILTER_STORAGE_KEY);
        if (!raw) return;

        try {
            const saved = JSON.parse(raw);
            const params = new URLSearchParams();
            let hasAny = false;
            FILTER_PARAMS.forEach(k => {
                if (saved[k]) { params.set(k, saved[k]); hasAny = true; }
            });
            if (hasAny) {
                window.location.replace(window.location.pathname + '?' + params.toString());
            }
        } catch (e) {
            sessionStorage.removeItem(FILTER_STORAGE_KEY);
        }
    }

    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        // Toggle del dropdown
        if (this.filterBtn) {
            this.filterBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleMenu();
            });
        }

        // Cerrar al hacer click fuera
        document.addEventListener('click', (e) => {
            if (this.filterMenu && !this.filterMenu.contains(e.target) && e.target !== this.filterBtn) {
                this.closeMenu();
            }
        });

        // Cerrar con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.filterMenu) {
                this.closeMenu();
            }
        });

        // Submit: guardar en sessionStorage y dejar que el form navegue normalmente
        if (this.filterForm) {
            this.filterForm.addEventListener('submit', (e) => {
                const fechaInicio = this.filterForm.querySelector('input[name="fecha_inicio"]');
                const fechaFin    = this.filterForm.querySelector('input[name="fecha_fin"]');

                if (fechaInicio?.value && fechaFin?.value && fechaFin.value < fechaInicio.value) {
                    alert('⚠️ La fecha final no puede ser menor que la fecha inicial');
                    e.preventDefault();
                    return false;
                }

                // Guardar filtros actuales en sessionStorage
                const saved = {};
                FILTER_PARAMS.forEach(k => {
                    const el = this.filterForm.querySelector(`[name="${k}"]`);
                    if (el && el.value) saved[k] = el.value;
                });
                if (Object.keys(saved).length > 0) {
                    sessionStorage.setItem(FILTER_STORAGE_KEY, JSON.stringify(saved));
                } else {
                    sessionStorage.removeItem(FILTER_STORAGE_KEY);
                }

                setTimeout(() => this.closeMenu(), 100);
            });
        }

        // Botón "Limpiar": marcar que el usuario limpió explícitamente y borrar storage
        const clearBtn = document.querySelector('.btn-clear-filter');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                sessionStorage.removeItem(FILTER_STORAGE_KEY);
                sessionStorage.setItem(FILTER_STORAGE_KEY + '_cleared', '1');
            });
        }

        // Limpiar mes cuando se ingresa rango de fechas
        const mesInput    = this.filterForm?.querySelector('input[name="mes"]');
        const fechaInicio = this.filterForm?.querySelector('input[name="fecha_inicio"]');
        const fechaFin    = this.filterForm?.querySelector('input[name="fecha_fin"]');

        if (mesInput && (fechaInicio || fechaFin)) {
            [fechaInicio, fechaFin].forEach(input => {
                if (input) {
                    input.addEventListener('change', () => {
                        if (input.value && mesInput.value) mesInput.value = '';
                    });
                }
            });
            mesInput.addEventListener('change', () => {
                if (mesInput.value) {
                    if (fechaInicio) fechaInicio.value = '';
                    if (fechaFin)    fechaFin.value    = '';
                }
            });
        }
    }

    toggleMenu() {
        if (this.filterMenu.style.display === 'none' || !this.filterMenu.style.display) {
            this.openMenu();
        } else {
            this.closeMenu();
        }
    }

    openMenu() {
        if (this.filterMenu) this.filterMenu.style.display = 'block';
    }

    closeMenu() {
        if (this.filterMenu) this.filterMenu.style.display = 'none';
    }
}

// Inicializar cuando DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new TicketsFilters());
} else {
    new TicketsFilters();
}
