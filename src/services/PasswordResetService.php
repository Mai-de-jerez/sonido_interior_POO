<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\interfaces\UsuarioDAOInterface;
use SonidoInteriorPoo\interfaces\PasswordResetDAOInterface;
use SonidoInteriorPoo\interfaces\PasswordResetServiceInterface;
use SonidoInteriorPoo\interfaces\TransactionManagerInterface;
use SonidoInteriorPoo\utils\EmailHelper;
use SonidoInteriorPoo\exceptions\BusinessRuleException;

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

    private function actualizarPasswordConToken(string $email, string $nuevaPassword): void {
        $passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

        $this->transactionManager->transaction(function () use ($email, $passwordHash) {
            $this->usuarioDAO->actualizarPassword($email, $passwordHash);
            $this->passwordResetDAO->borrarTokenDe($email);
        });
    }

    public function actualizarPasswordPorToken(string $token, string $nuevaPassword): void {
        $email = $this->obtenerEmailPorToken($token);

        if (!$email) {
            throw new BusinessRuleException("El enlace ha caducado o es inválido. Solicita uno nuevo ya.");
        }

        $this->actualizarPasswordConToken($email, $nuevaPassword);
    }
}