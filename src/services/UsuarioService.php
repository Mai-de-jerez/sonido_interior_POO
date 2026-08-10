<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\models\Usuario;
use SonidoInteriorPoo\interfaces\UsuarioDAOInterface;
use SonidoInteriorPoo\interfaces\UsuarioServiceInterface;
use SonidoInteriorPoo\interfaces\CarritoServiceInterface;
use SonidoInteriorPoo\utils\EmailHelper;

class UsuarioService implements UsuarioServiceInterface {

    private UsuarioDAOInterface $usuarioDAO;
    private CarritoServiceInterface $carritoService;

    public function __construct(
        UsuarioDAOInterface $usuarioDAO,
        CarritoServiceInterface $carritoService
    ) {
        $this->usuarioDAO = $usuarioDAO;
        $this->carritoService = $carritoService;
    }

    // ============================================================
    // AUTENTICACIÓN Y REGISTRO
    // ============================================================
    public function login(string $usuario, string $password): ?array {
        $usuarioEncontrado = $this->usuarioDAO->obtenerPorUsername($usuario);
        
        if (!$usuarioEncontrado) {
            $usuarioEncontrado = $this->usuarioDAO->obtenerPorEmail($usuario);
        }

        if (!$usuarioEncontrado || !password_verify($password, $usuarioEncontrado->getPassword())) {
            return null;
        }

        $cantidadesCarrito = $this->carritoService->contarUnidades((int) $usuarioEncontrado->getIdUsuario());

        return [
            'id_usuario'         => $usuarioEncontrado->getIdUsuario(),
            'usuario'            => $usuarioEncontrado->getUsuario(),
            'rol'                => $usuarioEncontrado->getRol(),
            'cantidades_carrito' => $cantidadesCarrito
        ];
    }

    public function registrar(string $usuario, string $email, string $password): bool {
        if ($this->usuarioDAO->existeUsuario($usuario, $email)) {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        return $this->usuarioDAO->registrar($usuario, $email, $passwordHash);
    }

    // ============================================================
    // RECUPERACIÓN DE CONTRASEÑA
    // ============================================================
    public function solicitarRecuperacion(string $email): void {
        $usuario = $this->usuarioDAO->obtenerPorEmail($email);

        if (!$usuario) {
            return;
        }

        $token = bin2hex(random_bytes(32));

        if ($this->usuarioDAO->guardarTokenRecuperacion($email, $token)) {
            EmailHelper::enviarEnlaceRecuperacion($email, $usuario->getUsuario(), $token);
        }
    }

    public function obtenerEmailPorToken(string $token): ?string {
        return $this->usuarioDAO->obtenerEmailPorToken($token);
    }

    /**
     * Hashea la nueva contraseña y llama al DAO para actualizarla en la BD
     * y borrar el token de recuperación (un solo uso por seguridad).
     */
    private function actualizarPasswordConToken(string $email, string $nuevaPassword): bool {
        $passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        return $this->usuarioDAO->actualizarPasswordYBorrarToken($email, $passwordHash);
    }

    public function actualizarPasswordPorToken(string $token, string $nuevaPassword): bool {
        $email = $this->obtenerEmailPorToken($token);
        if (!$email) {
            return false;
        }
        return $this->actualizarPasswordConToken($email, $nuevaPassword);
    }

    // ============================================================
    // GESTIÓN DE PERFIL Y USUARIO
    // ============================================================
    public function obtenerPorId(int $idUsuario): ?Usuario {
        return $this->usuarioDAO->obtenerPorId($idUsuario);
    }

    public function cambiarPassword(int $idUsuario, string $passwordActual, string $nuevaPassword): bool {
        $usuario = $this->usuarioDAO->obtenerPorId($idUsuario);
        
        if (!$usuario || !password_verify($passwordActual, $usuario->getPassword())) {
            return false;
        }

        $passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        return $this->usuarioDAO->cambiarPassword($idUsuario, $passwordHash);
    }

    public function actualizarNombre(int $idUsuario, string $nombre): bool {
        return $this->usuarioDAO->actualizarNombre($idUsuario, $nombre);
    }
}