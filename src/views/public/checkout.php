<?php
require_once __DIR__ . '/../../includes/seguridad.php';
require_once __DIR__ . '/../../models/carrito.php';
require_once __DIR__ . '/../../includes/conexion.php';

// obtener datos del carrito del usuario activo
$idCarrito = obtenerOCrearCarrito($conexion, $_SESSION['id_usuario']);
$lineasCarrito = obtenerProductosCarrito($conexion, $idCarrito);

// si el carrito está vacío, rebotar al carrito
if (empty($lineasCarrito)) {
    $_SESSION['mensaje_error'] = "Tu carrito está vacío. Añade algún producto antes de finalizar la compra.";
    mysqli_close($conexion);
    header("Location: carrito.php");
    exit();
}

// comprobar que ningún producto supere el stock actual en BD
$totalCarrito = 0;
foreach ($lineasCarrito as $linea) {
    if ($linea['cantidad'] > $linea['stock']) {
        $_SESSION['mensaje_error'] = "El producto '" . htmlspecialchars($linea['nombre']) . "' solo tiene " . $linea['stock'] . " unidades disponibles. Ajusta la cantidad.";
        mysqli_close($conexion);
        header("Location: carrito.php");
        exit();
    }
    $totalCarrito += $linea['cantidad'] * $linea['precio_unitario'];
}

mysqli_close($conexion);

$titulo = "Finalizar compra | Sonido Interior";
$pagina = "checkout";
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/menu.php';

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
        
        <!-- Formulario con la dirección de envío -->
        <section class="checkout-formulario tabla-card">
            <h3>Datos de entrega</h3>
            <form class="formulario-checkout" action="controllers/carrito/procesar-checkout.php" method="post">
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

        <!-- Resumen del pedido -->
        <aside class="carrito-resumen tabla-card">
            <h3>Resumen del pedido</h3>
            <ul class="checkout-lista-productos">
                <?php foreach ($lineasCarrito as $linea): ?>
                    <li class="checkout-item-producto">
                        <span><?php echo htmlspecialchars($linea['nombre']); ?> (x<?php echo $linea['cantidad']; ?>)</span>
                        <span><?php echo number_format($linea['cantidad'] * $linea['precio_unitario'], 2, ',', '.'); ?> €</span>
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

<?php include __DIR__ . '/../../includes/footer.php'; ?>