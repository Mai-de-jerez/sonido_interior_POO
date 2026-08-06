<?php
namespace SonidoInteriorPoo\models;

class Mensaje {
    private int $idMensaje;
    private string $nombre;
    private string $email;
    private ?string $telefono;
    private ?string $motivo;
    private string $mensaje;
    private string $fechaEnvio;
    private int $leido;

    public function __construct(int $idMensaje, string $nombre, string $email, ?string $telefono, ?string $motivo, string $mensaje, string $fechaEnvio, int $leido) {
        $this->idMensaje = $idMensaje;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->motivo = $motivo;
        $this->mensaje = $mensaje;
        $this->fechaEnvio = $fechaEnvio;
        $this->leido = $leido;
    }

    public function getIdMensaje(): int { return $this->idMensaje; }
    public function getNombre(): string { return $this->nombre; }
    public function getEmail(): string { return $this->email; }
    public function getTelefono(): ?string { return $this->telefono; }
    public function getMotivo(): ?string { return $this->motivo; }
    public function getMensaje(): string { return $this->mensaje; }
    public function getFechaEnvio(): string { return $this->fechaEnvio; }
    public function isLeido(): bool { return $this->leido === 1; }
}