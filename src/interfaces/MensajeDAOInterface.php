<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Mensaje;

interface MensajeDAOInterface {
    // lectura DB
    public function obtenerTodosAdmin(): array;
    public function obtenerPorId(int $idMensaje): ?Mensaje;
    public function contarNoLeidos(): int;    
    // escritura DB
    public function marcarComoLeido(int $idMensaje): bool;
    public function guardar(string $nombre, string $email, ?string $telefono, ?string $motivo, string $mensaje): bool;
    public function eliminar(int $idMensaje): bool;
}