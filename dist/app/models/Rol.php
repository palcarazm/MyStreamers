<?php

namespace Model;

class Rol extends ActiveRecord
{
    protected static String $table = 'roles';
    protected static String $date ='actualizado';
    protected static String $defaultOrder = 'PK_id_rol';
    protected static array $colDB = ['PK_id_rol', 'rol', 'usuarios_perms', 'participantes_perms', 'eventos_crear_perms', 'eventos_publicar_perms', 'noticias_crear_perms', 'noticias_publicar_perms', 'normas_crear_perms', 'normas_publicar_perms', 'config_perms','actualizado'];
    protected static String $PK = 'PK_id_rol';

    const PERMS_USUARIOS = 'usuarios_perms';
    const PERMS_PARTICIPANTES = 'participantes_perms';
    const PERMS_EVENTOS_CREAR = 'eventos_crear_perms';
    const PERMS_EVENTOS_PUBLICAR = 'eventos_publicar_perms';
    const PERMS_NOTICIAS_CREAR = 'noticias_crear_perms';
    const PERMS_NOTICIAS_PUBLICAR = 'noticias_publicar_perms';
    const PERMS_NORMAS_CREAR = 'normas_crear_perms';
    const PERMS_NORMAS_PUBLICAR = 'normas_publicar_perms';
    const PERMS_CONFIG = 'config_perms';

    protected $PK_id_rol;
    public $rol;
    public $usuarios_perms;
    public $participantes_perms;
    public $eventos_crear_perms;
    public $eventos_publicar_perms;
    public $noticias_crear_perms;
    public $normas_crear_perms;
    public $normas_publicar_perms;
    public $config_perms;
    protected $actualizado;

    public function __construct($args = [])
    {
        $this->PK_id_rol = $args['PK_id_rol'] ?? null;
        $this->rol = $args['rol'] ?? '';
        $this->usuarios_perms = $args['usuarios_perms'] ?? false;
        $this->participantes_perms = $args['participantes_perms'] ?? false;
        $this->eventos_crear_perms = $args['eventos_crear_perms'] ?? false;
        $this->eventos_publicar_perms = $args['eventos_publicar_perms'] ?? false;
        $this->noticias_crear_perms = $args['noticias_crear_perms'] ?? false;
        $this->noticias_publicar_perms = $args['noticias_publicar_perms'] ?? false;
        $this->normas_crear_perms = $args['normas_crear_perms'] ?? false;
        $this->normas_publicar_perms = $args['normas_publicar_perms'] ?? false;
        $this->config_perms = $args['config_perms'] ?? false;
        $this->actualizado = $args['actualizado'] ?? null;
    }

    /**
     * Verifica si dispone de los permisos requieridos
     *
     * @param string $perms Permisos a verificar
     * @return boolean Dispone de permisos (Y/N) devuelve null si permiso desconocido
     */
    public function can(string $perms):bool|null
    {
        return isset($this->$perms) ? $this->$perms : null;
    }
}
