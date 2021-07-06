<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class UsuariosMiddleware{

    private static $id_tipo_administrador = "admin";
    private static $id_tipo_cliente = "cliente";

    public function VerificarAccesoAdministradores(Request $request, RequestHandler $handler): Response{
        $dataToken = json_decode($request->getParsedBody()["dataToken"], true);
        $response = new Response();
        if(isset($dataToken)){
            //Verifico que el codigo_tipo_usuario sea socio
            if($dataToken['tipo_usuario'] == self::$id_tipo_administrador){
                //avanzo al siguiente MW o paso al controller de la apii
                $response = $handler->handle($request);
            }else{
                $response->getBody()->write(json_encode(array("error" => "No tienes accesos de administrador.")));
                $response = $response->withStatus(401);
            }
        }else{
            $response->getBody()->write(json_encode(array("error" => "Error en los datos ingresados en el dataToken")));
            $response = $response->withStatus(400);
        }
     return $response->withHeader('Content-Type', 'application/json');
    }

    public function verificarAccesoClientes(Request $request, RequestHandler $handler): Response{
         $dataToken = json_decode($request->getParsedBody()["dataToken"], true);
         $tipo_usuario = $dataToken['tipo_usuario'];
         $response = new Response();
         if(!isset($dataToken) || !isset($tipo_usuario)){
            $response->getBody()->write(json_encode(array("error" => "Error en los datos ingresados en el dataToken")));
            $response = $response->withStatus(400);
         }else{
            if($tipo_usuario == self::$id_tipo_cliente){
               $response = $handler->handle($request);
            }else{
               $response->getBody()->write(json_encode(array("error" => "No tienes accesos.")));
               $response = $response->withStatus(401);
            }
         }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>