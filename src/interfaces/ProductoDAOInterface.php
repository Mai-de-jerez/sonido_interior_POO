<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\Models\Producto;

interface ProductoDAOInterface {
    public function obtenerProductosAdmin(): array;
    public function obtenerUltimosProductosInicio(): array;
    public function obtenerProductosCatalogo(?int $idCategoria = null, string $orden = 'recientes', int $pagina = 1, int $porPagina = 12): array;
    public function contarProductosCatalogo(?int $idCategoria = null): int;
    public function obtenerPorId(int $idProducto): ?Producto;
    public function obtenerPorIdAdmin(int $idProducto): ?Producto;
    public function insertar(Producto $producto): bool;
    public function actualizar(Producto $producto): bool;
    public function eliminarLogico(int $idProducto): bool;
    public function reactivar(int $idProducto): bool;
    // Métodos para las estadísticas del dashboard
    public function contarTodosAdmin(): int;
    public function contarActivosAdmin(): int;
}