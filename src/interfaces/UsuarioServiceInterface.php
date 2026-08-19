<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Usuario;

interface UsuarioServiceInterface {
    // ---------- LOGIN / REGISTRO ----------
    public function login(string $usuario, string $password): ?array;
    public function registrar(string $usuario, string $email, string $password): void;
}