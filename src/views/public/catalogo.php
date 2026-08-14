<?php
// ============================================
// VISTA: catalogo.php (POO)
// ============================================

// Datos que llegan del controlador 
$productos = $data['productos'] ?? [];
$categorias = $data['categorias'] ?? [];
$idCategoria = $data['idCategoria'] ?? null;
$orden = $data['orden'] ?? 'recientes';
$paginaActual = $data['pagina'] ?? 1;
$totalPaginas = $data['totalPaginas'] ?? 1;
$totalProductos = $data['totalProductos'] ?? 0;
$porPagina = 8;

$titulo = "Catálogo | Sonido Interior";
$pagina = "catalogo";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/menu.php';
?>

<main class="contenedor">
    <section class="encabezado-pagina">
        <h2>Catálogo de productos</h2>
        <p>Explora nuestra selección de cuencos tibetanos, accesorios y sets de meditación.</p>
    </section>

    <!-- Filtros de categoría y ordenamiento -->
    <form class="filtros-catalogo" method="GET" action="catalogo">
        <div>
            <label for="categoria">Categorías</label>
            <select name="categoria" id="categoria" onchange="this.form.submit()">
                <option value="">Todas</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat->getIdCategoria(); ?>"
                        <?php echo ($idCategoria === $cat->getIdCategoria()) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat->getNombre()); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="orden">Ordenar por</label>
            <select name="orden" id="orden" onchange="this.form.submit()">
                <option value="recientes" <?php echo ($orden === 'recientes') ? 'selected' : ''; ?>>Más recientes</option>
                <option value="precio_asc" <?php echo ($orden === 'precio_asc') ? 'selected' : ''; ?>>Precio menor</option>
                <option value="precio_desc" <?php echo ($orden === 'precio_desc') ? 'selected' : ''; ?>>Precio mayor</option>
            </select>
        </div>
        <noscript><button type="submit" class="boton">Filtrar</button></noscript>
    </form>

    <!-- Mostramos los productos en un grid -->
    <section class="grid-productos catalogo-grid">
        <?php if (empty($productos)): ?>
            <p style="text-align: center; grid-column: 1 / -1; color: #8a735f; padding: 20px;">No hay productos disponibles en este momento.</p>
        <?php else: ?>
            <?php foreach ($productos as $prod): ?>
                <article class="tarjeta-producto">
                    <?php if ($prod->getImagen()): ?>
                        <img src="public/img/productos/<?php echo htmlspecialchars($prod->getImagen()); ?>" alt="<?php echo htmlspecialchars($prod->getNombre()); ?>">
                    <?php else: ?>
                        <img src="public/img/cuenco-12.svg" alt="Por defecto">
                    <?php endif; ?>
                    
                    <h3><?php echo htmlspecialchars($prod->getNombre()); ?></h3>
                    <p class="precio"><?php echo number_format($prod->getPrecio(), 2, ',', '.'); ?> €</p>
                    
                    <?php 
                    // Construir la URL de "volver" al catálogo con los filtros actuales
                    $queryActual = http_build_query([
                        'categoria' => $idCategoria,
                        'orden' => $orden,
                        'pag' => $paginaActual
                    ]);
                    ?>
                    <a href="<?php echo BASE_URL; ?>/detalle-producto/<?php echo $prod->getIdProducto(); ?>?volver=<?php echo urlencode($queryActual); ?>" class="boton secundario">Ver producto</a>
                    
                    <form method="POST" action="carrito/agregar" style="display:inline;">
                        <input type="hidden" name="id_producto" value="<?php echo $prod->getIdProducto(); ?>">
                        <input type="hidden" name="cantidad" value="1">
                        <button type="submit" class="boton">Añadir al carrito</button>
                    </form>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <!-- Paginación -->
    <?php 
    $rutaPaginador = 'catalogo';
    include __DIR__ . "/../includes/paginacion.php"; 
    ?>
    
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>