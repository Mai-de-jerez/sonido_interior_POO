<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\models\Mensaje;
use SonidoInteriorPoo\interfaces\MensajeDAOInterface;
use SonidoInteriorPoo\interfaces\MensajeServiceInterface;
use SonidoInteriorPoo\utils\EmailHelper;

class MensajeService implements MensajeServiceInterface {
    private MensajeDAOInterface $mensajeDAO;

    public function __construct(MensajeDAOInterface $mensajeDAO) {
        $this->mensajeDAO = $mensajeDAO;
    }

    // ---------- CREAR ----------
    public function crear(array $datos): bool {
        $nombre = trim($datos['nombre']);
        $email = trim($datos['email']);
        $telefono = trim($datos['telefono'] ?? '');
        $motivo = trim($datos['motivo'] ?? '');
        $mensaje = trim($datos['mensaje']);

        $guardado = $this->mensajeDAO->guardar(
            $nombre,
            $email,
            $telefono !== '' ? $telefono : null,
            $motivo !== '' ? $motivo : null,
            $mensaje
        );

        if ($guardado) {
            EmailHelper::enviarAvisoContacto($nombre, $email, $telefono ?: null, $motivo ?: null, $mensaje);
        }

        return $guardado;
    }

    // ---------- OBTENER ----------
    public function obtenerTodosAdmin(): array {
        return $this->mensajeDAO->obtenerTodosAdmin();
    }

    public function obtenerPorId(int $idMensaje): ?Mensaje {
        return $this->mensajeDAO->obtenerPorId($idMensaje);
    }

    public function contarNoLeidos(): int {
        return $this->mensajeDAO->contarNoLeidos();
    }

    // ---------- MARCAR LEÍDO ----------
    public function marcarComoLeido(int $idMensaje): bool {
        $mensaje = $this->mensajeDAO->obtenerPorId($idMensaje);

        if ($mensaje === null) {
            throw new \RuntimeException("El mensaje no existe.");
        }

        return $this->mensajeDAO->marcarComoLeido($idMensaje);
    }

    // ---------- ELIMINAR ----------
    public function eliminar(int $idMensaje): bool {
        $mensaje = $this->mensajeDAO->obtenerPorId($idMensaje);

        if ($mensaje === null) {
            throw new \RuntimeException("El mensaje no existe.");
        }

        return $this->mensajeDAO->eliminar($idMensaje);
    }
}