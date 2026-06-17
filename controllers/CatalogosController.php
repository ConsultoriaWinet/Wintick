<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Sistemas;
use app\models\Servicios;
use app\models\Tickets;

/**
 * CatalogosController - CRUD simple de catálogos (Sistemas y Servicios).
 * Acceso exclusivo para el rol Desarrolladores.
 */
class CatalogosController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'guardar'  => ['post'],
                    'eliminar' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Capa extra de seguridad: solo Desarrolladores.
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

    /** Devuelve la clase de modelo y el campo de Tickets según el tipo. */
    private function resolverTipo(string $tipo): ?array
    {
        if ($tipo === 'sistemas') {
            return ['model' => Sistemas::class, 'campoTicket' => 'Sistema_id'];
        }
        if ($tipo === 'servicios') {
            return ['model' => Servicios::class, 'campoTicket' => 'Servicio_id'];
        }
        return null;
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'sistemas'  => Sistemas::find()->orderBy(['Nombre' => SORT_ASC])->all(),
            'servicios' => Servicios::find()->orderBy(['Nombre' => SORT_ASC])->all(),
        ]);
    }

    /**
     * Crea uno o varios registros. Recibe JSON { tipo, nombres: [] }.
     * Ignora vacíos y duplicados (sin distinción de mayúsculas).
     */
    public function actionGuardar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = json_decode(Yii::$app->request->getRawBody(), true) ?: [];
        $cfg  = $this->resolverTipo($data['tipo'] ?? '');
        if (!$cfg) {
            return ['success' => false, 'message' => 'Tipo de catálogo inválido.'];
        }

        /** @var \yii\db\ActiveRecord $modelClass */
        $modelClass = $cfg['model'];
        $nombres    = is_array($data['nombres'] ?? null) ? $data['nombres'] : [];

        $time = time();
        $creados = 0;
        $duplicados = [];
        $errores = [];
        $itemsCreados = [];

        foreach ($nombres as $nombre) {
            $nombre = trim((string) $nombre);
            if ($nombre === '') {
                continue;
            }
            // Duplicado sin distinguir mayúsculas/acentos (collation de la tabla)
            $existe = $modelClass::find()->where(['Nombre' => $nombre])->exists();
            if ($existe) {
                $duplicados[] = $nombre;
                continue;
            }
            /** @var \yii\db\ActiveRecord $m */
            $m = new $modelClass();
            $m->Nombre = $nombre;
            $m->created_at = $time;
            $m->updated_at = $time;
            if ($m->save()) {
                $creados++;
                $itemsCreados[] = ['id' => $m->id, 'Nombre' => $m->Nombre];
            } else {
                $errores[] = $nombre;
            }
        }

        return [
            'success'    => true,
            'creados'    => $creados,
            'duplicados' => $duplicados,
            'errores'    => $errores,
            'items'      => $itemsCreados,
        ];
    }

    /**
     * Elimina registros por id (individual o masivo). Recibe JSON { tipo, ids: [] }.
     * Bloquea los que estén en uso por algún ticket para no dejar datos huérfanos.
     */
    public function actionEliminar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = json_decode(Yii::$app->request->getRawBody(), true) ?: [];
        $cfg  = $this->resolverTipo($data['tipo'] ?? '');
        if (!$cfg) {
            return ['success' => false, 'message' => 'Tipo de catálogo inválido.'];
        }

        /** @var \yii\db\ActiveRecord $modelClass */
        $modelClass = $cfg['model'];
        $campo      = $cfg['campoTicket'];

        $ids = array_values(array_filter(array_map('intval', (array) ($data['ids'] ?? []))));
        if (empty($ids)) {
            return ['success' => false, 'message' => 'No se seleccionó ningún registro.'];
        }

        $eliminados = 0;
        $eliminadosIds = [];
        $enUso = [];

        foreach ($ids as $id) {
            $usos = (int) Tickets::find()->where([$campo => $id])->count();
            if ($usos > 0) {
                $m = $modelClass::findOne($id);
                $enUso[] = [
                    'id'      => $id,
                    'nombre'  => $m ? $m->Nombre : ('#' . $id),
                    'tickets' => $usos,
                ];
                continue;
            }
            if ($modelClass::deleteAll(['id' => $id]) > 0) {
                $eliminados++;
                $eliminadosIds[] = $id;
            }
        }

        return [
            'success'    => true,
            'eliminados' => $eliminados,
            'ids'        => $eliminadosIds,
            'enUso'      => $enUso,
        ];
    }
}
