<?php
namespace SonidoInteriorPoo\interfaces;

interface PedidoDAOInterface {
    public function crear(int $idUsuario, float $total, string $direccionEnvio): int;
    public function crearDetalle(int $idPedido, int $idProducto, int $cantidad, float $precioUnitario): void;
}