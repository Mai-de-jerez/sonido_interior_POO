<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\interfaces\CarritoServiceInterface;
use SonidoInteriorPoo\middleware\AuthMiddleware;

class CarritoController {
    private CarritoServiceInterface $carritoService;

    public function __construct(CarritoServiceInterface $carritoService) {
        $this->carritoService = $carritoService;
    }

    // ============================================================
    // VER CARRITO
    // ============================================================
    public function ver(): void {
        AuthMiddleware::verificarCliente();
        $idUsuario = (int) $_SESSION['id_usuario'];

        $lineas = $this->carritoService->obtenerLineas($idUsuario);

        $data = ['lineas' => $lineas];
        extract($data);
        require __DIR__ . '/../views/public/carrito.php';
    }

    // ============================================================
    // AÑADIR PRODUCTO AL CARRITO
    // ============================================================
    public function agregar(): void {
        AuthMiddleware::verificarCliente();
        $idUsuario = (int) $_SESSION['id_usuario'];

        $idProducto = (isset($_POST['id_producto']) && ctype_digit($_POST['id_producto']))
            ? (int) $_POST['id_producto']
            : null;

        $cantidad = (isset($_POST['cantidad']) && ctype_digit($_POST['cantidad']))
            ? (int) $_POST['cantidad']
            : 1;

        $origen = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/catalogo');

        if ($idProducto === null) {
            header("Location: " . $origen);
            exit();
        }

        $resultado = $this->carritoService->agregarProducto($idUsuario, $idProducto, $cantidad);

        if ($resultado['ok']) {
            $_SESSION['cantidades_carrito'] = ($_SESSION['cantidades_carrito'] ?? 0) + $resultado['unidadesAnadidas'];
            $_SESSION['mensaje_exito'] = $resultado['mensaje'];
        } else {
            $_SESSION['mensaje_error'] = $resultado['mensaje'];
        }

        header("Location: " . $origen);
        exit();
    }

    // ============================================================
    // ACTUALIZAR CANTIDAD (sumar / restar)
    // ============================================================
    public function actualizarCantidad(): void {
        AuthMiddleware::verificarCliente();
        $idUsuario = (int) $_SESSION['id_usuario'];

        $idCarritoProducto = (isset($_POST['id_carrito_producto']) && ctype_digit($_POST['id_carrito_producto']))
            ? (int) $_POST['id_carrito_producto']
            : null;

        $accion = $_POST['accion'] ?? null;

        if ($idCarritoProducto === null || !in_array($accion, ['sumar', 'restar'], true)) {
            header("Location: " . BASE_URL . "/carrito");
            exit();
        }

        $resultado = $this->carritoService->actualizarCantidad($idUsuario, $idCarritoProducto, $accion);

        if ($resultado['ok']) {
            $_SESSION['cantidades_carrito'] = max(0, ($_SESSION['cantidades_carrito'] ?? 0) + $resultado['delta']);
        } elseif ($resultado['mensaje'] !== '') {
            $_SESSION['mensaje_error'] = $resultado['mensaje'];
        }

        header("Location: " . BASE_URL . "/carrito");
        exit();
    }

    // ============================================================
    // ELIMINAR LÍNEA DEL CARRITO
    // ============================================================
    public function eliminar(): void {
        AuthMiddleware::verificarCliente();
        $idUsuario = (int) $_SESSION['id_usuario'];

        $idCarritoProducto = (isset($_POST['id_carrito_producto']) && ctype_digit($_POST['id_carrito_producto']))
            ? (int) $_POST['id_carrito_producto']
            : null;

        if ($idCarritoProducto === null) {
            header("Location: " . BASE_URL . "/carrito");
            exit();
        }

        // Necesitamos la cantidad ANTES de eliminar, para poder restarla de la sesión
        $cantidadAEliminar = $this->carritoService->obtenerCantidadLinea($idUsuario, $idCarritoProducto);

        if ($cantidadAEliminar === null) {
            $_SESSION['mensaje_error'] = "Esa línea no pertenece a tu carrito.";
            header("Location: " . BASE_URL . "/carrito");
            exit();
        }

        $this->carritoService->eliminarLinea($idUsuario, $idCarritoProducto);

        $_SESSION['cantidades_carrito'] = max(0, ($_SESSION['cantidades_carrito'] ?? 0) - $cantidadAEliminar);

        header("Location: " . BASE_URL . "/carrito");
        exit();
    }

    // ============================================================
    // MOSTRAR CHECKOUT
    // ============================================================
    public function mostrarCheckout(): void {
        AuthMiddleware::verificarCliente();
        $idUsuario = (int) $_SESSION['id_usuario'];

        $lineas = $this->carritoService->obtenerLineas($idUsuario);

        if (empty($lineas)) {
            $_SESSION['mensaje_error'] = "Tu carrito está vacío. Añade algún producto antes de finalizar la compra.";
            header("Location: " . BASE_URL . "/carrito");
            exit();
        }

        $totalCarrito = 0;
        foreach ($lineas as $linea) {
            if ($linea->getCantidad() > $linea->getProducto()->getStock()) {
                $_SESSION['mensaje_error'] = "El producto '" . $linea->getProducto()->getNombre() . "' solo tiene " . $linea->getProducto()->getStock() . " unidades disponibles. Ajusta la cantidad.";
                header("Location: " . BASE_URL . "/carrito");
                exit();
            }
            $totalCarrito += $linea->getSubtotal();
        }

        $data = ['lineas' => $lineas, 'totalCarrito' => $totalCarrito];
        extract($data);
        require __DIR__ . '/../views/public/checkout.php';
    }

    // ============================================================
    // PROCESAR CHECKOUT
    // ============================================================
    public function procesarCheckout(): void {
        AuthMiddleware::verificarCliente();
        $idUsuario = (int) $_SESSION['id_usuario'];

        $direccionEnvio = trim($_POST['direccion_envio'] ?? '');

        if ($direccionEnvio === '') {
            $_SESSION['errores'] = ['direccion_envio' => "Introduce una dirección de envío."];
            header("Location: " . BASE_URL . "/checkout");
            exit();
        }

        $resultado = $this->carritoService->procesarCheckout($idUsuario, $direccionEnvio);

        if ($resultado['ok']) {
            $_SESSION['cantidades_carrito'] = 0;
            $_SESSION['ultimo_pedido_id'] = $resultado['idPedido'];
            $_SESSION['mensaje_exito'] = $resultado['mensaje'];
            header("Location: " . BASE_URL . "/pedido-exito");
        } else {
            $_SESSION['mensaje_error'] = $resultado['mensaje'];
            header("Location: " . BASE_URL . "/carrito");
        }
        exit();
    }

    // ============================================================
    // PÁGINA DE ÉXITO
    // ============================================================
    public function pedidoExito(): void {
        AuthMiddleware::verificarCliente();

        if (!isset($_SESSION['ultimo_pedido_id'])) {
            header("Location: " . BASE_URL . "/catalogo");
            exit();
        }

        $idPedido = $_SESSION['ultimo_pedido_id'];
        unset($_SESSION['ultimo_pedido_id']);

        $data = ['idPedido' => $idPedido];
        extract($data);
        require __DIR__ . '/../views/public/pedido-exito.php';
    }
}