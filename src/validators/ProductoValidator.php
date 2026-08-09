<?php
namespace SonidoInteriorPoo\validators;

class ProductoValidator {
    public function validar(array $datos, bool $esEdicion): array {
        $errores = [];

        $nombre = trim($datos['nombre'] ?? '');
        $precio = trim($datos['precio'] ?? '');
        $stock = trim($datos['stock'] ?? '');
        $diametro = trim($datos['diametro'] ?? '');
        $peso = trim($datos['peso'] ?? '');
        $material = trim($datos['material'] ?? '');
        $procedencia = trim($datos['procedencia'] ?? '');
        $descripcion = trim($datos['descripcion'] ?? '');

        if ($nombre === '') {
            $errores['nombre'] = "El nombre es obligatorio.";
        } elseif (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 50) {
            $errores['nombre'] = "El nombre debe tener entre 3 y 50 caracteres.";
        }

        if ($precio === '') {
            $errores['precio'] = "El precio es obligatorio.";
        } elseif (!is_numeric($precio) || $precio <= 0 || $precio > 2000) {
            $errores['precio'] = "El precio debe ser mayor que 0 y no superar 2000€.";
        }

        if ($stock === '') {
            $errores['stock'] = "El stock es obligatorio.";
        } elseif (!ctype_digit($stock) || (int) $stock <= 0 || (int) $stock > 10000) {
            $errores['stock'] = "El stock debe ser mayor que 0 y no superar 10000 unidades.";
        }

        if ($peso === '') {
            $errores['peso'] = "El peso es obligatorio.";
        } elseif (!is_numeric($peso) || $peso <= 0 || $peso > 10000) {
            $errores['peso'] = "El peso debe ser mayor que 0 y no superar 10000g.";
        }

        if ($diametro !== '' && (!is_numeric($diametro) || $diametro <= 0 || $diametro > 100)) {
            $errores['diametro'] = "El diámetro debe ser mayor que 0 y no superar 100cm.";
        }

        if ($material === '') {
            $errores['material'] = "El material es obligatorio.";
        } elseif (mb_strlen($material) < 3 || mb_strlen($material) > 50) {
            $errores['material'] = "El material debe tener entre 3 y 50 caracteres.";
        }

        if ($procedencia === '') {
            $errores['procedencia'] = "La procedencia es obligatoria.";
        } elseif (mb_strlen($procedencia) < 3 || mb_strlen($procedencia) > 100) {
            $errores['procedencia'] = "La procedencia debe tener entre 3 y 100 caracteres.";
        }

        if ($descripcion === '') {
            $errores['descripcion'] = "La descripción es obligatoria.";
        } elseif (mb_strlen($descripcion) < 15 || mb_strlen($descripcion) > 300) {
            $errores['descripcion'] = "La descripción debe tener entre 15 y 300 caracteres.";
        }

        if (!$esEdicion && (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE)) {
            $errores['imagen'] = "La imagen es obligatoria.";
        }

        return $errores;
    }
}