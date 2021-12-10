<?php

namespace Model;

class Usuario extends ActiveRecord
{
    protected static String $table = 'users';
    protected static String $date ='actualizado';
    protected static array $joins = array('roles'=>'id_rol');
    protected static String $defaultOrder = 'PK_id_user';
    protected static array $colDB = ['PK_id_user', 'username', 'email', 'pass', 'actualizado', 'FK_id_rol'];
    protected static String $PK = 'PK_id_user';

    protected $PK_id_user;
    public $username;
    public $email;
    public $pass;
    protected $actualizado;
    public $FK_id_rol;
    public $rol;

    public function __construct($args = [])
    {
        $this->PK_id_user = $args['PK_id_user'] ?? null;
        $this->username = $args['username'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->pass = $args['pass'] ?? '';
        $this->FK_id_rol = $args['FK_id_rol'] ?? '';
        $this->actualizado = $args['actualizado'] ?? null;
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
        }else{
            if($this->checkval('username' , 's')){
                self::$errors[]   = 'Este nombre de usuario dya se encuentra registrado';
            }
        }
        if (!$this->email) {
            self::$errors[]   = 'El correo electrónico es obligatorio';
        }else{
            if($this->checkval('email' , 's')){
                self::$errors[]   = 'Este correo electrónico ya se encuentra registrado';
            }
        }
        if (!$this->pass) {
            self::$errors[] = 'La contraseña es obligatoria';
        }

        return empty(self::$errors);
    }

    /**
     * Crea un objeto con los datos indicados
     *
     * @param mixed $record
     * @param array $fields
     * @return object
     */
    protected static function createObject(mixed $record , array $fields)
    {
        $obj = new static;
        $rol_values =[];
        foreach ($record as $key => $value) {
            if (property_exists($obj, $key) && $fields[$key] == static::$table) {
                $obj->$key = $value;
            }elseif( $fields[$key] == 'roles'){
                $rol_values[$key] = $value;
            }
        }
        $obj->rol = new Rol($rol_values);
        return $obj;
    }
}
