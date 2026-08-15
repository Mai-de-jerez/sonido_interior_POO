<?php
namespace SonidoInteriorPoo\core;

class Response {
    private const TYPE_REDIRECT = 'redirect';
    private const TYPE_VIEW = 'view';

    private string $type;
    private string $target;
    private array $data;
    private int $statusCode;

    private function __construct(string $type, string $target, array $data = [], int $statusCode = 200) {
        $this->type = $type;
        $this->target = $target;
        $this->data = $data;
        $this->statusCode = $statusCode;
    }

    public static function redirect(string $ruta, int $statusCode = 302): self {
        return new self(self::TYPE_REDIRECT, $ruta, [], $statusCode);
    }

    public static function view(string $vista, array $data = [], int $statusCode = 200): self {
        return new self(self::TYPE_VIEW, $vista, $data, $statusCode);
    }

    public static function notFound(string $vista = 'public/not-found'): self {
        return new self(self::TYPE_VIEW, $vista, [], 404);
    }

    public function withStatus(int $statusCode): self {
        $clon = clone $this;
        $clon->statusCode = $statusCode;
        return $clon;
    }

    public function getStatusCode(): int {
        return $this->statusCode;
    }

    // Ejecuta la respuesta: envía headers/HTML y termina el script
    public function send(): never {
        http_response_code($this->statusCode);

        if ($this->type === self::TYPE_REDIRECT) {
            $this->enviarRedirect();
        } else {
            $this->enviarVista();
        }
        exit();
    }

    private function enviarRedirect(): void {
        if (filter_var($this->target, FILTER_VALIDATE_URL)) {
            header("Location: " . $this->target);
            return;
        }
        header("Location: " . BASE_URL . "/" . ltrim($this->target, '/'));
    }

    private function enviarVista(): void {
        extract($this->data);
        require __DIR__ . '/../views/' . $this->target . '.php';
    }
}