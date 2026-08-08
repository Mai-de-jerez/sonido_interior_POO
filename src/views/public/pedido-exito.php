<?php
$idPedido = $data['idPedido'] ?? 0;

$titulo = "¡Pedido realizado! | Sonido Interior";
$pagina = "pedido-exito";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/menu.php';
?>

<main class="contenedor">
    <div class="exito-card tabla-card">
        <div class="exito-icono">✓</div>
        <h2>¡Gracias por tu compra!</h2>
        <p class="exito-subtitulo">Hemos recibido tu pedido correctamente.</p>
        
        <div class="exito-detalle">
            <p>Número de referencia del pedido: <strong>#<?php echo (int) $idPedido; ?></strong></p>
            <p>En breve comenzaremos a preparar tu paquete para el envío.</p>
        </div>

        <div class="exito-acciones">
            <a href="<?php echo BASE_URL; ?>/catalogo" class="boton principal">Volver al catálogo</a>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>