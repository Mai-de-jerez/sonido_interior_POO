<?php
namespace SonidoInteriorPoo\models;

class Pedido {
    private int $idPedido;
    private string $fechaPedido;
    private string $estado;
    private float $total;
    private string $direccionEnvio;
    private int $idUsuario;

    public function __construct(int $idPedido, string $fechaPedido, string $estado, float $total, string $direccionEnvio, int $idUsuario) {
        $this->idPedido = $idPedido;
        $this->fechaPedido = $fechaPedido;
        $this->estado = $estado;
        $this->total = $total;
        $this->direccionEnvio = $direccionEnvio;
        $this->idUsuario = $idUsuario;
    }

    public function getIdPedido(): int { return $this->idPedido; }
    public function getFechaPedido(): string { return $this->fechaPedido; }
    public function getEstado(): string { return $this->estado; }
    public function getTotal(): float { return $this->total; }
    public function getDireccionEnvio(): string { return $this->direccionEnvio; }
    public function getIdUsuario(): int { return $this->idUsuario; }
}