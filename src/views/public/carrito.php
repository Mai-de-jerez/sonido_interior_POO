<?php
require_once __DIR__ . '/../../includes/seguridad.php';
require_once __DIR__ . '/../../models/carrito.php';
require_once __DIR__ . '/../../includes/conexion.php';

$idCarrito = obtenerOCrearCarrito($conexion, $_SESSION['id_usuario']);
$lineasCarrito = obtenerProductosCarrito($conexion, $idCarrito);
mysqli_close($conexion);

$totalCarrito = 0;
foreach ($lineasCarrito as $linea) {
    $totalCarrito += $linea['cantidad'] * $linea['precio_unitario'];
}

$titulo = "Mi carrito | Sonido Interior";
$pagina = "carrito";
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/menu.php';
?>

<main class="contenedor">
    <div class="encabezado-pagina">
        <h2>Mi carrito</h2>
        <div class="linea-adorno-centro"></div>
    </div>

        <?php if (empty($lineasCarrito)): ?>

        <div class="carrito-vacio">
            <p>Tu carrito está vacío por ahora.</p>
            <a href="views/public/productos/catalogo.php" class="boton principal">Explorar catálogo</a>
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
                    <?php foreach ($lineasCarrito as $linea): ?>
                        <?php $subtotal = $linea['cantidad'] * $linea['precio_unitario']; ?>
                        <tr>
                            <td class="carrito-producto-celda">
                                <?php if (!empty($linea['imagen'])): ?>
                                    <img src="img/productos/<?php echo htmlspecialchars($linea['imagen']); ?>" alt="<?php echo htmlspecialchars($linea['nombre']); ?>">
                                <?php else: ?>
                                    <img src="img/cuenco-12.svg" alt="Por defecto">
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($linea['nombre']); ?></span>
                            </td>
                            <td><?php echo number_format($linea['precio_unitario'], 2, ',', '.'); ?> €</td>
                            <td>
                                <div class="carrito-stepper">
                                    <form action="controllers/carrito/actualizar-cantidad.php" method="post">
                                        <input type="hidden" name="id_carrito_producto" value="<?php echo $linea['id_carrito_producto']; ?>">
                                        <input type="hidden" name="accion" value="restar">
                                        <button type="submit" class="stepper-btn" <?php echo ($linea['cantidad'] <= 1) ? 'disabled' : ''; ?>>−</button>
                                    </form>
                                    <span class="stepper-cantidad"><?php echo $linea['cantidad']; ?></span>
                                    <form action="controllers/carrito/actualizar-cantidad.php" method="post">
                                        <input type="hidden" name="id_carrito_producto" value="<?php echo $linea['id_carrito_producto']; ?>">
                                        <input type="hidden" name="accion" value="sumar">
                                        <button type="submit" class="stepper-btn" <?php echo ($linea['cantidad'] >= $linea['stock']) ? 'disabled' : ''; ?>>+</button>
                                    </form>
                                </div>
                            </td>
                            <td class="carrito-desglose">
                                x<?php echo $linea['cantidad']; ?>
                            </td>
                            <td><?php echo number_format($subtotal, 2, ',', '.'); ?> €</td>
                            <td>
                                <form action="controllers/carrito/eliminar-producto.php" method="post">
                                    <input type="hidden" name="id_carrito_producto" value="<?php echo $linea['id_carrito_producto']; ?>">
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
            <a href="views/public/checkout.php" class="boton principal cta-btn">Finalizar compra</a>
        </aside>

    <?php endif; ?>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>