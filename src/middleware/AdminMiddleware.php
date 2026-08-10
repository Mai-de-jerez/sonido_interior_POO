<?php

namespace SonidoInteriorPoo\middleware;

use SonidoInteriorPoo\core\Session;

class AdminMiddleware
{
    public function handle(): void
    {
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
}