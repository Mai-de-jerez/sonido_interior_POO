<?php
session_start();
require_once __DIR__ . '/../../models/usuarios.php';
require_once __DIR__ . '/../../includes/conexion.php';

$errores = [];

$email = trim($_POST["email"] ?? '');
$usuario = trim($_POST["usuario"] ?? '');
$password = $_POST["password"] ?? '';
$password2 = $_POST["password2"] ?? '';

// --- Campos vacíos ---
if ($email === '' || $usuario === '' || $password === '' || $password2 === '') {
    $errores['general'] = "Todos los campos son obligatorios.";
}

// --- Email ---
if ($email !== '') {
    if (strlen($email) > 255) {
        $errores['email'] = "El email es demasiado largo.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = "Introduce un email válido.";
    }
}

// --- Usuario ---
if ($usuario !== '') {
    if (strlen($usuario) < 3 || strlen($usuario) > 50) {
        $errores['usuario'] = "El usuario debe tener entre 3 y 50 caracteres.";
    } elseif (!preg_match('/^[A-Za-zÀ-ÿñÑ0-9_ ]+$/', $usuario)) {
        $errores['usuario'] = "Solo letras, números, espacios y guión bajo.";
    }
}

// --- Password ---
if ($password !== '') {
    if (strlen($password) < 6 || strlen($password) > 72) {
        $errores['password'] = "La contraseña debe tener entre 6 y 72 caracteres.";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errores['password'] = "Debe incluir mayúscula, minúscula y número.";
    }
}

// --- Coincidencia de contraseñas ---
if ($password !== '' && $password2 !== '' && $password !== $password2) {
    $errores['password2'] = "Las contraseñas no coinciden.";
}

// --- Unicidad en BD, solo si el formato ya está bien ---
if (empty($errores)) {
    if (obtenerUsuarioPorUsername($conexion, $usuario)) {
        $errores['usuario'] = "Ese nombre de usuario ya existe.";
    }
    if (obtenerUsuarioPorEmail($conexion, $email)) {
        $errores['email'] = "Ese email ya está registrado.";
    }
}

// --- Si todo está bien, creamos el usuario ---
if (empty($errores)) {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $creado = registroUsuario($conexion, $usuario, $email, $passwordHash);
    mysqli_close($conexion);

    if ($creado) {
        header("Location: ../../views/public/login.php?status=registrado");
        exit();
    } else {
        $errores['general'] = "Error al registrar. Inténtalo de nuevo.";
    }
} else {
    mysqli_close($conexion);
}

// --- Si llegamos aquí, hubo errores: guardamos y volvemos al formulario ---
$_SESSION['errores'] = $errores;
$_SESSION['form_old'] = [
    'email' => $email,
    'usuario' => $usuario,
];
header("Location: ../../views/public/registro.php");
exit();