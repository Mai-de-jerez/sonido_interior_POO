<?php
namespace SonidoInteriorPoo\models;

use SonidoInteriorPoo\interfaces\CategoriaDAOInterface;
use SonidoInteriorPoo\core\Conexion;

class CategoriaDAO implements CategoriaDAOInterface {
    private Conexion $conexion;

    public function __construct(Conexion $conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerPorId(int $idCategoria): ?Categoria {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT id_categoria, nombre, descripcion, activo FROM categorias WHERE id_categoria = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idCategoria]);
        $fila = $stmt->fetch();

        return $fila ? Categoria::fromArray($fila) : null;
    }

    public function obtenerActivas(): array {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT id_categoria, nombre, descripcion, activo FROM categorias WHERE activo = 1";

        $filas = $pdo->query($sql)->fetchAll();
        return array_map(fn($fila) => Categoria::fromArray($fila), $filas);
    }

    public function obtenerTodasAdmin(): array {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT id_categoria, nombre, descripcion, activo FROM categorias ORDER BY id_categoria DESC";

        $filas = $pdo->query($sql)->fetchAll();
        return array_map(fn($fila) => Categoria::fromArray($fila), $filas);
    }

    public function crear(Categoria $categoria): void {
        $pdo = $this->conexion->getPdo();
        $sql = "INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $categoria->getNombre(),
            $categoria->getDescripcion(),
        ]);
    }

    public function actualizar(Categoria $categoria): void {
        $pdo = $this->conexion->getPdo();
        $sql = "UPDATE categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $categoria->getNombre(),
            $categoria->getDescripcion(),
            $categoria->getIdCategoria(),
        ]);
    }

    public function eliminarLogica(int $idCategoria): void {
        $pdo = $this->conexion->getPdo();
        $stmt = $pdo->prepare("UPDATE categorias SET activo = 0 WHERE id_categoria = ?");
        $stmt->execute([$idCategoria]);
    }

    public function reactivar(int $idCategoria): void {
        $pdo = $this->conexion->getPdo();
        $stmt = $pdo->prepare("UPDATE categorias SET activo = 1 WHERE id_categoria = ?");
        $stmt->execute([$idCategoria]);
    }
}