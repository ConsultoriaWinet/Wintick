<?php

use yii\db\Migration;

/**
 * Agrega índices a la tabla `tickets`, que es la más consultada del sistema
 * (listado, búsqueda, Cheka, dashboard, recordatorios, estadísticas) y hasta
 * ahora solo tenía el PK y el unique de Folio. Sin estos índices cada filtro
 * provoca un full table scan; con miles de tickets el sistema se vuelve lento.
 */
class m260618_000001_indices_tickets extends Migration
{
    public function safeUp()
    {
        // Filtros / ordenamientos individuales más frecuentes
        $this->createIndex('idx_tickets_asignado',  'tickets', 'Asignado_a');
        $this->createIndex('idx_tickets_estado',    'tickets', 'Estado');
        $this->createIndex('idx_tickets_horainicio', 'tickets', 'HoraInicio');
        $this->createIndex('idx_tickets_fechacrea', 'tickets', 'Fecha_creacion');
        $this->createIndex('idx_tickets_cliente',   'tickets', 'Cliente_id');
        $this->createIndex('idx_tickets_sistema',   'tickets', 'Sistema_id');
        $this->createIndex('idx_tickets_servicio',  'tickets', 'Servicio_id');

        // Índice compuesto: usado por Cheka y recordatorios (tickets de un
        // usuario en un rango de HoraInicio).
        $this->createIndex('idx_tickets_asig_inicio', 'tickets', ['Asignado_a', 'HoraInicio']);
    }

    public function safeDown()
    {
        $this->dropIndex('idx_tickets_asig_inicio', 'tickets');
        $this->dropIndex('idx_tickets_servicio',  'tickets');
        $this->dropIndex('idx_tickets_sistema',   'tickets');
        $this->dropIndex('idx_tickets_cliente',   'tickets');
        $this->dropIndex('idx_tickets_fechacrea', 'tickets');
        $this->dropIndex('idx_tickets_horainicio', 'tickets');
        $this->dropIndex('idx_tickets_estado',    'tickets');
        $this->dropIndex('idx_tickets_asignado',  'tickets');
    }
}
