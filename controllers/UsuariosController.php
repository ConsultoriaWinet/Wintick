<?php

namespace app\controllers;

use app\models\Usuarios;
use app\models\UsuariosSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class UsuariosController extends Controller
{
    /**
     * Lista oficial de roles permitidos.
     * Esto evita que se guarden variantes como: "consultores", "ADMIN", etc.
     */
    private const ROLES_VALIDOS = [
        'Consultores',
        'Administracion',
        'Supervisores',
        'Administradores',
        'Desarrolladores',
    ];

    /**
     * Reglas de acceso del módulo.
     * - index/view: requieren permiso "verUsuarios"
     * - create/update/delete: requieren permiso "administrarUsuarios"
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    // Ver usuarios (Administración, Supervisores, Administradores, Desarrolladores)
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['verUsuarios'],
                    ],
                    // Administrar usuarios (Administradores y Desarrolladores)
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['administrarUsuarios'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    // Seguridad: evitar borrado por GET
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Ajuste de layout para respuestas AJAX (modales, renderPartial).
     */
    public function beforeAction($action)
    {
        if (Yii::$app->request->isAjax) {
            $this->layout = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Listado principal.
     */
    public function actionIndex()
    {
        $model = new Usuarios();

        $searchModel = new UsuariosSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'model'        => $model,
        ]);
    }

    /**
     * Vista de un usuario.
     * Soporta AJAX (modal) y vista normal.
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            return $this->renderPartial('view', ['model' => $model]);
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Crear usuario.
     * - Hashea password si viene en el campo password_hash (como lo traes tú)
     * - Normaliza rol
     * - Guarda y sincroniza RBAC (auth_assignment)
     */
    public function actionCreate()
    {
        $model = new Usuarios();
        $model->scenario = 'create';
        $model->created_at = time();
        $model->updated_at = time();

        if ($this->request->isPost && $model->load($this->request->post())) {

            // 1) Normalizar/validar rol para evitar minúsculas y valores inválidos
            $model->rol = $this->normalizarRol($model->rol);

            // Si viene vacío o inválido, detenemos el guardado con error claro
            if (!$model->rol) {
                Yii::$app->session->setFlash('error', 'Rol inválido. Seleccione un rol válido.');
                return $this->redirect(['index']);
            }

            // 2) Hashear password solo si se proporcionó (tu formulario usa password_hash como input)
            if (!empty($model->password_hash)) {
                $model->password_hash = Yii::$app->security->generatePasswordHash($model->password_hash);
            }

            // 3) Guardar usuario
            if ($model->save()) {

                // 4) Sincronizar RBAC: asignar rol al usuario en auth_assignment
                $this->syncRbacAssignment($model->id, $model->rol);

                Yii::$app->session->setFlash('success', 'Usuario creado correctamente.');
                return $this->redirect(['index', 'created' => 1]);
            }

            // 5) Si falló el guardado, mostrar errores
            Yii::$app->session->setFlash('error', 'Error al crear el usuario: ' . $this->formatErrors($model));
            return $this->redirect(['index']);
        }

        $model->loadDefaultValues();

        return $this->render('create', [
            'model'       => $model,
            'rolesValidos'=> self::ROLES_VALIDOS, // útil si quieres dropdown en la vista
        ]);
    }

    /**
     * Actualizar usuario.
     * - Guarda cambios
     * - Si cambia rol, se sincroniza RBAC nuevamente
     * - Si viene password, se re-hashea
     */
    public function actionUpdate($id)
{
    $model = $this->findModel($id);

    if (Yii::$app->request->isPost) {

        $rolAnterior = $model->rol;
        $oldHash = $model->password_hash; // blindaje

        if ($model->load(Yii::$app->request->post())) {

            $model->updated_at = time();

            $model->rol = $this->normalizarRol($model->rol);
            if (!$model->rol) {
                Yii::$app->session->setFlash('error', 'Rol inválido. Seleccione un rol válido.');
                return $this->redirect(['index']);
            }

            // Si NO tienes un campo separado para contraseña, no permitas que se toque aquí:
            $model->password_hash = $oldHash;

            if ($model->save()) {
                if ($rolAnterior !== $model->rol) {
                    $this->syncRbacAssignment($model->id, $model->rol);
                }
                Yii::$app->session->setFlash('success', 'Usuario actualizado correctamente.');
                return $this->redirect(['index', 'updated' => 1]);
            }

            Yii::$app->session->setFlash('error', 'No se pudo actualizar el usuario: ' . $this->formatErrors($model));
            return $this->redirect(['index']);
        }
    }

    return $this->redirect(['index']);
}

    /**
     * Eliminar usuario.
     * - Elimina el registro
     * - Limpia asignaciones RBAC (opcional pero recomendado)
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Limpieza RBAC antes de borrar (por orden/consistencia)
        $auth = Yii::$app->authManager;
        $auth->revokeAll($model->id);

        $model->delete();

        Yii::$app->session->setFlash('success', 'Usuario eliminado correctamente.');
        return $this->redirect(['index', 'deleted' => 1]);
    }

    /**
     * Busca modelo o lanza 404.
     */
    protected function findModel($id)
    {
        if (($model = Usuarios::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('El usuario solicitado no existe.');
    }

    /* =========================================================
     *                 MÉTODOS DE SOPORTE (PRIVADOS)
     * ========================================================= */

    /**
     * Normaliza el rol a un valor oficial permitido.
     * - Trim
     * - Case sensitive controlado
     * - Si no coincide con la lista oficial, retorna null.
     */
    private function normalizarRol(?string $rol): ?string
    {
        if ($rol === null) {
            return null;
        }

        $rol = trim($rol);

        // Si viene igual a alguno de los roles válidos, lo regresamos tal cual
        if (in_array($rol, self::ROLES_VALIDOS, true)) {
            return $rol;
        }

        // Opcional: mapear variantes comunes a los oficiales
        $map = [
            'consultores'     => 'Consultores',
            'consultor'       => 'Consultores',
            'administracion'  => 'Administracion',
            'supervisores'    => 'Supervisores',
            'administradores' => 'Administradores',
            'desarrolladores' => 'Desarrolladores',
        ];

        $key = mb_strtolower($rol, 'UTF-8');
        return $map[$key] ?? null;
    }

    /**
     * Sincroniza el rol en RBAC (auth_assignment) para un usuario.
     * - Revoca todo (evita duplicados o roles viejos)
     * - Asigna el rol indicado
     */
    private function syncRbacAssignment(int $userId, string $roleName): void
    {
        $auth = Yii::$app->authManager;

        // Quita roles anteriores
        $auth->revokeAll($userId);

        // Obtiene el rol RBAC
        $role = $auth->getRole($roleName);

        if ($role) {
            $auth->assign($role, $userId);
            return;
        }

        // Si no existe el rol en RBAC, dejar evidencia en log (no romper la app)
        Yii::error("RBAC: el rol '{$roleName}' no existe en auth_item. user_id={$userId}");
    }

    /**
     * Formatea errores de validación de manera legible.
     */
    private function formatErrors($model): string
    {
        $errors = $model->getFirstErrors();
        if (empty($errors)) {
            return 'Error desconocido.';
        }

        $out = [];
        foreach ($errors as $field => $msg) {
            $out[] = "{$field}: {$msg}";
        }
        return implode(' | ', $out);
    }
}
