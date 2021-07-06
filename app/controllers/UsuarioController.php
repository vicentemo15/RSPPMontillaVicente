<?php
require_once './models/Usuario.php';
require_once './models/Autenticador.php';

class UsuarioController extends Usuario 
{
    public function Login($request, $response, $args){
      //Se verifica el usuario y devuelve el token
      $parametros = $request->getParsedBody();
      $mail = $parametros['mail'];
      $clave = $parametros['clave'];
      $tipo_usuario = $parametros['tipo_usuario'];
      if(!isset($mail) || !isset($clave) || !isset($tipo_usuario)){
        $response->getBody()->write(json_encode(array("error" => "Error en los datos ingresados para login.")));
        $response = $response->withStatus(400);
      }else{
        $usuario = Usuario::obtenerUsuario($mail, $tipo_usuario);
        if(isset($usuario)){
          //Existe el usuario, verificamos el password
          // tipo_usuario admmin administrador - tipo_usuario 2 vendedor
          if(password_verify($clave, $usuario->clave)){
            $datos = json_encode(array("id_usuario" => $usuario->id, "tipo_usuario" => $usuario->tipo_usuario));
            $token = AutentificadorJWT::CrearToken($datos);
            $response->getBody()->write(json_encode(array("token" => $token)));
          }else{
          $response->getBody()->write(json_encode(array("error" => "Ocurrió un error, password incorrecto.")));
          $response = $response->withStatus(400);
          }        
        }else{
          $response->getBody()->write(json_encode(array("error" => "Ocurrió un error al generar el token.")));
          $response = $response->withStatus(400);
        }
      }
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function UsuariosVentasSegunNombreCrypto($request, $response, $args){
      $nombreMoneda = $args['nombreMoneda'];
      if(!isset($nombreMoneda) ){
        $response->getBody()->write(json_encode(array("error" => "Error en los datos ingresados para reporte.")));
        $response = $response->withStatus(400);
      }else{
        $usuarios = Usuario::obtenerUsuariosCompraronMonedaSegunNombre($nombreMoneda);
          $response->getBody()->write(json_encode(array("Listado Usuarios" => $usuarios)));
          $response = $response->withStatus(400);

      }
      return $response->withHeader('Content-Type', 'application/json');
    }
}
