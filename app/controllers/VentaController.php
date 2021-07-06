<?php
require_once './models/Venta.php';
require_once './models/Crypto.php';
require_once './servicio/PdfServicio.php';

class VentaController extends Venta 
{
    public function CargarUno($request, $response, $args){
      //Se verifica el usuario y devuelve el token
      $parametros = $request->getParsedBody()["body"];
      $id_crypto = $parametros['id_crypto'];
      $id_cliente = $parametros['id_cliente'];
      $cantidad = $parametros['cantidad'];

      if(!isset($id_crypto) || !isset($id_cliente) || !isset($cantidad)){
        $response->getBody()->write(json_encode(array("error" => "Error en los datos ingresados para cargar la venta.")));
        $response = $response->withStatus(400);
      }else{
        //VERIFICO QUE EXISTA EL ID EMPLEADO Y LA crypto A VENDER
        $usuario = Usuario::obtenerUsuarioPorId($id_cliente);
        $crypto = Crypto::obtenerCrypto($id_crypto);
        if(!$usuario || !$crypto){
          $response->getBody()->write(json_encode(array("error" => "Ocurrió un error al generar la venta. No existe el empleado o la crypto")));
          $response = $response->withStatus(400);
        }else{
          //cargo la venta
          $venta = new Venta();
          $venta->id_cliente = $id_cliente;
          $venta->id_crypto = $id_crypto;
          $venta->cantidad = $cantidad;
          Venta::cargarVenta($venta);
          $response->getBody()->write(json_encode(array("Mensaje" => "Venta realizada con exito.")));
        }
      }
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function obtenerVentasCryptoAlemanas($request, $response, $args){
        $ventas = Venta::obtenerTodasLasCryptosAlemanasVendidas();
        $response->getBody()->write(json_encode(array("Mensaje" => $ventas)));
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function obtenerVentasMayor($request, $response, $args){

      ob_clean();
      ob_start();
      $ventas = Venta::obtenerVentasMayorImporte();
      $pdf = new PdfServicio();
      $pdf->SetTitle("Ventas Mayor Precio Cryptos");
      $pdf->AddPage();
      $pdf->Cell(150, 10, 'Ventas Mayor precio: ', 0, 1);
      foreach ($ventas as $venta) {
        $pdf->Cell(150, 10, Venta::toString($venta));
      }
      $pdf->Output();
      ob_end_flush();
  
      $payload = json_encode(array("mensaje" => "Descargado"));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/pdf');
  }

  public function obtenerVentasMasTransacciones($request, $response, $args){

    ob_clean();
    ob_start();
    $ventas = Venta::obtenerMasTransacciones();
    $pdf = new PdfServicio();
    $pdf->SetTitle("Ventas Cryptos Mayor Transacciones");
    $pdf->AddPage();
    $pdf->Cell(150, 10, 'Ventas Mayor transacciones: ', 0, 1);
    foreach ($ventas as $venta) {
      $pdf->Cell(150, 10, Venta::toString($venta));
    }
    $pdf->Output();
    ob_end_flush();

    $payload = json_encode(array("mensaje" => "Descargado"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/pdf');
  }

    public function DescargaPdf($request, $response, $args)
    {
      ob_clean();
      ob_start();
      $ventas = Venta::obtenerTodos();
      $pdf = new PdfServicio();
      $pdf->SetTitle("Ventas Cryptos");
      $pdf->AddPage();
      $pdf->Cell(150, 10, 'Ventas cryptos: ', 0, 1);
      foreach ($ventas as $venta) {
        $pdf->Cell(150, 10, Venta::toString($venta));
      }
      $pdf->Output();
      ob_end_flush();
  
      $payload = json_encode(array("mensaje" => "Descargado"));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/pdf');
    }
}
