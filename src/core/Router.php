<?php

namespace SonidoInteriorPoo\core;

class Router 
{
    private array $routes = [];
    private array $groupMiddlewares = [];

    // Agrupar rutas bajo middlewares comunes
    public function group(array $middlewares, callable $callback): void
    {
        $previousMiddlewares = $this->groupMiddlewares;
        $this->groupMiddlewares = array_merge($this->groupMiddlewares, $middlewares);

        $callback($this);

        $this->groupMiddlewares = $previousMiddlewares;
    }

    public function get(string $path, array $action): void 
    {
        $this->addRoute('GET', $path, $action);
    }

    public function post(string $path, array $action): void 
    {
        $this->addRoute('POST', $path, $action);
    }

    private function addRoute(string $method, string $path, array $action): void 
    {
        $this->routes["$method $path"] = [
            'action' => $action,
            'middlewares' => $this->groupMiddlewares
        ];
    }

    public function dispatch(string $method, string $uri, Container $container): void 
    {
        $uri = str_replace([BASE_URL, '/index.php'], '', $uri);
        $uri = strtok($uri, '?'); 
        
        if ($uri === '' || $uri === false) {
            $uri = '/';
        }

        $clave = "$method $uri";

        if (!array_key_exists($clave, $this->routes)) {
            http_response_code(404);

            require __DIR__ . '/../views/public/not-found.php';

            return;
        }

        $routeData = $this->routes[$clave];

        // 1. Ejecutar Middlewares del grupo (si los hay)
        foreach ($routeData['middlewares'] as $middlewareClass) {
            $middleware = new $middlewareClass();
            $middleware->handle(); // Si falla, redirige y corta con exit;
        }

        // 2. Resolver el controlador BAJO DEMANDA usando el Container
        [$controllerClass, $metodo] = $routeData['action'];
        
        $controller = is_string($controllerClass) 
            ? $container->get($controllerClass) 
            : $controllerClass;

        $controller->$metodo();
    }
}