<?php
namespace SonidoInteriorPoo\core;

class Session {

    /**
     * Inicia la sesión aplicando parámetros de seguridad en las cookies.
     */
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        if (self::has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public static function clear(): void {
        $_SESSION = [];
    }

    public static function regenerate(): void {
        session_regenerate_id(true);
    }

    public static function destroy(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }

            session_destroy();
        }
    }

    // ============================================================
    // MENSÁJERÍA EFÍMERA (FLASH MESSAGES)
    // ============================================================

    public static function setFlash(string $key, mixed $value): void {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed {
        if (isset($_SESSION['_flash'][$key])) {
            $value = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            
            if (empty($_SESSION['_flash'])) {
                unset($_SESSION['_flash']);
            }
            
            return $value;
        }
        
        return $default;
    }

    // ============================================================
    // HELPERS GENERALES DE AUTENTICACIÓN
    // ============================================================

    public static function isLoggedIn(): bool {
        return self::has('id_usuario');
    }

    public static function getUserId(): ?int {
        return self::get('id_usuario');
    }

    public static function getUserRole(): ?string {
        return self::get('rol');
    }

    // ============
    // CSRF TOKEN 
    // ============

    /**
     * Genera un token CSRF y lo guarda en sesión
     * Devuelve el token generado
     */
    public static function generateCsrfToken(): string {
        $token = bin2hex(random_bytes(32));
        self::set('csrf_token', $token);
        return $token;
    }

    /**
     * Obtiene el token CSRF actual
     * Si no existe, lo genera
     */
    public static function getCsrfToken(): string {
        $token = self::get('csrf_token');
        if ($token === null) {
            $token = self::generateCsrfToken();
        }
        return $token;
    }

    /**
     * Verifica si el token recibido es válido
     * El token se elimina después de usarlo (ONE-TIME)
     */
    public static function verifyCsrfToken(string $token): bool {
        $stored = self::get('csrf_token');
        
        if ($stored === null || $token !== $stored) {
            return false;
        }
        
        // Token válido, lo eliminamos para que sea de un solo uso
        self::remove('csrf_token');
        return true;
    }
}