<?php

$producto = $producto ?? null;
$csrf_token = $csrf_token ?? '';
// Construir URL de "volver" al catálogo

$urlVolver = BASE_URL . "/catalogo";

if (!empty($_GET['volver'])) {
    $urlVolver .= "?" . $_GET['volver'];
}

$titulo = $producto ? htmlspecialchars($producto->getNombre()) . " | Sonido Interior" : "Producto no encontrado | Sonido Interior";
$bodyClass = "catalogo";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/menu.php';
?>
<main class="contenedor">

    <?php if (!$producto): ?>

        <!-- Si no existe el producto (id inválido, borrado o inactivo) -->
        <section class="encabezado-pagina">
            <h2>Producto no encontrado</h2>
            <p>Puede que se haya agotado o ya no esté disponible.</p>
            <p style="text-align: center;">
                <a href="<?php echo htmlspecialchars($urlVolver); ?>" class="boton principal">Volver al catálogo</a>
            </p>
        </section>

    <?php else: ?>

        <section class="detalle-producto">

            <div class="detalle-producto-imagen">
                <?php if (!empty($producto->getImagen())): ?>
                    <img src="<?php echo BASE_URL; ?>/public/img/productos/<?php echo htmlspecialchars($producto->getImagen()); ?>" alt="<?php echo htmlspecialchars($producto->getNombre()); ?>">
                <?php else: ?>
                    <img src="public/img/cuenco-12.svg" alt="Por defecto">
                <?php endif; ?>
            </div>

            <div class="detalle-producto-info">
                <h2><?php echo htmlspecialchars($producto->getNombre()); ?></h2>
                <span class="precio"><?php echo number_format($producto->getPrecio(), 2, ',', '.'); ?> €</span>

                <p class="descripcion"><?php echo htmlspecialchars($producto->getDescripcion()); ?></p>

                <ul class="detalle-producto-caracteristicas">
                    <?php if (!empty($producto->getMaterial())): ?>
                        <li><strong>Material</strong><?php echo htmlspecialchars($producto->getMaterial()); ?></li>
                    <?php endif; ?>

                    <?php if (!empty($producto->getProcedencia())): ?>
                        <li><strong>Procedencia</strong><?php echo htmlspecialchars($producto->getProcedencia()); ?></li>
                    <?php endif; ?>

                    <?php if (!empty($producto->getDiametro())): ?>
                        <li><strong>Diámetro</strong><?php echo htmlspecialchars($producto->getDiametro()); ?> cm</li>
                    <?php endif; ?>

                    <?php if (!empty($producto->getPeso())): ?>
                        <li><strong>Peso</strong><?php echo htmlspecialchars($producto->getPeso()); ?> g</li>
                    <?php endif; ?>
                </ul>

                <?php if (!empty($producto->getNotaMusical())): ?>
                    <div style="margin-bottom: 22px;">
                        <strong style="display:block; font-size:12px; text-transform:uppercase; color:#8a735f; margin-bottom:6px;">Melodía</strong>
                        <audio controls style="width: 100%;">
                            <source src="public/sonidos/<?php echo htmlspecialchars($producto->getNotaMusical()); ?>" type="audio/mpeg">
                            Tu navegador no soporta audio.
                        </audio>
                    </div>
                <?php endif; ?>

                <?php if ($producto->getStock() > 0): ?>
                    <p class="detalle-producto-stock disponible">✓ Disponible (<?php echo $producto->getStock(); ?> unidades)</p>
                    <form action="<?php echo BASE_URL; ?>/carrito/agregar" method="post"> 
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="id_producto" value="<?php echo $producto->getIdProducto(); ?>">
                        <input type="hidden" name="cantidad" value="1">
                        <button type="submit" class="boton principal bloque">Añadir al carrito</button>
                    </form>
                <?php else: ?>
                    <p class="detalle-producto-stock agotado">✕ Agotado temporalmente</p>
                <?php endif; ?>

                <a href="<?php echo htmlspecialchars($urlVolver); ?>" class="boton cancelar bloque">Volver al catálogo</a>
            </div>

        </section>

    <?php endif; ?>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>