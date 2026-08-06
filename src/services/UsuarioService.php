<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\models\Usuario;
use SonidoInteriorPoo\models\UsuarioDAO;
use SonidoInteriorPoo\interfaces\UsuarioServiceInterface;

class UsuarioService implements UsuarioServiceInterface {
    private UsuarioDAO $usuarioDAO;

    public function __construct(UsuarioDAO $usuarioDAO) {
        $this->usuarioDAO = $usuarioDAO;
    }

    // ============================================================
    // LOGIN
    // ============================================================
    public function login(string $usuario, string $password): ?array {
        // Buscar por username o email
        $usuarioEncontrado = $this->usuarioDAO->obtenerPorUsername($usuario);
        
        if (!$usuarioEncontrado) {
            $usuarioEncontrado = $this->usuarioDAO->obtenerPorEmail($usuario);
        }

        if (!$usuarioEncontrado) {
            return null;
        }

        // Verificar contraseña
        if (!password_verify($password, $usuarioEncontrado->getPassword())) {
            return null;
        }

        return [
            'id_usuario' => $usuarioEncontrado->getIdUsuario(),
            'usuario' => $usuarioEncontrado->getUsuario(),
            'rol' => $usuarioEncontrado->getRol()
        ];
    }

    // ============================================================
    // REGISTRO
    // ============================================================
    public function registrar(string $usuario, string $email, string $password): bool {
        // Verificar si el usuario ya existe
        if ($this->usuarioDAO->existeUsuario($usuario, $email)) {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        return $this->usuarioDAO->registrar($usuario, $email, $passwordHash);
    }

    // ============================================================
    // OBTENER USUARIO POR ID
    // ============================================================
    public function obtenerPorId(int $idUsuario): ?Usuario {
        return $this->usuarioDAO->obtenerPorId($idUsuario);
    }

    // ============================================================
    // RECUPERACIÓN DE CONTRASEÑA - GUARDAR TOKEN
    // ============================================================
    public function guardarTokenRecuperacion(string $email, string $token): bool {
        // Verificar que el email existe
        $usuario = $this->usuarioDAO->obtenerPorEmail($email);
        if (!$usuario) {
            return false;
        }

        return $this->usuarioDAO->guardarTokenRecuperacion($email, $token);
    }

    // ============================================================
    // RECUPERACIÓN DE CONTRASEÑA - VERIFICAR TOKEN
    // ============================================================
    public function obtenerEmailPorToken(string $token): ?string {
        return $this->usuarioDAO->obtenerEmailPorToken($token);
    }

    // ============================================================
    // RECUPERACIÓN DE CONTRASEÑA - ACTUALIZAR
    // ============================================================
    public function actualizarPasswordConToken(string $email, string $nuevaPassword): bool {
        $passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        return $this->usuarioDAO->actualizarPasswordYBorrarToken($email, $passwordHash);
    }

    // ============================================================
    // CAMBIAR CONTRASEÑA (usuario logueado)
    // ============================================================
    public function cambiarPassword(int $idUsuario, string $passwordActual, string $nuevaPassword): bool {
        $usuario = $this->usuarioDAO->obtenerPorId($idUsuario);
        
        if (!$usuario) {
            return false;
        }

        // Verificar la contraseña actual
        if (!password_verify($passwordActual, $usuario->getPassword())) {
            return false;
        }

        $passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        return $this->usuarioDAO->cambiarPassword($idUsuario, $passwordHash);
    }

    // ============================================================
    // ACTUALIZAR NOMBRE
    // ============================================================
    public function actualizarNombre(int $idUsuario, string $nombre): bool {
        return $this->usuarioDAO->actualizarNombre($idUsuario, $nombre);
    }
}