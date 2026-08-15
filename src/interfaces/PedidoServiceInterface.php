<?php
namespace SonidoInteriorPoo\interfaces;

interface PedidoServiceInterface {
    // Usado por CarritoService dentro de la transacción de checkout
    public function crear(int $idUsuario, float $total, string $direccionEnvio): int|false;
    public function crearDetalle(int $idPedido, int $idProducto, int $cantidad, float $precioUnitario): bool;
}