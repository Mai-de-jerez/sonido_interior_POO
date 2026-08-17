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
     * Punto de entrada real. Único sitio con try/catch: si algo revienta
     * en cualquier punto de la resolución de la ruta (middleware, controller,
     * servicio, lo que sea), se captura aquí y se delega al ExceptionMapper.
     */
    public function dispatch(
        string $method,
        string $uri,
        Container $container
    ): void {
        $request = Request::fromGlobals();

        try {
            $resultado = $this->resolverRuta($method, $uri, $container, $request);
        } catch (\Throwable $e) {
            $resultado = ExceptionMapper::map($e, $request);
        }

        $resultado->send();
    }

    /**
     * Busca la ruta correspondiente y la ejecuta. Puede lanzar libremente
     * — no necesita capturar nada, porque dispatch() ya se encarga.
     */
    private function resolverRuta(
        string $method,
        string $uri,
        Container $container
    ): Response {
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

            return $this->ejecutarRoute($route, $container, $params);
        }

        // No existe ninguna ruta
        return Response::notFound();
    }

    /**
     * Ejecuta middleware, resuelve controlador y ejecuta acción.
     * Siempre devuelve un Response — nunca envía nada por sí mismo.
     */
    private function ejecutarRoute(
        array $route,
        Container $container,
        array $params
    ): Response {
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

        // 3. Inspeccionar la firma del método para saber si espera Request
        $reflection = new \ReflectionMethod($controller, $metodo);
        $parametros = $reflection->getParameters();

        $esperaRequest = isset($parametros[0])
            && $parametros[0]->getType() instanceof \ReflectionNamedType
            && $parametros[0]->getType()->getName() === Request::class;

        // 4. Ejecutar acción, pasando Request solo si el método lo espera
        $resultado = $esperaRequest
            ? $controller->$metodo(Request::fromGlobals(), ...$params)
            : $controller->$metodo(...$params);

        // 5. Garantizamos que siempre se devuelve un Response.
        if (!($resultado instanceof Response)) {
            throw new \RuntimeException(
                "El método {$metodo} de " . get_class($controller) .
                " no devolvió un Response."
            );
        }

        return $resultado;
    }
}
