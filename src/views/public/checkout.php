<?php
$lineas = $data['lineas'] ?? [];
$totalCarrito = $data['totalCarrito'] ?? 0;

$titulo = "Finalizar compra | Sonido Interior";
$pagina = "checkout";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/menu.php';

$errores = $_SESSION['errores'] ?? [];
$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['errores'], $_SESSION['form_old']);
?>

<main class="contenedor">
    <div class="encabezado-pagina">
        <h2>Confirmar y Pagar</h2>
        <div class="linea-adorno-centro"></div>
    </div>

    <div class="grid-checkout">

        <section class="checkout-formulario tabla-card">
            <h3>Datos de entrega</h3>
            <form class="formulario-checkout" action="<?php echo BASE_URL; ?>/checkout" method="post">
                <div class="campo-form">
                    <label for="direccion_envio">Dirección de envío completa *</label>
                    <input type="text" 
                           id="direccion_envio" 
                           name="direccion_envio" 
                           maxlength="255" 
                           placeholder="Ej: Calle Gran Vía 12, 3ºA, 28013 Madrid"
                           value="<?= htmlspecialchars($old['direccion_envio'] ?? '') ?>">
                    <span class="mensaje-error" id="error-direccion_envio"><?= isset($errores['direccion_envio']) ? htmlspecialchars($errores['direccion_envio']) : '' ?></span>
                </div>

                <div class="checkout-acciones">
                    <button type="submit" class="boton principal cta-btn btn-bloque">Confirmar pedido</button>
                </div>
            </form>
        </section>

        <aside class="carrito-resumen tabla-card">
            <h3>Resumen del pedido</h3>
            <ul class="checkout-lista-productos">
                <?php foreach ($lineas as $linea): ?>
                    <li class="checkout-item-producto">
                        <span><?php echo htmlspecialchars($linea->getProducto()->getNombre()); ?> (x<?php echo $linea->getCantidad(); ?>)</span>
                        <span><?php echo number_format($linea->getSubtotal(), 2, ',', '.'); ?> €</span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <hr class="checkout-separador">
            <div class="resumen-total">
                <span>Total a pagar:</span>
                <span><?php echo number_format($totalCarrito, 2, ',', '.'); ?> €</span>
            </div>
        </aside>

    </div>
</main>

<script>
const formCheckout = document.querySelector('.formulario-checkout');
if (formCheckout) {
    const inputDireccion = document.getElementById('direccion_envio');

    function validarDireccion() {
        const valor = inputDireccion.value.trim();
        const span = document.getElementById('error-direccion_envio');
        if (valor === '') {
            inputDireccion.classList.add('input-error');
            span.textContent = 'Introduce una dirección de envío.';
            return false;
        }
        inputDireccion.classList.remove('input-error');
        span.textContent = '';
        return true;
    }

    inputDireccion.addEventListener('blur', validarDireccion);
    inputDireccion.addEventListener('input', () => {
        if (inputDireccion.classList.contains('input-error')) validarDireccion();
    });

    formCheckout.addEventListener('submit', (e) => {
        if (!validarDireccion()) {
            e.preventDefault();
        }
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>