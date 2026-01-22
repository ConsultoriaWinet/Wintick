<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $id
 * @property string $Nombre
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property int $status
 * @property string $rol
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $color
 * 
 *
 * @property Comentarios[] $comentarios
 * @property Notificaciones[] $notificaciones
 */
class Usuarios extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
public $password; 

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
              [['password_reset_token'], 'default', 'value' => null],
        [['status'], 'default', 'value' => 10],

        [['Nombre', 'email', 'rol', 'color'], 'required'],
        [['status', 'created_at', 'updated_at'], 'integer'],
        [['Nombre', 'password_hash', 'password_reset_token', 'email', 'rol'], 'string', 'max' => 255],
        [['color'], 'string', 'max' => 20],

        [['Nombre'], 'unique'],
        [['email'], 'unique'],
        [['password_reset_token'], 'unique'],

      
        [['password'], 'required', 'on' => 'create'],
        [['password'], 'string', 'min' => 6],

      
        [['password'], 'safe', 'on' => 'update'],




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
            'password_hash' => 'Password ',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'rol' => 'Rol',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'color' => 'Color',
        ];
    }

    //Seccion de login 




    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * Finds user by username
     */
    public static function findByUsername($username)
    {
        return static::findOne(['Nombre' => $username]);
    }

    /**
     * Finds user by ID
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * Finds user by password reset token
     */
    public static function findByPasswordResetToken($token)
    {
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if ($timestamp + $expire < time()) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->password_hash;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // Si viene password en claro, lo hasheas UNA sola vez
        if (!empty($this->password)) {
            $this->setPassword($this->password); // ✅ sin generatePasswordHash aquí
        }

        return true;
    }

    /**
     * Validates password
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /////////////////////////////////////////////









    /**
     * Gets query for [[Comentarios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComentarios()
    {
        return $this->hasMany(Comentarios::class, ['usuario_id' => 'id']);
    }

    /**
     * Gets query for [[Notificaciones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotificaciones()
    {
        return $this->hasMany(Notificaciones::class, ['usuario_id' => 'id']);
    }

    //////////////Asignacion de ROLES (RBACController)//////////////
    public function getRole()
    {
        return $this->rol;
    }
    /**
     * Sincroniza el rol guardado en la BD con RBAC al hacer login.
     */
    public function afterLogin()
    {
        $auth = Yii::$app->authManager;

        // Limpiar asignaciones previas
        $auth->revokeAll($this->id);

        // Asignar el rol guardado en la BD
        $role = $auth->getRole($this->rol);
        if ($role !== null) {
            $auth->assign($role, $this->id);
        }
    }

}
