<?php
namespace SonidoInteriorPoo\dto;

class ProductoStockDTO {
    public function __construct(
        private int $stock,
        private string $nombre
    ) {}

    public function getStock(): int { return $this->stock; }
    public function getNombre(): string { return $this->nombre; }
}