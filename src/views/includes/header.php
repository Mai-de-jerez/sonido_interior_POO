<?php
if (!isset($titulo)) {
    $titulo = "Sonido Interior | Cuencos Tibetanos";
}
if (!isset($bodyClass)) {
    $bodyClass = "";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <base href="/sonido-interior-POO/">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="<?php echo $bodyClass; ?>">

<?php if (isset($_SESSION['mensaje_exito']) || isset($_SESSION['mensaje_error'])): 
    $esExito = isset($_SESSION['mensaje_exito']);
    $textoMensaje = $esExito ? $_SESSION['mensaje_exito'] : $_SESSION['mensaje_error'];
    $claseToast = $esExito ? 'toast-exito' : 'toast-error';
    unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);
?>
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
