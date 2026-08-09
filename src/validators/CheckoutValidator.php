<?php
namespace SonidoInteriorPoo\validators;

class CheckoutValidator {
    public function validar(array $datos): array {
        $errores = [];

        $direccionEnvio = trim($datos['direccion_envio'] ?? '');

        if ($direccionEnvio === '') {
            $errores['direccion_envio'] = "Introduce una dirección de envío.";
        } elseif (mb_strlen($direccionEnvio) < 10 || mb_strlen($direccionEnvio) > 255) {
            $errores['direccion_envio'] = "La dirección de envío debe tener entre 10 y 255 caracteres.";
        }

        return $errores;
    }
}