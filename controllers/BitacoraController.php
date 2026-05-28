<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use app\models\Clientes;
use yii\data\Pagination;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class BitacoraController extends Controller
{
    public function actionIndex()
    {
        // ====== CLIENTES PARA DROPDOWN ======
        $clientes = Clientes::find()
            ->select(['id', 'Nombre'])
            ->orderBy(['Nombre' => SORT_ASC])
            ->asArray()
            ->all();

        // ====== QUERY BASE ======
        $query = (new Query())
            ->select([
                'tickets.Folio',
                'tickets.HoraProgramada',
                'tickets.TiempoEfectivo',
                'tickets.Descripcion',

                'clientes.Nombre AS ClienteNombre',
                'sistemas.Nombre AS SistemaNombre',
                'tickets.Usuario_reporta AS UsuarioNombre'
            ])
            ->from('tickets')

            ->leftJoin(
                'clientes',
                'clientes.id = tickets.Cliente_id'
            )

            ->leftJoin(
                'sistemas',
                'sistemas.id = tickets.Sistema_id'
            )

            ->leftJoin(
                'usuarios',
                'usuarios.id = tickets.asignado_a'
            );

        // ====== FILTRO CLIENTE ======
        $clienteId = Yii::$app->request->get('Cliente_id');

        if (!empty($clienteId)) {

            $query->andWhere([
                'tickets.Cliente_id' => $clienteId
            ]);
        }

        // ====== FILTRO FOLIOS ======
        $folioInicial = Yii::$app->request->get('folio_inicial');
        $folioFinal = Yii::$app->request->get('folio_final');

        if (!empty($folioInicial) && !empty($folioFinal)) {

            $query->andWhere([
                'between',
                'tickets.Folio',
                $folioInicial,
                $folioFinal
            ]);

        } elseif (!empty($folioInicial)) {

            $query->andWhere([
                '>=',
                'tickets.Folio',
                $folioInicial
            ]);

        } elseif (!empty($folioFinal)) {

            $query->andWhere([
                '<=',
                'tickets.Folio',
                $folioFinal
            ]);
        }

        // ====== FILTRO MES ======
        $mes = Yii::$app->request->get('mes');

        if (!empty($mes)) {

            $partes = explode('-', $mes);

            if (count($partes) === 2) {

                $anio = $partes[0];
                $numeroMes = $partes[1];

                $query->andWhere([
                    'MONTH(tickets.HoraFinalizo)' => $numeroMes,
                    'YEAR(tickets.HoraFinalizo)' => $anio
                ]);
            }
        }

        // ====== FILTRO FECHAS ======
        $fechaInicio = Yii::$app->request->get('fecha_inicio');
        $fechaFin = Yii::$app->request->get('fecha_fin');

        if (!empty($fechaInicio) && !empty($fechaFin)) {

            $query->andWhere([
                'between',
                'tickets.HoraFinalizo',
                $fechaInicio . ' 00:00:00',
                $fechaFin . ' 23:59:59'
            ]);
        }

        // ==========================================
// EJECUTAR QUERY + PAGINACION
// ==========================================

        $tickets = [];

        $hayFiltros = !empty($_GET);

        if ($hayFiltros) {

            $countQuery = clone $query;

            $pages = new Pagination([
                'totalCount' => $countQuery->count(),
                'pageSize' => 20,
            ]);

            $tickets = $query
                ->orderBy([
                    'tickets.Folio' => SORT_DESC
                ])
                ->offset($pages->offset)
                ->limit($pages->limit)
                ->all();

        } else {

            $pages = new Pagination([
                'totalCount' => 0,
                'pageSize' => 20,
            ]);
        }
        // ====== TOTAL HORAS ======
        $totalHoras = 0;

        foreach ($tickets as $ticket) {

            $totalHoras += (float) ($ticket['TiempoEfectivo'] ?? 0);
        }

        // ==========================================
// TITULO DINAMICO DE BITACORA
// ==========================================

        // ===== CLIENTE =====
        $nombreCliente = 'Todos los clientes';

        if (!empty($clienteId)) {

            foreach ($clientes as $cliente) {

                if ($cliente['id'] == $clienteId) {

                    $nombreCliente = $cliente['Nombre'];
                    break;
                }
            }
        }

        // TITULO BASE
        $tituloBitacora = 'Bitácora de ' . $nombreCliente;

        // ARRAY DE DETALLES
        $detallesTitulo = [];


        // ==========================================
// FOLIOS
// ==========================================

        if (!empty($folioInicial) && !empty($folioFinal)) {

            $detallesTitulo[] =
                'del Folio ' .
                $folioInicial .
                ' al Folio ' .
                $folioFinal;

        } elseif (!empty($folioInicial)) {

            $detallesTitulo[] =
                'desde el Folio ' .
                $folioInicial;

        } elseif (!empty($folioFinal)) {

            $detallesTitulo[] =
                'hasta el Folio ' .
                $folioFinal;
        }


        // ==========================================
// MES
// ==========================================

        if (!empty($mes)) {

            $timestamp = strtotime($mes . '-01');

            $meses = [
                1 => 'Enero',
                2 => 'Febrero',
                3 => 'Marzo',
                4 => 'Abril',
                5 => 'Mayo',
                6 => 'Junio',
                7 => 'Julio',
                8 => 'Agosto',
                9 => 'Septiembre',
                10 => 'Octubre',
                11 => 'Noviembre',
                12 => 'Diciembre'
            ];

            $nombreMes = $meses[(int) $numeroMes];

            $anioMes = date('Y', $timestamp);

            $detallesTitulo[] =
                'de ' .
                $nombreMes .
                ' del ' .
                $anioMes;
        }


        // ==========================================
// FECHAS
// ==========================================

        if (!empty($fechaInicio) && !empty($fechaFin)) {

            $meses = [
                1 => 'Enero',
                2 => 'Febrero',
                3 => 'Marzo',
                4 => 'Abril',
                5 => 'Mayo',
                6 => 'Junio',
                7 => 'Julio',
                8 => 'Agosto',
                9 => 'Septiembre',
                10 => 'Octubre',
                11 => 'Noviembre',
                12 => 'Diciembre'
            ];

            $inicioTimestamp = strtotime($fechaInicio);
            $finTimestamp = strtotime($fechaFin);

            $diaInicio = date('d', $inicioTimestamp);
            $mesInicio = $meses[(int) date('m', $inicioTimestamp)];

            $diaFin = date('d', $finTimestamp);
            $mesFin = $meses[(int) date('m', $finTimestamp)];

            $fechaInicioTexto =
                $diaInicio . ' de ' . $mesInicio;

            $fechaFinTexto =
                $diaFin . ' de ' . $mesFin;

            $detallesTitulo[] =
                'del ' .
                $fechaInicioTexto .
                ' al ' .
                $fechaFinTexto;
        }

        // ==========================================
// UNIR TITULO
// ==========================================

        if (!empty($detallesTitulo)) {

            $tituloBitacora .= ' ' . implode(' | ', $detallesTitulo);
        }
        // ====== RENDER ======
        return $this->render('index', [
            'tickets' => $tickets,
            'clientes' => $clientes,
            'totalHoras' => $totalHoras,
            'tituloBitacora' => $tituloBitacora,
            'pages' => $pages
        ]);
    }

    public function actionExportarExcel()
    {
        // ==========================================
        // QUERY BASE
        // ==========================================

        $query = (new Query())
            ->select([
                'tickets.Folio',
                'tickets.HoraProgramada',
                'tickets.TiempoEfectivo',
                'tickets.Descripcion',

                'clientes.Nombre AS ClienteNombre',
                'sistemas.Nombre AS SistemaNombre',
                'tickets.Usuario_reporta AS UsuarioNombre'
            ])
            ->from('tickets')

            ->leftJoin(
                'clientes',
                'clientes.id = tickets.Cliente_id'
            )

            ->leftJoin(
                'sistemas',
                'sistemas.id = tickets.Sistema_id'
            )

            ->leftJoin(
                'usuarios',
                'usuarios.id = tickets.asignado_a'
            );


        // ==========================================
        // FILTRO CLIENTE
        // ==========================================

        $clienteId = Yii::$app->request->get('Cliente_id');

        if (!empty($clienteId)) {

            $query->andWhere([
                'tickets.Cliente_id' => $clienteId
            ]);
        }


        // ==========================================
        // FILTRO FOLIOS
        // ==========================================

        $folioInicial = Yii::$app->request->get('folio_inicial');
        $folioFinal = Yii::$app->request->get('folio_final');

        if (!empty($folioInicial) && !empty($folioFinal)) {

            $query->andWhere([
                'between',
                'tickets.Folio',
                $folioInicial,
                $folioFinal
            ]);

        } elseif (!empty($folioInicial)) {

            $query->andWhere([
                '>=',
                'tickets.Folio',
                $folioInicial
            ]);

        } elseif (!empty($folioFinal)) {

            $query->andWhere([
                '<=',
                'tickets.Folio',
                $folioFinal
            ]);
        }


        // ==========================================
        // FILTRO MES
        // ==========================================

        $mes = Yii::$app->request->get('mes');

        if (!empty($mes)) {

            $partes = explode('-', $mes);

            if (count($partes) === 2) {

                $anio = $partes[0];
                $numeroMes = $partes[1];

                $query->andWhere([
                    'MONTH(tickets.HoraFinalizo)' => $numeroMes,
                    'YEAR(tickets.HoraFinalizo)' => $anio
                ]);
            }
        }


        // ==========================================
        // FILTRO FECHAS
        // ==========================================

        $fechaInicio = Yii::$app->request->get('fecha_inicio');
        $fechaFin = Yii::$app->request->get('fecha_fin');

        if (!empty($fechaInicio) && !empty($fechaFin)) {

            $query->andWhere([
                'between',
                'tickets.HoraFinalizo',
                $fechaInicio . ' 00:00:00',
                $fechaFin . ' 23:59:59'
            ]);
        }


        // ==========================================
        // OBTENER TICKETS
        // ==========================================

        $tickets = $query
            ->orderBy([
                'tickets.Folio' => SORT_DESC
            ])
            ->all();


        // ==========================================
        // CREAR EXCEL
        // ==========================================

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();


        // ==========================================
        // TITULOS
        // ==========================================

        $sheet->setCellValue('A1', 'FECHA');
        $sheet->setCellValue('B1', 'HORAS UTILIZADAS');
        $sheet->setCellValue('C1', 'FOLIO');
        $sheet->setCellValue('D1', 'SISTEMA');
        $sheet->setCellValue('E1', 'DETALLE DE ACTIVIDADES');
        $sheet->setCellValue('F1', 'USUARIO');


        // ==========================================
        // ESTILOS HEADER
        // ==========================================

        $sheet->getStyle('A1:F1')
            ->getFont()
            ->setBold(true);


        // ==========================================
        // DATOS
        // ==========================================

        $fila = 2;

        $totalHoras = 0;

        foreach ($tickets as $ticket) {

            $sheet->setCellValue(
                'A' . $fila,
                $ticket['HoraProgramada'] ?? ''
            );

            $sheet->setCellValue(
                'B' . $fila,
                $ticket['TiempoEfectivo'] ?? '0'
            );

            $sheet->setCellValue(
                'C' . $fila,
                $ticket['Folio'] ?? ''
            );

            $sheet->setCellValue(
                'D' . $fila,
                $ticket['SistemaNombre'] ?? ''
            );

            $sheet->setCellValue(
                'E' . $fila,
                $ticket['Descripcion'] ?? ''
            );

            $sheet->setCellValue(
                'F' . $fila,
                $ticket['UsuarioNombre'] ?? ''
            );

            $totalHoras += (float) ($ticket['TiempoEfectivo'] ?? 0);

            $fila++;
        }


        // ==========================================
        // TOTAL HORAS
        // ==========================================

        $sheet->setCellValue(
            'A' . $fila,
            'TOTAL HORAS'
        );

        $sheet->setCellValue(
            'B' . $fila,
            number_format($totalHoras, 2)
        );


        // ==========================================
        // AUTO SIZE
        // ==========================================

        foreach (range('A', 'F') as $columna) {

            $sheet->getColumnDimension($columna)
                ->setAutoSize(true);
        }


        // ==========================================
        // NOMBRE ARCHIVO
        // ==========================================

        $nombreArchivo = 'Bitacora.xlsx';


        // ==========================================
        // DESCARGAR
        // ==========================================

        header(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        header(
            'Content-Disposition: attachment;filename="' . $nombreArchivo . '"'
        );

        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);

        $writer->save('php://output');

        exit;
    }
}