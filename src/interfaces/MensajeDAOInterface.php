<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Mensaje;

interface MensajeDAOInterface {
    public function guardar(string $nombre, string $email, ?string $telefono, ?string $motivo, string $mensaje): bool;
    public function obtenerTodosAdmin(): array;
    public function obtenerPorId(int $idMensaje): ?Mensaje;
    public function marcarComoLeido(int $idMensaje): bool;
    public function contarNoLeidos(): int;
    public function eliminar(int $idMensaje): bool;
}