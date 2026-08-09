<?php
namespace SonidoInteriorPoo\validators;

class MensajeValidator {
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
}