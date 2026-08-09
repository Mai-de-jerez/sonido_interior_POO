<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Categoria;

interface CategoriaServiceInterface {
    
    public function obtenerPorId(int $idCategoria): ?Categoria;
    public function obtenerTodasAdmin(): array;
    public function obtenerActivas(): array;
    public function crear(array $datos): bool;
    public function actualizar(int $idCategoria, array $datos): bool;
    public function eliminarLogica(int $idCategoria): bool;
    public function reactivar(int $idCategoria): bool;
}