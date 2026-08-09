<?php
namespace SonidoInteriorPoo\models;

use SonidoInteriorPoo\interfaces\CarritoDAOInterface;
use SonidoInteriorPoo\dto\CarritoLineaDTO;

class CarritoDAO implements CarritoDAOInterface { 
    private Conexion $conexion;

    public function __construct(Conexion $conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerOCrearCarrito(int $idUsuario): int {
        $pdo = $this->conexion->getPdo();

        $stmt = $pdo->prepare("SELECT id_carrito FROM carrito WHERE id_usuario = ?");
        $stmt->execute([$idUsuario]);
        $fila = $stmt->fetch();

        if ($fila) {
            return (int) $fila['id_carrito'];
        }

        $stmtInsert = $pdo->prepare("INSERT INTO carrito (id_usuario) VALUES (?)");
        $stmtInsert->execute([$idUsuario]);

        return (int) $pdo->lastInsertId();
    }

    public function agregarProducto(int $idCarrito, int $idProducto, int $cantidad, float $precioUnitario): bool {
        $pdo = $this->conexion->getPdo();
        $sql = "INSERT INTO carrito_producto (id_carrito, id_producto, cantidad, precio_unitario)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE cantidad = cantidad + VALUES(cantidad)";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$idCarrito, $idProducto, $cantidad, $precioUnitario]);
    }

    // Devuelve CarritoLineaDTO[] con el Producto completo ya unido
    public function obtenerLineas(int $idCarrito): array {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT cp.id_carrito_producto, cp.cantidad, cp.precio_unitario,
                       p.id_producto, p.id_categoria, p.nombre, p.descripcion, p.precio, p.stock,
                       p.imagen, p.diametro, p.peso, p.material, p.nota_musical, p.procedencia,
                       p.activo, p.fecha_alta
                FROM carrito_producto cp
                INNER JOIN productos p ON cp.id_producto = p.id_producto
                WHERE cp.id_carrito = ?
                ORDER BY cp.id_carrito_producto DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idCarrito]);
        $filas = $stmt->fetchAll();

        return array_map(
            fn($fila) => new CarritoLineaDTO(
                (int) $fila['id_carrito_producto'],
                (int) $fila['cantidad'],
                (float) $fila['precio_unitario'],
                Producto::fromArray($fila)
            ),
            $filas
        );
    }

    public function actualizarCantidad(int $idCarritoProducto, int $cantidad): bool {
        $pdo = $this->conexion->getPdo();
        $stmt = $pdo->prepare("UPDATE carrito_producto SET cantidad = ? WHERE id_carrito_producto = ?");
        return $stmt->execute([$cantidad, $idCarritoProducto]);
    }

    public function eliminarLinea(int $idCarritoProducto): bool {
        $pdo = $this->conexion->getPdo();
        $stmt = $pdo->prepare("DELETE FROM carrito_producto WHERE id_carrito_producto = ?");
        return $stmt->execute([$idCarritoProducto]);
    }

    public function vaciarCarrito(int $idCarrito): bool {
        $pdo = $this->conexion->getPdo();
        $stmt = $pdo->prepare("DELETE FROM carrito_producto WHERE id_carrito = ?");
        return $stmt->execute([$idCarrito]);
    }

    public function lineaPerteneceAUsuario(int $idCarritoProducto, int $idUsuario): bool {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT cp.id_carrito_producto
                FROM carrito_producto cp
                INNER JOIN carrito c ON cp.id_carrito = c.id_carrito
                WHERE cp.id_carrito_producto = ? AND c.id_usuario = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idCarritoProducto, $idUsuario]);
        return $stmt->fetch() !== false;
    }

    public function contarUnidades(int $idUsuario): int {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT COALESCE(SUM(cp.cantidad), 0) AS total 
                FROM carrito_producto cp
                INNER JOIN carrito c ON cp.id_carrito = c.id_carrito
                WHERE c.id_usuario = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idUsuario]);
        return (int) $stmt->fetchColumn();
    }
}