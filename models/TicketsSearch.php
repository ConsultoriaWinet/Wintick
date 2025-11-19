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
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'Asignado_a', 'TiempoRestante', 'HoraFinalizo', 'TiempoEfectivo', 'Cliente_id', 'Sistema_id', 'Servicio_id', 'Creado_por'], 'integer'],
            [['Folio', 'Usuario_reporta', 'Estado', 'Descripcion', 'Solucion', 'HoraProgramada', 'HoraInicio', 'Fecha_creacion', 'Fecha_actualizacion'], 'safe'],
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
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'Asignado_a' => $this->Asignado_a,
            'HoraProgramada' => $this->HoraProgramada,
            'HoraInicio' => $this->HoraInicio,
            'TiempoRestante' => $this->TiempoRestante,
            'HoraFinalizo' => $this->HoraFinalizo,
            'TiempoEfectivo' => $this->TiempoEfectivo,
            'Cliente_id' => $this->Cliente_id,
            'Sistema_id' => $this->Sistema_id,
            'Servicio_id' => $this->Servicio_id,
            'Creado_por' => $this->Creado_por,
            'Fecha_creacion' => $this->Fecha_creacion,
            'Fecha_actualizacion' => $this->Fecha_actualizacion,
        ]);

        $query->andFilterWhere(['like', 'Folio', $this->Folio])
            ->andFilterWhere(['like', 'Usuario_reporta', $this->Usuario_reporta])
            ->andFilterWhere(['like', 'Estado', $this->Estado])
            ->andFilterWhere(['like', 'Descripcion', $this->Descripcion])
            ->andFilterWhere(['like', 'Solucion', $this->Solucion]);

        return $dataProvider;
    }
}
