<?php
namespace SonidoInteriorPoo\models;

use PDO;
use PDOException;

class UsuarioDAO {
    private Conexion $conexion;

    public function __construct(Conexion $conexion) {
        $this->conexion = $conexion;
    }

    // ============================================================
    // OBTENER USUARIO POR USERNAME
    // ============================================================
    public function obtenerPorUsername(string $usuario): ?Usuario {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT id_usuario, usuario, password, rol FROM usuarios WHERE usuario = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario]);
        $fila = $stmt->fetch();

        if (!$fila) {
            return null;
        }

        return new Usuario(
            (int) $fila['id_usuario'],
            null, // nombre
            '',   // email (no lo traemos en esta consulta)
            $fila['usuario'],
            $fila['password'],
            $fila['rol'],
            ''    // fecha_registro (no lo traemos)
        );
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

        if (!$fila) {
            return null;
        }

        return new Usuario(
            (int) $fila['id_usuario'],
            $fila['nombre'],
            $fila['email'],
            $fila['usuario'],
            $fila['password'],
            $fila['rol'],
            $fila['fecha_registro']
        );
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

        if (!$fila) {
            return null;
        }

        return new Usuario(
            (int) $fila['id_usuario'],
            $fila['nombre'],
            $fila['email'],
            $fila['usuario'],
            $fila['password'],
            $fila['rol'],
            $fila['fecha_registro']
        );
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
    // GUARDAR TOKEN DE RECUPERACIÓN
    // ============================================================
    public function guardarTokenRecuperacion(string $email, string $token): bool {
        $pdo = $this->conexion->getPdo();
        
        // Iniciar transacción
        $pdo->beginTransaction();
        
        try {
            // Borrar tokens anteriores del mismo email
            $sqlDelete = "DELETE FROM password_resets WHERE email = ?";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->execute([$email]);
            
            // Insertar nuevo token con expiración de 30 minutos
            $sqlInsert = "INSERT INTO password_resets (email, token, expira) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute([$email, $token]);
            
            $pdo->commit();
            return true;
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            return false;
        }
    }

    // ============================================================
    // OBTENER EMAIL POR TOKEN (VÁLIDO)
    // ============================================================
    public function obtenerEmailPorToken(string $token): ?string {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT email FROM password_resets WHERE token = ? AND expira > NOW() LIMIT 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$token]);
        $fila = $stmt->fetch();
        
        return $fila ? $fila['email'] : null;
    }

    // ============================================================
    // ACTUALIZAR CONTRASEÑA Y BORRAR TOKEN
    // ============================================================
    public function actualizarPasswordYBorrarToken(string $email, string $nuevaPasswordHash): bool {
        $pdo = $this->conexion->getPdo();
        
        // Iniciar transacción
        $pdo->beginTransaction();
        
        try {
            // Actualizar contraseña en usuarios
            $sqlUser = "UPDATE usuarios SET password = ? WHERE email = ?";
            $stmtUser = $pdo->prepare($sqlUser);
            $stmtUser->execute([$nuevaPasswordHash, $email]);
            
            // Borrar token usado
            $sqlToken = "DELETE FROM password_resets WHERE email = ?";
            $stmtToken = $pdo->prepare($sqlToken);
            $stmtToken->execute([$email]);
            
            $pdo->commit();
            return true;
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            return false;
        }
    }

    // ============================================================
    // VERIFICAR SI USUARIO EXISTE (por username o email)
    // ============================================================
    public function existeUsuario(string $usuario, string $email): bool {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT COUNT(*) FROM usuarios WHERE usuario = ? OR email = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario, $email]);
        $count = (int) $stmt->fetchColumn();
        
        return $count > 0;
    }

    // ============================================================
    // ACTUALIZAR NOMBRE DE USUARIO
    // ============================================================
    public function actualizarNombre(int $idUsuario, string $nombre): bool {
        $pdo = $this->conexion->getPdo();
        $sql = "UPDATE usuarios SET nombre = ? WHERE id_usuario = ?";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre, $idUsuario]);
    }

    // ============================================================
    // CAMBIAR CONTRASEÑA (sin token)
    // ============================================================
    public function cambiarPassword(int $idUsuario, string $nuevaPasswordHash): bool {
        $pdo = $this->conexion->getPdo();
        $sql = "UPDATE usuarios SET password = ? WHERE id_usuario = ?";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nuevaPasswordHash, $idUsuario]);
    }
}