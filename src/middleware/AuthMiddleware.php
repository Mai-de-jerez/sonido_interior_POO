<?php
namespace SonidoInteriorPoo\middleware;

class AuthMiddleware {
    
    public static function verificarAdmin(): void {
        
        // Si no está logueado
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /login?status=denegado");
            exit();
        }
        
        // Si no es ADMIN
        if ($_SESSION['rol'] !== 'ADMIN') {
            http_response_code(403);
            echo "Acceso denegado. Se requiere rol de administrador.";
            exit();
        }
    }

    public static function verificarCliente(): void {
        session_start();
        
        // Si no está logueado
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /login?status=denegado");
            exit();
        }
        
        // Si no es CLIENTE
        if ($_SESSION['rol'] !== 'CLIENTE' && $_SESSION['rol'] !== 'ADMIN') {
            http_response_code(403);
            echo "Acceso denegado. Se requiere rol de cliente.";
            exit();
        }
    }

    public static function verificarLogueado(): void {
        session_start();
        
        // Si no está logueado (cualquier rol)
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /login?status=denegado");
            exit();
        }
    }
}