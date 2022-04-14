<?php

namespace Router;

use Exception;
use Model\Api;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class Token
{
    protected $status;
    protected $message;
    protected $scopes;
    const SUCCESS_CODE = 200;
    const SECURITY = 'Bearer';

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
     * Devuelve la cadena del token
     *
     * @return string|null Cadena del token
     */
    public static function get():string|null
    {   
        $_HEADER = getallheaders();
        if(!isset($_HEADER['Authorization'])){
            return null;
        }else{
            $security = explode(" ",$_HEADER['Authorization']);
            if($security[0] == self::SECURITY){
                return $security[1];
            }else{
                return null;
            }
        }
    }

    /**
     * Valida el token
     *
     * @param string $scope
     * @return Token
     */
    public static function validate(string $scope):Token
    {
        $token = self::get();
        if (is_null($token)){
            return new Token(array(
                'status' => 403,
                'message' => Api::RES_403_Unauthenticaded
            ));
        }
        try {
            $token = JWT::decode($token, new Key(SECRET, 'HS256'));
            $data = $token->data;
        } catch (ExpiredException $e) {
            return new Token(array(
                'status' => 401,
                'message' => 'Token ha expirado.'
            ));
        } catch (SignatureInvalidException $e){
            return new Token(array(
                'status' => 401,
                'message' => 'Token invalido.'
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
                'message' => Api::RES_403_Unauthorized
            ));
        }
        if (!in_array($scope, $data->scopes)) {
            return new Token(array(
                'status' => 403,
                'message' => Api::RES_403_Unauthorized,
                'scopes' => $data->scopes
            ));
        }
        return new Token(array(
            'status' => self::SUCCESS_CODE,
            'message' => 'Accesso autorizado.',
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
