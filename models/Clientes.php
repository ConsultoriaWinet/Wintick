<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clientes".
 *
 * @property int $id
 * @property string $Nombre
 * @property string $Razon_social
 * @property string|null $RFC
 * @property string $Correo
 * @property string $Contacto_nombre
 * @property int $Tiempo
 * @property int $Whatsapp_contacto
 * @property int $Telefono
 * @property int $Estado
 * @property int $created_at
 * @property int $updated_at
 */
class Clientes extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clientes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['RFC'], 'default', 'value' => null],
            [['Estado'], 'default', 'value' => 1],
            [['Nombre', 'Razon_social', 'Correo', 'Contacto_nombre', 'Tiempo', 'Whatsapp_contacto', 'Telefono', 'created_at', 'updated_at'], 'required'],
            [['Tiempo', 'Whatsapp_contacto', 'Telefono', 'Estado', 'created_at', 'updated_at'], 'integer'],
            [['Nombre', 'Razon_social', 'RFC', 'Correo', 'Contacto_nombre'], 'string', 'max' => 255],
            [['RFC'], 'unique'],
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
            'Razon_social' => 'Razon Social',
            'RFC' => 'Rfc',
            'Correo' => 'Correo',
            'Contacto_nombre' => 'Contacto Nombre',
            'Tiempo' => 'Tiempo',
            'Whatsapp_contacto' => 'Whatsapp Contacto',
            'Telefono' => 'Telefono',
            'Estado' => 'Estado',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
