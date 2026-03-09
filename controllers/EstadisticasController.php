<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Tickets;
use app\models\Comentarios;
use app\models\Clientes;
use app\models\Usuarios;
use yii\db\Expression;

class EstadisticasController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $mesActual  = Yii::$app->request->get('mes', date('Y-m'));
        $yearActual = date('Y', strtotime($mesActual . '-01'));

        // Rango de fechas calculado UNA sola vez y reutilizado en todos los métodos
        $inicio = $mesActual . '-01 00:00:00';
        $fin    = date('Y-m-t 23:59:59', strtotime($inicio));

        // 1 query en vez de 4 para los totales del mes
        $estadisticasTickets = $this->getEstadisticasTickets($inicio, $fin);

        // tasaResolucion se calcula con los mismos datos — sin query adicional
        $tasaResolucion = [
            'total'    => $estadisticasTickets['total'],
            'cerrados' => $estadisticasTickets['cerrados'],
            'tasa'     => $estadisticasTickets['total'] > 0
                ? round($estadisticasTickets['cerrados'] / $estadisticasTickets['total'] * 100, 2)
                : 0,
        ];

        $ticketsPorEstado    = $this->getTicketsPorEstado($inicio, $fin);
        $ticketsPorPrioridad = $this->getTicketsPorPrioridad($inicio, $fin);
        $ticketsPorCliente   = $this->getTicketsPorCliente($inicio, $fin);
        $ticketsPorTecnico   = $this->getTicketsPorTecnico($inicio, $fin);
        $tiempoPromedio      = $this->getTiempoPromedioResolucion($inicio, $fin);
        $ticketsPorDia       = $this->getTicketsPorDia($inicio, $fin);

        $consultoresDelMes  = $this->getConsultoresDelMes($inicio, $fin);
        $consultoresDelAnio = $this->getConsultoresDelAnio($yearActual);
        $topConsultoresMes  = $this->getTopConsultoresMes($inicio, $fin);
        $topConsultoresAnio = $this->getTopConsultoresAnio($yearActual);

        $clientesMasAtendidos     = $this->getClientesMasAtendidos($inicio, $fin);
        $clientesMasAtendidosAnio = $this->getClientesMasAtendidosAnio($yearActual);
        $ticketsPorSistema        = $this->getTicketsPorSistema($inicio, $fin);
        $ticketsPorServicio       = $this->getTicketsPorServicio($inicio, $fin);

        // 1 query en vez de 2 para comparar mes actual vs anterior
        $comparacionMes = $this->getComparacionMesAnterior($mesActual, $inicio, $fin);

        $ticketsPorHora  = $this->getTicketsPorHora($inicio, $fin);
        $tiempoRespuesta = $this->getTiempoRespuestaPromedio($inicio, $fin);

        return $this->render('index', [
            'mesActual' => $mesActual,
            'yearActual' => $yearActual,
            'estadisticasTickets' => $estadisticasTickets,
            'ticketsPorEstado' => $ticketsPorEstado,
            'ticketsPorPrioridad' => $ticketsPorPrioridad,
            'ticketsPorCliente' => $ticketsPorCliente,
            'ticketsPorTecnico' => $ticketsPorTecnico,
            'tiempoPromedio' => $tiempoPromedio,
            'ticketsPorDia' => $ticketsPorDia,
            'consultoresDelMes' => $consultoresDelMes,
            'consultoresDelAnio' => $consultoresDelAnio,
            'topConsultoresMes' => $topConsultoresMes,
            'topConsultoresAnio' => $topConsultoresAnio,
            'clientesMasAtendidos' => $clientesMasAtendidos,
            'clientesMasAtendidosAnio' => $clientesMasAtendidosAnio,
            'ticketsPorSistema' => $ticketsPorSistema,
            'ticketsPorServicio' => $ticketsPorServicio,
            'comparacionMes' => $comparacionMes,
            'ticketsPorHora' => $ticketsPorHora,
            'tiempoRespuesta' => $tiempoRespuesta,
            'tasaResolucion' => $tasaResolucion,
        ]);
    }        
    
    
    /**
     * Totales del mes en UNA sola query (antes eran 4 queries separadas).
     */
    private function getEstadisticasTickets(string $inicio, string $fin): array
    {
        $row = Tickets::find()
            ->select([
                'COUNT(*) as total',
                'SUM(CASE WHEN Estado = "ABIERTO"     THEN 1 ELSE 0 END) as abiertos',
                'SUM(CASE WHEN Estado = "EN PROCESO"  THEN 1 ELSE 0 END) as en_proceso',
                'SUM(CASE WHEN Estado = "CERRADO"     THEN 1 ELSE 0 END) as cerrados',
            ])
            ->where(['between', 'Fecha_creacion', $inicio, $fin])
            ->asArray()
            ->one();

        return [
            'total'     => (int)($row['total']      ?? 0),
            'abiertos'  => (int)($row['abiertos']   ?? 0),
            'enProceso' => (int)($row['en_proceso']  ?? 0),
            'cerrados'  => (int)($row['cerrados']    ?? 0),
        ];
    }
    
    private function getTicketsPorEstado(string $inicio, string $fin): array
    {
        return Tickets::find()
            ->select(['Estado', 'COUNT(*) as total'])
            ->where(['between', 'Fecha_creacion', $inicio, $fin])
            ->groupBy('Estado')
            ->asArray()
            ->all();
    }

    private function getTicketsPorPrioridad(string $inicio, string $fin): array
    {
        return Tickets::find()
            ->select(['Prioridad', 'COUNT(*) as total'])
            ->where(['between', 'Fecha_creacion', $inicio, $fin])
            ->groupBy('Prioridad')
            ->asArray()
            ->all();
    }

    private function getTicketsPorCliente(string $inicio, string $fin): array
    {
        return Tickets::find()
            ->select(['clientes.Nombre as cliente', 'COUNT(tickets.id) as total'])
            ->innerJoin('clientes', 'clientes.id = tickets.Cliente_id')
            ->where(['between', 'tickets.Fecha_creacion', $inicio, $fin])
            ->groupBy('tickets.Cliente_id')
            ->orderBy(['total' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();
    }

    private function getTicketsPorTecnico(string $inicio, string $fin): array
    {
        return Tickets::find()
            ->select(['usuarios.email as tecnico', 'COUNT(tickets.id) as total'])
            ->innerJoin('usuarios', 'usuarios.id = tickets.Asignado_a')
            ->where(['between', 'tickets.Fecha_creacion', $inicio, $fin])
            ->andWhere(['is not', 'tickets.Asignado_a', null])
            ->groupBy('tickets.Asignado_a')
            ->orderBy(['total' => SORT_DESC])
            ->asArray()
            ->all();
    }

    private function getTiempoPromedioResolucion(string $inicio, string $fin): float
    {
        $resultado = Tickets::find()
            ->select(['AVG(TIMESTAMPDIFF(HOUR, HoraInicio, HoraFinalizo)) as horas_promedio'])
            ->where(['Estado' => 'CERRADO'])
            ->andWhere(['between', 'Fecha_creacion', $inicio, $fin])
            ->andWhere(['is not', 'HoraInicio', null])
            ->andWhere(['is not', 'HoraFinalizo', null])
            ->asArray()
            ->one();

        return round($resultado['horas_promedio'] ?? 0, 2);
    }

    private function getTicketsPorDia(string $inicio, string $fin): array
    {
        return Tickets::find()
            ->select(['DATE(Fecha_creacion) as fecha', 'COUNT(*) as total'])
            ->where(['between', 'Fecha_creacion', $inicio, $fin])
            ->groupBy('DATE(Fecha_creacion)')
            ->orderBy(['fecha' => SORT_ASC])
            ->asArray()
            ->all();
    }

    // ✅ NUEVAS FUNCIONES - ESTADÍSTICAS DE CONSULTORES
    
    private function getConsultoresDelMes(string $inicio, string $fin): array
    {
        return Tickets::find()
            ->select([
                'usuarios.email as nombre',
                'COUNT(tickets.id) as total',
                'SUM(CASE WHEN tickets.Estado = "CERRADO"    THEN 1 ELSE 0 END) as cerrados',
                'SUM(CASE WHEN tickets.Estado = "ABIERTO"    THEN 1 ELSE 0 END) as abiertos',
                'SUM(CASE WHEN tickets.Estado = "EN PROCESO" THEN 1 ELSE 0 END) as en_proceso',
            ])
            ->innerJoin('usuarios', 'usuarios.id = tickets.Asignado_a')
            ->where(['usuarios.rol' => 'Consultor'])
            ->andWhere(['between', 'tickets.Fecha_creacion', $inicio, $fin])
            ->groupBy('tickets.Asignado_a')
            ->orderBy(['total' => SORT_DESC])
            ->asArray()
            ->all();
    }
    
    private function getConsultoresDelAnio($year)
    {
        $inicio = $year . '-01-01 00:00:00';
        $fin = $year . '-12-31 23:59:59';
        
        return Tickets::find()
            ->select([
                'usuarios.email as nombre',
                'COUNT(tickets.id) as total',
                'SUM(CASE WHEN tickets.Estado = "CERRADO" THEN 1 ELSE 0 END) as cerrados',
                'AVG(TIMESTAMPDIFF(HOUR, tickets.HoraInicio, tickets.HoraFinalizo)) as tiempo_promedio'
            ])
            ->innerJoin('usuarios', 'usuarios.id = tickets.Asignado_a')
            ->where(['usuarios.rol' => 'Consultor'])
            ->andWhere(['between', 'tickets.Fecha_creacion', $inicio, $fin])
            ->groupBy('tickets.Asignado_a')
            ->orderBy(['total' => SORT_DESC])
            ->asArray()
            ->all();
    }
    
    private function getTopConsultoresMes(string $inicio, string $fin): array
    {
        return Tickets::find()
            ->select([
                'usuarios.email as nombre',
                'COUNT(tickets.id) as total',
                'SUM(CASE WHEN tickets.Estado = "CERRADO" THEN 1 ELSE 0 END) as cerrados',
            ])
            ->innerJoin('usuarios', 'usuarios.id = tickets.Asignado_a')
            ->where(['usuarios.rol' => 'Consultor'])
            ->andWhere(['between', 'tickets.Fecha_creacion', $inicio, $fin])
            ->groupBy('tickets.Asignado_a')
            ->orderBy(['cerrados' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();
    }
    
    private function getTopConsultoresAnio($year)
    {
        $inicio = $year . '-01-01 00:00:00';
        $fin = $year . '-12-31 23:59:59';
        
        return Tickets::find()
            ->select([
                'usuarios.email as nombre',
                'COUNT(tickets.id) as total',
                'SUM(CASE WHEN tickets.Estado = "CERRADO" THEN 1 ELSE 0 END) as cerrados'
            ])
            ->innerJoin('usuarios', 'usuarios.id = tickets.Asignado_a')
            ->where(['usuarios.rol' => 'Consultor'])
            ->andWhere(['between', 'tickets.Fecha_creacion', $inicio, $fin])
            ->groupBy('tickets.Asignado_a')
            ->orderBy(['cerrados' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();
    }
    
    // ✅ ESTADÍSTICAS DE CLIENTES
    
    private function getClientesMasAtendidos(string $inicio, string $fin): array
    {
        return Tickets::find()
            ->select([
                'clientes.Nombre as cliente',
                'COUNT(tickets.id) as total',
                'SUM(CASE WHEN tickets.Estado = "CERRADO" THEN 1 ELSE 0 END) as cerrados',
                'AVG(TIMESTAMPDIFF(HOUR, tickets.HoraInicio, tickets.HoraFinalizo)) as tiempo_promedio',
            ])
            ->innerJoin('clientes', 'clientes.id = tickets.Cliente_id')
            ->where(['between', 'tickets.Fecha_creacion', $inicio, $fin])
            ->groupBy('tickets.Cliente_id')
            ->orderBy(['total' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();
    }
    
    private function getClientesMasAtendidosAnio($year)
    {
        $inicio = $year . '-01-01 00:00:00';
        $fin = $year . '-12-31 23:59:59';
        
        return Tickets::find()
            ->select([
                'clientes.Nombre as cliente',
                'COUNT(tickets.id) as total',
                'SUM(CASE WHEN tickets.Estado = "CERRADO" THEN 1 ELSE 0 END) as cerrados'
            ])
            ->innerJoin('clientes', 'clientes.id = tickets.Cliente_id')
            ->where(['between', 'tickets.Fecha_creacion', $inicio, $fin])
            ->groupBy('tickets.Cliente_id')
            ->orderBy(['total' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();
    }
    
    private function getTicketsPorSistema(string $inicio, string $fin): array
    {
        return Tickets::find()
            ->select(['sistemas.Nombre as sistema', 'COUNT(tickets.id) as total'])
            ->innerJoin('sistemas', 'sistemas.id = tickets.Sistema_id')
            ->where(['between', 'tickets.Fecha_creacion', $inicio, $fin])
            ->groupBy('tickets.Sistema_id')
            ->orderBy(['total' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();
    }

    private function getTicketsPorServicio(string $inicio, string $fin): array
    {
        return Tickets::find()
            ->select(['servicios.Nombre as servicio', 'COUNT(tickets.id) as total'])
            ->innerJoin('servicios', 'servicios.id = tickets.Servicio_id')
            ->where(['between', 'tickets.Fecha_creacion', $inicio, $fin])
            ->andWhere(['is not', 'tickets.Servicio_id', null])
            ->groupBy('tickets.Servicio_id')
            ->orderBy(['total' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();
    }

    /**
     * Comparación mes actual vs anterior en UNA query (antes eran 2).
     */
    private function getComparacionMesAnterior(string $mes, string $inicioActual, string $finActual): array
    {
        $mesAnterior  = date('Y-m', strtotime($mes . '-01 -1 month'));
        $inicioAnterior = $mesAnterior . '-01 00:00:00';
        $finAnterior    = date('Y-m-t 23:59:59', strtotime($inicioAnterior));

        $rows = Tickets::find()
            ->select([
                new Expression("DATE_FORMAT(Fecha_creacion, '%Y-%m') as mes"),
                new Expression('COUNT(*) as total'),
            ])
            ->where(['between', 'Fecha_creacion', $inicioAnterior, $finActual])
            ->groupBy(new Expression("DATE_FORMAT(Fecha_creacion, '%Y-%m')"))
            ->asArray()
            ->all();

        $porMes = [];
        foreach ($rows as $row) {
            $porMes[$row['mes']] = (int) $row['total'];
        }

        $actual   = $porMes[$mes]        ?? 0;
        $anterior = $porMes[$mesAnterior] ?? 0;
        $diferencia = $actual - $anterior;

        return [
            'actual'     => $actual,
            'anterior'   => $anterior,
            'diferencia' => $diferencia,
            'porcentaje' => $anterior > 0 ? round(($diferencia / $anterior) * 100, 2) : 0,
        ];
    }

    private function getTicketsPorHora(string $inicio, string $fin): array
    {
        return Tickets::find()
            ->select(['HOUR(Fecha_creacion) as hora', 'COUNT(*) as total'])
            ->where(['between', 'Fecha_creacion', $inicio, $fin])
            ->groupBy('HOUR(Fecha_creacion)')
            ->orderBy(['hora' => SORT_ASC])
            ->asArray()
            ->all();
    }

    private function getTiempoRespuestaPromedio(string $inicio, string $fin): float
    {
        $resultado = Tickets::find()
            ->select(['AVG(TIMESTAMPDIFF(MINUTE, Fecha_creacion, HoraInicio)) as minutos_promedio'])
            ->where(['between', 'Fecha_creacion', $inicio, $fin])
            ->andWhere(['is not', 'HoraInicio', null])
            ->asArray()
            ->one();

        return round($resultado['minutos_promedio'] ?? 0, 2);
    }
}
