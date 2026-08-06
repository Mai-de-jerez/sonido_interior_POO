<?php
namespace SonidoInteriorPoo\models;

class DetallePedido {
    private int $idDetalle;
    private int $cantidad;
    private float $precioUnitario;
    private float $subtotal;
    private int $idPedido;
    private int $idProducto;

    public function __construct(int $idDetalle, int $cantidad, float $precioUnitario, float $subtotal, int $idPedido, int $idProducto) {
        $this->idDetalle = $idDetalle;
        $this->cantidad = $cantidad;
        $this->precioUnitario = $precioUnitario;
        $this->subtotal = $subtotal;
        $this->idPedido = $idPedido;
        $this->idProducto = $idProducto;
    }

    public function getIdDetalle(): int { return $this->idDetalle; }
    public function getCantidad(): int { return $this->cantidad; }
    public function getPrecioUnitario(): float { return $this->precioUnitario; }
    public function getSubtotal(): float { return $this->subtotal; }
    public function getIdPedido(): int { return $this->idPedido; }
    public function getIdProducto(): int { return $this->idProducto; }
}