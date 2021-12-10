<?php

namespace Model;

class Sitio extends ActiveRecord
{
    /**
     * Devuelve el título del sitio
     *
     * @return string
     */
    public static function getTitulo():string
    {
        return self::query("SELECT valor FROM opciones WHERE opcion = 'titulo'")[0]['valor'];
    }

    /**
     * Imprime el título del sitio
     *
     * @return void
     */
    public static function printTitulo():void
    {
        echo self::getTitulo();
    }
    
    /**
     * Devuelve la descripción del sitio
     *
     * @return string
     */
    public static function getDescripcion():string
    {
        return self::query("SELECT valor FROM opciones WHERE opcion = 'descripcion'")[0]['valor'];
    }

    /**
     * Imprime la descripción del sitio
     *
     * @return void
     */
    public static function printDescripcion():void
    {
        echo self::getDescripcion();
    }

    /**
     * Devuelve el estado del módulo indicado
     *
     * @param string $modulo
     * @return bool Verdadero si activo
     */
    public static function isEnabled(string $modulo):bool
    {
        return self::query("SELECT activo FROM modulos WHERE modulo = '{$modulo}'")[0]['activo'];
    }

    /**
     * Crea un objeto con los datos indicados
     *
     * @param mixed $record
     * @return array
     */
    protected static function createObject(mixed $record, array $fields =[])
    {
        return $record;
    }
}
