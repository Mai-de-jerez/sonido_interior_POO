<?php
namespace SonidoInteriorPoo\dto;

use SonidoInteriorPoo\models\Producto;

class CarritoLineaDTO {
    public function __construct(
        private readonly int $idCarritoProducto,
        private readonly int $cantidad,
        private readonly float $precioUnitario,
        private readonly Producto $producto
    ) {}

    public function getIdCarritoProducto(): int { return $this->idCarritoProducto; }
    public function getCantidad(): int { return $this->cantidad; }
    public function getPrecioUnitario(): float { return $this->precioUnitario; }
    public function getProducto(): Producto { return $this->producto; }
    public function getSubtotal(): float { return $this->cantidad * $this->precioUnitario; }
}