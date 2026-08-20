<?php
namespace SonidoInteriorPoo\services;


use SonidoInteriorPoo\interfaces\CarritoDAOInterface;
use SonidoInteriorPoo\interfaces\ProductoDAOInterface;
use SonidoInteriorPoo\interfaces\CarritoServiceInterface;

class CarritoService implements CarritoServiceInterface {

    private CarritoDAOInterface $carritoDAO;
    private ProductoDAOInterface $productoDAO;


    public function __construct(
        CarritoDAOInterface $carritoDAO,
        ProductoDAOInterface $productoDAO
    ) {
        $this->carritoDAO = $carritoDAO;
        $this->productoDAO = $productoDAO;
    }

    public function obtenerLineas(int $idUsuario): array {
        $idCarrito = $this->carritoDAO->obtenerOCrearCarrito($idUsuario);
        return $this->carritoDAO->obtenerLineas($idCarrito);
    }

    public function contarUnidades(int $idUsuario): int {
        return $this->carritoDAO->contarUnidades($idUsuario);
    }

    // Devuelve ['ok' => bool, 'mensaje' => string]
    public function agregarProducto(int $idUsuario, int $idProducto, int $cantidad): array {
        $producto = $this->productoDAO->obtenerPorId($idProducto);

        if ($producto === null) {
            return ['ok' => false, 'mensaje' => 'El producto no existe.'];
        }

        $idCarrito = $this->carritoDAO->obtenerOCrearCarrito($idUsuario);
        $lineas = $this->carritoDAO->obtenerLineas($idCarrito);

        $cantidadEnCarrito = 0;
        foreach ($lineas as $linea) {
            if ($linea->getProducto()->getIdProducto() === $idProducto) {
                $cantidadEnCarrito = $linea->getCantidad();
                break;
            }
        }

        $cantidadTotalFutura = $cantidadEnCarrito + $cantidad;

        if ($cantidadTotalFutura > $producto->getStock()) {
            return [
                'ok' => false,
                'mensaje' => "No puedes añadir tantas unidades. Stock disponible: {$producto->getStock()} (ya tienes {$cantidadEnCarrito} en el carrito)."
            ];
        }

        $this->carritoDAO->agregarProducto($idCarrito, $idProducto, $cantidad, $producto->getPrecio());

        return ['ok' => true, 'mensaje' => '¡Producto añadido al carrito correctamente!', 'unidadesAnadidas' => $cantidad];
    }

    // $accion: 'sumar' | 'restar'. Devuelve ['ok' => bool, 'mensaje' => string, 'delta' => int]
    public function actualizarCantidad(int $idUsuario, int $idCarritoProducto, string $accion): array {
        if (!$this->carritoDAO->lineaPerteneceAUsuario($idCarritoProducto, $idUsuario)) {
            return ['ok' => false, 'mensaje' => 'Esa línea no pertenece a tu carrito.', 'delta' => 0];
        }

        $idCarrito = $this->carritoDAO->obtenerOCrearCarrito($idUsuario);
        $lineas = $this->carritoDAO->obtenerLineas($idCarrito);

        $lineaActual = null;
        foreach ($lineas as $linea) {
            if ($linea->getIdCarritoProducto() === $idCarritoProducto) {
                $lineaActual = $linea;
                break;
            }
        }

        if ($lineaActual === null) {
            return ['ok' => false, 'mensaje' => 'La línea no existe.', 'delta' => 0];
        }

        $cantidadActual = $lineaActual->getCantidad();

        if ($accion === 'sumar') {
            $stockReal = $lineaActual->getProducto()->getStock();
            $nuevaCantidad = $cantidadActual + 1;

            if ($nuevaCantidad > $stockReal) {
                return ['ok' => false, 'mensaje' => 'No hay más stock disponible para este producto.', 'delta' => 0];
            }

            $this->carritoDAO->actualizarCantidad($idCarritoProducto, $nuevaCantidad);
            return ['ok' => true, 'mensaje' => '', 'delta' => 1];
        }

        if ($accion === 'restar' && $cantidadActual > 1) {
            $this->carritoDAO->actualizarCantidad($idCarritoProducto, $cantidadActual - 1);
            return ['ok' => true, 'mensaje' => '', 'delta' => -1];
        }

        return ['ok' => false, 'mensaje' => 'Acción no válida.', 'delta' => 0];
    }

    // Devuelve el número de unidades eliminadas (para restar de sesión), o false si no pertenece al usuario    
    public function eliminarLinea(int $idUsuario, int $idCarritoProducto): bool {
        if (!$this->carritoDAO->lineaPerteneceAUsuario($idCarritoProducto, $idUsuario)) {
            return false;
        }

        $this->carritoDAO->eliminarLinea($idCarritoProducto);
        return true;
    }

    // Devuelve la cantidad eliminada, o null si la línea no pertenecía al usuario
    public function obtenerCantidadLinea(int $idUsuario, int $idCarritoProducto): ?int {
        if (!$this->carritoDAO->lineaPerteneceAUsuario($idCarritoProducto, $idUsuario)) {
            return null;
        }

        $idCarrito = $this->carritoDAO->obtenerOCrearCarrito($idUsuario);
        foreach ($this->carritoDAO->obtenerLineas($idCarrito) as $linea) {
            if ($linea->getIdCarritoProducto() === $idCarritoProducto) {
                return $linea->getCantidad();
            }
        }
        return null;
    }
}
