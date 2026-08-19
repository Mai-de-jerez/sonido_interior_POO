<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Usuario;

interface UsuarioDAOInterface {
    // lectura
    public function obtenerPorUsername(string $usuario): ?Usuario;
    public function obtenerPorEmail(string $email): ?Usuario;
    public function obtenerPorId(int $idUsuario): ?Usuario;
    public function existeUsuario(string $usuario): bool;
    public function existeEmail(string $email): bool;
    // escritura
    public function registrar(string $usuario, string $email, string $passwordHash): bool;
    public function actualizarPassword(string $email, string $nuevaPasswordHash): bool;   
}