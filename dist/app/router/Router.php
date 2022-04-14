<?php

namespace Router;

class Router
{
    public $routes = []; // Arreglo de rutas

    /**
     * Añade una ruta al arreglo de rutas
     *
     * @param String $method Método de solicitud
     * @param String $uri URI solicitada 
     * @param array|String $fn Función a invocar
     * @param string $perms permisos requeridos por defecto ninguno
     * @return void
     */
    public function add(String $method, String $uri, array|String $fn, string $perms = null): void
    {
        $this->routes[$method][$uri] = [
            'function' => $fn,
            'perms' => $perms
        ];
    }

    /**
     * Compureba si la ruta existe
     * @return void
     */

    public function route(): void
    {
        $currentURL = explode('?', $_SERVER['REQUEST_URI'] ?? '/', 2)[0]; // Ruta solicitada
        $method = $_SERVER['REQUEST_METHOD']; //Método de solicitud
        $fn = $this->routes[$method][$currentURL]['function'] ?? null;
        $perms = $this->routes[$method][$currentURL]['perms'];
        $isPublic = is_null($perms);

        if ($fn) { // Página existe
            if(!$isPublic){ //No es publica
                if (!isAuth()) { // No está authentificado
                    header('Location:/login?dest=' . $currentURL);
                }
                if(!getAuthUser()->can($perms)){
                    if(preg_match('/^\/api/',$currentURL)){
                        $this->render('api/api', 'layout-api', array('response' => array(
                            'status' => 403,
                            'message' => 'Acceso no autorizado',
                            'content' => array()
                        )));
                    }else{
                        $this->render('public/403','layout-public',array('title'=>'Acceso no autorizado'));
                    }
                    return;
                }
            }
            call_user_func($fn, $this);
        } else { // Redirección a página 404
            if(preg_match('/^\/api/',$currentURL)){
                $this->render('api/api', 'layout-api', array('response' => array(
                    'status' => 404,
                    'message' => 'API no encontrada',
                    'content' => array()
                )));
            }else{
                $this->render('public/404','layout-public',array('title'=>'Página no encontrada'));
            }
        }
    }

    /**
     * Muestra la vista
     *
     * @param String $view Vista a cargar
     * @param String $layout Layout a cargar
     * @param array $data Arreglo de datos para la vista
     * @return void
     */
    public function render(String $view, String $layout, array $data = []): void
    {
        foreach ($data as $key => $value) {
            $$key = $value;
        }
        ob_start();
        include __DIR__ . '/../../views/' . $view . '.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../views/' . $layout . '.php';
    }
}
