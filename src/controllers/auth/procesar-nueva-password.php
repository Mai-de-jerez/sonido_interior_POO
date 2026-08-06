<?php
session_start();
require_once __DIR__ . '/../../models/auth.php';
require_once __DIR__ . '/../../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['token'])) {

    $token = $_POST['token'];
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $errores = [];

    if ($password === '' || $confirmPassword === '') {
        $errores['general'] = "Todos los campos son obligatorios.";
    } else {
        if (strlen($password) < 6 || strlen($password) > 72) {
            $errores['password'] = "La contraseña debe tener entre 6 y 72 caracteres.";
        } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errores['password'] = "Debe incluir mayúscula, minúscula y número.";
        }

        if ($password !== $confirmPassword) {
            $errores['confirm_password'] = "Las contraseñas no coinciden.";
        }
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        header("Location: ../../views/public/restablecer-password.php?token=" . urlencode($token));
        exit();
    }

    $email = obtenerEmailPorToken($conexion, $token);

    if (!$email) {
        $_SESSION['error_reset'] = "El enlace ha caducado o es inválido. Solicita uno nuevo.";
        header("Location: ../../views/public/recuperar-password.php");
        exit();
    }

    $hashPassword = password_hash($password, PASSWORD_DEFAULT);

    if (actualizarPasswordYBorrarToken($conexion, $email, $hashPassword)) {
        $_SESSION['login_mensaje'] = "¡Contraseña cambiada con éxito! Ya puedes acceder.";
        header("Location: ../../views/public/login.php");
        exit();
    } else {
        $_SESSION['error_reset'] = "Error al actualizar la contraseña. Inténtalo de nuevo.";
        header("Location: ../../views/public/restablecer-password.php?token=" . urlencode($token));
        exit();
    }

} else {
    header("Location: ../../views/public/recuperar-password.php");
    exit();
}