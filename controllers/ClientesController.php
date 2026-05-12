<?php

namespace app\controllers;

use Yii;
use app\models\Clientes;
use app\models\ClientesSearch;
use app\models\DevLog;
use yii\web\Controller;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


class ClientesController extends Controller
{
    /**
     * @inheritDoc
     */
  public function behaviors()
{
    return array_merge(parent::behaviors(), [
        'access' => [
            'class' => AccessControl::class,
            'only' => ['index','view','create','update','delete','historial','search'],
            'rules' => [
                // ✅ Ver clientes (Admin, Supervisores, Administracion, etc.)
                [
                    'allow' => true,
                    'actions' => ['index','view','historial','search'],
                    'roles' => ['verClientes'],
                ],
                // ✅ Administrar clientes (Supervisores y arriba)
                [
                    'allow' => true,
                    'actions' => ['create','update','delete'],
                    'roles' => ['administrarClientes'],
                ],
            ],
        ],
        'verbs' => [
            'class' => VerbFilter::class,
            'actions' => [
                'delete' => ['POST'],
            ],
        ],
    ]);
}
    /**
     * Lists all Clientes models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ClientesSearch();

        $validSizes = [10, 20, 50, 100, 500];
        $pageSize   = (int) Yii::$app->request->get('pageSize', 20);
        if (!in_array($pageSize, $validSizes)) {
            $pageSize = 20;
        }

        $dataProvider = $searchModel->search($this->request->queryParams, $pageSize);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pageSize'     => $pageSize,
        ]);
    }

    /**
     * Displays a single Clientes model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Cliente actualizado correctamente');
            return $this->refresh();
        }


        return $this->render('view', [
            'model' => $model,
        ]);
    }


    /**
     * Creates a new Clientes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
 public function actionCreate()
        {
            $model = new Clientes();

            if ($model->load(Yii::$app->request->post())) {

                $time = time();
                $model->created_at = $time;
                $model->updated_at = $time;

                if ($model->save()) {
                    DevLog::log(
                        DevLog::TIPO_CREAR,
                        "Cliente [{$model->Nombre}] creado — RFC: {$model->RFC} | tipo: {$model->Tipo_servicio} | prioridad: {$model->Prioridad}",
                        [
                            'nombre'        => $model->Nombre,
                            'razon_social'  => $model->Razon_social,
                            'rfc'           => $model->RFC,
                            'correo'        => $model->Correo,
                            'tipo_servicio' => $model->Tipo_servicio,
                            'prioridad'     => $model->Prioridad,
                            'criticidad'    => $model->Criticidad,
                            'tiempo_sla'    => $model->Tiempo,
                        ],
                        'clientes', $model->id, 'Clientes'
                    );
                    Yii::$app->session->setFlash('success', 'Cliente creado correctamente');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                Yii::$app->session->setFlash(
                    'error',
                    'No se pudo guardar: ' . json_encode($model->errors)
                );
            }

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    /**
     * Updates an existing Clientes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->updated_at = date('Y-m-d H:i:s');
            DevLog::log(
                DevLog::TIPO_ACTUALIZAR,
                "Cliente [{$model->Nombre}] actualizado — RFC: {$model->RFC}",
                [
                    'nombre'        => $model->Nombre,
                    'rfc'           => $model->RFC,
                    'tipo_servicio' => $model->Tipo_servicio,
                    'prioridad'     => $model->Prioridad,
                    'estado'        => $model->Estado,
                    'tiempo_sla'    => $model->Tiempo,
                ],
                'clientes', $model->id, 'Clientes'
            );
            return $this->redirect(['index']);
        }

        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }








    /**
     * Deletes an existing Clientes model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $nombre = $model->Nombre;
        $rfc    = $model->RFC;
        $model->delete();

        DevLog::log(
            DevLog::TIPO_ELIMINAR,
            "Cliente [{$nombre}] ELIMINADO — RFC: {$rfc} | ID #{$id}",
            ['nombre' => $nombre, 'rfc' => $rfc, 'id' => $id],
            'clientes', $id, 'Clientes'
        );

        return $this->redirect(['index']);
    }

    /**
     * Devuelve en JSON los tickets asociados a un cliente (para el modal de historial).
     */
    public function actionHistorial($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $cliente = $this->findModel($id);

        $tickets = \app\models\Tickets::find()
            ->select(['id', 'Folio', 'Fecha_creacion', 'TiempoEfectivo', 'Estado'])
            ->where(['Cliente_id' => $id])
            ->orderBy(['Fecha_creacion' => SORT_DESC])
            ->asArray()
            ->all();

        return [
            'cliente' => Html::encode($cliente->Nombre),
            'tickets' => $tickets,
        ];
    }

    /**
     * Búsqueda AJAX — devuelve JSON con todos los clientes que coincidan con $q.
     * No paginado (hasta 500 resultados), para el buscador instantáneo.
     */
    public function actionSearch()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $q = trim(Yii::$app->request->get('q', ''));

        $query = Clientes::find()
            ->select(['id', 'Nombre', 'Razon_social', 'RFC', 'Correo', 'Tiempo'])
            ->orderBy(['Nombre' => SORT_ASC])
            ->limit(500);

        if ($q !== '') {
            $query->andWhere(['or',
                ['like', 'Nombre',          $q],
                ['like', 'Razon_social',    $q],
                ['like', 'RFC',             $q],
                ['like', 'Correo',          $q],   // campo JSON, LIKE funciona
                ['like', 'Contacto_nombre', $q],
            ]);
        }

        $rows = $query->all();

        $results = array_map(function ($c) {
            // Extraer primer correo del JSON
            $correos = json_decode($c->Correo ?? '[]', true) ?: [];
            $primerCorreo = $correos[0]['valor'] ?? '';
            $tiempo = (float) $c->Tiempo;
            return [
                'id'          => $c->id,
                'Nombre'      => $c->Nombre,
                'Razon_social'=> (string) ($c->Razon_social ?? ''),
                'RFC'         => (string) ($c->RFC ?? ''),
                'Correo'      => $primerCorreo,
                'Tiempo'      => $c->Tiempo,
                'tiempoOk'    => $tiempo > 0,
            ];
        }, $rows);

        return ['results' => $results, 'count' => count($results)];
    }

    /**
     * Finds the Clientes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Clientes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Clientes::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
