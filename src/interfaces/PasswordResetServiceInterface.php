<?php
namespace SonidoInteriorPoo\interfaces;

interface PasswordResetServiceInterface {
    public function solicitarRecuperacion(string $email): void;
    public function actualizarPasswordPorToken(string $token, string $nuevaPassword): void;
}