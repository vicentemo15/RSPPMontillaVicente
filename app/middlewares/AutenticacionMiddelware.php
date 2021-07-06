<?php

require_once './models/Autenticador.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AutenticacionMiddelware
{

  public function VerificarTokenUsuario(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    try {
      $header = $request->getHeaderLine('Authorization');
      $token = trim(explode("Bearer", $header)[1]);
      $esValido = false;
      AutentificadorJWT::verificarToken($token);
      $esValido = true;
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    if ($esValido) {
      $dataToken = AutentificadorJWT::ObtenerData($token);
      $requestContent = $request->getParsedBody();
      $payload = array("body" => $requestContent, "dataToken" => $dataToken);
      $request = $request->withParsedBody($payload);
    }
    $response = $handler->handle($request);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
