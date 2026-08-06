<?php
// ============================================
// VISTA: src/views/public/index.php (HOME)
// ============================================

// Datos que llegan del controlador 
$productos = $data['productos'] ?? [];

$titulo = "Sonido Interior | Cuencos Tibetanos";
$bodyClass = "home";
$pagina = "inicio";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/menu.php';
?>

<main>
    <section class="hero">
        <div class="hero-texto">
            <h2>Armonía que<br>se siente</h2>
            <p>Cuencos tibetanos artesanales para meditación, relajación y bienestar interior.</p>
            <a href="/catalogo" class="boton principal">Ver catálogo</a>
        </div>
    </section>

    <section class="beneficios-superiores">
        <article>
            <div class="icono">♨</div>
            <h3>Artesanía auténtica</h3>
            <p>Cuencos seleccionados de forma ética y consciente.</p>
        </article>
        <article>
            <div class="icono">≋</div>
            <h3>Vibración y equilibrio</h3>
            <p>Cada cuenco tiene una vibración única y transformadora.</p>
        </article>
        <article>
            <div class="icono">☘</div>
            <h3>Bienestar y conexión</h3>
            <p>Herramientas para tu práctica diaria y crecimiento personal.</p>
        </article>
    </section>

    <section class="seccion">
        <h2 class="titulo-seccion">Productos destacados</h2>
        <div class="grid-productos destacados">
            <?php if (empty($productos)): ?>
                <p style="text-align: center; grid-column: 1 / -1; color: #8a735f; padding: 20px;">Próximamente nuevos cuencos disponibles.</p>
            <?php else: ?>
                <?php foreach ($productos as $prod): ?>
                    <a href="detalle-producto?id=<?php echo $prod->getIdProducto(); ?>" class="tarjeta-producto">
                        <?php if ($prod->getImagen()): ?>
                            <img src="public/img/productos/<?php echo htmlspecialchars($prod->getImagen()); ?>" alt="<?php echo htmlspecialchars($prod->getNombre()); ?>">
                        <?php else: ?>
                            <img src="public/img/cuenco-12.svg" alt="Por defecto">
                        <?php endif; ?>
                        
                        <h3><?php echo htmlspecialchars($prod->getNombre()); ?></h3>
                        <p class="precio"><?php echo number_format($prod->getPrecio(), 2, ',', '.'); ?> €</p>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="franja-servicios">
        <article>
            <strong>🚚 Envíos 24/48h</strong>
            <p>A toda la península</p>
        </article>
        <article>
            <strong>↩ Devoluciones fáciles</strong>
            <p>Hasta 14 días</p>
        </article>
        <article>
            <strong>☏ Atención personalizada</strong>
            <p>Te ayudamos a elegir</p>
        </article>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>