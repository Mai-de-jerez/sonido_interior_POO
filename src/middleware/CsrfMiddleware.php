<?php
namespace SonidoInteriorPoo\middleware;

use SonidoInteriorPoo\core\Session;

class CsrfMiddleware
{
    public function handle(): void
    {
        // Solo comprobamos CSRF en peticiones que modifican datos
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $token = $_POST['csrf_token'] ?? '';

        if (!Session::verifyCsrfToken($token)) {
            http_response_code(403);
            echo "Token de seguridad inválido. Por favor, recarga la página e inténtalo de nuevo.";
            exit();
        }
    }
}