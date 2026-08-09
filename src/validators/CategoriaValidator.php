<?php
namespace SonidoInteriorPoo\validators;

class CategoriaValidator {
    public function validar(array $datos): array {
        $errores = [];

        $nombre = trim($datos['nombre'] ?? '');
        $descripcion = trim($datos['descripcion'] ?? '');

        if ($nombre === '') {
            $errores['nombre'] = "El nombre es obligatorio.";
        } elseif (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 50) {
            $errores['nombre'] = "El nombre debe tener entre 3 y 50 caracteres.";
        }

        if ($descripcion === '') {
            $errores['descripcion'] = "La descripción es obligatoria.";
        } elseif (mb_strlen($descripcion) < 15 || mb_strlen($descripcion) > 300) {
            $errores['descripcion'] = "La descripción debe tener entre 15 y 300 caracteres.";
        }

        return $errores;
    }
}