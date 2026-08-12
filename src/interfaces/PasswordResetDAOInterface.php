<?php
namespace SonidoInteriorPoo\interfaces;

interface PasswordResetDAOInterface {
    public function borrarTokensDe(string $email): bool;
    public function insertarToken(string $email, string $token): bool;
    public function borrarTokenDe(string $email): bool;
    public function obtenerEmailPorToken(string $token): ?string;
}