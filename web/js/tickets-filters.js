/**
 * TicketsFilters - Manejo avanzado de filtros para la vista de tickets
 */

class TicketsFilters {
    constructor() {
        this.filterBtn = document.getElementById('compactFilterBtn');
        this.filterMenu = document.getElementById('compactFilterMenu');
        this.filterForm = document.getElementById('compactFilterForm');
        this.init();
    }

    init() {
        this.setupEventListeners();
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

        // Manejador de submit: validar y PERMITIR envío
        if (this.filterForm) {
            this.filterForm.addEventListener('submit', (e) => {
                // Validación SOLO de aviso
                const fechaInicio = this.filterForm.querySelector('input[name="fecha_inicio"]');
                const fechaFin = this.filterForm.querySelector('input[name="fecha_fin"]');
                
                if (fechaInicio?.value && fechaFin?.value && fechaFin.value < fechaInicio.value) {
                    alert('⚠️ La fecha final no puede ser menor que la fecha inicial');
                    e.preventDefault();
                    return false;
                }
                
                // PERMITIR que el formulario se envíe
                setTimeout(() => this.closeMenu(), 100);
            });
        }

        // Limpiar mes cuando se ingresa rango de fechas
        const mesInput = this.filterForm?.querySelector('input[name="mes"]');
        const fechaInicio = this.filterForm?.querySelector('input[name="fecha_inicio"]');
        const fechaFin = this.filterForm?.querySelector('input[name="fecha_fin"]');

        if (mesInput && (fechaInicio || fechaFin)) {
            [fechaInicio, fechaFin].forEach(input => {
                if (input) {
                    input.addEventListener('change', () => {
                        if (input.value && mesInput.value) {
                            mesInput.value = '';
                        }
                    });
                }
            });

            mesInput.addEventListener('change', () => {
                if (mesInput.value) {
                    if (fechaInicio) fechaInicio.value = '';
                    if (fechaFin) fechaFin.value = '';
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
        if (this.filterMenu) {
            this.filterMenu.style.display = 'block';
        }
    }

    closeMenu() {
        if (this.filterMenu) {
            this.filterMenu.style.display = 'none';
        }
    }
}

// Inicializar cuando DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new TicketsFilters();
    });
} else {
    new TicketsFilters();
}

