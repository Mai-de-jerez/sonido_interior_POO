<?php
$producto = $data['producto'] ?? null;
$csrf_token = $data['csrf_token'] ?? '';

if (!$producto) {
    header("Location: " . BASE_URL . "/admin/productos?status=notfound");
    exit();
}

$titulo = "Eliminar producto | Administración";
$bodyClass = "admin-body";
$paginaAdmin = "productos";
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/menu-admin.php';
?>

<main class="admin-main">
    <header class="admin-topbar">
        <div>
            <h2>Baja producto</h2>
            <p>Inicio › Productos › Eliminar</p>
        </div>
        <div class="admin-usuario">Administrador</div>
    </header>

    <section class="admin-contenido">
        <div class="modal-confirmacion-overlay">
            <div class="modal-confirmacion-card">
                <div class="modal-confirmacion-icono">✕</div>
                <h3>¿Dar de baja este producto?</h3>
                <p>
                    Vas a desactivar <strong><?php echo htmlspecialchars($producto->getNombre()); ?></strong>.
                    Dejará de verse en la tienda, pero sus datos no se borrarán.
                </p>

                <form action="<?php echo BASE_URL; ?>/admin/productos/eliminar" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="id_producto" value="<?php echo $producto->getIdProducto(); ?>">

                    <div class="acciones-formulario">
                        <a href="<?php echo BASE_URL; ?>/admin/productos" class="boton cancelar">Cancelar</a>
                        <button type="submit" class="boton principal">Sí, desactivar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
<?php include __DIR__ . '/../../includes/footer-simple.php'; ?>