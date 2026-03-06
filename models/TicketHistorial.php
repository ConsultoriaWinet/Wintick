<?php

namespace app\models;

use Yii;

/**
 * Modelo para la tabla ticket_historial.
 * Registra cada cambio de campo relevante en un ticket.
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $usuario_id
 * @property string $campo
 * @property string|null $valor_anterior
 * @property string|null $valor_nuevo
 * @property string $fecha
 */
class TicketHistorial extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'ticket_historial';
    }

    public function rules()
    {
        return [
            [['ticket_id', 'usuario_id', 'campo', 'fecha'], 'required'],
            [['ticket_id', 'usuario_id'], 'integer'],
            [['campo'], 'string', 'max' => 50],
            [['valor_anterior', 'valor_nuevo'], 'string'],
            [['fecha'], 'safe'],
        ];
    }

    public function getTicket()
    {
        return $this->hasOne(Tickets::class, ['id' => 'ticket_id']);
    }

    public function getUsuario()
    {
        return $this->hasOne(Usuarios::class, ['id' => 'usuario_id']);
    }

    /**
     * Registra un cambio en un campo de un ticket.
     * Solo guarda si los valores son distintos.
     */
    public static function registrar(int $ticketId, int $usuarioId, string $campo, $valorAnterior, $valorNuevo): void
    {
        if ((string)$valorAnterior === (string)$valorNuevo) {
            return;
        }

        $h = new self();
        $h->ticket_id      = $ticketId;
        $h->usuario_id     = $usuarioId;
        $h->campo          = $campo;
        $h->valor_anterior = (string)$valorAnterior;
        $h->valor_nuevo    = (string)$valorNuevo;
        $h->fecha          = date('Y-m-d H:i:s');
        $h->save(false);
    }

    /**
     * Etiquetas legibles para los campos registrados.
     */
    public static function labelCampo(string $campo): string
    {
        $labels = [
            'Estado'     => 'Estado',
            'Prioridad'  => 'Prioridad',
            'Asignado_a' => 'Asignado a',
            'Cliente_id' => 'Cliente',
            'Sistema_id' => 'Sistema',
            'Servicio_id'=> 'Servicio',
            'Solucion'   => 'Solución',
            'HoraFinalizo' => 'Hora de cierre',
            'TiempoEfectivo' => 'Tiempo efectivo',
        ];
        return $labels[$campo] ?? $campo;
    }
}
