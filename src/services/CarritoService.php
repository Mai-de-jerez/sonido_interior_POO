<?php
namespace SonidoInteriorPoo\services;


use SonidoInteriorPoo\interfaces\CarritoDAOInterface;
use SonidoInteriorPoo\interfaces\ProductoDAOInterface;
use SonidoInteriorPoo\interfaces\CarritoServiceInterface;
use SonidoInteriorPoo\exceptions\NotFoundException;
use SonidoInteriorPoo\exceptions\BusinessRuleException;

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


    public function agregarProducto(int $idUsuario, int $idProducto, int $cantidad): array {

        if ($cantidad <= 0) {
            throw new BusinessRuleException("La cantidad debe ser mayor que 0");
        }

        $producto = $this->productoDAO->obtenerPorId($idProducto);
        if ($producto === null || !$producto->isActivo()) {
            throw new NotFoundException('Producto no disponible');
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
            throw new BusinessRuleException("Stock insuficiente. Disponible: {$producto->getStock()}");
        }

        $this->carritoDAO->agregarProducto($idCarrito, $idProducto, $cantidad, $producto->getPrecio());

        return ['ok' => true, 'mensaje' => '¡Producto añadido al carrito correctamente!', 'unidadesAnadidas' => $cantidad];
    }


    public function actualizarCantidad(int $idUsuario, int $idCarritoProducto, string $accion): int {

        if (!in_array($accion, ['sumar', 'restar'], true)) {
            throw new BusinessRuleException('Acción no válida');
        }
        
        if (!$this->carritoDAO->lineaPerteneceAUsuario($idCarritoProducto, $idUsuario)) {
            throw new NotFoundException('Esa línea no pertenece a tu carrito.');
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
            throw new NotFoundException('La línea no existe.');
        }

        $cantidadActual = $lineaActual->getCantidad();

        if ($accion === 'sumar') {
            $stockInfo = $this->productoDAO->obtenerStockParaUpdate(
                $lineaActual->getProducto()->getIdProducto()
            );
            
            if ($stockInfo === null) {
                throw new NotFoundException('Producto no disponible');
            }
            
            $nuevaCantidad = $cantidadActual + 1;
            if ($nuevaCantidad > $stockInfo->getStock()) {
                throw new BusinessRuleException(
                    "No hay más stock disponible. Stock: {$stockInfo->getStock()}"
                );
            }

            $this->carritoDAO->actualizarCantidad($idCarritoProducto, $nuevaCantidad);
            return 1;
        }

        if ($accion === 'restar' && $cantidadActual > 1) {
            $this->carritoDAO->actualizarCantidad($idCarritoProducto, $cantidadActual - 1);
            return -1; 
        }

        throw new BusinessRuleException('No puedes reducir más la cantidad');
    }

    public function eliminarLinea(int $idUsuario, int $idCarritoProducto): int {

        if (!$this->carritoDAO->lineaPerteneceAUsuario($idCarritoProducto, $idUsuario)) {
            throw new NotFoundException('Esa línea no pertenece a tu carrito.');
        }

        $cantidad = $this->obtenerCantidadLinea($idUsuario, $idCarritoProducto);
        
        $this->carritoDAO->eliminarLinea($idCarritoProducto);
        return $cantidad; 
    }

    public function obtenerCantidadLinea(int $idUsuario, int $idCarritoProducto): int {

        if (!$this->carritoDAO->lineaPerteneceAUsuario($idCarritoProducto, $idUsuario)) {
            throw new NotFoundException('Esa línea no pertenece a tu carrito.');
        }

        $idCarrito = $this->carritoDAO->obtenerOCrearCarrito($idUsuario);
        foreach ($this->carritoDAO->obtenerLineas($idCarrito) as $linea) {
            if ($linea->getIdCarritoProducto() === $idCarritoProducto) {
                return $linea->getCantidad();
            }
        }
        
        throw new NotFoundException('Línea no encontrada');
    }
}
