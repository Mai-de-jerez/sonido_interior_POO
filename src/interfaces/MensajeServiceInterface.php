<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Mensaje;

interface MensajeServiceInterface {
    public function validar(array $datos): array;
    public function crear(array $datos): bool;
    public function obtenerTodosAdmin(): array;
    public function obtenerPorId(int $idMensaje): ?Mensaje;
    public function marcarComoLeido(int $idMensaje): bool;
    public function contarNoLeidos(): int;
    public function eliminar(int $idMensaje): bool;
}