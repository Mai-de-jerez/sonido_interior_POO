<?php
namespace SonidoInteriorPoo\models;

class Usuario {
    private ?int $idUsuario;
    private ?string $nombre;
    private string $email;
    private string $usuario;
    private string $password;
    private string $rol;
    private string $fechaRegistro;

    public function __construct(?int $idUsuario, ?string $nombre, string $email, string $usuario, string $password, string $rol, string $fechaRegistro) {
        $this->idUsuario = $idUsuario;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->usuario = $usuario;
        $this->password = $password;
        $this->rol = $rol;
        $this->fechaRegistro = $fechaRegistro;
    }

    // Named constructor: crea un Usuario a partir de una fila de BD (array asociativo de PDO)
    public static function fromArray(array $fila): self {
        return new self(
            (int) $fila['id_usuario'],
            $fila['nombre'],
            $fila['email'],
            $fila['usuario'],
            $fila['password'],
            $fila['rol'],
            $fila['fecha_registro']
        );
    }

    public function getIdUsuario(): ?int { return $this->idUsuario; }
    public function getNombre(): ?string { return $this->nombre; }
    public function getEmail(): string { return $this->email; }
    public function getUsuario(): string { return $this->usuario; }
    public function getRol(): string { return $this->rol; }
    public function getPassword(): string { return $this->password; }
    public function getFechaRegistro(): string { return $this->fechaRegistro; }
}