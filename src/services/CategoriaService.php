<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\interfaces\CategoriaDAOInterface;
use SonidoInteriorPoo\models\Categoria;
use SonidoInteriorPoo\interfaces\CategoriaServiceInterface;
use SonidoInteriorPoo\exceptions\NotFoundException;

class CategoriaService implements CategoriaServiceInterface {
    private CategoriaDAOInterface $categoriaDAO;

    public function __construct(CategoriaDAOInterface $categoriaDAO) {
        $this->categoriaDAO = $categoriaDAO;
    }

    public function obtenerPorId(int $idCategoria): ?Categoria {
        return $this->categoriaDAO->obtenerPorId($idCategoria);
    }

    public function obtenerTodasAdmin(): array {
        return $this->categoriaDAO->obtenerTodasAdmin();
    }

    public function obtenerActivas(): array {
        return $this->categoriaDAO->obtenerActivas();
    }

    public function crear(array $datos): bool {
        $categoria = new Categoria(
            0,
            trim($datos['nombre']),
            trim($datos['descripcion'])
        );

        return $this->categoriaDAO->crear($categoria);
    }

    public function actualizar(int $idCategoria, array $datos): bool {
        $categoriaActual = $this->categoriaDAO->obtenerPorId($idCategoria);

        if ($categoriaActual === null) {
            throw new NotFoundException("Categoría no encontrada.");
        }

        $categoria = new Categoria(
            $idCategoria,
            trim($datos['nombre']),
            trim($datos['descripcion'])
        );

        return $this->categoriaDAO->actualizar($categoria);
    }

    public function eliminarLogica(int $idCategoria): bool {
        $categoria = $this->categoriaDAO->obtenerPorId($idCategoria);

        if ($categoria === null) {
            throw new NotFoundException("La categoría no existe.");
        }

        return $this->categoriaDAO->eliminarLogica($idCategoria);
    }

    public function reactivar(int $idCategoria): bool {
        $categoria = $this->categoriaDAO->obtenerPorId($idCategoria);

        if ($categoria === null) {
            throw new NotFoundException("La categoría no existe.");
        }

        return $this->categoriaDAO->reactivar($idCategoria);
    }
}