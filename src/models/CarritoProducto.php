<?php
namespace SonidoInteriorPoo\models;

class CarritoProducto {
    private int $idCarritoProducto;
    private int $cantidad;
    private float $precioUnitario;
    private int $idCarrito;
    private int $idProducto;

    public function __construct(int $idCarritoProducto, int $cantidad, float $precioUnitario, int $idCarrito, int $idProducto) {
        $this->idCarritoProducto = $idCarritoProducto;
        $this->cantidad = $cantidad;
        $this->precioUnitario = $precioUnitario;
        $this->idCarrito = $idCarrito;
        $this->idProducto = $idProducto;
    }

    public function getIdCarritoProducto(): int { return $this->idCarritoProducto; }
    public function getCantidad(): int { return $this->cantidad; }
    public function getPrecioUnitario(): float { return $this->precioUnitario; }
    public function getIdCarrito(): int { return $this->idCarrito; }
    public function getIdProducto(): int { return $this->idProducto; }
}