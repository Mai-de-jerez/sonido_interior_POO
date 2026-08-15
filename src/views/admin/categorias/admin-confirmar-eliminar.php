<?php
$categoria = $categoria ?? null;
$csrf_token = $csrf_token ?? '';

if (!$categoria) {
    header("Location: " . BASE_URL . "/admin/categorias?status=notfound");
    exit();
}

$titulo = "Confirmar eliminación | Administración";
$bodyClass = "admin-body";
$paginaAdmin = "categorias";

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/menu-admin.php';
?>

<main class="admin-main">
    <header class="admin-topbar">
        <div>
            <h2>Confirmar eliminación</h2>
            <p>Inicio › Categorías › Eliminar</p>
        </div>
    </header>

    <section class="admin-contenido">
        <div class="modal-confirmacion-overlay">
            <div class="modal-confirmacion-card">
                <div class="modal-confirmacion-icono">✕</div>
                <h3>¿Seguro que quieres eliminar la categoría? </h3>
                <p>
                    Vas a desactivar <strong><?php echo htmlspecialchars($categoria->getNombre()); ?></strong>.
                    Dejará de verse como activa y no la podrás asignar a productos.
                </p> 
            <form
                action="<?php echo BASE_URL; ?>/admin/categorias/eliminar/<?php echo $categoria->getIdCategoria(); ?>"
                method="post"
            >
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="acciones-formulario">
                    <a href="<?php echo BASE_URL; ?>/admin/categorias" class="boton cancelar">Cancelar</a>
                    <button type="submit" class="boton principal">Sí, desactivar</button>
                </div>
            </form>
            </div>
        </div>
    </section>
</main>
<?php include __DIR__ . '/../../includes/footer-simple.php'; ?>