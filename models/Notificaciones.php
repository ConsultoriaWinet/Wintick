<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notificaciones".
 *
 * @property int $id
 * @property int $usuario_id
 * @property int|null $ticket_id
 * @property string|null $tipo
 * @property string $titulo
 * @property string|null $mensaje
 * @property int|null $leida
 * @property string $fecha_creacion
 *
 * @property Tickets $ticket
 * @property Usuarios $usuario
 */
class Notificaciones extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notificaciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'tipo', 'mensaje'], 'default', 'value' => null],
            [['leida'], 'default', 'value' => 0],
            [['usuario_id', 'titulo'], 'required'],
            [['usuario_id', 'ticket_id', 'leida'], 'integer'],
            [['mensaje'], 'string'],
            [['fecha_creacion'], 'safe'],
            [['tipo'], 'string', 'max' => 50],
            [['titulo'], 'string', 'max' => 255],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tickets::class, 'targetAttribute' => ['ticket_id' => 'id']],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::class, 'targetAttribute' => ['usuario_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario_id' => 'Usuario ID',
            'ticket_id' => 'Ticket ID',
            'tipo' => 'Tipo',
            'titulo' => 'Titulo',
            'mensaje' => 'Mensaje',
            'leida' => 'Leida',
            'fecha_creacion' => 'Fecha Creacion',
        ];
    }

    /**
     * Gets query for [[Ticket]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Tickets::class, ['id' => 'ticket_id']);
    }

    /**
     * Gets query for [[Usuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::class, ['id' => 'usuario_id']);
    }

}
