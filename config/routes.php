<?php

use SonidoInteriorPoo\controllers\CategoriaController;
use SonidoInteriorPoo\controllers\ProductoController;
use SonidoInteriorPoo\controllers\UsuarioController;
use SonidoInteriorPoo\controllers\StaticPagesController;
use SonidoInteriorPoo\controllers\MensajeController;
use SonidoInteriorPoo\controllers\CarritoController;
use SonidoInteriorPoo\middleware\AuthMiddleware;
use SonidoInteriorPoo\middleware\AdminMiddleware;
use SonidoInteriorPoo\middleware\CsrfMiddleware;

$router->group([CsrfMiddleware::class], function ($router) {

    // ============================================================
    // PÁGINAS ESTÁTICAS Y PÚBLICAS
    // ============================================================
    $router->get('/', [ProductoController::class, 'home']);
    $router->get('/catalogo', [ProductoController::class, 'catalogo']);
    $router->get('/detalle-producto/{id}', [ProductoController::class, 'detalle']);
    $router->get('/login', [StaticPagesController::class, 'login']);
    $router->get('/registro', [StaticPagesController::class, 'registro']);
    $router->get('/sonoterapia', [StaticPagesController::class, 'sonoterapia']);
    $router->get('/sobre-nosotros', [StaticPagesController::class, 'sobreNosotros']);
    $router->get('/contacto', [StaticPagesController::class, 'contacto']);
    $router->post('/contacto', [MensajeController::class, 'procesarContacto']);

    // ============================================================
    // AUTENTICACIÓN
    // ============================================================
    $router->post('/login', [UsuarioController::class, 'procesarLogin']);
    $router->get('/logout', [UsuarioController::class, 'logout']);
    $router->post('/registro', [UsuarioController::class, 'procesarRegistro']);
    $router->get('/recuperar-password', [UsuarioController::class, 'mostrarRecuperar']);
    $router->post('/recuperar-password', [UsuarioController::class, 'procesarRecuperar']);
    $router->get('/restablecer-password', [UsuarioController::class, 'mostrarRestablecer']);
    $router->post('/restablecer-password', [UsuarioController::class, 'procesarRestablecer']);

    // ============================================================
    // RUTAS PRIVADAS DE CLIENTE (Requieren estar autenticado)
    // ============================================================
    $router->group([AuthMiddleware::class], function ($router) {
        $router->get('/carrito', [CarritoController::class, 'ver']);
        $router->post('/carrito/agregar', [CarritoController::class, 'agregar']);
        $router->post('/carrito/actualizar-cantidad', [CarritoController::class, 'actualizarCantidad']);
        $router->post('/carrito/eliminar', [CarritoController::class, 'eliminar']);
        $router->get('/checkout', [CarritoController::class, 'mostrarCheckout']);
        $router->post('/checkout', [CarritoController::class, 'procesarCheckout']);
        $router->get('/pedido-exito', [CarritoController::class, 'pedidoExito']);
    });

    // ============================================================
    // RUTAS PRIVADAS DE ADMIN (Requieren rol Admin)
    // ============================================================
    $router->group([AdminMiddleware::class], function ($router) {
        $router->get('/admin/dashboard', [ProductoController::class, 'dashboard']);

        $router->get('/admin/productos', [ProductoController::class, 'listar']);
        $router->get('/admin/productos/crear', [ProductoController::class, 'nuevo']);
        $router->get('/admin/productos/editar/{id}', [ProductoController::class, 'editar']);
        $router->get('/admin/productos/eliminar/{id}', [ProductoController::class, 'confirmarEliminar']);
        $router->get('/admin/productos/reactivar/{id}', [ProductoController::class, 'confirmarReactivar']);
        $router->post('/admin/productos/guardar', [ProductoController::class, 'crear']);
        $router->post('/admin/productos/actualizar/{id}', [ProductoController::class, 'actualizar']);
        $router->post('/admin/productos/eliminar/{id}', [ProductoController::class, 'eliminar']);
        $router->post('/admin/productos/reactivar/{id}', [ProductoController::class, 'reactivar']);

        $router->get('/admin/mensajes', [MensajeController::class, 'listar']);
        $router->post('/admin/mensajes/marcar-leido/{id}', [MensajeController::class, 'marcarLeido']);
        $router->post('/admin/mensajes/eliminar/{id}', [MensajeController::class, 'eliminar']);

        $router->get('/admin/categorias', [CategoriaController::class, 'listar']);
        $router->get('/admin/categorias/crear', [CategoriaController::class, 'nuevo']);
        $router->get('/admin/categorias/editar/{id}', [CategoriaController::class, 'editar']);
        $router->get('/admin/categorias/eliminar/{id}', [CategoriaController::class, 'confirmarEliminar']);
        $router->get('/admin/categorias/reactivar/{id}', [CategoriaController::class, 'confirmarReactivar']);
        $router->post('/admin/categorias/guardar', [CategoriaController::class, 'crear']);
        $router->post('/admin/categorias/actualizar/{id}', [CategoriaController::class, 'actualizar']);
        $router->post('/admin/categorias/eliminar/{id}', [CategoriaController::class, 'eliminar']);
        $router->post('/admin/categorias/reactivar/{id}', [CategoriaController::class, 'reactivar']);
    });
});