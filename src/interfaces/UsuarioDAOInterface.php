<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Usuario;

interface UsuarioDAOInterface {
    public function obtenerPorUsername(string $usuario): ?Usuario;
    public function obtenerPorEmail(string $email): ?Usuario;
    public function obtenerPorId(int $idUsuario): ?Usuario;
    public function registrar(string $usuario, string $email, string $passwordHash): bool;
    public function actualizarPassword(string $email, string $nuevaPasswordHash): bool;
    public function existeUsuario(string $usuario, string $email): bool;
}