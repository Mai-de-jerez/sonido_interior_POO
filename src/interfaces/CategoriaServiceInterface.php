<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Categoria;

interface CategoriaServiceInterface {
    
    public function obtenerPorId(int $idCategoria): ?Categoria;
    public function obtenerTodasAdmin(): array;
    public function obtenerActivas(): array;
    public function crear(array $datos): void;
    public function actualizar(int $idCategoria, array $datos): void;
    public function eliminarLogica(int $idCategoria): void;
    public function reactivar(int $idCategoria): void;
}