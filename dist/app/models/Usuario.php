<?php

namespace Model;

use DateTime;
use Exception;

class Usuario extends ActiveRecord
{
    protected static String $table = 'users';
    protected static String $date = 'actualizado';
    protected static array $joins = array('roles' => 'id_rol');
    protected static String $image = 'imagen';
    protected static String $imageDefault = '/user.png';
    protected static String $defaultOrder = 'PK_id_user';
    protected static array $colDB = ['PK_id_user', 'username', 'email', 'pass', 'actualizado', 'FK_id_rol', 'otp', 'otp_valid', 'imagen', 'bloqueado', 'descripcion', 'perfil_publico', 'twitch_user'];
    protected static String $PK = 'PK_id_user';
    protected static array $searchTerm = ['username'];

    protected int|null $PK_id_user;
    public string $username;
    public string $email;
    public string|null $pass;
    protected string|null $actualizado;
    public int $FK_id_rol;
    public Rol $rol;
    protected string|null $otp;
    protected string|null $otp_valid;
    public string|null $imagen;
    protected bool|null $bloqueado;
    public string|null $descripcion;
    protected bool|null $perfil_publico;
    public string|null $twitch_user;

    public function __construct($args = [])
    {
        $this->PK_id_user = is_null($args['PK_id_user'] ?? null) ? null : (int) $args['PK_id_user'];
        $this->username = (string) ($args['username'] ?? '');
        $this->email = (string) ($args['email'] ?? '');
        $this->pass = (string) ($args['pass'] ?? '');
        $this->FK_id_rol = (int) ($args['FK_id_rol'] ?? '');
        $this->actualizado = is_null($args['actualizado'] ?? null) ? null : (string) $args['actualizado'];
        $this->otp = is_null($args['otp'] ?? null) ? null : (string) $args['otp'];
        $this->otp_valid = is_null($args['otp_valid'] ?? null) ? null : (string) $args['otp_valid'];
        $this->imagen = is_null($args['imagen'] ?? null) ? null : (string) $args['imagen'];
        $this->bloqueado = is_null($args['bloqueado'] ?? null) ? null : (bool) $args['bloqueado'];
        $this->descripcion = is_null($args['descripcion'] ?? null) ? null : (string) $args['descripcion'];
        $this->perfil_publico = is_null($args['perfil_publico'] ?? null) ? null : (bool) $args['perfil_publico'];
        $this->twitch_user = is_null($args['twitch_user'] ?? null) ? null : (string) $args['twitch_user'];
    }

    /**
     * Valida los datos del registros
     *
     * @return bool validación superada (Si/No)
     */
    public function validate(): bool
    {
        self::$errors = [];
        if (!$this->username) {
            self::$errors[]   = 'El nombre de usuario es obligatorio';
        } else {
            if ($this->checkval('username', 's') && is_null($this->PK_id_user)) {
                self::$errors[]   = 'Este nombre de usuario ya se encuentra registrado';
            }
        }
        if (!$this->email) {
            self::$errors[]   = 'El correo electrónico es obligatorio';
        } else {
            if ($this->checkval('email', 's')  && is_null($this->PK_id_user)) {
                self::$errors[]   = 'Este correo electrónico ya se encuentra registrado';
            }
        }
        if (!$this->pass) {
            self::$errors[] = 'La contraseña es obligatoria';
        }

        return empty(self::$errors);
    }

    /**
     * Cambia la contraseña del usuario
     *
     * @param String $pass Contraseña sin cifrar
     * @return boolean Completado con éxito (Si/No)
     */
    public function setPass(String $pass): bool
    {
        if (!checkPasswordStrength($pass)) {
            self::$errors[] = 'La contraseña no cumple los requisitos de seguridad.';
            return false;
        }
        $this->pass = password_hash($pass, PASSWORD_BCRYPT, array('cost' => 12));
        return $this->save();
    }

    /**
     * Valida la contraseña de usuario
     *
     * @param String $pass
     * @return boolean Contraseña correcta (Si/No)
     */
    public function validatePass(String $pass): bool
    {
        return password_verify($pass, $this->pass);
    }

    /**
     * Verifica si el usuario está bloqueado
     *
     * @return boolean Bloueado (S/N)
     */
    public function isBlocked(): bool
    {
        return $this->bloqueado;
    }

    /**
     * Bloquea al usuario
     *
     * @return boolean Bloqueado con éxito (S/N)
     */
    public function bloquear(): bool
    {
        $this->bloqueado = 1;
        return $this->save();
    }

    /**
     * Desbloquea al usuario
     *
     * @return boolean Desbloqueado con éxito (S/N)
     */
    public function desbloquear(): bool
    {
        $this->bloqueado = 0;
        return $this->save();
    }

    /**
     * Verifica si el usuario tiene perfil público
     *
     * @return boolean perfil público (S/N)
     */
    public function hasProfile(): bool
    {
        return !is_null($this->perfil_publico);
    }

    /**
     * Verifica si el perfil público está publicado
     *
     * @return boolean|null Publicado (S/N), sin perfil devuelve null
     */
    public function isPublished(): bool|null
    {
        return $this->perfil_publico;
    }

    /**
     * Publica el perfil público
     *
     * @return boolean Publicado con éxito (S/N)
     */
    public function publicar(): bool
    {
        $this->perfil_publico = 1;
        return $this->save();
    }

    /**
     * Oculta el perfil público
     *
     * @return boolean Ocultado con éxito (S/N)
     */
    public function ocultar(): bool
    {
        $this->perfil_publico = 0;
        return $this->save();
    }

    /**
     * Cambia el usuario de Twitch 
     *
     * @param String $twitch_user Usuario de Twitch
     * @return boolean Completado con éxito (Si/No)
     */
    public function setTwitch(String $twitch_user): bool
    {
        $this->twitch_user = $twitch_user;
        return $this->save();
    }

    /**
     * Crea un objeto con los datos indicados
     *
     * @param mixed $record
     * @param array $fields
     * @return static
     */
    protected static function createObject(mixed $record, array $fields): static
    {
        $obj = new static;
        $rol_values = [];
        foreach ($record as $key => $value) {
            if (property_exists($obj, $key) && $fields[$key] == static::$table) {
                $obj->$key = $value;
            } elseif ($fields[$key] == 'roles') {
                $rol_values[$key] = $value;
            }
        }
        $obj->rol = new Rol($rol_values);
        return $obj;
    }

    /**
     * Buscar usuario por nombre o email
     *
     * @param String $usuario nombre o email
     * @return Usuario|null
     */
    public static function findUser(String $usuario): Usuario|null
    {
        $query = "SELECT * FROM " . static::$table;
        if (!empty(static::$joins)) {
            foreach (static::$joins as $table => $FK) {
                $query .= " INNER JOIN " . $table . " ON FK_" . $FK . " = PK_"  . $FK;
            }
        }
        $query .= " WHERE username = '${usuario}' OR email = '${usuario}'";
        $resultado = self::query($query);
        return array_shift($resultado);
    }

    /**
     * Obtiene los usuarios con perfil público activo
     *
     * @return array|null
     */
    public static function findActiveProfiles(): array|null
    {
        $query = "SELECT * FROM " . static::$table;
        if (!empty(static::$joins)) {
            foreach (static::$joins as $table => $FK) {
                $query .= " INNER JOIN " . $table . " ON FK_" . $FK . " = PK_"  . $FK;
            }
        }
        $query .= " WHERE perfil_publico = 1";
        return self::query($query);
    }

    /**
     * Obtiene los usuarios con perfil público activo con Twitch
     *
     * @return array|null
     */
    public static function findActiveStreamProfiles(): array|null
    {
        $query = "SELECT * FROM " . static::$table;
        if (!empty(static::$joins)) {
            foreach (static::$joins as $table => $FK) {
                $query .= " INNER JOIN " . $table . " ON FK_" . $FK . " = PK_"  . $FK;
            }
        }
        $query .= " WHERE perfil_publico = 1 AND twitch_user IS NOT NULL";
        return self::query($query);
    }

    /**
     * Crea un OTP para el usuario
     *
     * @return string Código OTP
     * @throws Exception OTP no guardado en base de datos
     */
    public function createOTP(): string
    {
        $OTP = strtoupper(bin2hex(random_bytes(8)));
        $this->otp = password_hash($OTP, PASSWORD_BCRYPT, array('cost' => 12));
        $this->otp_valid = date('Y-m-d H:i:s', strtotime("+2 day"));
        if ($this->save()) {
            return $OTP;
        } else {
            throw new Exception('Imposible guardar en la base de datos');
        }
    }

    /**
     * Elimina el OTP para el usuario
     *
     * @return boolean Completado con éxito (Si/No)
     */
    public function deleteOTP(): bool
    {
        $this->otp = null;
        $this->otp_valid = null;
        $query = "UPDATE " . static::$table . " SET ";
        $query .= "otp = NULL , otp_valid = NULL";
        $query .= " WHERE " . static::$PK . " = '" . self::$db->escape_string($this->{static::$PK}) . "'";

        if (self::$db->query($query)) {
            return true;
        } else {
            static::$errors[] = self::$db->error;
            return false;
        }
    }

    /**
     * Valida el OTP para el usuario
     *
     * @param String $otp OTP sin encriptar
     * @return boolean Completado con éxito (Si/No)
     */
    public function validateOTP(String $otp): bool
    {
        if (is_null($this->otp)) {
            static::$errors[] = 'No se encuentra código OTP para el usuario indicado';
            return false;
        }
        if (!(new DateTime($this->otp_valid))->diff(new DateTime("now"))->invert || !password_verify($otp, $this->otp)) {
            static::$errors[] = 'Código OTP incorrecto o expirado';
            return false;
        }
        return true;
    }

    /**
     * Verifica si dispone de los permisos requieridos
     *
     * @param string $perms Permisos a verificar
     * @return boolean Dispone de permisos (Y/N) devuelve null si permiso desconocido
     */
    public function can(string $perms): bool|null
    {
        return $this->rol->can($perms);
    }

    /**
     * Imprime la fila del usuario actual de la tabla de usuarios
     *
     * @return void
     */
    public function printRow(): void
    {
        include TEMPLATES_DIR . '/user/admin-row.php';
    }

    /**
     * Imprime la tarjeta del usuario actual del archivo
     *
     * @return void
     */
    public function printCard($link = 'PROFILE'): void
    {
        include TEMPLATES_DIR . '/user/public-card.php';
    }

    /**
     * Obtiene los enlaces del usuario
     *
     * @return array
     */
    public function getEnlaces(): array
    {
        return Enlace::findUserLinks($this->PK_id_user);
    }

    /**
     * Establece los enlaces del usuario
     *
     * @param array $enlaces [{FK_id_enlace,enlace}]
     * @return bool Enlaces establecidos (S/N)
     */
    public function setEnlaces(array $enlaces): bool
    {
        if (!Enlace::deleteUserLinks($this->PK_id_user)) {
            static::$errors[] = 'Error al borrar los enlaces previos.';
            return false;
        }
        foreach ($enlaces as $args) {
            $args['FK_id_user'] = $this->PK_id_user;
            $enlace = new Enlace($args);
            if (!$enlace->save()) {
                static::$errors[] = 'Error al guardar el enlace ' . $args['enlace'] . '.';
            }
        }
        return empty(static::$errors);
    }

    /**
     * Establece un canal de YouTube para el usuario
     * @param String $channels ID del canal de YouTube
     * @return bool Canal establecido (S/N)
     */
    public function setYoutubeChannel(String $channel): bool
    {
        $canal = new Canal(['FK_id_user' => $this->getID(), 'PK_id_canal' => $channel]);
        return $canal->save();
    }

    /**
     * Devuelve los canales de YouTube del usuario
     * @return array Canales
     */
    public function getYoutubeChannels(): array
    {
        return Canal::findByUserID($this->getID());
    }
}
