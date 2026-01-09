<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tickets;

/**
 * TicketsSearch represents the model behind the search form of `app\models\Tickets`.
 */
class TicketsSearch extends Tickets
{
    // Propiedades para filtros adicionales
    public $fecha_inicio;
    public $fecha_fin;
    public $mes;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'Asignado_a', 'TiempoRestante', 'HoraFinalizo', 'TiempoEfectivo', 'Cliente_id', 'Sistema_id', 'Servicio_id', 'Creado_por'], 'integer'],
            [['Folio', 'Usuario_reporta', 'Estado', 'Descripcion', 'Solucion', 'HoraProgramada', 'HoraInicio', 'Fecha_creacion', 'Fecha_actualizacion', 'fecha_inicio', 'fecha_fin', 'mes', 'Prioridad'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Tickets::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // ✅ LÓGICA DE USUARIO ASIGNADO - APLICAR FILTRO POR DEFECTO
        $asignadoParam = isset($params['asignado_a']) ? $params['asignado_a'] : null;
        if (!isset($params['asignado_a']) && !\Yii::$app->user->isGuest) {
            // Si no viene parámetro asignado_a, filtrar por usuario actual
            $query->andWhere(['Asignado_a' => \Yii::$app->user->id]);
        } elseif ($asignadoParam !== '' && $asignadoParam !== null) {
            // Si viene parámetro explícito, aplicarlo
            $query->andWhere(['Asignado_a' => $asignadoParam]);
        }
        // Si asignado_a === '', mostrar todos sin filtro de Asignado_a

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'TiempoRestante' => $this->TiempoRestante,
            'HoraFinalizo' => $this->HoraFinalizo,
            'TiempoEfectivo' => $this->TiempoEfectivo,
            'Cliente_id' => $this->Cliente_id,
            'Sistema_id' => $this->Sistema_id,
            'Servicio_id' => $this->Servicio_id,
            'Creado_por' => $this->Creado_por,
        ]);

        $query->andFilterWhere(['like', 'Folio', $this->Folio])
            ->andFilterWhere(['like', 'Usuario_reporta', $this->Usuario_reporta])
            ->andFilterWhere(['like', 'Estado', $this->Estado])
            ->andFilterWhere(['like', 'Descripcion', $this->Descripcion])
            ->andFilterWhere(['like', 'Solucion', $this->Solucion])
            ->andFilterWhere(['like', 'Prioridad', $this->Prioridad]);

        // ✅ FILTROS DE FECHA (HoraProgramada)
        // Filtro por mes (formato: YYYY-MM)
        if (!empty($this->mes)) {
            $primerDia = $this->mes . '-01 00:00:00';
            $ultimoDia = date('Y-m-t 23:59:59', strtotime($this->mes . '-01'));
            $query->andWhere(['>=', 'HoraProgramada', $primerDia])
                  ->andWhere(['<=', 'HoraProgramada', $ultimoDia]);
        } else {
            // Si no hay mes específico, filtrar por rango de fechas
            if (!empty($this->fecha_inicio)) {
                $query->andWhere(['>=', 'HoraProgramada', $this->fecha_inicio . ' 00:00:00']);
            }
            if (!empty($this->fecha_fin)) {
                $query->andWhere(['<=', 'HoraProgramada', $this->fecha_fin . ' 23:59:59']);
            }
        }

        return $dataProvider;
    }
}
