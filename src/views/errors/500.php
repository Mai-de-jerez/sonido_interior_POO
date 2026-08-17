<?php
$mensaje = $mensaje ?? 'Ha ocurrido un error inesperado.';

$titulo = "Error | Sonido Interior";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/menu.php';
?>

<main class="contenedor">
    <div class="encabezado-pagina">
        <h2>Vaya, algo ha ido mal</h2>
        <div class="linea-adorno-centro"></div>
    </div>

    <div class="carrito-vacio">
        <p><?php echo htmlspecialchars($mensaje); ?></p>
        <a href="<?php echo BASE_URL; ?>/" class="boton principal">Volver al inicio</a>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>