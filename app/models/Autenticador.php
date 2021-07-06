<?php

use Firebase\JWT\JWT;

class AutentificadorJWT
{
    private static $claveSecreta = 'Clave$';
    private static $tipoEncriptacion = ['HS256'];

    public static function CrearToken($datos, $esCliente = false)
    {
        $ahora = time();
        $expirationTime = $esCliente ? $ahora + 3600 : $ahora + 14400;
        $payload = array(
            'iat' => $ahora,
            'exp' => $expirationTime,
            'data' => $datos,
            'app' => "Segundo Parcial - Vicente Montilla"
        );
        return JWT::encode($payload, self::$claveSecreta);
    }

    public static function VerificarToken($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        try {
            $decodificado = JWT::decode(
                $token,
                self::$claveSecreta,
                self::$tipoEncriptacion
            );
        } catch (Exception $e) {
            throw $e;
        }
        return $decodificado;
    }


    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }

    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }
}