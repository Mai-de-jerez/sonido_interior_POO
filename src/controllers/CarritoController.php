<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;
use SonidoInteriorPoo\core\Request;
use SonidoInteriorPoo\core\Response;
use SonidoInteriorPoo\interfaces\CarritoServiceInterface;
use SonidoInteriorPoo\interfaces\CheckoutServiceInterface;
use SonidoInteriorPoo\validators\CheckoutValidator;

class CarritoController extends Controller {
    private CarritoServiceInterface $carritoService;
    private CheckoutServiceInterface $checkoutService;
    private CheckoutValidator $checkoutValidator;

    public function __construct(
        CarritoServiceInterface $carritoService,
        CheckoutServiceInterface $checkoutService,
        CheckoutValidator $checkoutValidator
    ) {
        $this->carritoService = $carritoService;
        $this->checkoutService = $checkoutService;
        $this->checkoutValidator = $checkoutValidator;
    }

    // ============================================================
    // VER CARRITO
    // ============================================================
    public function ver(): Response {
        $idUsuario = $this->getUserId();
        $lineas = $this->carritoService->obtenerLineas($idUsuario);

        return Response::view('public/carrito', [
            'lineas' => $lineas,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // AÑADIR PRODUCTO AL CARRITO
    // ============================================================
    public function agregar(Request $request): Response {

        $origen = $request->referer(BASE_URL . '/catalogo');
        $idUsuario = $this->getUserId();

        $idProductoRaw = $request->post('id_producto');
        $idProducto = ctype_digit((string) $idProductoRaw) ? (int) $idProductoRaw : null;

        $cantidadRaw = $request->post('cantidad');
        $cantidad = ctype_digit((string) $cantidadRaw) ? (int) $cantidadRaw : 1;

        if ($idProducto === null) {
            return Response::redirect($origen);
        }

        $resultado = $this->carritoService->agregarProducto($idUsuario, $idProducto, $cantidad);

        if ($resultado['ok']) {
            $this->setSession('cantidades_carrito', ($this->getSession('cantidades_carrito', 0) + $resultado['unidadesAnadidas']));
            $this->setFlash('mensaje_exito', $resultado['mensaje']);
        } else {
            $this->setFlash('mensaje_error', $resultado['mensaje']);
        }

        return Response::redirect($origen);
    }

    // ============================================================
    // ACTUALIZAR CANTIDAD (sumar / restar)
    // ============================================================
    public function actualizarCantidad(Request $request): Response {
        $idUsuario = $this->getUserId();

        $idRaw = $request->post('id_carrito_producto');
        $idCarritoProducto = ctype_digit((string) $idRaw) ? (int) $idRaw : null;

        $accion = $request->post('accion');

        if ($idCarritoProducto === null || !in_array($accion, ['sumar', 'restar'], true)) {
            return Response::redirect('carrito');
        }

        $resultado = $this->carritoService->actualizarCantidad($idUsuario, $idCarritoProducto, $accion);

        if ($resultado['ok']) {
            $this->setSession('cantidades_carrito', max(0, ($this->getSession('cantidades_carrito', 0) + $resultado['delta'])));
        } elseif ($resultado['mensaje'] !== '') {
            $this->setFlash('mensaje_error', $resultado['mensaje']);
        }

        return Response::redirect('carrito');
    }

    // ============================================================
    // ELIMINAR LÍNEA DEL CARRITO
    // ============================================================
    public function eliminar(Request $request): Response {
        $idUsuario = $this->getUserId();

        $idRaw = $request->post('id_carrito_producto');
        $idCarritoProducto = ctype_digit((string) $idRaw) ? (int) $idRaw : null;

        if ($idCarritoProducto === null) {
            return Response::redirect('carrito');
        }

        $cantidadAEliminar = $this->carritoService->obtenerCantidadLinea($idUsuario, $idCarritoProducto);

        if ($cantidadAEliminar === null) {
            $this->setFlash('mensaje_error', 'Esa línea no pertenece a tu carrito.');
            return Response::redirect('carrito');
        }

        $this->carritoService->eliminarLinea($idUsuario, $idCarritoProducto);

        $this->setSession('cantidades_carrito', max(0, ($this->getSession('cantidades_carrito', 0) - $cantidadAEliminar)));

        return Response::redirect('carrito');
    }

    // ============================================================
    // MOSTRAR CHECKOUT
    // ============================================================
    public function mostrarCheckout(): Response {
        $idUsuario = $this->getUserId();
        $lineas = $this->carritoService->obtenerLineas($idUsuario);

        if (empty($lineas)) {
            $this->setFlash('mensaje_error', 'Tu carrito está vacío. Añade algún producto antes de finalizar la compra.');
            return Response::redirect('carrito');
        }

        $totalCarrito = 0;
        foreach ($lineas as $linea) {
            if ($linea->getCantidad() > $linea->getProducto()->getStock()) {
                $this->setFlash('mensaje_error', "El producto '" . $linea->getProducto()->getNombre() . "' solo tiene " . $linea->getProducto()->getStock() . " unidades disponibles. Ajusta la cantidad.");
                return Response::redirect('carrito');
            }
            $totalCarrito += $linea->getSubtotal();
        }

        return Response::view('public/checkout', [
            'lineas' => $lineas,
            'totalCarrito' => $totalCarrito,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // PROCESAR CHECKOUT
    // ============================================================
    public function procesarCheckout(Request $request): Response {
        $idUsuario = $this->getUserId();
        $datos = $request->allPost();

        $errores = $this->checkoutValidator->validar($datos);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->setSession('form_old', $datos);
            return Response::redirect('checkout');
        }

        $direccionEnvio = trim($request->post('direccion_envio', ''));
        $resultado = $this->checkoutService->procesarCheckout($idUsuario, $direccionEnvio);

        if ($resultado['ok']) {
            $this->setSession('cantidades_carrito', 0);
            $this->setSession('ultimo_pedido_id', $resultado['idPedido']);
            $this->setFlash('mensaje_exito', $resultado['mensaje']);
            return Response::redirect('pedido-exito');
        }

        $this->setFlash('mensaje_error', $resultado['mensaje']);
        return Response::redirect('carrito');
    }

    // ============================================================
    // PÁGINA DE ÉXITO
    // ============================================================
    public function pedidoExito(): Response {
        if (!$this->hasSession('ultimo_pedido_id')) {
            return Response::redirect('catalogo');
        }

        $idPedido = $this->getSession('ultimo_pedido_id');
        $this->removeSession('ultimo_pedido_id');

        return Response::view('public/pedido-exito', ['idPedido' => $idPedido]);
    }
}