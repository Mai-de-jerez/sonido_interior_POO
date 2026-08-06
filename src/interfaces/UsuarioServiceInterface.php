<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Usuario;

interface UsuarioServiceInterface {
    // ---------- LOGIN / REGISTRO ----------
    public function login(string $usuario, string $password): ?array;
    public function registrar(string $usuario, string $email, string $password): bool;

    // ---------- OBTENER USUARIO ----------
    public function obtenerPorId(int $idUsuario): ?Usuario;

    // ---------- RECUPERACIÓN DE CONTRASEÑA ----------
    public function guardarTokenRecuperacion(string $email, string $token): bool;
    public function obtenerEmailPorToken(string $token): ?string;
    public function actualizarPasswordConToken(string $email, string $nuevaPassword): bool;

    // ---------- CAMBIAR CONTRASEÑA (logueado) ----------
    public function cambiarPassword(int $idUsuario, string $passwordActual, string $nuevaPassword): bool;

    // ---------- ACTUALIZAR PERFIL ----------
    public function actualizarNombre(int $idUsuario, string $nombre): bool;
}