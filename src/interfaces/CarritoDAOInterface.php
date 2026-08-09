<?php
namespace SonidoInteriorPoo\interfaces;

interface CarritoDAOInterface {
    public function obtenerOCrearCarrito(int $idUsuario): int;
    public function agregarProducto(int $idCarrito, int $idProducto, int $cantidad, float $precioUnitario): bool;
    public function obtenerLineas(int $idCarrito): array;
    public function actualizarCantidad(int $idCarritoProducto, int $cantidad): bool;
    public function eliminarLinea(int $idCarritoProducto): bool;
    public function vaciarCarrito(int $idCarrito): bool;
    public function lineaPerteneceAUsuario(int $idCarritoProducto, int $idUsuario): bool;
    public function contarUnidades(int $idUsuario): int;
}