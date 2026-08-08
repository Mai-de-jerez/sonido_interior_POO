<?php
$lineas = $data['lineas'] ?? [];

$totalCarrito = 0;
foreach ($lineas as $linea) {
    $totalCarrito += $linea->getSubtotal();
}

$titulo = "Mi carrito | Sonido Interior";
$pagina = "carrito";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/menu.php';
?>

<main class="contenedor">
    <div class="encabezado-pagina">
        <h2>Mi carrito</h2>
        <div class="linea-adorno-centro"></div>
    </div>

        <?php if (empty($lineas)): ?>

        <div class="carrito-vacio">
            <p>Tu carrito está vacío por ahora.</p>
            <a href="<?php echo BASE_URL; ?>/catalogo" class="boton principal">Explorar catálogo</a>
        </div>

        <?php else: ?>

        <div class="tabla-card">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio unitario</th>
                        <th>Cantidad</th>
                        <th>Desglose</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lineas as $linea): ?>
                        <?php $producto = $linea->getProducto(); ?>
                        <tr>
                            <td class="carrito-producto-celda">
                                <?php if (!empty($producto->getImagen())): ?>
                                    <img src="public/img/productos/<?php echo htmlspecialchars($producto->getImagen()); ?>" alt="<?php echo htmlspecialchars($producto->getNombre()); ?>">
                                <?php else: ?>
                                    <img src="public/img/cuenco-12.svg" alt="Por defecto">
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($producto->getNombre()); ?></span>
                            </td>
                            <td><?php echo number_format($linea->getPrecioUnitario(), 2, ',', '.'); ?> €</td>
                            <td>
                                <div class="carrito-stepper">
                                    <form action="<?php echo BASE_URL; ?>/carrito/actualizar-cantidad" method="post">
                                        <input type="hidden" name="id_carrito_producto" value="<?php echo $linea->getIdCarritoProducto(); ?>">
                                        <input type="hidden" name="accion" value="restar">
                                        <button type="submit" class="stepper-btn" <?php echo ($linea->getCantidad() <= 1) ? 'disabled' : ''; ?>>−</button>
                                    </form>
                                    <span class="stepper-cantidad"><?php echo $linea->getCantidad(); ?></span>
                                    <form action="<?php echo BASE_URL; ?>/carrito/actualizar-cantidad" method="post">
                                        <input type="hidden" name="id_carrito_producto" value="<?php echo $linea->getIdCarritoProducto(); ?>">
                                        <input type="hidden" name="accion" value="sumar">
                                        <button type="submit" class="stepper-btn" <?php echo ($linea->getCantidad() >= $producto->getStock()) ? 'disabled' : ''; ?>>+</button>
                                    </form>
                                </div>
                            </td>
                            <td class="carrito-desglose">
                                x<?php echo $linea->getCantidad(); ?>
                            </td>
                            <td><?php echo number_format($linea->getSubtotal(), 2, ',', '.'); ?> €</td>
                            <td>
                                <form action="<?php echo BASE_URL; ?>/carrito/eliminar" method="post">
                                    <input type="hidden" name="id_carrito_producto" value="<?php echo $linea->getIdCarritoProducto(); ?>">
                                    <button type="submit" class="btn-quitar-linea" title="Quitar del carrito">✕</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <aside class="carrito-resumen">
            <h3>Resumen del pedido</h3>
            <div class="resumen-total">
                <span>Total:</span>
                <span><?php echo number_format($totalCarrito, 2, ',', '.'); ?> €</span>
            </div>
            <a href="<?php echo BASE_URL; ?>/checkout" class="boton principal cta-btn">Finalizar compra</a>
        </aside>

    <?php endif; ?>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>