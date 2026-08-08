<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
session_start();

// --- DEFINICIÓN DE LA CONSTANTE BASE ---
define('BASE_URL', '/sonido-interior-POO');
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . BASE_URL);

use SonidoInteriorPoo\core\Router;
use SonidoInteriorPoo\models\Conexion;
use SonidoInteriorPoo\models\CategoriaDAO;
use SonidoInteriorPoo\models\ProductoDAO;
use SonidoInteriorPoo\models\UsuarioDAO;
use SonidoInteriorPoo\models\MensajeDAO;
use SonidoInteriorPoo\services\CategoriaService;
use SonidoInteriorPoo\services\ProductoService;
use SonidoInteriorPoo\services\UsuarioService;
use SonidoInteriorPoo\services\MensajeService;
use SonidoInteriorPoo\controllers\CategoriaController;
use SonidoInteriorPoo\controllers\ProductoController;
use SonidoInteriorPoo\controllers\UsuarioController;
use SonidoInteriorPoo\controllers\StaticPagesController;
use SonidoInteriorPoo\controllers\MensajeController;
use SonidoInteriorPoo\models\CarritoDAO;
use SonidoInteriorPoo\models\PedidoDAO;
use SonidoInteriorPoo\services\CarritoService;
use SonidoInteriorPoo\controllers\CarritoController;

// --- Contenedor de dependencias ---
$conexion = new Conexion();

$categoriaDAO = new CategoriaDAO($conexion);
$productoDAO = new ProductoDAO($conexion);
$usuarioDAO = new UsuarioDAO($conexion);
$mensajeDAO = new MensajeDAO($conexion);

$categoriaService = new CategoriaService($categoriaDAO);
$productoService = new ProductoService($productoDAO, $categoriaDAO);
$usuarioService = new UsuarioService($usuarioDAO);
$mensajeService = new MensajeService($mensajeDAO);

$categoriaController = new CategoriaController($categoriaService);
$productoController = new ProductoController($productoService, $categoriaService);
$usuarioController = new UsuarioController($usuarioService);
$staticPagesController = new StaticPagesController();
$mensajeController = new MensajeController($mensajeService);

$carritoDAO = new CarritoDAO($conexion);
$pedidoDAO = new PedidoDAO($conexion);
$carritoService = new CarritoService($conexion, $carritoDAO, $productoDAO, $pedidoDAO);
$carritoController = new CarritoController($carritoService);

// --- Instancia del Router ---
$router = new Router();

// ============================================================
// PÁGINAS ESTÁTICAS PÚBLICAS
// ============================================================
$router->get('/login', [$staticPagesController, 'login']);
$router->get('/registro', [$staticPagesController, 'registro']);
$router->get('/sonoterapia', [$staticPagesController, 'sonoterapia']);
$router->get('/sobre-nosotros', [$staticPagesController, 'sobreNosotros']);
$router->get('/contacto', [$staticPagesController, 'contacto']);

// ============================================================
// PÁGINAS PÚBLICAS NO ESTÁTICAS
// ============================================================
// home
$router->get('/', [$productoController, 'home']);
// productos
$router->get('/catalogo', [$productoController, 'catalogo']);
$router->get('/detalle-producto', [$productoController, 'detalle']);
// carrito
$router->get('/carrito', [$carritoController, 'ver']);
$router->post('/carrito/agregar', [$carritoController, 'agregar']);
$router->post('/carrito/actualizar-cantidad', [$carritoController, 'actualizarCantidad']);
$router->post('/carrito/eliminar', [$carritoController, 'eliminar']);
// checkout
$router->get('/checkout', [$carritoController, 'mostrarCheckout']);
$router->post('/checkout', [$carritoController, 'procesarCheckout']);
// redirección checkout exitoso
$router->get('/pedido-exito', [$carritoController, 'pedidoExito']);
// contacto
$router->post('/contacto', [$mensajeController, 'procesarContacto']);
// Autenticación
$router->post('/login', [$usuarioController, 'procesarLogin']);
$router->get('/logout', [$usuarioController, 'logout']);    
$router->post('/registro', [$usuarioController, 'procesarRegistro']);
$router->get('/recuperar-password', [$usuarioController, 'mostrarRecuperar']);
$router->post('/recuperar-password', [$usuarioController, 'procesarRecuperar']);
$router->get('/restablecer-password', [$usuarioController, 'mostrarRestablecer']);
$router->post('/restablecer-password', [$usuarioController, 'procesarRestablecer']);
// ============================================================
// RUTAS ADMIN
// ============================================================
$router->get('/admin/dashboard', [$productoController, 'dashboard']);

// Productos Admin
$router->get('/admin/productos', [$productoController, 'listar']);
$router->get('/admin/productos/crear', [$productoController, 'nuevo']);
$router->get('/admin/productos/editar', [$productoController, 'editar']);
$router->get('/admin/productos/eliminar', [$productoController, 'confirmarEliminar']);
$router->get('/admin/productos/reactivar', [$productoController, 'confirmarReactivar']);
$router->post('/admin/productos/guardar', [$productoController, 'crear']);
$router->post('/admin/productos/actualizar', [$productoController, 'actualizar']);
$router->post('/admin/productos/eliminar', [$productoController, 'eliminar']);
$router->post('/admin/productos/reactivar', [$productoController, 'reactivar']);

// Mensajes Admin
$router->get('/admin/mensajes', [$mensajeController, 'listar']);
$router->post('/admin/mensajes/marcar-leido', [$mensajeController, 'marcarLeido']);
$router->post('/admin/mensajes/eliminar', [$mensajeController, 'eliminar']);

// Categorías Admin
$router->get('/admin/categorias', [$categoriaController, 'listar']);
$router->get('/admin/categorias/crear', [$categoriaController, 'nuevo']);
$router->get('/admin/categorias/editar', [$categoriaController, 'editar']);
$router->post('/admin/categorias/guardar', [$categoriaController, 'crear']);
$router->post('/admin/categorias/actualizar', [$categoriaController, 'actualizar']);
$router->post('/admin/categorias/eliminar', [$categoriaController, 'eliminar']);
$router->post('/admin/categorias/reactivar', [$categoriaController, 'reactivar']);

// --- Despachar la petición ---
$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));