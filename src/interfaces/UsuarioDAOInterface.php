<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Usuario;

interface UsuarioDAOInterface {
    public function obtenerPorUsername(string $usuario): ?Usuario;
    public function obtenerPorEmail(string $email): ?Usuario;
    public function obtenerPorId(int $idUsuario): ?Usuario;
    public function registrar(string $usuario, string $email, string $passwordHash): bool;
    public function guardarTokenRecuperacion(string $email, string $token): bool;
    public function obtenerEmailPorToken(string $token): ?string;
    public function actualizarPasswordYBorrarToken(string $email, string $nuevaPasswordHash): bool;
    public function existeUsuario(string $usuario, string $email): bool;
    public function actualizarNombre(int $idUsuario, string $nombre): bool;
    public function cambiarPassword(int $idUsuario, string $nuevaPasswordHash): bool;
}