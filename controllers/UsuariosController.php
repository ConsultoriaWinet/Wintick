<?php

namespace app\controllers;

use app\models\Usuarios;
use app\models\UsuariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

class UsuariosController extends Controller
{
    public function beforeAction($action)
    {
        if (Yii::$app->request->isAjax) {
            $this->layout = false;
        }

        return parent::beforeAction($action);
    }

    // 🔍 Vista de un usuario (soporte AJAX)
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            return $this->renderPartial('view', ['model' => $model]);
        }

        return $this->render('view', ['model' => $model]);
    }

    // 📌 Listado principal
    public function actionIndex()
    {
        $model = new Usuarios();
      
        $searchModel = new UsuariosSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            
        ]);
    }

    // ✏️ Actualizar usuario desde el modal
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index', 'updated' => 1]);
            }

            // Errores de validación
            $errores = '';
            foreach ($model->getErrors() as $campo => $mensajes) {
                foreach ($mensajes as $mensaje) {
                    $errores .= "$campo: $mensaje<br>";
                }
            }

            Yii::$app->session->setFlash('error', 'No se pudo actualizar el usuario.<br>' . $errores);
            return $this->redirect(['index']);
        }

        return $this->redirect(['index']);
    }

    // ➕ Crear usuario
    public function actionCreate()
    {
        $model = new Usuarios();
        $model->created_at = time();
        $model->updated_at = time();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                if (!empty($model->password_hash)) {
                    $model->password_hash = Yii::$app->security->generatePasswordHash($model->password_hash);
                }

                if ($model->save()) {
                    // Redirigir al index con indicador de creación
                    return $this->redirect(['index', 'created' => 1]);
                }

                Yii::$app->session->setFlash('error', 'Error al crear el usuario.');
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', ['model' => $model]);
    }


    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index', 'deleted' => 1]);
    }

    // 🔎 Buscar modelo
    protected function findModel($id)
    {
        if (($model = Usuarios::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
