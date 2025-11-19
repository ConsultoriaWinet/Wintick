<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comentarios".
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $usuario_id
 * @property string $comentario
 * @property string|null $tipo comentario, nota_interna, solucion
 * @property string $fecha_creacion
 *
 * @property Tickets $ticket
 * @property Usuarios $usuario
 */
class Comentarios extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comentarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo'], 'default', 'value' => 'comentario'],
            [['ticket_id', 'usuario_id', 'comentario'], 'required'],
            [['ticket_id', 'usuario_id'], 'integer'],
            [['comentario'], 'string'],
            [['fecha_creacion'], 'safe'],
            [['tipo'], 'string', 'max' => 20],
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
            'ticket_id' => 'Ticket ID',
            'usuario_id' => 'Usuario ID',
            'comentario' => 'Comentario',
            'tipo' => 'Tipo',
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
