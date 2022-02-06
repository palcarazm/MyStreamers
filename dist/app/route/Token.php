<?php

namespace Route;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class Token
{
    protected $status;
    protected $message;
    protected $scopes;

    public function __construct($args = [])
    {
        $this->status = $args['status'] ?? 404;
        $this->message = $args['message'] ?? '';
        $this->scopes = $args['scopes'] ?? null;
    }
    /**
     * Crea un token para los scopes indicados
     *
     * @param array $scopes
     * @return string
     */
    public static function create(array $scopes = []): string
    {
        return JWT::encode(array(
            'iat' => time(),              // inicio del token
            'exp' => time() + (60 * 60),       // validez (1 h)
            'data' => [
                'scopes' => $scopes
            ]
        ), SECRET, 'HS256');
    }

    /**
     * Valida el token
     *
     * @param string $scope
     * @param string $token
     * @return Token
     */
    public static function validate(string $scope, string $token):Token
    {
        try {
            $token = JWT::decode($token, new Key(SECRET, 'HS256'));
            $data = $token->data;
        } catch (ExpiredException $e) {
            return new Token(array(
                'status' => 401,
                'message' => 'Token ha expirado'
            ));
        } catch (SignatureInvalidException $e){
            return new Token(array(
                'status' => 401,
                'message' => 'Token invalido'
            ));
        } catch (Exception $e){
            return new Token(array(
                'status' => 500,
                'message' => $e
            ));
        }
        if(!isset($data->scopes)){
            return new Token(array(
                'status' => 403,
                'message' => 'No tiene permisos para este servicio'
            ));
        }
        if (!in_array($scope, $data->scopes)) {
            return new Token(array(
                'status' => 403,
                'message' => 'No tiene permisos para este servicio',
                'scopes' => $data->scopes
            ));
        }
        return new Token(array(
            'status' => 200,
            'message' => 'Accesso autorizado',
            'scopes' => $data->scopes
        ));
    }

    /**
     * Devuelve el estado
     *
     * @return integer
     */
    public function getStatus():int
    {
        return $this->status;
    }

    /**
     * Devuelve el mensaje de estado
     *
     * @return string
     */
    public function getMessage():string
    {
        return $this->message;
    }

    /**
     * Devuelve los servicos autorizados
     *
     * @return array
     */
    public function getScopes():array
    {
        return $this->scopes;
    }
}
