<?php
namespace SonidoInteriorPoo\models;

class Producto {
    private int $idProducto;
    private int $idCategoria;
    private string $nombre;
    private ?string $descripcion;
    private float $precio;
    private int $stock;
    private ?string $imagen;
    private ?float $diametro;
    private ?float $peso;
    private ?string $material;
    private ?string $notaMusical;
    private ?string $procedencia;
    private int $activo;
    private ?string $fechaAlta;


    public function __construct(
        int $idProducto, int $idCategoria, string $nombre, ?string $descripcion,
        float $precio, int $stock, ?string $imagen, ?float $diametro,
        ?float $peso, ?string $material, ?string $notaMusical, ?string $procedencia,
        int $activo = 1, ?string $fechaAlta = null
    ) {
        $this->idProducto = $idProducto;
        $this->idCategoria = $idCategoria;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->stock = $stock;
        $this->imagen = $imagen;
        $this->diametro = $diametro;
        $this->peso = $peso;
        $this->material = $material;
        $this->notaMusical = $notaMusical;
        $this->procedencia = $procedencia;
        $this->activo = $activo;
        $this->fechaAlta = $fechaAlta;
    }

    // Named constructor: crea un Producto a partir de una fila de BD (array asociativo de PDO)
    public static function fromArray(array $fila): self {
        return new self(
            (int) $fila['id_producto'],
            (int) $fila['id_categoria'],
            $fila['nombre'],
            $fila['descripcion'] ?? null,
            (float) $fila['precio'],
            (int) $fila['stock'],
            $fila['imagen'] ?? null,
            isset($fila['diametro']) ? (float) $fila['diametro'] : null,
            isset($fila['peso']) ? (float) $fila['peso'] : null,
            $fila['material'] ?? null,
            $fila['nota_musical'] ?? null,
            $fila['procedencia'] ?? null,
            (int) ($fila['activo'] ?? 1),
            $fila['fecha_alta'] ?? null
        );
    }

    public function getIdProducto(): int { return $this->idProducto; }
    public function getIdCategoria(): int { return $this->idCategoria; }
    public function getNombre(): string { return $this->nombre; }
    public function getDescripcion(): ?string { return $this->descripcion; }
    public function getPrecio(): float { return $this->precio; }
    public function getStock(): int { return $this->stock; }
    public function getImagen(): ?string { return $this->imagen; }
    public function getDiametro(): ?float { return $this->diametro; }
    public function getPeso(): ?float { return $this->peso; }
    public function getMaterial(): ?string { return $this->material; }
    public function getNotaMusical(): ?string { return $this->notaMusical; }
    public function getProcedencia(): ?string { return $this->procedencia; }
    public function isActivo(): bool { return $this->activo === 1; }
    public function getFechaAlta(): ?string { return $this->fechaAlta; }
}