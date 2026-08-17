<?php
namespace SonidoInteriorPoo\core;

class Request {
    private array $query;
    private array $post;
    private array $files;
    private array $server;

    public function __construct(array $query, array $post, array $files, array $server) {
        $this->query = $query;
        $this->post = $post;
        $this->files = $files;
        $this->server = $server;
    }

    public static function fromGlobals(): self {
        return new self($_GET, $_POST, $_FILES, $_SERVER);
    }

    public function get(string $key, mixed $default = null): mixed {
        return $this->query[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed {
        return $this->post[$key] ?? $default;
    }

    public function file(string $key): ?array {
        return $this->files[$key] ?? null;
    }

    public function allPost(): array {
        return $this->post;
    }

    public function allFiles(): array {
        return $this->files;
    }

    public function referer(?string $default = null): ?string {
        return $this->server['HTTP_REFERER'] ?? $default;
    }

    public function host(): ?string {
        return $this->server['HTTP_HOST'] ?? null;
    }

    public function method(): string {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function uri(): string {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    public function all(): array {
        return array_merge($this->query, $this->post);
    }
}