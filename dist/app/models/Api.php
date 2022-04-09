<?php

namespace Model;

use stdClass;
use Exception;
use Route\Token;
use Route\Router;
use Notihnio\RequestParser\RequestParser;

class Api
{
    const RES_400 = 'Debe incluirse todos los valores requeridos con los formatos y longitudes adecuados.';
    const RES_403_Unauthorized = 'No dispone de autorización para emplear este servicio.';
    const RES_403_Unauthenticaded = 'Debe estar authenticado para emplear este servicio.';
    protected Router $router;
    protected String $method;
    protected array $authMethods;
    protected array $schema;
    public array $in;

    /**
     * Constructor de la API
     *
     * @param Router $router Router del sitio
     * @param string $method Metodo de llamada (GET POST DELETE PATCH)
     * @param array $schema Especificación de entrada ([ {name* required* type* max min filter schema} ])
     * @param array $authMethods Métodos de autentificación soportados (SESSION TOKEN)
     */
    public function __construct(Router $router, string $method, array $schema, array $authMethods = array())
    {
        $this->router = $router;
        $this->method = strtoupper($method);
        $this->schema = $schema;
        $this->authMethods = $authMethods;
        $this->parse();
    }

    /**
     * Carga los parametros de entrada
     *
     * @return void
     */
    public function parse(): void
    {
        RequestParser::parse();
        switch ($this->method) {
            case 'GET':
                $this->in = $_GET;
                break;
            case 'POST':
                $this->in = empty($_POST) ? json_decode(file_get_contents("php://input"), true) : $_POST;
                break;
            case 'DELETE':
                $this->in = empty($_DETETE) ? json_decode(file_get_contents("php://input"), true) : $_DETETE;
                break;
            case 'PATCH':
                $this->in = empty($_PATCH) ? json_decode(file_get_contents("php://input"), true) : $_PATCH;
                break;
            default:
                throw new Exception("Metodo no soportado por el dominio", 1);
                break;
        }
    }

    public function auth(string $scope): bool
    {
        foreach ($this->authMethods as $authMethod) {
            switch (strtoupper($authMethod)) {
                case 'SESSION':
                    $authUser = getAuthUser();
                    if (is_null($authUser)) {
                        break;
                    }
                    if (!$authUser->can($scope)) {
                        $this->send(403,Api::RES_403_Unauthorized,new stdClass());
                        return false;
                    }
                    return true;
                case 'TOKEN':
                    if (is_null(Token::get())) {
                        break;
                    }
                    $token = Token::validate($scope);
                    if ($token->getStatus() != Token::SUCCESS_CODE) {
                        $this->send($token->getStatus(), $token->getMessage(), new stdClass());
                        return false;
                    }
                    return true;
                default:
                    throw new Exception("Metodo de autentificación no soportado por el dominio", 1);
                    break;
            }
        }
        $this->send(403,Api::RES_403_Unauthenticaded,new stdClass());
        return false;
    }

    /**
     * Valida que la entrada cumple la especificación y lanza la respuesta de error en caso contrario.
     *
     * @return bool Cumlpe la espeficicación (S/N)
     */
    public function validate(): bool
    {
        if (!$this->validateParam($this->in, $this->schema)) {
            $this->send(400, API::RES_400, new stdClass());
            return false;
        }
        return true;
    }

    /**
     * Valida que el parametro cumple la especificación
     *
     * @param mixed $params Parametros
     * @param array $specs Espeficación
     * @return bool Cumlpe la espeficicación (S/N)
     */
    private function validateParam(mixed $params, array $specs): bool
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
                        if (!$this->validateParam($param, $spec['schema'])) {
                            return false;
                        }
                    }
                    break;
                case 'object':
                    if (!$this->validateParam($params[$spec['name']], $spec['schema'])) {
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
                    if (!filter_var($params[$spec['name']], FILTER_VALIDATE_BOOLEAN,)) {
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

    /**
     * Envía el mensaje de respuesta de la API
     *
     * @param integer $code
     * @param String $message
     * @param array|object $content
     * @return void
     */
    public function send(int $code, String $message, array|object $content): void
    {
        $this->router->render('api/api', 'layout-api', array('response' => array(
            'status' => $code,
            'message' => $message,
            'content' => $content
        )));
    }
}
