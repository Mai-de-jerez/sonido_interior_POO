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

    public function mostrarCheckout(): Response {
        $idUsuario = $this->getUserId();
        $resumen = $this->carritoService->validarYCalcularTotal($idUsuario);

        return Response::view('public/checkout', [
            'lineas' => $resumen->getLineas(),
            'totalCarrito' => $resumen->getTotal(),
            'csrf_token' => $this->csrfToken()
        ]);
    }

    public function procesarCheckout(Request $request): Response {
        $idUsuario = $this->getUserId();
        $datos = $request->allPost();

        $errores = $this->checkoutValidator->validar($datos);

        if (!empty($errores)) {
            $this->setFlash('errores', $errores);
            $this->setFlash('form_old', $datos);
            return Response::redirect('checkout');
        }

        $direccionEnvio = trim($request->post('direccion_envio', ''));
        $idPedido = $this->checkoutService->procesarCheckout($idUsuario, $direccionEnvio);

        $this->setSession('cantidades_carrito', 0);
        $this->setSession('ultimo_pedido_id', $idPedido);
        $this->setFlash('mensaje_exito', "¡Pedido #{$idPedido} realizado con éxito!");

        return Response::redirect('pedido-exito');
    }

    public function pedidoExito(): Response {
        if (!$this->hasSession('ultimo_pedido_id')) {
            return Response::redirect('catalogo');
        }

        $idPedido = $this->getSession('ultimo_pedido_id');
        $this->removeSession('ultimo_pedido_id');

        return Response::view('public/pedido-exito', ['idPedido' => $idPedido]);
    }
}