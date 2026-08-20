<?php
namespace SonidoInteriorPoo\interfaces;

use SonidoInteriorPoo\models\Categoria;

interface CategoriaDAOInterface {
    public function obtenerPorId(int $idCategoria): ?Categoria;
    public function obtenerActivas(): array;
    public function obtenerTodasAdmin(): array;
    public function crear(Categoria $categoria): void;
    public function actualizar(Categoria $categoria): void;
    public function eliminarLogica(int $idCategoria): void;
    public function reactivar(int $idCategoria): void;
}