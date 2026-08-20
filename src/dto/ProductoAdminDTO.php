<?php
namespace SonidoInteriorPoo\dto;

class ProductoAdminDTO {
    public function __construct(
        private readonly int $idProducto,
        private readonly string $imagen,
        private readonly string $nombre,
        private readonly string $nombreCategoria,
        private readonly float $precio,
        private readonly int $stock,
        private readonly ?string $notaMusical,
        private readonly int $activo
    ) {}

    public function getIdProducto(): int { return $this->idProducto; }
    public function getImagen(): string { return $this->imagen; }
    public function getNombre(): string { return $this->nombre; }
    public function getNombreCategoria(): string { return $this->nombreCategoria; }
    public function getPrecio(): float { return $this->precio; }
    public function getStock(): int { return $this->stock; }
    public function getNotaMusical(): ?string { return $this->notaMusical; }
    public function getActivo(): int { return $this->activo; }
    public function isActivo(): bool { return $this->activo === 1; }
}