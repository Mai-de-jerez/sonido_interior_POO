<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\dto\LoginDTO;

interface UsuarioServiceInterface {
    // ---------- LOGIN / REGISTRO ----------
    public function login(string $usuario, string $password): ?LoginDTO;
    public function registrar(string $usuario, string $email, string $password): void;
}