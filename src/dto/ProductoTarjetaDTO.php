<?php
namespace SonidoInteriorPoo\dto;

class ProductoTarjetaDTO {
    public function __construct(
        private int $idProducto,
        private string $nombre,
        private float $precio,
        private string $imagen
    ) {}

    public function getIdProducto(): int { return $this->idProducto; }
    public function getNombre(): string { return $this->nombre; }
    public function getPrecio(): float { return $this->precio; }
    public function getImagen(): string { return $this->imagen; }
}