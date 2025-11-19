<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sistemas".
 *
 * @property int $id
 * @property string $Nombre
 * @property int $created_at
 * @property int $updated_at
 */
class Sistemas extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sistemas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Nombre', 'created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['Nombre'], 'string', 'max' => 255],
            [['Nombre'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Nombre' => 'Nombre',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
