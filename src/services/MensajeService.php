<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\models\Mensaje;
use SonidoInteriorPoo\models\MensajeDAO;
use SonidoInteriorPoo\interfaces\MensajeServiceInterface;
use SonidoInteriorPoo\utils\EmailHelper;

class MensajeService implements MensajeServiceInterface {
    private MensajeDAO $mensajeDAO;

    public function __construct(MensajeDAO $mensajeDAO) {
        $this->mensajeDAO = $mensajeDAO;
    }

    // ---------- VALIDACIÓN ----------
    public function validar(array $datos): array {
        $errores = [];

        $nombre = trim($datos['nombre'] ?? '');
        $email = trim($datos['email'] ?? '');
        $telefono = trim($datos['telefono'] ?? '');
        $motivo = trim($datos['motivo'] ?? '');
        $mensaje = trim($datos['mensaje'] ?? '');

        if ($nombre === '') {
            $errores['nombre'] = "El nombre es obligatorio.";
        } elseif (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 50) {
            $errores['nombre'] = "El nombre debe tener entre 3 y 50 caracteres.";
        }

        if ($email === '') {
            $errores['email'] = "El email es obligatorio.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = "Introduce un email válido.";
        }

        if ($telefono === '') {
            $errores['telefono'] = "El teléfono es obligatorio.";
        } elseif (!preg_match('/^(\+34\s?)?[6789]\d{2}\s?\d{3}\s?\d{3}$/', $telefono)) {
            $errores['telefono'] = "Introduce un teléfono válido (ej: 600 123 456).";
        }

        if ($motivo === '') {
            $errores['motivo'] = "El asunto es obligatorio.";
        } elseif (mb_strlen($motivo) < 3 || mb_strlen($motivo) > 50) {
            $errores['motivo'] = "El asunto debe tener entre 3 y 50 caracteres.";
        }

        if ($mensaje === '') {
            $errores['mensaje'] = "El mensaje es obligatorio.";
        } elseif (mb_strlen($mensaje) < 30 || mb_strlen($mensaje) > 255) {
            $errores['mensaje'] = "El mensaje debe tener entre 30 y 255 caracteres.";
        }

        return $errores;
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