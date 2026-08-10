<?php
namespace SonidoInteriorPoo\models;

use SonidoInteriorPoo\interfaces\MensajeDAOInterface;
use SonidoInteriorPoo\core\Conexion;

class MensajeDAO implements MensajeDAOInterface {
    private Conexion $conexion;

    public function __construct(Conexion $conexion) {
        $this->conexion = $conexion;
    }

    // Guardar un mensaje de contacto nuevo
    public function guardar(string $nombre, string $email, ?string $telefono, ?string $motivo, string $mensaje): bool {
        $pdo = $this->conexion->getPdo();
        $sql = "INSERT INTO mensajes (nombre, email, telefono, motivo, mensaje) VALUES (?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre, $email, $telefono, $motivo, $mensaje]);
    }

    // Obtener todos los mensajes para el admin (más recientes primero)
    public function obtenerTodosAdmin(): array {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT id_mensaje, nombre, email, telefono, motivo, mensaje, fecha_envio, leido
                FROM mensajes
                ORDER BY fecha_envio DESC";

        $filas = $pdo->query($sql)->fetchAll();
        return array_map(fn($fila) => Mensaje::fromArray($fila), $filas);
    }

    // Obtener un mensaje por ID
    public function obtenerPorId(int $idMensaje): ?Mensaje {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT id_mensaje, nombre, email, telefono, motivo, mensaje, fecha_envio, leido
                FROM mensajes
                WHERE id_mensaje = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idMensaje]);
        $fila = $stmt->fetch();

        return $fila ? Mensaje::fromArray($fila) : null;
    }

    // Marcar un mensaje como leído
    public function marcarComoLeido(int $idMensaje): bool {
        $pdo = $this->conexion->getPdo();
        $stmt = $pdo->prepare("UPDATE mensajes SET leido = 1 WHERE id_mensaje = ?");
        return $stmt->execute([$idMensaje]);
    }

    // Contar mensajes no leídos (útil para un badge en el admin)
    public function contarNoLeidos(): int {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT COUNT(*) FROM mensajes WHERE leido = 0";
        $stmt = $pdo->query($sql);
        return (int) $stmt->fetchColumn();
    }

    // Borrar un mensaje (borrado físico, no lógico — un mensaje de contacto no tiene sentido "reactivarlo")
    public function eliminar(int $idMensaje): bool {
        $pdo = $this->conexion->getPdo();
        $stmt = $pdo->prepare("DELETE FROM mensajes WHERE id_mensaje = ?");
        return $stmt->execute([$idMensaje]);
    }
}