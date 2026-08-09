<?php
namespace SonidoInteriorPoo\core;

class Session {

    /**
     * Inicia la sesión aplicando parámetros de seguridad en las cookies.
     */
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,                            // Expira al cerrar el navegador
                'path'     => '/',                          // Disponible en todo el sitio
                'domain'   => '',                           // Dominio actual
                'secure'   => isset($_SERVER['HTTPS']),     // Solo encriptado si hay HTTPS
                'httponly' => true,                         // Impide acceso vía Javascript (protección XSS)
                'samesite' => 'Lax'                         // Mitiga ataques CSRF
            ]);
            
            session_start();
        }
    }

    /**
     * Guarda un valor en la sesión.
     */
    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    /**
     * Obtiene un valor de la sesión. Si no existe, devuelve el valor por defecto.
     */
    public static function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Comprueba si existe una clave en la sesión.
     */
    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    /**
     * Elimina una clave concreta de la sesión.
     */
    public static function remove(string $key): void {
        if (self::has($key)) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Regenera el ID de la sesión para prevenir Session Fixation.
     */
    public static function regenerate(): void {
        session_regenerate_id(true);
    }

    /**
     * Destruye por completo la sesión y limpia la cookie del navegador (Logout).
     */
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

    /**
     * Guarda un mensaje o dato efímero para la siguiente petición.
     */
    public static function setFlash(string $key, mixed $value): void {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Lee un mensaje efímero y lo elimina de la sesión.
     */
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
}