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
        $mesActual = Yii::$app->request->get('mes', date('Y-m'));
        $yearActual = date('Y', strtotime($mesActual . '-01'));
        
        // Estadísticas generales
        $estadisticasTickets = $this->getEstadisticasTickets($mesActual);
        $ticketsPorEstado = $this->getTicketsPorEstado($mesActual);
        $ticketsPorPrioridad = $this->getTicketsPorPrioridad($mesActual);
        $ticketsPorCliente = $this->getTicketsPorCliente($mesActual);
        $ticketsPorTecnico = $this->getTicketsPorTecnico($mesActual);
        $tiempoPromedio = $this->getTiempoPromedioResolucion($mesActual);
        $ticketsPorDia = $this->getTicketsPorDia($mesActual);
        
        // ✅ NUEVAS ESTADÍSTICAS DE CONSULTORES
        $consultoresDelMes = $this->getConsultoresDelMes($mesActual);
        $consultoresDelAnio = $this->getConsultoresDelAnio($yearActual);
        $topConsultoresMes = $this->getTopConsultoresMes($mesActual);
        $topConsultoresAnio = $this->getTopConsultoresAnio($yearActual);
        
        // ✅ ESTADÍSTICAS DE CLIENTES
        $clientesMasAtendidos = $this->getClientesMasAtendidos($mesActual);
        $clientesMasAtendidosAnio = $this->getClientesMasAtendidosAnio($yearActual);
        $ticketsPorSistema = $this->getTicketsPorSistema($mesActual);
        $ticketsPorServicio = $this->getTicketsPorServicio($mesActual);
        
        // ✅ COMPARACIÓN MES vs MES ANTERIOR
        $comparacionMes = $this->getComparacionMesAnterior($mesActual);
        
        // ✅ TICKETS POR HORA DEL DÍA
        $ticketsPorHora = $this->getTicketsPorHora($mesActual);
        
        // ✅ TIEMPO DE RESPUESTA PROMEDIO
        $tiempoRespuesta = $this->getTiempoRespuestaPromedio($mesActual);
        
        // ✅ TASA DE RESOLUCIÓN
        $tasaResolucion = $this->getTasaResolucion($mesActual);
        
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
    
    
    private function getEstadisticasTickets($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
        return [
            'total' => Tickets::find()->where(['between', 'Fecha_creacion', $inicio, $fin])->count(),
            'abiertos' => Tickets::find()->where(['Estado' => 'ABIERTO'])->andWhere(['between', 'Fecha_creacion', $inicio, $fin])->count(),
            'enProceso' => Tickets::find()->where(['Estado' => 'EN PROCESO'])->andWhere(['between', 'Fecha_creacion', $inicio, $fin])->count(),
            'cerrados' => Tickets::find()->where(['Estado' => 'CERRADO'])->andWhere(['between', 'Fecha_creacion', $inicio, $fin])->count(),
        ];
    }
    
    private function getTicketsPorEstado($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
        return Tickets::find()
            ->select(['Estado', 'COUNT(*) as total'])
            ->where(['between', 'Fecha_creacion', $inicio, $fin])
            ->groupBy('Estado')
            ->asArray()
            ->all();
    }
    
    private function getTicketsPorPrioridad($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
        return Tickets::find()
            ->select(['Prioridad', 'COUNT(*) as total'])
            ->where(['between', 'Fecha_creacion', $inicio, $fin])
            ->groupBy('Prioridad')
            ->asArray()
            ->all();
    }
    
    private function getTicketsPorCliente($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
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
    
    private function getTicketsPorTecnico($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
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
    
    private function getTiempoPromedioResolucion($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
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
    
    private function getTicketsPorDia($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
        return Tickets::find()
            ->select(['DATE(Fecha_creacion) as fecha', 'COUNT(*) as total'])
            ->where(['between', 'Fecha_creacion', $inicio, $fin])
            ->groupBy('DATE(Fecha_creacion)')
            ->orderBy(['fecha' => SORT_ASC])
            ->asArray()
            ->all();
    }

    // ✅ NUEVAS FUNCIONES - ESTADÍSTICAS DE CONSULTORES
    
    private function getConsultoresDelMes($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
        return Tickets::find()
            ->select([
                'usuarios.email as nombre',
                'COUNT(tickets.id) as total',
                'SUM(CASE WHEN tickets.Estado = "CERRADO" THEN 1 ELSE 0 END) as cerrados',
                'SUM(CASE WHEN tickets.Estado = "ABIERTO" THEN 1 ELSE 0 END) as abiertos',
                'SUM(CASE WHEN tickets.Estado = "EN PROCESO" THEN 1 ELSE 0 END) as en_proceso'
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
    
    private function getTopConsultoresMes($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
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
    
    private function getClientesMasAtendidos($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
        return Tickets::find()
            ->select([
                'clientes.Nombre as cliente',
                'COUNT(tickets.id) as total',
                'SUM(CASE WHEN tickets.Estado = "CERRADO" THEN 1 ELSE 0 END) as cerrados',
                'AVG(TIMESTAMPDIFF(HOUR, tickets.HoraInicio, tickets.HoraFinalizo)) as tiempo_promedio'
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
    
    private function getTicketsPorSistema($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
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
    
    private function getTicketsPorServicio($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
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
    
    private function getComparacionMesAnterior($mes)
    {
        $mesAnterior = date('Y-m', strtotime($mes . '-01 -1 month'));
        
        $ticketsMesActual = Tickets::find()
            ->where(['between', 'Fecha_creacion', $mes . '-01 00:00:00', date('Y-m-t 23:59:59', strtotime($mes . '-01'))])
            ->count();
            
        $ticketsMesAnterior = Tickets::find()
            ->where(['between', 'Fecha_creacion', $mesAnterior . '-01 00:00:00', date('Y-m-t 23:59:59', strtotime($mesAnterior . '-01'))])
            ->count();
        
        $diferencia = $ticketsMesActual - $ticketsMesAnterior;
        $porcentaje = $ticketsMesAnterior > 0 ? round(($diferencia / $ticketsMesAnterior) * 100, 2) : 0;
        
        return [
            'actual' => $ticketsMesActual,
            'anterior' => $ticketsMesAnterior,
            'diferencia' => $diferencia,
            'porcentaje' => $porcentaje
        ];
    }
    
    private function getTicketsPorHora($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
        return Tickets::find()
            ->select(['HOUR(Fecha_creacion) as hora', 'COUNT(*) as total'])
            ->where(['between', 'Fecha_creacion', $inicio, $fin])
            ->groupBy('HOUR(Fecha_creacion)')
            ->orderBy(['hora' => SORT_ASC])
            ->asArray()
            ->all();
    }
    
    private function getTiempoRespuestaPromedio($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
        $resultado = Tickets::find()
            ->select(['AVG(TIMESTAMPDIFF(MINUTE, Fecha_creacion, HoraInicio)) as minutos_promedio'])
            ->where(['between', 'Fecha_creacion', $inicio, $fin])
            ->andWhere(['is not', 'HoraInicio', null])
            ->asArray()
            ->one();
        
        return round($resultado['minutos_promedio'] ?? 0, 2);
    }
    
    private function getTasaResolucion($mes)
    {
        $inicio = $mes . '-01 00:00:00';
        $fin = date('Y-m-t 23:59:59', strtotime($inicio));
        
        $total = Tickets::find()->where(['between', 'Fecha_creacion', $inicio, $fin])->count();
        $cerrados = Tickets::find()->where(['Estado' => 'CERRADO'])->andWhere(['between', 'Fecha_creacion', $inicio, $fin])->count();
        
        $tasa = $total > 0 ? round(($cerrados / $total) * 100, 2) : 0;
        
        return [
            'total' => $total,
            'cerrados' => $cerrados,
            'tasa' => $tasa
        ];
    }
}