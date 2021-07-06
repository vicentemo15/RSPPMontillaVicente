<?php
require_once './models/Crypto.php';
require_once './interfaces/IApiUsable.php';

class CryptoController extends Crypto implements IApiUsable
{


  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody()["body"];
    $nombre = $parametros['nombre'];
    $precio = $parametros['precio'];
    $nacionalidad = $parametros['nacionalidad'];
    if (!isset($parametros) || !isset($nombre) || !isset($precio) || !isset($nacionalidad)) {
      $payload = json_encode(array("error" => "Error en los parametros para crear el producto."));
      $response = $response->withStatus(400);
    } else {
      $ruta_temporal = "";
      if ($_FILES["foto"]) {
        $fechaActual = new DateTime();
        $marcaTemporal = $fechaActual->getTimestamp();
        $nombre_real = $_FILES["foto"]["name"];
        $extension = explode(".", $nombre_real)[1];
        $ruta_temporal = "./Fotos/Cryptos/" . $marcaTemporal . "." . $extension;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $ruta_temporal);
      }
      $crypto = new Crypto();
      $crypto->nombre = $nombre;
      $crypto->precio = $precio;
      $crypto->nacionalidad = $nacionalidad;
      $crypto->foto = $ruta_temporal;
      $crypto->crear();
      $payload = json_encode(array("mensaje" => "crypto creado con exito."));
      $response = $response->withStatus(201);
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos producto por id
    $id = $args['id'];
    if (!isset($id)) {
      $payload = array("error" => "Error en los parametros para buscar el producto.");
      $response = $response->withStatus(400);
    } else {
      $crypto = Crypto::obtenerCrypto($id);
      $payload = json_encode(array("crypto" => $crypto));
      $response = $response->withStatus(200);
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerSegunNacionalidad($request, $response, $args)
  {
    $nacionalidad = $args['nacionalidad'];
    if (!isset($nacionalidad)) {
      $payload = array("error" => "Error en los parametros para buscar el producto.");
      $response = $response->withStatus(400);
    } else {
      $hortaliza = Crypto::obtenerSegunNacionalidad($nacionalidad);
      $payload = json_encode(array("cryptos segun nacionalidad" => $hortaliza));
      $response = $response->withStatus(200);
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Crypto::obtenerTodos();
    $payload = json_encode(array("listaCryptos" => $lista));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody()["body"];
    $id = $parametros['id'];
    $precio = $parametros['precio'];
    if (!isset($parametros) || !isset($id) || !isset($precio)) {
      $payload = json_encode(array("error" => "Error en los parametros para modificar el producto."));
      $response = $response->withStatus(400);
    } else {
      $hortalizaModificar = Crypto::obtenerCrypto($id);
      if (!$hortalizaModificar) {
        $payload = json_encode(array("error" => "No existe el producto a modificar."));
        $response = $response->withStatus(400);
      } else {
        $crypto = new Crypto();
        $crypto->id = $id;
        $crypto->precio = $precio;
        Crypto::modificar($crypto);
        $payload = json_encode(array("mensaje" => "crypto modificado con exito."));
      }
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody()["body"];
    $id = $parametros['id'];
    if (!isset($parametros) || !isset($id)) {
      $payload = json_encode(array("error" => "Error en los parametros para borrar el crypto."));
      $response = $response->withStatus(400);
    } else {
      $productoBorrar = Crypto::obtenerCrypto($id);
      if (!$productoBorrar) {
        $payload = json_encode(array("error" => "No existe el crypto a eliminar."));
        $response = $response->withStatus(400);
      } else {
        Crypto::borrar($id);
        $payload = json_encode(array("mensaje" => "Crypto borrado con exito."));
      }
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
