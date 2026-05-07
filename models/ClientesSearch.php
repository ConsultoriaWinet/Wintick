<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Clientes;

/**
 * ClientesSearch represents the model behind the search form of `app\models\Clientes`.
 */
class ClientesSearch extends Clientes
{
    /** Búsqueda universal (server-side) */
    public $q;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'Tiempo', 'Whatsapp_contacto', 'Telefono', 'Estado', 'created_at', 'updated_at'], 'integer'],
            [['Nombre', 'Razon_social', 'RFC', 'Correo', 'Criticidad', 'Contacto_nombre', 'Prioridad', 'Tipo_servicio', 'q'], 'safe'],
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
    public function search($params, $pageSize = 20, $formName = null)
    {
        $query = Clientes::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Búsqueda universal: OR LIKE en los campos más importantes
        if (!empty($this->q)) {
            $query->andWhere(['or',
                ['like', 'Nombre',          $this->q],
                ['like', 'Razon_social',    $this->q],
                ['like', 'RFC',             $this->q],
                ['like', 'Correo',          $this->q],
                ['like', 'Contacto_nombre', $this->q],
            ]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'Tiempo' => $this->Tiempo,
            'Whatsapp_contacto' => $this->Whatsapp_contacto,
            'Telefono' => $this->Telefono,
            'Estado' => $this->Estado,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'Nombre', $this->Nombre])
            ->andFilterWhere(['like', 'Razon_social', $this->Razon_social])
            ->andFilterWhere(['like', 'RFC', $this->RFC])
            ->andFilterWhere(['like', 'Correo', $this->Correo])
            ->andFilterWhere(['like', 'Contacto_nombre', $this->Contacto_nombre])
            ->andFilterWhere(['like', 'Prioridad', $this->Prioridad])
            ->andFilterWhere(['like', 'Tipo_servicio', $this->Tipo_servicio]);

        return $dataProvider;
    }
}
