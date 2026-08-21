<?php
namespace SonidoInteriorPoo\dto;

class LoginDTO {
    public function __construct(
        private readonly int $idUsuario,
        private readonly string $usuario,
        private readonly string $rol,
        private readonly int $cantidadesCarrito
    ) {}

    public function getIdUsuario(): int { return $this->idUsuario; }
    public function getUsuario(): string { return $this->usuario; }
    public function getRol(): string { return $this->rol; }
    public function getCantidadesCarrito(): int { return $this->cantidadesCarrito; }
}