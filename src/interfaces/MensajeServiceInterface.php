<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Mensaje;

interface MensajeServiceInterface {
    // ---------- ESCRITURA ----------
    public function crear(array $datos): void;
    public function eliminar(int $idMensaje): void;
    public function marcarComoLeido(int $idMensaje): void;
    // ---------- LECTURA ----------
    public function obtenerTodosAdmin(): array;
    public function obtenerPorId(int $idMensaje): ?Mensaje;    
    public function contarNoLeidos(): int;
    
}