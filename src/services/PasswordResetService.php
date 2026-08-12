<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\interfaces\UsuarioDAOInterface;
use SonidoInteriorPoo\interfaces\PasswordResetDAOInterface;
use SonidoInteriorPoo\interfaces\PasswordResetServiceInterface;
use SonidoInteriorPoo\interfaces\TransactionManagerInterface;
use SonidoInteriorPoo\utils\EmailHelper;

class PasswordResetService implements PasswordResetServiceInterface {
 
    private UsuarioDAOInterface $usuarioDAO;
    private PasswordResetDAOInterface $passwordResetDAO;
    private TransactionManagerInterface $transactionManager;

    public function __construct(
        UsuarioDAOInterface $usuarioDAO,
        PasswordResetDAOInterface $passwordResetDAO,
        TransactionManagerInterface $transactionManager
    ) {
        $this->usuarioDAO = $usuarioDAO;
        $this->passwordResetDAO = $passwordResetDAO;
        $this->transactionManager = $transactionManager;
    }

    public function solicitarRecuperacion(string $email): void {
        $usuario = $this->usuarioDAO->obtenerPorEmail($email);

        if (!$usuario) {
            return;
        }

        $token = bin2hex(random_bytes(32));

        $this->transactionManager->transaction(function () use ($email, $token) {
            $this->passwordResetDAO->borrarTokensDe($email);
            $this->passwordResetDAO->insertarToken($email, $token);
        });

        EmailHelper::enviarEnlaceRecuperacion($email, $usuario->getUsuario(), $token);
    }

    private function obtenerEmailPorToken(string $token): ?string {
        return $this->passwordResetDAO->obtenerEmailPorToken($token);
    }

    private function actualizarPasswordConToken(string $email, string $nuevaPassword): bool {
        $passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

        $this->transactionManager->transaction(function () use ($email, $passwordHash) {
            $this->usuarioDAO->actualizarPassword($email, $passwordHash);
            $this->passwordResetDAO->borrarTokenDe($email);
        });

        return true;
    }

    public function actualizarPasswordPorToken(string $token, string $nuevaPassword): bool {
        $email = $this->obtenerEmailPorToken($token);
        if (!$email) {
            return false;
        }
        return $this->actualizarPasswordConToken($email, $nuevaPassword);
    }
}