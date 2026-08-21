<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\interfaces\PedidoDAOInterface;
use SonidoInteriorPoo\interfaces\PedidoServiceInterface;

class PedidoService implements PedidoServiceInterface {
    private PedidoDAOInterface $pedidoDAO;

    public function __construct(PedidoDAOInterface $pedidoDAO) {
        $this->pedidoDAO = $pedidoDAO;
    }

    public function crear(int $idUsuario, float $total, string $direccionEnvio): int {
        return $this->pedidoDAO->crear($idUsuario, $total, $direccionEnvio);
    }

    public function crearDetalle(int $idPedido, int $idProducto, int $cantidad, float $precioUnitario): void {
        $this->pedidoDAO->crearDetalle($idPedido, $idProducto, $cantidad, $precioUnitario);
    }
}