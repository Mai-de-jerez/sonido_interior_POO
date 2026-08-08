<?php
namespace SonidoInteriorPoo\utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelper {

    // Envía un aviso por email cuando llega un mensaje de contacto nuevo.
    public static function enviarAvisoContacto(
        string $nombre,
        string $email,
        ?string $telefono,
        ?string $motivo,
        string $mensaje
    ): bool {
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
                        <div class='info'><strong>Teléfono:</strong> " . htmlspecialchars($telefono ?? '') . "</div>
                        <div class='info'><strong>Asunto:</strong> " . htmlspecialchars($motivo ?? '') . "</div>
                        <div class='mensaje-box'>" . htmlspecialchars($mensaje) . "</div>
                    </div>
                </body>
                </html>
            ";

            $mail->AltBody = "Nuevo mensaje de contacto:\n\n" .
                             "Nombre: $nombre\n" .
                             "Email: $email\n" .
                             "Teléfono: " . ($telefono ?? '') . "\n" .
                             "Asunto: " . ($motivo ?? '') . "\n\n" .
                             "Mensaje:\n$mensaje";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Error PHPMailer: {$mail->ErrorInfo}");
            return false;
        }
    }

    public static function enviarEnlaceRecuperacion(string $email, string $nombreUsuario, string $token): bool {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'localhost';
            $mail->Port       = 1025;
            $mail->SMTPAuth   = false;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('web@sonidointerior.com', 'Sonido Interior');
            $mail->addAddress($email, $nombreUsuario ?: 'Usuario');

            $enlace = \SITE_URL . "/restablecer-password?token=" . urlencode($token);

            $mail->isHTML(true);
            $mail->Subject = "Restablece tu contraseña - Sonido Interior";
            $mail->Body = "
                <p>Hola,</p>
                <p>Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para crear una nueva:</p>
                <p><a href='{$enlace}'>Restablecer mi contraseña</a></p>
                <p>Este enlace caducará en 30 minutos. Si no has sido tú, ignora este mensaje.</p>
            ";
            $mail->AltBody = "Restablece tu contraseña visitando: {$enlace}\n\nEste enlace caduca en 30 minutos.";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Error email recuperación: {$mail->ErrorInfo}");
            return false;
        }
    }
}