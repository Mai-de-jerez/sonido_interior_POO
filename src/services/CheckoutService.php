<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\interfaces\TransactionManagerInterface;
use SonidoInteriorPoo\interfaces\CarritoDAOInterface;
use SonidoInteriorPoo\interfaces\ProductoDAOInterface;
use SonidoInteriorPoo\interfaces\PedidoServiceInterface;
use SonidoInteriorPoo\interfaces\CheckoutServiceInterface;

class CheckoutService implements CheckoutServiceInterface {
    private TransactionManagerInterface $transactionManager;
    private CarritoDAOInterface $carritoDAO;
    private ProductoDAOInterface $productoDAO;
    private PedidoServiceInterface $pedidoService;

    public function __construct(
        TransactionManagerInterface $transactionManager,
        CarritoDAOInterface $carritoDAO,
        ProductoDAOInterface $productoDAO,
        PedidoServiceInterface $pedidoService
    ) {
        $this->transactionManager = $transactionManager;
        $this->carritoDAO = $carritoDAO;
        $this->productoDAO = $productoDAO;
        $this->pedidoService = $pedidoService;
    }

    // Devuelve ['ok' => bool, 'mensaje' => string, 'idPedido' => ?int]
    public function procesarCheckout(int $idUsuario, string $direccionEnvio): array {
        $idCarrito = $this->carritoDAO->obtenerOCrearCarrito($idUsuario);
        $lineas = $this->carritoDAO->obtenerLineas($idCarrito);

        if (empty($lineas)) {
            return ['ok' => false, 'mensaje' => 'Tu carrito está vacío.', 'idPedido' => null];
        }

        try {
            return $this->transactionManager->transaction(function () use ($idUsuario, $direccionEnvio, $idCarrito, $lineas) {
                $totalPedido = 0;

                foreach ($lineas as $linea) {
                    $stockFila = $this->productoDAO->obtenerStockParaUpdate($linea->getProducto()->getIdProducto());

                    if (!$stockFila || $linea->getCantidad() > $stockFila->getStock()) {
                        $nombre = $stockFila ? $stockFila->getNombre() : $linea->getProducto()->getNombre();
                        $disponible = $stockFila ? $stockFila->getStock() : 0;
                        throw new \RuntimeException("El stock del producto '{$nombre}' ha cambiado. Disponibles: {$disponible}.");
                    }

                    $totalPedido += $linea->getSubtotal();
                }

                $idPedido = $this->pedidoService->crear($idUsuario, $totalPedido, $direccionEnvio);

                if (!$idPedido) {
                    throw new \RuntimeException('No se pudo registrar la cabecera del pedido.');
                }

                foreach ($lineas as $linea) {
                    $detalleOk = $this->pedidoService->crearDetalle(
                        $idPedido,
                        $linea->getProducto()->getIdProducto(),
                        $linea->getCantidad(),
                        $linea->getPrecioUnitario()
                    );

                    $stockOk = $this->productoDAO->descontarStock($linea->getProducto()->getIdProducto(), $linea->getCantidad());

                    if (!$detalleOk || !$stockOk) {
                        throw new \RuntimeException('Error al procesar el desglose del pedido.');
                    }
                }

                $this->carritoDAO->vaciarCarrito($idCarrito);

                return ['ok' => true, 'mensaje' => "¡Pedido #{$idPedido} realizado con éxito!", 'idPedido' => $idPedido];
            });
        } catch (\Throwable $e) {
            return ['ok' => false, 'mensaje' => $e->getMessage(), 'idPedido' => null];
        }
    }

}