<?php

namespace Router;

use Exception;

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
        $perms = $this->routes[$method][$currentURL]['perms'] ?? null;
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
    
    /**
     * Muestra la página de error genérica
     *
     * @return void
     */
    public function renderError():void
    {
        $this->render('public/500','layout-public',array('title'=>'Oups!'));
    }

     /**
     * Valida que el parametro cumple la especificación
     *
     * @param mixed $params Parametros
     * @param array $specs Espeficación ([ {name* required* type* max min filter schema} ])
     * @return bool Cumlpe la espeficicación (S/N)
     */
    public function validate(mixed $params, array $specs): bool
    {
        $valid = true;

        foreach ($specs as $spec) {
            if (!$spec['required'] && !isset($params[$spec['name']])) {
                continue;
            } elseif ($spec['required'] && !isset($params[$spec['name']])) {
                return false;
            }
            switch ($spec['type']) {
                case 'array':
                    foreach ($params[$spec['name']] as $param) {
                        if (!$this->validate($param, $spec['schema'])) {
                            return false;
                        }
                    }
                    break;
                case 'object':
                    if (!$this->validate($params[$spec['name']], $spec['schema'])) {
                        return false;
                    }
                    break;
                case 'integer':
                    $options = [];
                    if (isset($spec['max'])) {
                        $options['max_range'] = $spec['max'];
                    }
                    if (isset($spec['min'])) {
                        $options['min_range'] = $spec['min'];
                    }
                    if (!filter_var($params[$spec['name']], FILTER_VALIDATE_INT, array($options))) {
                        return false;
                    }
                    break;
                case 'number':
                    $options = [];
                    if (isset($spec['max'])) {
                        $options['max_range'] = $spec['max'];
                    }
                    if (isset($spec['min'])) {
                        $options['min_range'] = $spec['min'];
                    }
                    if (!filter_var($params[$spec['name']], FILTER_VALIDATE_FLOAT, array($options))) {
                        return false;
                    }
                    break;
                case 'string':
                    if (isset($spec['max']) && strlen($params[$spec['name']]) > $spec['max']) {
                        return false;
                    }
                    if (isset($spec['min']) && strlen($params[$spec['name']]) < $spec['min']) {
                        return false;
                    }
                    if (isset($spec['filter'])) {
                        if (!filter_var($params[$spec['name']], $spec['filter'])) {
                            return false;
                        }
                    }
                    break;
                case 'boolean':
                    if (is_null(filter_var($params[$spec['name']], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) {
                        return false;
                    }
                    break;
                default:
                    throw new Exception("Tipo de no soportado", 1);
                    break;
            }
        }
        return $valid;
    }
}
