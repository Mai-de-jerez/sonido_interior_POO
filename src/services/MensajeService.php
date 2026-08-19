<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\models\Mensaje;
use SonidoInteriorPoo\interfaces\MensajeDAOInterface;
use SonidoInteriorPoo\interfaces\MensajeServiceInterface;
use SonidoInteriorPoo\utils\EmailHelper;
use SonidoInteriorPoo\exceptions\NotFoundException;
use SonidoInteriorPoo\exceptions\BusinessRuleException;

class MensajeService implements MensajeServiceInterface {
    private MensajeDAOInterface $mensajeDAO;

    public function __construct(MensajeDAOInterface $mensajeDAO) {
        $this->mensajeDAO = $mensajeDAO;
    }

    // ---------- LECTURA ----------
    public function obtenerTodosAdmin(): array {
        return $this->mensajeDAO->obtenerTodosAdmin();
    }

    public function obtenerPorId(int $idMensaje): ?Mensaje {
        return $this->mensajeDAO->obtenerPorId($idMensaje);
    }

    public function contarNoLeidos(): int {
        return $this->mensajeDAO->contarNoLeidos();
    }

    // ---------- ESCRITURA ----------

    // crear un mensaje de contacto nuevo y enviar un aviso por email al admin
    public function crear(array $datos): void {
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

        if (!$guardado) {
            throw new BusinessRuleException("Error al guardar el mensaje en la base de datos.");
        }
        // Enviar aviso por email al admin
        EmailHelper::enviarAvisoContacto($nombre, $email, $telefono ?: null, $motivo ?: null, $mensaje);
    }

    // marcar como leído un mensaje (para el admin)
    public function marcarComoLeido(int $idMensaje): void {
        $mensaje = $this->mensajeDAO->obtenerPorId($idMensaje);

        if ($mensaje === null) {
            throw new NotFoundException("El mensaje no existe.");
        }

        if (!$this->mensajeDAO->marcarComoLeido($idMensaje)) {
            throw new BusinessRuleException("No se pudo marcar el mensaje como leído.");
        }
    }

    // eliminar un mensaje (para el admin)
    public function eliminar(int $idMensaje): void {
        $mensaje = $this->mensajeDAO->obtenerPorId($idMensaje);

        if ($mensaje === null) {
            throw new NotFoundException("El mensaje no existe.");
        }

        if (!$this->mensajeDAO->eliminar($idMensaje)) {
            throw new BusinessRuleException("No se pudo eliminar el mensaje.");
        }
    }

    
}