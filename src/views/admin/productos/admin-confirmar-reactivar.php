<?php
$producto = $data['producto'] ?? null;

if (!$producto) {
    header("Location: admin/productos?status=notfound");
    exit();
}

$titulo = "Reactivar producto | Administración";
$bodyClass = "admin-body";
$paginaAdmin = "productos";
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/menu-admin.php';
?>

<main class="admin-main">
    <header class="admin-topbar">
        <div>
            <h2>Reactivar producto</h2>
            <p>Inicio › Productos › Reactivar</p>
        </div>
        <div class="admin-usuario">Administrador</div>
    </header>

    <section class="admin-contenido">
        <div class="modal-confirmacion-overlay">
            <div class="modal-confirmacion-card">
                <div class="modal-confirmacion-icono">↺</div>
                <h3>¿Reactivar este producto?</h3>
                <p>
                    Vas a reactivar <strong><?php echo htmlspecialchars($producto->getNombre()); ?></strong>.
                    Volverá a estar visible en la tienda.
                </p>

                <form action="productos/reactivar" method="post">
                    <input type="hidden" name="id_producto" value="<?php echo $producto->getIdProducto(); ?>">

                    <div class="acciones-formulario">
                        <a href="admin/productos" class="boton cancelar">Cancelar</a>
                        <button type="submit" class="boton principal">Sí, reactivar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../../includes/footer-simple.php'; ?>