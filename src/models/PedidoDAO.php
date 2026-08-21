<?php
namespace SonidoInteriorPoo\models;

use SonidoInteriorPoo\interfaces\PedidoDAOInterface;
use SonidoInteriorPoo\core\Conexion;

class PedidoDAO implements PedidoDAOInterface {
    private Conexion $conexion;

    public function __construct(Conexion $conexion) {
        $this->conexion = $conexion;
    }

    public function crear(int $idUsuario, float $total, string $direccionEnvio): int {
        $pdo = $this->conexion->getPdo();
        $sql = "INSERT INTO pedidos (id_usuario, total, direccion_envio, estado) VALUES (?, ?, ?, 'PAGADO')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idUsuario, $total, $direccionEnvio]);

        return (int) $pdo->lastInsertId();
    }

    public function crearDetalle(int $idPedido, int $idProducto, int $cantidad, float $precioUnitario): void {
        $pdo = $this->conexion->getPdo();
        $subtotal = $cantidad * $precioUnitario;
        $sql = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario, subtotal)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idPedido, $idProducto, $cantidad, $precioUnitario, $subtotal]);
    }
}