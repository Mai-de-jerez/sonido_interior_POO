<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;
use SonidoInteriorPoo\interfaces\CarritoServiceInterface;
use SonidoInteriorPoo\validators\CheckoutValidator;

class CarritoController extends Controller {
    private CarritoServiceInterface $carritoService;
    private CheckoutValidator $checkoutValidator;

    public function __construct(
        CarritoServiceInterface $carritoService,
        CheckoutValidator $checkoutValidator
    ) {
        $this->carritoService = $carritoService;
        $this->checkoutValidator = $checkoutValidator;
    }

    // ============================================================
    // VER CARRITO
    // ============================================================
    public function ver(): void {
   
        $idUsuario = $this->getUserId();

        $lineas = $this->carritoService->obtenerLineas($idUsuario);

        $this->renderizar('public/carrito', ['lineas' => $lineas]);
    }

    // ============================================================
    // AÑADIR PRODUCTO AL CARRITO
    // ============================================================
    public function agregar(): void {

        $idUsuario = $this->getUserId();

        $idProducto = (isset($_POST['id_producto']) && ctype_digit($_POST['id_producto']))
            ? (int) $_POST['id_producto']
            : null;

        $cantidad = (isset($_POST['cantidad']) && ctype_digit($_POST['cantidad']))
            ? (int) $_POST['cantidad']
            : 1;

        $origen = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/catalogo');

        if ($idProducto === null) {
            $this->redirigir($origen);
        }

        $resultado = $this->carritoService->agregarProducto($idUsuario, $idProducto, $cantidad);

        if ($resultado['ok']) {
            $this->setSession('cantidades_carrito', ($this->getSession('cantidades_carrito', 0) + $resultado['unidadesAnadidas']));
            $this->setFlash('mensaje_exito', $resultado['mensaje']);
        } else {
            $this->setFlash('mensaje_error', $resultado['mensaje']);
        }

        $this->redirigir($origen);
    }

    // ============================================================
    // ACTUALIZAR CANTIDAD (sumar / restar)
    // ============================================================
    public function actualizarCantidad(): void {

        $idUsuario = $this->getUserId();

        $idCarritoProducto = (isset($_POST['id_carrito_producto']) && ctype_digit($_POST['id_carrito_producto']))
            ? (int) $_POST['id_carrito_producto']
            : null;

        $accion = $_POST['accion'] ?? null;

        if ($idCarritoProducto === null || !in_array($accion, ['sumar', 'restar'], true)) {
            $this->redirigir('carrito');
        }

        $resultado = $this->carritoService->actualizarCantidad($idUsuario, $idCarritoProducto, $accion);

        if ($resultado['ok']) {
            $this->setSession('cantidades_carrito', max(0, ($this->getSession('cantidades_carrito', 0) + $resultado['delta'])));
        } elseif ($resultado['mensaje'] !== '') {
            $this->setFlash('mensaje_error', $resultado['mensaje']);
        }

        $this->redirigir('carrito');
    }

    // ============================================================
    // ELIMINAR LÍNEA DEL CARRITO
    // ============================================================
    public function eliminar(): void {

        $idUsuario = $this->getUserId();

        $idCarritoProducto = (isset($_POST['id_carrito_producto']) && ctype_digit($_POST['id_carrito_producto']))
            ? (int) $_POST['id_carrito_producto']
            : null;

        if ($idCarritoProducto === null) {
            $this->redirigir('carrito');
        }

        $cantidadAEliminar = $this->carritoService->obtenerCantidadLinea($idUsuario, $idCarritoProducto);

        if ($cantidadAEliminar === null) {
            $this->setFlash('mensaje_error', 'Esa línea no pertenece a tu carrito.');
            $this->redirigir('carrito');
        }

        $this->carritoService->eliminarLinea($idUsuario, $idCarritoProducto);

        $this->setSession('cantidades_carrito', max(0, ($this->getSession('cantidades_carrito', 0) - $cantidadAEliminar)));

        $this->redirigir('carrito');
    }

    // ============================================================
    // MOSTRAR CHECKOUT
    // ============================================================
    public function mostrarCheckout(): void {

        $idUsuario = $this->getUserId();

        $lineas = $this->carritoService->obtenerLineas($idUsuario);

        if (empty($lineas)) {
            $this->setFlash('mensaje_error', 'Tu carrito está vacío. Añade algún producto antes de finalizar la compra.');
            $this->redirigir('carrito');
        }

        $totalCarrito = 0;
        foreach ($lineas as $linea) {
            if ($linea->getCantidad() > $linea->getProducto()->getStock()) {
                $this->setFlash('mensaje_error', "El producto '" . $linea->getProducto()->getNombre() . "' solo tiene " . $linea->getProducto()->getStock() . " unidades disponibles. Ajusta la cantidad.");
                $this->redirigir('carrito');
            }
            $totalCarrito += $linea->getSubtotal();
        }

        $this->renderizar('public/checkout', [
            'lineas' => $lineas,
            'totalCarrito' => $totalCarrito
        ]);
    }

    // ============================================================
    // PROCESAR CHECKOUT
    // ============================================================
    public function procesarCheckout(): void {

        $idUsuario = $this->getUserId();

        $errores = $this->checkoutValidator->validar($_POST);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->setSession('form_old', $_POST);
            $this->redirigir('checkout');
        }

        $direccionEnvio = trim($_POST['direccion_envio']);
        $resultado = $this->carritoService->procesarCheckout($idUsuario, $direccionEnvio);

        if ($resultado['ok']) {
            $this->setSession('cantidades_carrito', 0);
            $this->setSession('ultimo_pedido_id', $resultado['idPedido']);
            $this->setFlash('mensaje_exito', $resultado['mensaje']);
            $this->redirigir('pedido-exito');
        } else {
            $this->setFlash('mensaje_error', $resultado['mensaje']);
            $this->redirigir('carrito');
        }
    }

    // ============================================================
    // PÁGINA DE ÉXITO
    // ============================================================
    public function pedidoExito(): void {

        if (!$this->hasSession('ultimo_pedido_id')) {
            $this->redirigir('catalogo');
        }

        $idPedido = $this->getSession('ultimo_pedido_id');
        $this->removeSession('ultimo_pedido_id');

        $this->renderizar('public/pedido-exito', ['idPedido' => $idPedido]);
    }
}