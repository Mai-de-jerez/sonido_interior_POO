<?php

namespace SonidoInteriorPoo\core;

class Router
{
    private array $routes = [];
    private array $groupMiddlewares = [];

    /**
     * Agrupa rutas bajo uno o varios middlewares.
     */
    public function group(array $middlewares, callable $callback): void
    {
        $previousMiddlewares = $this->groupMiddlewares;

        $this->groupMiddlewares = array_merge(
            $this->groupMiddlewares,
            $middlewares
        );

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

    /**
     * Registra una ruta.
     */
    private function addRoute(
        string $method,
        string $path,
        array $action
    ): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $this->crearPattern($path),
            'action' => $action,
            'middlewares' => $this->groupMiddlewares
        ];
    }

    /**
     * Convierte:
     *
     * /productos/{id}
     *
     * en:
     *
     * #^/productos/(?P<id>[^/]+)$#
     */
    private function crearPattern(string $path): string
    {
        $pattern = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            function (array $match): string {
                $nombreParametro = $match[1];

                return '(?P<' . $nombreParametro . '>[^/]+)';
            },
            $path
        );

        return '#^' . $pattern . '$#';
    }

    /**
     * Busca y ejecuta la ruta correspondiente.
     */
    public function dispatch(
        string $method,
        string $uri,
        Container $container
    ): void {
        // Eliminar BASE_URL e index.php
        $uri = str_replace(
            [BASE_URL, '/index.php'],
            '',
            $uri
        );

        // Eliminar query string
        $uri = strtok($uri, '?');

        if ($uri === '' || $uri === false) {
            $uri = '/';
        }

        foreach ($this->routes as $route) {

            // El método HTTP tiene que coincidir
            if ($route['method'] !== $method) {
                continue;
            }

            // ¿Coincide la URL con el patrón?
            if (!preg_match($route['pattern'], $uri, $matches)) {
                continue;
            }

            // Extraer únicamente parámetros con nombre
            $params = [];

            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }

            $this->ejecutarRoute(
                $route,
                $container,
                $params
            );

            return;
        }

        // No existe ninguna ruta
        $this->mostrar404();
    }

    /**
     * Ejecuta middleware, resuelve controlador y ejecuta acción.
     */
    private function ejecutarRoute(
        array $route,
        Container $container,
        array $params
    ): void {
        // 1. Middlewares
        foreach ($route['middlewares'] as $middlewareClass) {
            $middleware = new $middlewareClass();
            $middleware->handle();
        }

        // 2. Resolver controlador mediante Container
        [$controllerClass, $metodo] = $route['action'];

        $controller = is_string($controllerClass)
            ? $container->get($controllerClass)
            : $controllerClass;

        // 3. Ejecutar acción pasando parámetros
        $controller->$metodo(...$params);
    }

    /**
     * Mostrar página 404.
     */
    private function mostrar404(): void
    {
        http_response_code(404);

        require __DIR__ . '/../views/public/not-found.php';
    }
}


