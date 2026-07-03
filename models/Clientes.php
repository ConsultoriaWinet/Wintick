<?php

namespace app\models;

use Yii;

/**
 * @property int $id
 * @property string $Nombre
 * @property string|null $Razon_social
 * @property string|null $RFC
 * @property string|null $Correo         JSON: [{"label":"...","valor":"..."}]
 * @property string|null $Contacto_nombre
 * @property string $Tiempo
 * @property string|null $Whatsapp_contacto  JSON: [{"label":"...","valor":"..."}]
 * @property string|null $Telefono           JSON: [{"label":"...","valor":"..."}]
 * @property string $Prioridad
 * @property string $Criticidad
 * @property int $Estado
 * @property int $created_at
 * @property int $updated_at
 */
class Clientes extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'clientes';
    }

    public function rules()
    {
        return [
            [['RFC', 'Razon_social', 'Contacto_nombre', 'Telefono', 'Whatsapp_contacto', 'Correo'], 'default', 'value' => null],
            [['Tiempo'], 'default', 'value' => '0'],
            [['Tipo_servicio'], 'default', 'value' => 'POLIZA'],
            [['Estado'], 'default', 'value' => 1],
            [['Nombre', 'Prioridad', 'Criticidad', 'created_at', 'updated_at'], 'required'],
            [['Estado', 'created_at', 'updated_at'], 'integer'],
            [['Nombre', 'Razon_social', 'RFC', 'Contacto_nombre', 'Prioridad'], 'string', 'max' => 255],
            [['Telefono', 'Whatsapp_contacto', 'Correo'], 'string'],
            [['RFC'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Nombre' => 'Nombre',
            'Razon_social' => 'Razón Social',
            'RFC' => 'RFC',
            'Correo' => 'Correos',
            'Contacto_nombre' => 'Contacto Principal',
            'Tiempo' => 'Tiempo',
            'Whatsapp_contacto' => 'WhatsApp',
            'Telefono' => 'Teléfonos',
            'Prioridad' => 'Prioridad',
            'Criticidad' => 'Criticidad',
            'Tipo_servicio' => 'Tipo de Servicio',
            'Estado' => 'Estado',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

    /** Devuelve el array decodificado de teléfonos. */
    public function getTelefonos(): array
    {
        if (empty($this->Telefono)) {
            return [];
        }

        $telefonos = json_decode($this->Telefono, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($telefonos)) {
            return $telefonos;
        }

        // Compatibilidad con registros antiguos
        return [$this->Telefono];
    }

    /** Devuelve el array decodificado de WhatsApps. */
    public function getWhatsapps(): array
    {
        if (empty($this->Whatsapp_contacto)) {
            return [];
        }

        $datos = json_decode($this->Whatsapp_contacto, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($datos)) {
            return $datos;
        }

        return [$this->Whatsapp_contacto];
    }

    /** Devuelve el array decodificado de correos. */
    public function getCorreos(): array
    {
        return json_decode($this->Correo ?? '[]', true) ?: [];
    }

    /** Primer teléfono disponible (para mostrar en índice). */
    public function getPrimerTelefono(): string
    {
        $lista = $this->getTelefonos();
        return $lista[0]['valor'] ?? '';
    }

    /** Primer WhatsApp disponible (para mostrar en índice). */
    public function getPrimerWhatsapp(): string
    {
        $lista = $this->getWhatsapps();
        return $lista[0]['valor'] ?? '';
    }

    /** Primer correo disponible (para mostrar en índice). */
    public function getPrimerCorreo(): string
    {
        $lista = $this->getCorreos();
        return $lista[0]['valor'] ?? '';
    }
}
