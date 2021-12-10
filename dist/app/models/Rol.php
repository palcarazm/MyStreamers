<?php

namespace Model;

class Rol extends ActiveRecord
{
    protected static String $table = 'roles';
    protected static String $date ='actualizado';
    protected static String $defaultOrder = 'PK_id_rol';
    protected static array $colDB = ['PK_id_rol', 'rol', 'usuarios_perms', 'participantes_perms', 'eventos_crear_perms', 'eventos_publicar_perms', 'noticias_crear_perms', 'normas_crear_perms', 'normas_publicar_perms', 'config_perms','actualizado'];
    protected static String $PK = 'PK_id_rol';

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
        debug($args);
        $this->PK_id_rol = $args['PK_id_rol'] ?? null;
        $this->rol = $args['rol'] ?? '';
        $this->usuarios_perms = $args['usuarios_perms'] ?? '';
        $this->participantes_perms = $args['participantes_perms'] ?? '';
        $this->eventos_crear_perms = $args['eventos_crear_perms'] ?? '';
        $this->eventos_publicar_perms = $args['eventos_publicar_perms'] ?? '';
        $this->noticias_crear_perms = $args['noticias_crear_perms'] ?? '';
        $this->normas_crear_perms = $args['normas_crear_perms'] ?? '';
        $this->normas_publicar_perms = $args['normas_publicar_perms'] ?? '';
        $this->config_perms = $args['config_perms'] ?? '';
        $this->actualizado = $args['actualizado'] ?? null;
    }
}
