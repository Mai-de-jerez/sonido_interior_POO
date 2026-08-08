<?php

namespace SonidoInteriorPoo\core; 

class Router 
{
    private array $routes = [];

    // Registrar rutas HTTP GET
    public function get(string $path, array|callable $action): void 
    {
        $this->addRoute('GET', $path, $action);
    }

    // Registrar rutas HTTP POST
    public function post(string $path, array|callable $action): void 
    {
        $this->addRoute('POST', $path, $action);
    }

    private function addRoute(string $method, string $path, array|callable $action): void 
    {
        $this->routes["$method $path"] = $action;
    }

    // Procesar la petición y ejecutar el controlador
    public function dispatch(string $method, string $uri): void 
    {
        // Limpiar la URI eliminando la carpeta del proyecto e index.php
        $uri = str_replace([BASE_URL, '/index.php'], '', $uri);
        $uri = strtok($uri, '?'); // Elimina query strings (?id=1)
        
        if ($uri === '' || $uri === false) {
            $uri = '/';
        }

        $clave = "$method $uri";

        if (array_key_exists($clave, $this->routes)) {
            $accion = $this->routes[$clave];

            if (is_array($accion)) {
                [$controller, $metodoControlador] = $accion;
                $controller->$metodoControlador();
            } else {
                $accion();
            }
        } else {
            http_response_code(404);
            echo "Página no encontrada. URI: " . htmlspecialchars($uri);
        }
    }
}