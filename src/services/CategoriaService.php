<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\interfaces\CategoriaDAOInterface;
use SonidoInteriorPoo\models\Categoria;
use SonidoInteriorPoo\interfaces\CategoriaServiceInterface;
use SonidoInteriorPoo\exceptions\NotFoundException;
use SonidoInteriorPoo\exceptions\BusinessRuleException;

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

    public function crear(array $datos): void {
        $categoria = new Categoria(
            0,
            trim($datos['nombre']),
            trim($datos['descripcion'])
        );

        if (!$this->categoriaDAO->crear($categoria)) {
            throw new BusinessRuleException("Error al guardar la categoría en la base de datos.");
        }
    }


    public function actualizar(int $idCategoria, array $datos): void {
        $categoriaActual = $this->categoriaDAO->obtenerPorId($idCategoria);

        if ($categoriaActual === null) {
            throw new NotFoundException("Categoría no encontrada.");
        }

        $categoria = new Categoria(
            $idCategoria,
            trim($datos['nombre']),
            trim($datos['descripcion'])
        );

        if (!$this->categoriaDAO->actualizar($categoria)) {
            throw new BusinessRuleException("Error al actualizar la categoría en la base de datos.");
        }
    }

    public function eliminarLogica(int $idCategoria): void {
        $categoria = $this->categoriaDAO->obtenerPorId($idCategoria);

        if ($categoria === null) {
            throw new NotFoundException("La categoría no existe.");
        }

        if (!$this->categoriaDAO->eliminarLogica($idCategoria)) {
            throw new BusinessRuleException("No se pudo eliminar la categoría.");
        }
    }

    public function reactivar(int $idCategoria): void {
        $categoria = $this->categoriaDAO->obtenerPorId($idCategoria);

        if ($categoria === null) {
            throw new NotFoundException("La categoría no existe.");
        }

        if (!$this->categoriaDAO->reactivar($idCategoria)) {
            throw new BusinessRuleException("No se pudo reactivar la categoría.");
        }
    }
}