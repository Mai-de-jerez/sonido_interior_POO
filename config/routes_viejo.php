<?php

// Obtener los controladores necesarios
$categoriaController = $container->getCategoriaController();
$productoController = $container->getProductoController();
$usuarioController = $container->getUsuarioController();
$staticPagesController = $container->getStaticPagesController();
$mensajeController = $container->getMensajeController();
$carritoController = $container->getCarritoController();

// ============================================================
// PÁGINAS ESTÁTICAS PÚBLICAS
// ============================================================

$router->get('/login', [$staticPagesController, 'login']);
$router->get('/registro', [$staticPagesController, 'registro']);
$router->get('/sonoterapia', [$staticPagesController, 'sonoterapia']);
$router->get('/sobre-nosotros', [$staticPagesController, 'sobreNosotros']);
$router->get('/contacto', [$staticPagesController, 'contacto']);


// ============================================================
// PÁGINAS PÚBLICAS
// ============================================================

// Home
$router->get('/', [$productoController, 'home']);

// Productos
$router->get('/catalogo', [$productoController, 'catalogo']);
$router->get('/detalle-producto', [$productoController, 'detalle']);

// Carrito
$router->get('/carrito', [$carritoController, 'ver']);
$router->post('/carrito/agregar', [$carritoController, 'agregar']);
$router->post('/carrito/actualizar-cantidad', [$carritoController, 'actualizarCantidad']);
$router->post('/carrito/eliminar', [$carritoController, 'eliminar']);

// Checkout
$router->get('/checkout', [$carritoController, 'mostrarCheckout']);
$router->post('/checkout', [$carritoController, 'procesarCheckout']);

// Pedido
$router->get('/pedido-exito', [$carritoController, 'pedidoExito']);

// Contacto
$router->post('/contacto', [$mensajeController, 'procesarContacto']);


// ============================================================
// AUTENTICACIÓN
// ============================================================

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
