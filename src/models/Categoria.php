<?php
namespace SonidoInteriorPoo\models;

class Categoria {
    private int $idCategoria;
    private string $nombre;
    private ?string $descripcion;
    private int $activo;

    public function __construct(int $idCategoria, string $nombre, ?string $descripcion, int $activo = 1) {
        $this->idCategoria = $idCategoria;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->activo = $activo;
    }

    public static function fromArray(array $fila): self {
        return new self(
            (int) $fila['id_categoria'],
            $fila['nombre'],
            $fila['descripcion'] ?? null,
            (int) ($fila['activo'] ?? 1)
        );
    }

    public function getIdCategoria(): int { return $this->idCategoria; }
    public function getNombre(): string { return $this->nombre; }
    public function getDescripcion(): ?string { return $this->descripcion; }
    public function isActivo(): bool { return $this->activo === 1; }
}