<?php
namespace SonidoInteriorPoo\interfaces;
use SonidoInteriorPoo\dto\ResumenCheckoutDTO;

interface CarritoServiceInterface {
    public function obtenerLineas(int $idUsuario): array;
    public function contarUnidades(int $idUsuario): int;
    public function validarYCalcularTotal(int $idUsuario): ResumenCheckoutDTO;
    public function obtenerCantidadLinea(int $idUsuario, int $idCarritoProducto): ?int;

    public function agregarProducto(int $idUsuario, int $idProducto, int $cantidad): void;
    public function actualizarCantidad(int $idUsuario, int $idCarritoProducto, string $accion): int;
    public function eliminarLinea(int $idUsuario, int $idCarritoProducto): int;
    
}