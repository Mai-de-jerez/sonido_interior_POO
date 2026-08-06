<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\models\Categoria;
use SonidoInteriorPoo\models\CategoriaDAO;
use SonidoInteriorPoo\interfaces\CategoriaServiceInterface;

class CategoriaService implements CategoriaServiceInterface {
    private CategoriaDAO $categoriaDAO;

    public function __construct(CategoriaDAO $categoriaDAO) {
        $this->categoriaDAO = $categoriaDAO;
    }

    // obtener categorias para el administrador
    public function obtenerTodasAdmin(): array {
        return $this->categoriaDAO->obtenerTodasAdmin();
    }

    // obtener categorias activas
    public function obtenerActivas(): array {
        return $this->categoriaDAO->obtenerActivas();
    }

    // validar categoria
    public function validar(array $datos): array {
        $errores = [];

        $nombre = trim($datos['nombre'] ?? '');
        $descripcion = trim($datos['descripcion'] ?? '');

        if ($nombre === '') {
            $errores['nombre'] = "El nombre es obligatorio.";
        } elseif (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 50) {
            $errores['nombre'] = "El nombre debe tener entre 3 y 50 caracteres.";
        }

        if ($descripcion === '') {
            $errores['descripcion'] = "La descripción es obligatoria.";
        } elseif (mb_strlen($descripcion) < 15 || mb_strlen($descripcion) > 300) {
            $errores['descripcion'] = "La descripción debe tener entre 15 y 300 caracteres.";
        }

        return $errores;
    }

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