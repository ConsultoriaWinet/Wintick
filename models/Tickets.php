<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tickets".
 *
 * @property int $id
 * @property string $Folio
 * @property string $Usuario_reporta
 * @property int $Asignado_a
 * @property string $Estado
 * @property string $Descripcion
 * @property string|null $Solucion
 * @property string|null $HoraProgramada
 * @property string|null $HoraInicio
 * @property int|null $TiempoRestante
 * @property int|null $HoraFinalizo
 * @property int|null $TiempoEfectivo
 * @property int $Cliente_id
 * @property int $Sistema_id
 * @property int $Servicio_id
 * @property int $Creado_por
 * @property string $Fecha_creacion
 * @property string $Fecha_actualizacion
 *
 * @property Comentarios[] $comentarios
 * @property Notificaciones[] $notificaciones
 */
class Tickets extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tickets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Solucion', 'HoraProgramada', 'HoraInicio', 'TiempoRestante', 'HoraFinalizo', 'TiempoEfectivo'], 'default', 'value' => null],
            [['Folio', 'Usuario_reporta', 'Asignado_a', 'Estado', 'Descripcion', 'Cliente_id', 'Sistema_id', 'Servicio_id', 'Creado_por'], 'required'],
            [['Asignado_a', 'TiempoRestante', 'HoraFinalizo', 'TiempoEfectivo', 'Cliente_id', 'Sistema_id', 'Servicio_id', 'Creado_por'], 'integer'],
            [['Descripcion', 'Solucion'], 'string'],
            [['HoraProgramada', 'HoraInicio', 'Fecha_creacion', 'Fecha_actualizacion'], 'safe'],
            [['Folio', 'Usuario_reporta', 'Estado'], 'string', 'max' => 255],
            [['Folio'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Folio' => 'Folio',
            'Usuario_reporta' => 'Usuario Reporta',
            'Asignado_a' => 'Asignado A',
            'Estado' => 'Estado',
            'Descripcion' => 'Descripcion',
            'Solucion' => 'Solucion',
            'HoraProgramada' => 'Hora Programada',
            'HoraInicio' => 'Hora Inicio',
            'TiempoRestante' => 'Tiempo Restante',
            'HoraFinalizo' => 'Hora Finalizo',
            'TiempoEfectivo' => 'Tiempo Efectivo',
            'Cliente_id' => 'Cliente ID',
            'Sistema_id' => 'Sistema ID',
            'Servicio_id' => 'Servicio ID',
            'Creado_por' => 'Creado Por',
            'Fecha_creacion' => 'Fecha Creacion',
            'Fecha_actualizacion' => 'Fecha Actualizacion',
        ];
    }

    /**
     * Gets query for [[Comentarios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComentarios()
    {
        return $this->hasMany(Comentarios::class, ['ticket_id' => 'id']);
    }

    /**
     * Gets query for [[Notificaciones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotificaciones()
    {
        return $this->hasMany(Notificaciones::class, ['ticket_id' => 'id']);
    }

}
