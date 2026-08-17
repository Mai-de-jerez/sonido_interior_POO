<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;
use SonidoInteriorPoo\core\Request;
use SonidoInteriorPoo\core\Response;
use SonidoInteriorPoo\interfaces\CarritoServiceInterface;
use SonidoInteriorPoo\interfaces\CheckoutServiceInterface;
use SonidoInteriorPoo\validators\CheckoutValidator;

class CheckoutController extends Controller {
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