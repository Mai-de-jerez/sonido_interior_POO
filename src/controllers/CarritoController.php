<?php

namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;
use SonidoInteriorPoo\core\Request;
use SonidoInteriorPoo\core\Response;
use SonidoInteriorPoo\interfaces\CarritoServiceInterface;

class CarritoController extends Controller {
    private CarritoServiceInterface $carritoService;

    public function __construct(
        CarritoServiceInterface $carritoService
    ) {
        $this->carritoService = $carritoService;
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
        
        $this->setSession(
            'cantidades_carrito', 
            $this->getSession('cantidades_carrito', 0) + $resultado['unidadesAnadidas']
        );
        $this->setFlash('mensaje_exito', $resultado['mensaje']);

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

        $delta = $this->carritoService->actualizarCantidad($idUsuario, $idCarritoProducto, $accion);
        
        $this->setSession(
            'cantidades_carrito', 
            max(0, $this->getSession('cantidades_carrito', 0) + $delta)
        );

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

        $cantidadAEliminar = $this->carritoService->eliminarLinea($idUsuario, $idCarritoProducto);
        
        $this->setSession(
            'cantidades_carrito', 
            max(0, $this->getSession('cantidades_carrito', 0) - $cantidadAEliminar)
        );

        return Response::redirect('carrito');
    }
}