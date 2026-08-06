<?php
if (!isset($pagina)) {
    $pagina = "";
}

// Leemos las unidades directamente de la sesión (sin tocar BD)
$cantidadesCarrito = $_SESSION['cantidades_carrito'] ?? 0;
?>
<header class="cabecera">
    <div class="logo">
        <div class="logo-icono">◉</div>
        <div>
            <h1>Sonido Interior</h1>
            <p>Cuencos Tibetanos</p>
        </div>
    </div>

    <nav class="menu">
        <a href="." class="<?php echo ($pagina == 'inicio') ? 'activo' : ''; ?>">Inicio</a>
        <a href="catalogo" class="<?php echo ($pagina == 'catalogo') ? 'activo' : ''; ?>">Catálogo</a>
        <a href="sonoterapia" class="<?php echo ($pagina == 'sonoterapia') ? 'activo' : ''; ?>">Sonoterapia</a>
        <a href="sobre-nosotros" class="<?php echo ($pagina == 'nosotros') ? 'activo' : ''; ?>">Sobre nosotros</a>
        <a href="contacto" class="<?php echo ($pagina == 'contacto') ? 'activo' : ''; ?>">Contacto</a>
    </nav>

    <div class="acciones-header">
        <a href="#">🔍</a>
        <?php if (isset($_SESSION['id_usuario'])): ?>
            <a href="controllers/auth/logout.php" title="Cerrar sesión">⏻</a>

            <a href="views/public/carrito.php" title="Mi carrito" class="btn-carrito-header">
                🛒
                <?php if ($cantidadesCarrito > 0): ?>
                    <span class="badge-carrito"><?php echo $cantidadesCarrito; ?></span>
                <?php endif; ?>
            </a>
        <?php else: ?>
            <!-- <a href="views/public/login.php" title="Iniciar sesión">👤</a> -->
            <a href="login" title="Iniciar sesión">👤</a>
        <?php endif; ?>
    </div>
</header>
