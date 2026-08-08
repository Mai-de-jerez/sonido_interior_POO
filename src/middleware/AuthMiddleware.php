<?php
namespace SonidoInteriorPoo\middleware;

class AuthMiddleware {
    
    public static function verificarAdmin(): void {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . BASE_URL . "/login?status=denegado");
            exit();
        }
        
        if ($_SESSION['rol'] !== 'ADMIN') {
            http_response_code(403);
            echo "Acceso denegado. Se requiere rol de administrador.";
            exit();
        }
    }

    public static function verificarCliente(): void {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . BASE_URL . "/login?status=denegado");
            exit();
        }
        
        if ($_SESSION['rol'] !== 'CLIENTE' && $_SESSION['rol'] !== 'ADMIN') {
            http_response_code(403);
            echo "Acceso denegado. Se requiere rol de cliente.";
            exit();
        }
    }

    public static function verificarLogueado(): void {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . BASE_URL . "/login?status=denegado");
            exit();
        }
    }
}