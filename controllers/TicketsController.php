<?php

namespace app\controllers;

use app\models\Tickets;
use app\models\TicketsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * TicketsController implements the CRUD actions for Tickets model.
 */
class TicketsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Tickets models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TicketsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tickets model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Tickets model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Tickets();
        $model->Folio = str_pad(Tickets::find()->max('id') + 1, 4, '0', STR_PAD_LEFT);
        
        // CORRECCIÓN: Seleccionar 'id' y 'email', y mapear id => email
        $consultores = \app\models\Usuarios::find()
            ->select(['id', 'email']) // Necesitamos el ID para guardarlo
            ->where(['rol' => 'consultor'])
            ->asArray()
            ->all();
            
        // El primer parámetro es la clave (lo que se guarda: id), el segundo es el valor (lo que se ve: email)
        $consultoresList = \yii\helpers\ArrayHelper::map($consultores, 'id', 'email');
        $model->consultoresList = $consultoresList;
        
        // Obtener fecha del POST si existe (viene del calendario)
        $fechaSeleccionada = Yii::$app->request->post('fecha_seleccionada');
        if ($fechaSeleccionada) {
            // Convertir fecha del calendario (YYYY-MM-DD) a formato datetime
            $model->Fecha_creacion = $fechaSeleccionada . ' ' . date('H:i:s');
            $model->Fecha_actualizacion = $fechaSeleccionada . ' ' . date('H:i:s');
            
            // Guardar en sesión para mostrar mensaje
            Yii::$app->session->setFlash('fechaDesdeCalendario', $fechaSeleccionada);
        } else {
            // Si no hay fecha en POST, usar fecha actual
            $model->Fecha_creacion = date('Y-m-d H:i:s');
            $model->Fecha_actualizacion = date('Y-m-d H:i:s');
        }
        
        $model->Estado = 'Abierto';
        
        if ($this->request->isPost && $model->load($this->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Ticket creado exitosamente.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'consultoresList'=> $consultoresList,
        ]);
    }

    /**
     * Updates an existing Tickets model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Tickets model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Tickets model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Tickets the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tickets::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
