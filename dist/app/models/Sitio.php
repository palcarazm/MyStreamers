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
     * Devuelve el tema del sitio
     *
     * @return string
     */
    public static function getTema():string
    {
        return self::query("SELECT valor FROM opciones WHERE opcion = 'tema'")[0]['valor'];
    }

    /**
     * Devuelve el directorio del tema del sitio
     *
     * @return string
     */
    public static function getTemaDir():string
    {
        return '/themes/'. self::getTema();
    }

    /**
     * Imprime el tema del sitio
     *
     * @return void
     */
    public static function printTema():void
    {
        echo self::getTema();
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
    protected static function createObject(mixed $record, array $fields =[]): array
    {
        return $record;
    }

    /**
     * Devuelve los objetos que coinciden con un patron
     *
     * @param string $pattern
     * @return void
     * @override
     */
    public static function search(string $pattern): array
    {
        return array_merge([], Usuario::search($pattern), Video::search($pattern));
    }
}
