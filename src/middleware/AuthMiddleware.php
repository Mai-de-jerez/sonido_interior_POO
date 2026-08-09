<?php
namespace SonidoInteriorPoo\middleware;

use SonidoInteriorPoo\core\Session;

class AuthMiddleware {
    
    public static function verificarAdmin(): void {
        if (!Session::isLoggedIn()) {
            header("Location: " . BASE_URL . "/login?status=denegado");
            exit();
        }
        
        if (Session::getUserRole() !== 'ADMIN') {
            http_response_code(403);
            echo "Acceso denegado. Se requiere rol de administrador.";
            exit();
        }
    }

    public static function verificarCliente(): void {
        if (!Session::isLoggedIn()) {
            header("Location: " . BASE_URL . "/login?status=denegado");
            exit();
        }
        
        $rol = Session::getUserRole();
        if ($rol !== 'CLIENTE' && $rol !== 'ADMIN') {
            http_response_code(403);
            echo "Acceso denegado. Se requiere rol de cliente.";
            exit();
        }
    }

    public static function verificarLogueado(): void {
        if (!Session::isLoggedIn()) {
            header("Location: " . BASE_URL . "/login?status=denegado");
            exit();
        }
    }
}