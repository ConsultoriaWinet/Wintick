<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Modelo de log completo para desarrolladores.
 * Registra absolutamente todos los eventos del sistema.
 *
 * Uso:
 *   DevLog::log('login', 'Usuario admin inició sesión desde 192.168.1.1', [...datos...], 'site');
 *   DevLog::log('crear', "Ticket #0042 creado por Juan", ['folio' => '0042', 'cliente' => 'ACME'], 'tickets', 42, 'Tickets');
 *   DevLog::log('eliminar', "Cliente ACME eliminado", ['nombre' => 'ACME', 'rfc' => 'ACM010101'], 'clientes', 5, 'Clientes');
 *
 * @property int         $id
 * @property int|null    $usuario_id
 * @property string|null $usuario_nombre
 * @property string|null $usuario_rol
 * @property string      $tipo
 * @property string|null $modulo
 * @property string      $accion
 * @property string|null $modelo
 * @property int|null    $modelo_id
 * @property string|null $datos          JSON con datos completos
 * @property string|null $ip
 * @property string|null $user_agent
 * @property string      $created_at
 */
class DevLog extends ActiveRecord
{
    // ─── Tipos de evento ────────────────────────────────────────────────────
    const TIPO_LOGIN      = 'login';
    const TIPO_LOGOUT     = 'logout';
    const TIPO_CREAR      = 'crear';
    const TIPO_ACTUALIZAR = 'actualizar';
    const TIPO_ELIMINAR   = 'eliminar';
    const TIPO_VISTA      = 'vista';
    const TIPO_ERROR      = 'error';
    const TIPO_SISTEMA    = 'sistema';

    public static function tableName(): string
    {
        return 'dev_log';
    }

    // ─── Método principal de logging ────────────────────────────────────────

    /**
     * Registra un evento en el log del desarrollador.
     *
     * @param string   $tipo      Tipo de evento (usar constantes TIPO_*)
     * @param string   $accion    Descripción legible del evento
     * @param array    $datos     Datos adicionales (antes/después, parámetros, etc.)
     * @param string   $modulo    Módulo o controlador origen (tickets, clientes, etc.)
     * @param int|null $modeloId  ID del registro afectado
     * @param string   $modelo    Nombre de la clase del modelo afectado
     */
    public static function log(
        string $tipo,
        string $accion,
        array  $datos    = [],
        string $modulo   = '',
        ?int   $modeloId = null,
        string $modelo   = ''
    ): void {
        // ── Recopilar datos del usuario (cada llamada individual en try por seguridad) ──
        $usuarioId     = null;
        $usuarioNombre = 'Anónimo';
        $usuarioRol    = 'Sin sesión';
        try {
            if (!Yii::$app->user->isGuest) {
                $identity      = Yii::$app->user->identity;
                $usuarioId     = $identity->id     ?? null;
                $usuarioNombre = $identity->Nombre ?? ($identity->email ?? 'N/A');
                $usuarioRol    = $identity->rol    ?? 'N/A';
            }
        } catch (\Throwable $e) { /* usuario no disponible */ }

        $ip        = '0.0.0.0';
        $userAgent = 'N/A';
        $url       = '';
        $method    = 'GET';
        try {
            $ip        = $_SERVER['REMOTE_ADDR']     ?? '0.0.0.0';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
            $url       = $_SERVER['REQUEST_URI']     ?? '';
            $method    = $_SERVER['REQUEST_METHOD']  ?? 'GET';
        } catch (\Throwable $e) { /* variables de servidor no disponibles */ }

        $datos['_env'] = ['url' => $url, 'method' => $method, 'ts' => date('H:i:s')];

        $datosJson = null;
        try {
            $datosJson = json_encode($datos, JSON_UNESCAPED_UNICODE) ?: null;
        } catch (\Throwable $e) { /* json encoding failed */ }

        // ── Inserción directa con SQL preparado (no usa ActiveRecord para evitar fallos en cadena) ──
        try {
            Yii::$app->db->createCommand(
                'INSERT INTO dev_log
                 (usuario_id, usuario_nombre, usuario_rol, tipo, modulo, accion, modelo, modelo_id, datos, ip, user_agent, created_at)
                 VALUES
                 (:uid, :unombre, :urol, :tipo, :modulo, :accion, :modelo, :modeloid, :datos, :ip, :ua, NOW())'
            )->bindValues([
                ':uid'      => $usuarioId,
                ':unombre'  => mb_substr($usuarioNombre, 0, 255),
                ':urol'     => mb_substr($usuarioRol, 0, 100),
                ':tipo'     => $tipo,
                ':modulo'   => $modulo ?: null,
                ':accion'   => mb_substr($accion, 0, 65535),
                ':modelo'   => $modelo ?: null,
                ':modeloid' => $modeloId,
                ':datos'    => $datosJson,
                ':ip'       => mb_substr($ip, 0, 45),
                ':ua'       => mb_substr($userAgent, 0, 500),
            ])->execute();
        } catch (\Throwable $e) {
            // Último recurso: log en archivo del sistema
            error_log('[DevLog] Fallo al guardar log tipo=' . $tipo . ' error=' . $e->getMessage());
        }
    }

    // ─── Helpers de presentación ────────────────────────────────────────────

    public static function tipoLabels(): array
    {
        return [
            self::TIPO_LOGIN      => 'Inicio de Sesión',
            self::TIPO_LOGOUT     => 'Cierre de Sesión',
            self::TIPO_CREAR      => 'Crear',
            self::TIPO_ACTUALIZAR => 'Actualizar',
            self::TIPO_ELIMINAR   => 'Eliminar',
            self::TIPO_VISTA      => 'Vista',
            self::TIPO_ERROR      => 'Error',
            self::TIPO_SISTEMA    => 'Sistema',
        ];
    }

    public static function tipoColors(): array
    {
        return [
            self::TIPO_LOGIN      => '#3b82f6',  // azul
            self::TIPO_LOGOUT     => '#6b7280',  // gris
            self::TIPO_CREAR      => '#22c55e',  // verde
            self::TIPO_ACTUALIZAR => '#f59e0b',  // naranja
            self::TIPO_ELIMINAR   => '#ef4444',  // rojo
            self::TIPO_VISTA      => '#8BA590',  // verde sage
            self::TIPO_ERROR      => '#dc2626',  // rojo oscuro
            self::TIPO_SISTEMA    => '#7c3aed',  // morado
        ];
    }

    public static function tipoIcons(): array
    {
        return [
            self::TIPO_LOGIN      => 'bi-box-arrow-in-right',
            self::TIPO_LOGOUT     => 'bi-box-arrow-right',
            self::TIPO_CREAR      => 'bi-plus-circle-fill',
            self::TIPO_ACTUALIZAR => 'bi-pencil-fill',
            self::TIPO_ELIMINAR   => 'bi-trash-fill',
            self::TIPO_VISTA      => 'bi-eye-fill',
            self::TIPO_ERROR      => 'bi-exclamation-circle-fill',
            self::TIPO_SISTEMA    => 'bi-gear-fill',
        ];
    }

    public function getTipoLabel(): string
    {
        return self::tipoLabels()[$this->tipo] ?? $this->tipo;
    }

    public function getTipoColor(): string
    {
        return self::tipoColors()[$this->tipo] ?? '#6b7280';
    }

    public function getTipoIcon(): string
    {
        return self::tipoIcons()[$this->tipo] ?? 'bi-circle';
    }

    /** Decodifica el JSON de datos para mostrar en la vista */
    public function getDatosDecodificados(): array
    {
        if (!$this->datos) {
            return [];
        }
        $decoded = json_decode($this->datos, true);
        return is_array($decoded) ? $decoded : [];
    }

    // ─── Relaciones ─────────────────────────────────────────────────────────

    public function getUsuario()
    {
        return $this->hasOne(Usuarios::class, ['id' => 'usuario_id']);
    }

    // ─── Estadísticas rápidas ────────────────────────────────────────────────

    /** Conteo de eventos del día actual por tipo */
    public static function statsHoy(): array
    {
        $hoy = date('Y-m-d');
        $rows = self::find()
            ->select(['tipo', 'COUNT(*) AS total'])
            ->where(['>=', 'created_at', $hoy . ' 00:00:00'])
            ->groupBy('tipo')
            ->asArray()
            ->all();

        $stats = [];
        foreach ($rows as $r) {
            $stats[$r['tipo']] = (int)$r['total'];
        }
        return $stats;
    }

    /** Últimos N minutos de actividad única de usuarios */
    public static function usuariosActivos(int $minutos = 30): int
    {
        $desde = date('Y-m-d H:i:s', strtotime("-{$minutos} minutes"));
        return (int) self::find()
            ->select('usuario_id')
            ->distinct()
            ->where(['>=', 'created_at', $desde])
            ->andWhere(['IS NOT', 'usuario_id', null])
            ->count();
    }
}
