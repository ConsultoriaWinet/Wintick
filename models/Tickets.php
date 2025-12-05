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
 * @property double|null $TiempoRestante
 * @property string|null $HoraFinalizo
 * @property double|null $TiempoEfectivo
 * @property int $Cliente_id
 * @property int $Sistema_id
 * @property int $Servicio_id
 * @property int $Creado_por
 * @property string $Fecha_creacion
 * @property string $Fecha_actualizacion
 *
 * @property Comentarios[] $comentarios
 * @property Notificaciones[] $notificaciones
 * @property Clientes $cliente
 * @property Clientes $clientes
 * @property Sistemas $sistema
 * @property Servicios $servicio
 * @property Usuarios $usuarioAsignado
 * @property Usuarios $usuarioCreador
 */
class Tickets extends \yii\db\ActiveRecord
{
    public $consultoresList;

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
            [['Asignado_a', 'Cliente_id', 'Sistema_id', 'Servicio_id', 'Creado_por'], 'integer'],
            [['Descripcion', 'Solucion'], 'string'],
            [['HoraProgramada', 'HoraInicio', 'HoraFinalizo', 'Fecha_creacion', 'Fecha_actualizacion'], 'safe'],
           
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

    /**
     * Gets query for [[Cliente]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Clientes::class, ['id' => 'Cliente_id']);
    }

    /**
     * Gets query for [[Clientes]] (alias).
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClientes()
    {
        return $this->getCliente();
    }

    /**
     * Gets query for [[Sistema]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSistema()
    {
        return $this->hasOne(Sistemas::class, ['id' => 'Sistema_id']);
    }

    /**
     * Gets query for [[Servicio]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServicio()
    {
        return $this->hasOne(Servicios::class, ['id' => 'Servicio_id']);
    }

    /**
     * Gets query for [[UsuarioAsignado]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioAsignado()
    {
        return $this->hasOne(Usuarios::class, ['id' => 'Asignado_a']);
    }

    /**
     * Gets query for [[UsuarioCreador]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioCreador()
    {
        return $this->hasOne(Usuarios::class, ['id' => 'Creado_por']);
    }
}
