<?php
namespace SonidoInteriorPoo\models;

class Carrito {
    private int $idCarrito;
    private string $fechaCreacion;
    private int $idUsuario;

    public function __construct(int $idCarrito, string $fechaCreacion, int $idUsuario) {
        $this->idCarrito = $idCarrito;
        $this->fechaCreacion = $fechaCreacion;
        $this->idUsuario = $idUsuario;
    }

    public function getIdCarrito(): int { return $this->idCarrito; }
    public function getFechaCreacion(): string { return $this->fechaCreacion; }
    public function getIdUsuario(): int { return $this->idUsuario; }
}