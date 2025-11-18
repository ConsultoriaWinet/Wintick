<?php
// commands/RbacController.php

namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();


        echo "RBAC inicializado con éxito.\n";
        return ExitCode::OK;
    }
}