<?php
namespace Controllers;

use Model\Sitio;
use Router\Router;

class AdminController{
    /**
     * Página de configuración del sitio
     *
     * @param Router $router
     * @return void
     */
    public static function configSitio(Router $router)
    {
        $router->render('config/sitio','layout-admin',array(
            'title'=>'Configuración del Sitio',
            'titulo' => Sitio::getTitulo(),
            'tema' => Sitio::getTema(),
            'descripcion' => Sitio::getDescripcion(),
            'eventos' => Sitio::isEnabled("eventos"),
            'noticias' => Sitio::isEnabled("noticias"),
            'normas' => Sitio::isEnabled("normas"),
            'enlaces' => Sitio::isEnabled("enlaces")
        ));
    }
}