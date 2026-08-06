<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../models/mensajes.php';
require_once __DIR__ . '/../../includes/conexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$nombre = trim($_POST["nombre"] ?? '');
$email = trim($_POST["email"] ?? '');
$telefono = trim($_POST["telefono"] ?? '');
$motivo = trim($_POST["asunto"] ?? '');
$mensaje = trim($_POST["mensaje"] ?? '');

$errores = [];

// --- Nombre ---
if ($nombre === '') {
    $errores['nombre'] = "El nombre es obligatorio.";
} elseif (strlen($nombre) < 3 || strlen($nombre) > 50) {
    $errores['nombre'] = "El nombre debe tener entre 3 y 50 caracteres.";
}

// --- Email ---
if ($email === '') {
    $errores['email'] = "El email es obligatorio.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores['email'] = "Introduce un email válido.";
}

// --- Teléfono ---
if ($telefono === '') {
    $errores['telefono'] = "El teléfono es obligatorio.";
} elseif (!preg_match('/^(\+34\s?)?[6789]\d{2}\s?\d{3}\s?\d{3}$/', $telefono)) {
    $errores['telefono'] = "Introduce un teléfono válido (ej: 600 123 456).";
}

// --- Asunto ---
if ($motivo === '') {
    $errores['asunto'] = "El asunto es obligatorio.";
} elseif (strlen($motivo) < 3 || strlen($motivo) > 50) {
    $errores['asunto'] = "El asunto debe tener entre 3 y 50 caracteres.";
}

// --- Mensaje ---
if ($mensaje === '') {
    $errores['mensaje'] = "El mensaje es obligatorio.";
} elseif (strlen($mensaje) < 30 || strlen($mensaje) > 255) {
    $errores['mensaje'] = "El mensaje debe tener entre 30 y 255 caracteres.";
}

if (!empty($errores)) {
    mysqli_close($conexion);
    $_SESSION['errores'] = $errores;
    $_SESSION['form_old'] = [
        'nombre' => $nombre,
        'email' => $email,
        'telefono' => $telefono,
        'asunto' => $motivo,
        'mensaje' => $mensaje,
    ];
    header("Location: ../../views/public/contacto.php");
    exit();
}

// 1. Guardamos el mensaje en la base de datos
$guardado = guardarMensaje($conexion, $nombre, $email, $telefono, $motivo, $mensaje);
mysqli_close($conexion);

if (!$guardado) {
    $_SESSION['contacto_status'] = 'error';
    header("Location: ../../views/public/contacto.php");
    exit();
}

// 2. Enviamos el email usando PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'localhost';
    $mail->Port       = 1025;
    $mail->SMTPAuth   = false;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('web@sonidointerior.com', 'Sonido Interior Web');
    $mail->addAddress('hola@sonidointerior.com', 'Atención al Cliente');
    $mail->addReplyTo($email, $nombre);

    $mail->isHTML(true);
    $mail->Subject = "Nuevo mensaje de contacto" . ($motivo ? ": " . $motivo : "");

    $mail->Body = "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; color: #333; }
                .container { max-width: 600px; background: #ffffff; padding: 25px; border-radius: 8px; border: 1px solid #ddd; }
                h2 { color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-top: 0; }
                .info { margin-bottom: 15px; font-size: 14px; }
                .info strong { color: #555; }
                .mensaje-box { background: #f9f9f9; padding: 15px; border-left: 4px solid #356b2f; margin-top: 15px; font-style: italic; white-space: pre-line; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Nuevo mensaje desde la Web</h2>
                <div class='info'><strong>Nombre:</strong> " . htmlspecialchars($nombre) . "</div>
                <div class='info'><strong>Email:</strong> " . htmlspecialchars($email) . "</div>
                <div class='info'><strong>Teléfono:</strong> " . htmlspecialchars($telefono) . "</div>
                <div class='info'><strong>Asunto:</strong> " . htmlspecialchars($motivo) . "</div>
                <div class='mensaje-box'>" . htmlspecialchars($mensaje) . "</div>
            </div>
        </body>
        </html>
    ";

    $mail->AltBody = "Nuevo mensaje de contacto:\n\n" .
                     "Nombre: $nombre\n" .
                     "Email: $email\n" .
                     "Teléfono: $telefono\n" .
                     "Asunto: $motivo\n\n" .
                     "Mensaje:\n$mensaje";

    $mail->send();

} catch (Exception $e) {
    error_log("Error PHPMailer: {$mail->ErrorInfo}");
}

$_SESSION['contacto_status'] = 'success';
header("Location: ../../views/public/contacto.php");
exit();