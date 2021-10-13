<?php

namespace Model;

class Usuario extends ActiveRecord
{
    protected static $tabla = 'users';
    protected static $defaultOrder = 'PK_id_user';
    protected static $colDB = ['PK_id_user', 'username ', 'email', 'pass', 'actualizado', 'FK_id_rol'];
    protected static $PK = 'PK_id_user';

    public $PK_id_user;
    public $username;
    public $email;
    public $pass;
    public $actualizado;
    public $FK_id_rol;
    //public $rol;

    public function __construct($args = [])
    {
        $this->PK_id_user = $args['PK_id_user'] ?? null;
        $this->username = $args['username'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->pass = $args['pass'] ?? '';
        $this->actualizado = $args['actualizado'] ?? '';
        $this->FK_id_rol = $args['FK_id_rol'] ?? '';
        //$this->rol = $args[''] ?? '';
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
}
