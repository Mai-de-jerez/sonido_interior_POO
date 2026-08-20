<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Producto;
use SonidoInteriorPoo\dto\ProductoStockDTO;

interface ProductoDAOInterface {
    public function obtenerProductosAdmin(): array;
    public function obtenerUltimosProductosInicio(): array;
    public function obtenerProductosCatalogo(?int $idCategoria = null, string $orden = 'recientes', int $pagina = 1, int $porPagina = 12): array;
    public function obtenerPorId(int $idProducto): ?Producto;
    public function obtenerPorIdAdmin(int $idProducto): ?Producto;
    public function obtenerStockParaUpdate(int $idProducto): ?ProductoStockDTO;
    // conteo de productos para el catálogo público y dashboard
    public function contarProductosCatalogo(?int $idCategoria = null): int;
    public function contarTodosAdmin(): int;
    public function contarActivosAdmin(): int;
    // Métodos para el carrito y stock   
    public function descontarStock(int $idProducto, int $cantidad): bool;
    // Escrritura de datos
    public function insertar(Producto $producto):void;
    public function actualizar(Producto $producto): void;
    public function eliminarLogico(int $idProducto): void;
    public function reactivar(int $idProducto): void;
    
}