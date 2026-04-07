<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use app\models\DevLog;

/**
 * LogsController - Panel de logs exclusivo para Desarrolladores.
 * Muestra absolutamente todos los movimientos del sistema.
 */
class LogsController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Bloqueo adicional por rol: solo Desarrolladores.
     * Se hace aquí y no solo en RBAC para una capa extra de seguridad.
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/site/login']);
        }

        $rol = Yii::$app->user->identity->rol ?? '';
        if ($rol !== 'Desarrolladores') {
            throw new ForbiddenHttpException(
                'Acceso denegado. Esta sección es exclusiva para Desarrolladores.'
            );
        }

        return true;
    }

    /**
     * Index - Lista paginada y filtrable de todos los logs.
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;

        // ── Filtros desde GET ──
        $filtroTipo    = $request->get('tipo',    '');
        $filtroModulo  = $request->get('modulo',  '');
        $filtroUsuario = $request->get('usuario', '');
        $filtroFecha   = $request->get('fecha',   date('Y-m-d')); // default: hoy
        $filtroFechaFin= $request->get('fecha_fin', '');
        $filtroBuscar  = $request->get('buscar',  '');
        $perPage       = (int)$request->get('per_page', 100);
        $page          = (int)$request->get('page', 1);
        if ($page < 1) $page = 1;

        // ── Query base ──
        $query = DevLog::find()->orderBy(['created_at' => SORT_DESC]);

        if ($filtroTipo) {
            $query->andWhere(['tipo' => $filtroTipo]);
        }
        if ($filtroModulo) {
            $query->andWhere(['modulo' => $filtroModulo]);
        }
        if ($filtroUsuario) {
            $query->andWhere(['like', 'usuario_nombre', $filtroUsuario]);
        }
        if ($filtroFecha) {
            $query->andWhere(['>=', 'created_at', $filtroFecha . ' 00:00:00']);
        }
        if ($filtroFechaFin) {
            $query->andWhere(['<=', 'created_at', $filtroFechaFin . ' 23:59:59']);
        }
        if ($filtroBuscar) {
            $query->andWhere(['or',
                ['like', 'accion',         $filtroBuscar],
                ['like', 'usuario_nombre', $filtroBuscar],
                ['like', 'ip',             $filtroBuscar],
                ['like', 'datos',          $filtroBuscar],
            ]);
        }

        $total  = $query->count();
        $offset = ($page - 1) * $perPage;
        $logs   = $query->limit($perPage)->offset($offset)->all();

        // ── Estadísticas ──
        $statsHoy        = DevLog::statsHoy();
        $usuariosActivos = DevLog::usuariosActivos(30);

        // ── Valores únicos para los filtros ──
        $tiposDisponibles   = DevLog::tipoLabels();
        $modulosDisponibles = DevLog::find()
            ->select('modulo')->distinct()->column();
        $modulosDisponibles = array_filter($modulosDisponibles);
        sort($modulosDisponibles);

        $totalPages = (int)ceil($total / $perPage);

        return $this->render('index', compact(
            'logs',
            'total',
            'page',
            'totalPages',
            'perPage',
            'filtroTipo',
            'filtroModulo',
            'filtroUsuario',
            'filtroFecha',
            'filtroFechaFin',
            'filtroBuscar',
            'statsHoy',
            'usuariosActivos',
            'tiposDisponibles',
            'modulosDisponibles'
        ));
    }

    /**
     * Detalle de un log individual (JSON completo, para modal o página).
     */
    public function actionDetalle($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $log = DevLog::findOne($id);
        if (!$log) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'Log no encontrado'];
        }

        return [
            'id'             => $log->id,
            'tipo'           => $log->tipo,
            'tipo_label'     => $log->getTipoLabel(),
            'tipo_color'     => $log->getTipoColor(),
            'usuario_nombre' => $log->usuario_nombre,
            'usuario_rol'    => $log->usuario_rol,
            'modulo'         => $log->modulo,
            'modelo'         => $log->modelo,
            'modelo_id'      => $log->modelo_id,
            'accion'         => $log->accion,
            'datos'          => $log->getDatosDecodificados(),
            'ip'             => $log->ip,
            'user_agent'     => $log->user_agent,
            'created_at'     => $log->created_at,
        ];
    }

    /**
     * Limpiar logs antiguos (más de N días).
     */
    public function actionLimpiar()
    {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $dias = (int)Yii::$app->request->post('dias', 90);
        if ($dias < 7) $dias = 7; // mínimo 7 días

        $fecha  = date('Y-m-d H:i:s', strtotime("-{$dias} days"));
        $borrados = DevLog::deleteAll(['<', 'created_at', $fecha]);

        DevLog::log(
            DevLog::TIPO_SISTEMA,
            "Limpieza de logs: se eliminaron {$borrados} registros anteriores a {$fecha}",
            ['registros_eliminados' => $borrados, 'dias_conservados' => $dias],
            'logs'
        );

        Yii::$app->session->setFlash('success', "Se eliminaron {$borrados} registros de log (anteriores a {$dias} días).");
        return $this->redirect(['index']);
    }
}
