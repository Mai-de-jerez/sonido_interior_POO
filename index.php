<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/vendor/autoload.php';
session_start();

use SonidoInteriorPoo\models\Conexion;
use SonidoInteriorPoo\models\CategoriaDAO;
use SonidoInteriorPoo\models\ProductoDAO;
use SonidoInteriorPoo\services\CategoriaService;
use SonidoInteriorPoo\services\ProductoService;
use SonidoInteriorPoo\controllers\CategoriaController;
use SonidoInteriorPoo\controllers\ProductoController;
use SonidoInteriorPoo\controllers\UsuarioController;
use SonidoInteriorPoo\services\UsuarioService;
use SonidoInteriorPoo\models\UsuarioDAO;

// --- Contenedor de dependencias ---
$conexion = new Conexion();
$categoriaDAO = new CategoriaDAO($conexion);
$productoDAO = new ProductoDAO($conexion);
$categoriaService = new CategoriaService($categoriaDAO);
$productoService = new ProductoService($productoDAO, $categoriaDAO);
$categoriaController = new CategoriaController($categoriaService);
$productoController = new ProductoController($productoService, $categoriaService);
$usuarioDAO = new UsuarioDAO($conexion);
$usuarioService = new UsuarioService($usuarioDAO);
$usuarioController = new UsuarioController(
    $usuarioService,
    $productoService,
    $categoriaService
);

// --- Enrutado ---
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metodo = $_SERVER['REQUEST_METHOD'];

// 🔥 ELIMINAR LA PARTE DEL PROYECTO Y /index.php
$uri = str_replace('/sonido-interior-POO', '', $uri);
$uri = str_replace('/index.php', '', $uri);

// Si está vacío o es solo "/", poner "/"
if ($uri === '' || $uri === '/') {
    $uri = '/';
}

// 🔥 Función reutilizable para la home
$homeAction = function() use ($productoService) {
    $productos = $productoService->obtenerUltimosProductosInicio();
    $data = ['productos' => $productos];
    extract($data);
    require __DIR__ . '/src/views/public/index.php';
};

$routes = [
    // ============================================================
    // RUTAS POST
    // ============================================================
    'POST /categorias/guardar'    => [$categoriaController, 'crear'],
    'POST /categorias/actualizar' => [$categoriaController, 'actualizar'],
    'POST /categorias/eliminar'   => [$categoriaController, 'eliminar'],
    'GET  /categorias/reactivar'  => [$categoriaController, 'reactivar'],

    'POST /productos/guardar'     => [$productoController, 'crear'],
    'POST /productos/actualizar'  => [$productoController, 'actualizar'],
    'POST /productos/eliminar'    => [$productoController, 'eliminar'],
    'GET  /productos/reactivar'   => [$productoController, 'reactivar'],
    'GET /detalle-producto' => [$productoController, 'detalle'],

    // ============================================================
    // RUTAS GET - PÚBLICAS
    // ============================================================
    'GET /'                       => $homeAction,
    'GET /index.php'              => $homeAction,
    'GET /catalogo' => [$productoController, 'catalogo'],

    // LOGIN
    'POST /login' => [$usuarioController, 'procesarLogin'],
    'GET /login' => function() {
        require __DIR__ . '/src/views/public/login.php';
    },
    'GET /logout' => [$usuarioController, 'logout'],
    
    // REGISTRO
    'POST /registro' => [$usuarioController, 'procesarRegistro'],
    'GET /registro' => function() {
        require __DIR__ . '/src/views/public/registro.php';
    },

    // ============================================================
    // RUTAS GET - ADMIN
    // ============================================================
    'GET /admin/dashboard'        => [$usuarioController, 'dashboard'],
    'GET /admin/productos'        => [$productoController, 'listar'],
    'GET /admin/productos/crear'  => [$productoController, 'nuevo'],
    'GET /admin/productos/editar' => [$productoController, 'editar'],
    'GET /admin/categorias'       => [$categoriaController, 'listar'],
    'GET /admin/categorias/crear' => [$categoriaController, 'nuevo'],
    'GET /admin/categorias/editar'=> [$categoriaController, 'editar'],
];

$clave = "$metodo $uri";

if (array_key_exists($clave, $routes)) {
    $accion = $routes[$clave];
    if (is_array($accion)) {
        [$controller, $metodoControlador] = $accion;
        $controller->$metodoControlador();
    } else {
        $accion();
    }
} else {
    http_response_code(404);
    echo "Página no encontrada. URI: " . $uri;
}