<?php
session_start();
if (!isset($rolNecesario)) {
    $rolNecesario = null; // por defecto, solo pedimos estar logueado
}
 
// Primero comprobamos que haya sesión, sea cual sea el rol que se pida

if (!isset($_SESSION['id_usuario'])) {
    header("Location: /sonido-interior/views/public/login.php?status=denegado");
    exit();
}
 
// Si la página pide un rol concreto, comprobamos que coincida
if ($rolNecesario !== null && $_SESSION['rol'] !== $rolNecesario) {
    header("Location: /sonido-interior/views/public/login.php?status=denegado");
    exit();
}
?>