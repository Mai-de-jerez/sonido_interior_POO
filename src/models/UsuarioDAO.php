<?php
namespace SonidoInteriorPoo\models;

use SonidoInteriorPoo\core\Conexion;
use SonidoInteriorPoo\interfaces\UsuarioDAOInterface;

class UsuarioDAO implements UsuarioDAOInterface{
    private Conexion $conexion;

    public function __construct(Conexion $conexion) {
        $this->conexion = $conexion;
    }

    // ============================================================
    // OBTENER USUARIO POR USERNAME
    // ============================================================
    public function obtenerPorUsername(string $usuario): ?Usuario {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT id_usuario, nombre, email, usuario, password, rol, fecha_registro FROM usuarios WHERE usuario = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario]);
        $fila = $stmt->fetch();

        return $fila ? Usuario::fromArray($fila) : null;
    }

    // ============================================================
    // OBTENER USUARIO POR EMAIL
    // ============================================================
    public function obtenerPorEmail(string $email): ?Usuario {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT id_usuario, usuario, email, password, rol, nombre, fecha_registro FROM usuarios WHERE email = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $fila = $stmt->fetch();

        return $fila ? Usuario::fromArray($fila) : null; 
    }

    // ============================================================
    // OBTENER USUARIO POR ID
    // ============================================================
    public function obtenerPorId(int $idUsuario): ?Usuario {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT id_usuario, nombre, email, usuario, password, rol, fecha_registro FROM usuarios WHERE id_usuario = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idUsuario]);
        $fila = $stmt->fetch();

        return $fila ? Usuario::fromArray($fila) : null;
    }
   
 
    // ============================================================
    // VERIFICAR SI USUARIO EXISTE (por username o email)
    // ============================================================
    public function existeUsuario(string $usuario): bool {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT COUNT(*) FROM usuarios WHERE usuario = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function existeEmail(string $email): bool {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        return (int) $stmt->fetchColumn() > 0;
    }

    // ============================================================
    // REGISTRAR NUEVO USUARIO
    // ============================================================
    public function registrar(string $usuario, string $email, string $passwordHash): bool {
        $pdo = $this->conexion->getPdo();
        $sql = "INSERT INTO usuarios (usuario, email, password, rol) VALUES (?, ?, ?, 'CLIENTE')";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$usuario, $email, $passwordHash]);
    }

    
    // ============================================================
    // ACTUALIZAR CONTRASEÑA
    // ============================================================
    public function actualizarPassword(string $email, string $nuevaPasswordHash): bool {
        $pdo = $this->conexion->getPdo();
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
        return $stmt->execute([$nuevaPasswordHash, $email]);
    }
}