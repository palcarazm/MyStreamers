<?php

namespace Model;

use stdClass;
use Exception;
use Router\Token;
use Router\Router;
use Notihnio\RequestParser\RequestParser;

class Api
{
    const RES_400 = 'Debe incluirse todos los valores requeridos con los formatos y longitudes adecuados.';
    const RES_403_Unauthorized = 'No dispone de autorización para emplear este servicio.';
    const RES_403_Unauthenticaded = 'Debe estar authenticado para emplear este servicio.';
    const RES_500_DB = 'Un error inesperado con la base de datos hace imposible completar la operación.';

    const AUTH_SESSION = 'SESSION';
    const AUTH_TOKEN = 'TOKEN';
    const AUTH_SELF = 'SELF';
    const AUTH_NOT = 'NOT_AUTH';

    protected Router $router;
    protected String $method;
    protected array $authMethods;
    protected string $authMethod = self::AUTH_NOT;
    protected array $bodySchema;
    protected array $querySchema;
    public array $in;
    public array $query;

    /**
     * Constructor de la API
     *
     * @param Router $router Router del sitio
     * @param string $method Metodo de llamada (GET POST PUT DELETE PATCH)
     * @param array $bodySchema Especificación de entrada ([ {name* required* type* max min filter schema} ])
     * @param array $querySchema Especificación de entrada ([ {name* required* type* max min filter schema} ])
     * @param array $authMethods Métodos de autentificación soportados (SESSION TOKEN SELF)
     */
    public function __construct(Router $router, string $method, array $bodySchema = array(), array $querySchema = array(), array $authMethods = array())
    {
        $this->router = $router;
        $this->method = strtoupper($method);
        $this->bodySchema = $bodySchema;
        $this->querySchema = $querySchema;
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
        $this->query = $_GET;
        switch ($this->method) {
            case 'GET':
                $in = array();
                break;
            case 'POST':
                $in = empty($_POST) ? json_decode(file_get_contents("php://input"), true) : $_POST;
                break;
            case 'PUT':
                $in = empty($_PUT) ? json_decode(file_get_contents("php://input"), true) : $_PUT;
                break;
            case 'DELETE':
                $in = empty($_DETETE) ? json_decode(file_get_contents("php://input"), true) : $_DETETE;
                break;
            case 'PATCH':
                $in = empty($_PATCH) ? json_decode(file_get_contents("php://input"), true) : $_PATCH;
                break;
            default:
                throw new Exception("Metodo no soportado por el dominio", 1);
                break;
        }
        $this->in = is_null($in) ? array() : $in;
    }

    /**
     * Verifica si cumple los requisitos de autenticación
     *
     * @param string $scope Permiso que verificar
     * @param integer $id Usuario sobre el que se hacen acciones
     * @return boolean autenticación realizada (S/N)
     */
    public function auth(string $scope, int $id = 0): bool
    {
        $auth = false;
        foreach ($this->authMethods as $authMethod) {
            switch (strtoupper($authMethod)) {
                case self::AUTH_SESSION:
                    $authUser = getAuthUser();
                    if (is_null($authUser)) {
                        break;
                    }
                    if ($authUser->can($scope)) {
                        $this->authMethod = self::AUTH_SESSION;
                        return true;
                    }
                case self::AUTH_TOKEN:
                    if (is_null(Token::get())) {
                        break;
                    }
                    $token = Token::validate($scope);
                    if ($token->getStatus() == Token::SUCCESS_CODE) {
                        $this->authMethod = self::AUTH_TOKEN;
                        return true;
                    }
                case self::AUTH_SELF:
                    $authUser = getAuthUser();
                    if (is_null($authUser)) {
                        break;
                    }
                    if ($authUser->getID() == $id) {
                        $auth = true;
                        $this->authMethod = self::AUTH_SELF;
                    }
                    break;
                default:
                    throw new Exception("Metodo de autentificación no soportado por el dominio", 1);
                    break;
            }
        }
        if (!$auth) {
            $this->send(403, Api::RES_403_Unauthenticaded, new stdClass());
        }
        return $auth;
    }

    /**
     * Devuelve el método de autenticación empleado
     *
     * @return String método de autenticación
     */
    public function getAuthMethod(): String
    {
        return $this->authMethod;
    }

    /**
     * Valida que la entrada cumple la especificación y lanza la respuesta de error en caso contrario.
     *
     * @return bool Cumlpe la espeficicación (S/N)
     */
    public function validate(): bool
    {
        if (!empty($this->bodySchema)) {
            if (!$this->router->validate($this->in, $this->bodySchema)) {
                $this->send(400, API::RES_400, new stdClass());
                return false;
            }
        }
        if (!empty($this->querySchema)) {
            if (!$this->router->validate($this->query, $this->querySchema)) {
                $this->send(400, API::RES_400, new stdClass());
                return false;
            }
        }
        return true;
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

    /**
     * Envia el mensaje de error genérico con la base de datos
     *
     * @return void
     */
    public function sendErrorDB(): void
    {
        $this->send(500, self::RES_500_DB, new stdClass());
    }
}
