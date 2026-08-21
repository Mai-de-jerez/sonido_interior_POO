<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\interfaces\TransactionManagerInterface;
use SonidoInteriorPoo\interfaces\CarritoDAOInterface;
use SonidoInteriorPoo\interfaces\ProductoDAOInterface;
use SonidoInteriorPoo\interfaces\PedidoServiceInterface;
use SonidoInteriorPoo\interfaces\CheckoutServiceInterface;
use SonidoInteriorPoo\exceptions\BusinessRuleException;

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

    public function procesarCheckout(int $idUsuario, string $direccionEnvio): int {
        $idCarrito = $this->carritoDAO->obtenerOCrearCarrito($idUsuario);
        $lineas = $this->carritoDAO->obtenerLineas($idCarrito);

        if (empty($lineas)) {
            throw new BusinessRuleException('Tu carrito está vacío.');
        }

        return $this->transactionManager->transaction(function () use ($idUsuario, $direccionEnvio, $idCarrito, $lineas) {
            $totalPedido = 0;

            foreach ($lineas as $linea) {
                $stockInfo = $this->productoDAO->obtenerStockParaUpdate($linea->getProducto()->getIdProducto());

                if ($stockInfo === null || $linea->getCantidad() > $stockInfo->getStock()) {
                    $nombre = $stockInfo?->getNombre() ?? $linea->getProducto()->getNombre();
                    $disponible = $stockInfo?->getStock() ?? 0;
                    throw new BusinessRuleException("El stock del producto '{$nombre}' ha cambiado. Disponibles: {$disponible}.");
                }

                $totalPedido += $linea->getSubtotal();
            }

            $idPedido = $this->pedidoService->crear($idUsuario, $totalPedido, $direccionEnvio);

            foreach ($lineas as $linea) {
                $this->pedidoService->crearDetalle(
                    $idPedido,
                    $linea->getProducto()->getIdProducto(),
                    $linea->getCantidad(),
                    $linea->getPrecioUnitario()
                );

                if (!$this->productoDAO->descontarStock($linea->getProducto()->getIdProducto(), $linea->getCantidad())) {
                    throw new BusinessRuleException("No había stock suficiente de '{$linea->getProducto()->getNombre()}' al confirmar el pedido.");
                }
            }

            $this->carritoDAO->vaciarCarrito($idCarrito);

            return $idPedido;
        });
    }
}