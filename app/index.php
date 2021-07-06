<?php
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';
require_once './middlewares/AutenticacionMiddelware.php';
require_once './middlewares/UsuariosMiddleware.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/CryptoController.php';
require_once './controllers/VentaController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

//$app->setBasePath("/lasCryptos");
$app->setBasePath('/prog_3/SPMontillaVicenteCripto/app');
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

date_default_timezone_set('America/Argentina/Buenos_Aires');

// Routes
$app->group('/autenticacion', function (RouteCollectorProxy $group) {
    //Accesible para todos los usuarios.
    $group->post('/login', \UsuarioController::class . ':Login'); 
});

$app->group('/ventas', function (RouteCollectorProxy $group) { 

    $group->post('[/]', \VentaController::class . ':CargarUno')->add(\UsuariosMiddleware::class . ':verificarAccesoClientes');

    $group->get('/ventasAlemanas', \VentaController::class . ':obtenerVentasCryptoAlemanas')
    ->add(\UsuariosMiddleware::class . ':VerificarAccesoAdministradores');

    $group->get('/ventasMayor', \VentaController::class . ':obtenerVentasMayor')
    ->add(\UsuariosMiddleware::class . ':VerificarAccesoAdministradores');

    $group->get('/masTransacciones', \VentaController::class . ':obtenerVentasMasTransacciones')
    ->add(\UsuariosMiddleware::class . ':VerificarAccesoAdministradores');

    $group->get('/nombre={nombreMoneda}', \UsuarioController::class . ':UsuariosVentasSegunNombreCrypto')
    ->add(\UsuariosMiddleware::class . ':VerificarAccesoAdministradores');

    $group->get('/descargaPdf', \VentaController::class . ':DescargaPdf');

})->add(\AutenticacionMiddelware::class . ':verificarTokenUsuario');

$app->group('/cryptos', function (RouteCollectorProxy $group) {

    $group->get('/id={id}', \CryptoController::class . ':TraerUno')->add(\UsuariosMiddleware::class . ':verificarAccesoClientes')
    ->add(\AutenticacionMiddelware::class . ':verificarTokenUsuario');

    $group->get('/nacionalidad={nacionalidad}', \CryptoController::class . ':TraerSegunNacionalidad');

    $group->get('/', \CryptoController::class . ':TraerTodos');

    $group->post('[/]', \CryptoController::class . ':CargarUno')->add(\UsuariosMiddleware::class . ':VerificarAccesoAdministradores')
    ->add(\AutenticacionMiddelware::class . ':verificarTokenUsuario');

    $group->put('[/]', \CryptoController::class . ':ModificarUno')
    ->add(\UsuariosMiddleware::class . ':VerificarAccesoAdministradores')
    ->add(\AutenticacionMiddelware::class . ':verificarTokenUsuario');

    $group->delete('[/]', \CryptoController::class . ':BorrarUno')
    ->add(\UsuariosMiddleware::class . ':VerificarAccesoAdministradores')
    ->add(\AutenticacionMiddelware::class . ':verificarTokenUsuario');
});

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Vicente Montilla Inc.");
    return $response;

});

$app->run();
