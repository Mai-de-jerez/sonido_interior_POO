<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\interfaces\CategoriaDAOInterface;
use SonidoInteriorPoo\models\Categoria;
use SonidoInteriorPoo\interfaces\CategoriaServiceInterface;

class CategoriaService implements CategoriaServiceInterface {
    private CategoriaDAOInterface $categoriaDAO;

    public function __construct(CategoriaDAOInterface $categoriaDAO) {
        $this->categoriaDAO = $categoriaDAO;
    }

    // LECTURA
    // obtener una categoría por su ID
    public function obtenerPorId(int $idCategoria): ?Categoria {
        return $this->categoriaDAO->obtenerPorId($idCategoria);
    }

    // obtener categorias para el administrador
    public function obtenerTodasAdmin(): array {
        return $this->categoriaDAO->obtenerTodasAdmin();
    }

    // obtener categorias activas
    public function obtenerActivas(): array {
        return $this->categoriaDAO->obtenerActivas();
    }

    // ESCRITURA
    // crear categoria
    public function crear(array $datos): bool {
        $categoria = new Categoria(
            0,
            trim($datos['nombre']),
            trim($datos['descripcion'])
        );

        return $this->categoriaDAO->crear($categoria);
    }

    // actualizar categoria
    public function actualizar(int $idCategoria, array $datos): bool {
        $categoriaActual = $this->categoriaDAO->obtenerPorId($idCategoria);

        if ($categoriaActual === null) {
            throw new \RuntimeException("Categoría no encontrada.");
        }

        $categoria = new Categoria(
            $idCategoria,
            trim($datos['nombre']),
            trim($datos['descripcion'])
        );

        return $this->categoriaDAO->actualizar($categoria);
    }

    // borrado lógico
    public function eliminarLogica(int $idCategoria): bool {
        $categoria = $this->categoriaDAO->obtenerPorId($idCategoria);

        if ($categoria === null) {
            throw new \RuntimeException("La categoría no existe.");
        }

        return $this->categoriaDAO->eliminarLogica($idCategoria);
    }

    // reactivar una categoria
    public function reactivar(int $idCategoria): bool {
        $categoria = $this->categoriaDAO->obtenerPorId($idCategoria);

        if ($categoria === null) {
            throw new \RuntimeException("La categoría no existe.");
        }

        return $this->categoriaDAO->reactivar($idCategoria);
    }
}