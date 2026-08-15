<?php
namespace SonidoInteriorPoo\core;

abstract class Controller {

    // ============================================================
    // ACCESO A SESIÓN (WRAPPER DE Session)
    // ============================================================
    protected function setSession(string $key, mixed $value): void {
        Session::set($key, $value);
    }

    protected function getSession(string $key, mixed $default = null): mixed {
        return Session::get($key, $default);
    }

    protected function hasSession(string $key): bool {
        return Session::has($key);
    }

    protected function removeSession(string $key): void {
        Session::remove($key);
    }

    // ============================================================
    // MENSAJES FLASH (WRAPPER DE Session)
    // ============================================================
    protected function setFlash(string $key, mixed $value): void {
        Session::setFlash($key, $value);
    }

    protected function getFlash(string $key, mixed $default = null): mixed {
        return Session::getFlash($key, $default);
    }

    // ============================================================
    // HELPERS DE AUTENTICACIÓN (WRAPPER DE Session)
    // ============================================================
    protected function isLoggedIn(): bool {
        return Session::isLoggedIn();
    }

    protected function getUserId(): ?int {
        return Session::getUserId();
    }

    protected function getUserRole(): ?string {
        return Session::getUserRole();
    }

    // ============================================================
    // CSRF PROTECTION
    // ============================================================

    protected function csrfToken(): string {
        return Session::getCsrfToken();
    }
}