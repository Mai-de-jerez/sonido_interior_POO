<?php
use SonidoInteriorPoo\core\Session;

if (!isset($titulo)) {
    $titulo = "Sonido Interior | Cuencos Tibetanos";
}
if (!isset($bodyClass)) {
    $bodyClass = "";
}

// Leemos los mensajes flash (getFlash borra la clave de la sesión automáticamente)
$mensajeExito = Session::getFlash('mensaje_exito');
$mensajeError = Session::getFlash('mensaje_error');

$textoMensaje = $mensajeExito ?? $mensajeError;
$claseToast   = $mensajeExito ? 'toast-exito' : 'toast-error';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/estilos.css">
</head>
<body class="<?php echo $bodyClass; ?>">

<?php if ($textoMensaje): ?>
    <div id="toast-msg" class="toast-notificacion <?php echo $claseToast; ?>">
        <span><?php echo htmlspecialchars($textoMensaje); ?></span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('toast-msg');
            if (toast) {
                setTimeout(() => {
                    toast.classList.add('oculto');
                    setTimeout(() => toast.remove(), 500);
                }, 3000);
            }
        });
    </script>
<?php endif; ?>
