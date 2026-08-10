<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

use SonidoInteriorPoo\core\Session;
use SonidoInteriorPoo\core\Router;
use SonidoInteriorPoo\core\Container;

// inicializamos la sesion
Session::start();

// Creamos el contenedor instanciando a la Clase Container
$container = new Container();

// Creamos Router instanciando a Router
$router = new Router();  

// Registramos las rutas
require_once __DIR__ . '/../config/routes.php';

// --- Despachamos la petición ---
$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));