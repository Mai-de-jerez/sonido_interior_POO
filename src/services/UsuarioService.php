<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\models\Usuario;
use SonidoInteriorPoo\interfaces\UsuarioDAOInterface;
use SonidoInteriorPoo\interfaces\UsuarioServiceInterface;
use SonidoInteriorPoo\interfaces\CarritoServiceInterface;
use SonidoInteriorPoo\exceptions\BusinessRuleException;


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

    public function registrar(string $usuario, string $email, string $password): void {
        if ($this->usuarioDAO->existeUsuario($usuario)) {
            throw new BusinessRuleException("Ese nombre de usuario ya está en uso.");
        }

        if ($this->usuarioDAO->existeEmail($email)) {
            throw new BusinessRuleException("Ese email ya está registrado.");
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if (!$this->usuarioDAO->registrar($usuario, $email, $passwordHash)) {
            throw new BusinessRuleException("No se pudo completar el registro.");
        }
    }
}

