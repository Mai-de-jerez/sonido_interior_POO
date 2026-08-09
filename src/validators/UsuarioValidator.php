<?php
namespace SonidoInteriorPoo\validators;

class UsuarioValidator {

    public function validarLogin(array $datos): array {
        $errores = [];
        $usuario = trim($datos['usuario'] ?? '');
        $password = $datos['password'] ?? '';

        if ($usuario === '') {
            $errores['usuario'] = "Introduce tu usuario.";
        }
        if ($password === '') {
            $errores['password'] = "Introduce tu contraseña.";
        }

        return $errores;
    }

    public function validarRegistro(array $datos): array {
        $errores = [];
        $usuario = trim($datos['usuario'] ?? '');
        $email = trim($datos['email'] ?? '');
        $password = $datos['password'] ?? '';
        $passwordConfirm = $datos['password_confirm'] ?? '';

        if ($usuario === '') {
            $errores['usuario'] = "El usuario es obligatorio.";
        } elseif (strlen($usuario) < 3 || strlen($usuario) > 50) {
            $errores['usuario'] = "El usuario debe tener entre 3 y 50 caracteres.";
        }

        if ($email === '') {
            $errores['email'] = "El email es obligatorio.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = "El email no es válido.";
        }

        if ($password === '') {
            $errores['password'] = "La contraseña es obligatoria.";
        } elseif (strlen($password) < 6) {
            $errores['password'] = "La contraseña debe tener al menos 8 caracteres.";
        }

        if ($password !== $passwordConfirm) {
            $errores['password_confirm'] = "Las contraseñas no coinciden.";
        }

        return $errores;
    }

    public function validarRecuperacion(array $datos): array {
        $errores = [];
        $email = trim($datos['email'] ?? '');

        if ($email === '') {
            $errores['email'] = "El correo es obligatorio.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = "Introduce un email válido.";
        }

        return $errores;
    }

    public function validarNuevaPassword(array $datos): array {
        $errores = [];
        $password = $datos['password'] ?? '';
        $confirmPassword = $datos['confirm_password'] ?? '';

        if ($password === '' || $confirmPassword === '') {
            $errores['general'] = "Todos los campos son obligatorios.";
            return $errores;
        }

        if (strlen($password) < 6 || strlen($password) > 72) {
            $errores['password'] = "La contraseña debe tener entre 6 y 72 caracteres.";
        } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errores['password'] = "Debe incluir mayúscula, minúscula y número.";
        }

        if ($password !== $confirmPassword) {
            $errores['confirm_password'] = "Las contraseñas no coinciden.";
        }

        return $errores;
    }
}