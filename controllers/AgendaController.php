<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Tickets;

class AgendaController extends Controller
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
        $userId = Yii::$app->user->id;

        $soloMios = Yii::$app->user->can('verTickets') && !Yii::$app->user->can('asignarTicket');

        $query = Tickets::find()
            ->with(['cliente', 'sistema', 'servicio', 'usuarioAsignado'])
            ->where(['not in', 'Estado', ['CERRADO', 'CERRADO_CLIENTE']])
            ->orderBy([
                'HoraProgramada' => SORT_ASC,
                'HoraInicio' => SORT_ASC,
            ]);

        if ($soloMios) {
            $query->andWhere(['Asignado_a' => $userId]);
        }

        $tickets = $query->all();

        return $this->render('index', [
            'tickets' => $tickets,
        ]);
    }
}