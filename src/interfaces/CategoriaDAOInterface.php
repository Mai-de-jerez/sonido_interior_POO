<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Categoria;

interface CategoriaDAOInterface {
    public function obtenerPorId(int $idCategoria): ?Categoria;
    public function obtenerActivas(): array;
    public function obtenerTodasAdmin(): array;
    public function crear(Categoria $categoria): bool;
    public function actualizar(Categoria $categoria): bool;
    public function eliminarLogica(int $idCategoria): bool;
    public function reactivar(int $idCategoria): bool;
}