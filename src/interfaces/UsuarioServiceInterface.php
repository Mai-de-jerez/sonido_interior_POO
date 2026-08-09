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
    public function solicitarRecuperacion(string $email): void;
    public function obtenerEmailPorToken(string $token): ?string;
    public function actualizarPasswordPorToken(string $token, string $nuevaPassword): bool;

    // ---------- CAMBIAR CONTRASEÑA (logueado) ----------
    public function cambiarPassword(int $idUsuario, string $passwordActual, string $nuevaPassword): bool;

    // ---------- ACTUALIZAR PERFIL ----------
    public function actualizarNombre(int $idUsuario, string $nombre): bool;
}