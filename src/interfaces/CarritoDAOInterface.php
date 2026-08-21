<?php
namespace SonidoInteriorPoo\interfaces;
use SonidoInteriorPoo\dto\CarritoLineaDTO;

interface CarritoDAOInterface {
    public function obtenerOCrearCarrito(int $idUsuario): int;
    public function agregarProducto(int $idCarrito, int $idProducto, int $cantidad, float $precioUnitario): void;
    public function obtenerLineas(int $idCarrito): array;
    public function actualizarCantidad(int $idCarritoProducto, int $cantidad): void;
    public function eliminarLinea(int $idCarritoProducto): void;
    public function vaciarCarrito(int $idCarrito): void;
    public function obtenerLineaDeUsuario(int $idCarritoProducto, int $idUsuario): ?CarritoLineaDTO;
    public function contarUnidades(int $idUsuario): int;
}