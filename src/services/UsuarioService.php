<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\models\Usuario;
use SonidoInteriorPoo\models\UsuarioDAO;
use SonidoInteriorPoo\interfaces\UsuarioServiceInterface;
use SonidoInteriorPoo\utils\EmailHelper;

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
    // RECUPERACIÓN DE CONTRASEÑA - SOLICITAR (genera token + envía email)
    // ============================================================
    public function solicitarRecuperacion(string $email): void {
        $usuario = $this->usuarioDAO->obtenerPorEmail($email);

        if (!$usuario) {
            // No revelamos si el email existe o no (mismo comportamiento que el código viejo)
            return;
        }

        $token = bin2hex(random_bytes(32));

        if ($this->usuarioDAO->guardarTokenRecuperacion($email, $token)) {
            EmailHelper::enviarEnlaceRecuperacion($email, $usuario->getUsuario(), $token);
        }
    }

    // ============================================================
    // VALIDAR NUEVA CONTRASEÑA (para el formulario de restablecer)
    // ============================================================
    public function validarNuevaPassword(array $datos): array {
        $errores = [];

        $password = $datos['password'] ?? '';
        $confirmPassword = $datos['confirm_password'] ?? '';

        if ($password === '' || $confirmPassword === '') {
            $errores['general'] = "Todos los campos son obligatorios.";
            return $errores;
        }

        if (strlen($password) < 6 || strlen($password) > 72) {
            $errores['password'] = "La contraseña debe tener entre 6 y 72 caracteres.";
        } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errores['password'] = "Debe incluir mayúscula, minúscula y número.";
        }

        if ($password !== $confirmPassword) {
            $errores['confirm_password'] = "Las contraseñas no coinciden.";
        }

        return $errores;
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