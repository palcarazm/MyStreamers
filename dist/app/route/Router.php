<?php

namespace Route;

class Router
{
    public $routes = []; // Arreglo de rutas

    /**
     * Añade una ruta al arreglo de rutas
     *
     * @param String $method Método de solicitud
     * @param String $uri URI solicitada 
     * @param array|String $fn Función a invocar
     * @param boolean $isPublic Accesible sin autentificación (Y/N)
     * @return void
     */
    public function add(String $method, String $uri, array|String $fn, bool $isPublic = true): void
    {
        $this->routes[$method][$uri] = [
            'function' => $fn,
            'isPublic' => $isPublic
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

        if (!isset($_SESSION)) {
            session_start();
        }
        $auth = isset($_SESSION['auth']) ? $_SESSION['auth'] : false; // Comprueba si el usuario está autenticado

        $fn = $this->routes[$method][$currentURL]['function'] ?? null;
        $isPublic = $this->routes[$method][$currentURL]['isPublic'] ?? null;

        if ($fn) { // Página existe
            if (!$isPublic && !$auth) { // Requiere autenticación y no se aporta
                header('Location:/login');
            }
            call_user_func($fn, $this);
        } else { // Redirección a página 404
            $this->render('404','');
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
