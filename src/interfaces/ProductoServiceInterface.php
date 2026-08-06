<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Producto;

interface ProductoServiceInterface {
    // ---------- VALIDACIÓN ----------
    public function validar(array $datos, bool $esEdicion): array;

    // ---------- OBTENER PRODUCTOS ----------
    public function obtenerPorIdAdmin(int $idProducto): ?Producto;
    public function obtenerPorId(int $idProducto): ?Producto;
    public function obtenerProductosAdmin(): array;
    public function obtenerUltimosProductosInicio(): array;
    public function obtenerProductosCatalogo(?int $idCategoria = null, string $orden = 'recientes', int $pagina = 1, int $porPagina = 12): array;
    public function contarProductosCatalogo(?int $idCategoria = null): int;

    // ---------- CREAR / ACTUALIZAR ----------
    public function crear(array $datos, array $ficheros): bool;
    public function actualizar(int $idProducto, array $datos, array $ficheros): bool;

    // ---------- ELIMINAR / REACTIVAR ----------
    public function eliminarLogico(int $idProducto): bool;
    public function reactivar(int $idProducto): bool;
}