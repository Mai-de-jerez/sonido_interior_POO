<?php
namespace SonidoInteriorPoo\models;

use SonidoInteriorPoo\core\Conexion;
use SonidoInteriorPoo\interfaces\PasswordResetDAOInterface;

class PasswordResetDAO implements PasswordResetDAOInterface {
    private Conexion $conexion;

    public function __construct(Conexion $conexion) {
        $this->conexion = $conexion;
    }

    public function borrarTokensDe(string $email): bool {
        $pdo = $this->conexion->getPdo();
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
        return $stmt->execute([$email]);
    }

    public function insertarToken(string $email, string $token): bool {
        $pdo = $this->conexion->getPdo();
        $sql = "INSERT INTO password_resets (email, token, expira) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$email, $token]);
    }

    public function borrarTokenDe(string $email): bool {
        $pdo = $this->conexion->getPdo();
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
        return $stmt->execute([$email]);
    }

    public function obtenerEmailPorToken(string $token): ?string {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT email FROM password_resets WHERE token = ? AND expira > NOW() LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$token]);
        $fila = $stmt->fetch();
        return $fila ? $fila['email'] : null;
    }
}