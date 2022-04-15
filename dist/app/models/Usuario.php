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
    protected static array $colDB = ['PK_id_user', 'username', 'email', 'pass', 'actualizado', 'FK_id_rol', 'otp', 'otp_valid', 'imagen', 'bloqueado'];
    protected static String $PK = 'PK_id_user';

    protected $PK_id_user;
    public $username;
    public $email;
    public $pass;
    protected $actualizado;
    public $FK_id_rol;
    public $rol;
    protected $otp;
    protected $otp_valid;
    public $imagen;
    protected $bloqueado;

    public function __construct($args = [])
    {
        $this->PK_id_user = $args['PK_id_user'] ?? null;
        $this->username = $args['username'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->pass = $args['pass'] ?? '';
        $this->FK_id_rol = $args['FK_id_rol'] ?? '';
        $this->actualizado = $args['actualizado'] ?? null;
        $this->otp = $args['otp'] ?? null;
        $this->otp_valid = $args['otp_valid'] ?? null;
        $this->imagen = $args['imagen'] ?? null;
        $this->bloqueado = $args['bloqueado'] ?? null;
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
        $this->bloqueado = true;
        return $this->save;
    }

    /**
     * Desbloquea al usuario
     *
     * @return boolean Desbloqueado con éxito (S/N)
     */
    public function desbloquear(): bool
    {
        $this->bloqueado = false;
        return $this->save;
    }

    /**
     * Crea un objeto con los datos indicados
     *
     * @param mixed $record
     * @param array $fields
     * @return object
     */
    protected static function createObject(mixed $record, array $fields)
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
}
