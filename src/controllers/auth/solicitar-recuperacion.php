<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../models/usuarios.php';
require_once __DIR__ . '/../../models/auth.php';
require_once __DIR__ . '/../../includes/conexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $errores = [];

    if ($email === '') {
        $errores['email'] = "El correo es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = "Introduce un email válido.";
    }

    if (!empty($errores)) {
        mysqli_close($conexion);
        $_SESSION['errores'] = $errores;
        $_SESSION['form_old'] = ['email' => $email];
        header("Location: ../../views/public/recuperar-password.php");
        exit();
    }

    // Comprobamos si el email existe en la tabla usuarios
    $usuario = obtenerUsuarioPorEmail($conexion, $email);

    if ($usuario) {
        $token = bin2hex(random_bytes(32));

        if (guardarTokenRecuperacion($conexion, $email, $token)) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = getenv('MAIL_HOST') ?: 'localhost';
                $mail->Port     = 1025;
                $mail->CharSet  = 'UTF-8';

                $mail->setFrom('web@sonidointerior.com', 'Sonido Interior');
                $mail->addAddress($email, $usuario['usuario'] ?? 'Usuario');

                // Comprobamos si la variable de Docker MAIL_HOST existe en cualquier superglobal o getenv
                $mailHost = $_ENV['MAIL_HOST'] ?? $_SERVER['MAIL_HOST'] ?? getenv('MAIL_HOST');

                if ($mailHost === 'mailpit') {
                    $enlace = "http://localhost:8083/sonido-interior/views/public/restablecer-password.php?token=" . $token;
                } else {
                    $enlace = "http://localhost/sonido-interior/views/public/restablecer-password.php?token=" . $token;
                }

                $mail->isHTML(true); 
                $mail->Subject = "Restablece tu contraseña - Sonido Interior";
                $mail->Body    = "
                    <p>Hola,</p>
                    <p>Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para crear una nueva:</p>
                    <p><a href='{$enlace}'>Restablecer mi contraseña</a></p>
                    <p>Este enlace caducará en 30 minutos. Si no has sido tú, ignora este mensaje.</p>
                ";

                $mail->send();
            } catch (Exception $e) {
                error_log("Error al enviar email de recuperación: " . $e->getMessage());
            }
        }
    }

    mysqli_close($conexion);
    $_SESSION['recuperacion_mensaje'] = "Si el correo introducido está registrado, recibirás las instrucciones en tu bandeja de entrada.";
    header("Location: ../../views/public/recuperar-password.php");
    exit();
}