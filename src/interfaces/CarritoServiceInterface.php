<?php
namespace SonidoInteriorPoo\interfaces;

interface CarritoServiceInterface {
    public function obtenerLineas(int $idUsuario): array;
    public function contarUnidades(int $idUsuario): int;
    public function agregarProducto(int $idUsuario, int $idProducto, int $cantidad): array;
    public function actualizarCantidad(int $idUsuario, int $idCarritoProducto, string $accion): array;
    public function eliminarLinea(int $idUsuario, int $idCarritoProducto): bool;
    public function obtenerCantidadLinea(int $idUsuario, int $idCarritoProducto): ?int;
    public function procesarCheckout(int $idUsuario, string $direccionEnvio): array;
}