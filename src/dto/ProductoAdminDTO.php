<?php
namespace SonidoInteriorPoo\dto;

use SonidoInteriorPoo\models\Producto;

class ProductoAdminDTO {
    public function __construct(
        private readonly Producto $producto,
        private readonly string $nombreCategoria
    ) {}

    public function getProducto(): Producto {
        return $this->producto;
    }

    public function getNombreCategoria(): string {
        return $this->nombreCategoria;
    }
}