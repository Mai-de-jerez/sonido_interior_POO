<?php
$categoria = $data['categoria'] ?? null;
$csrf_token = $data['csrf_token'] ?? '';

if (!$categoria) {
    header("Location: " . BASE_URL . "/admin/categorias?status=notfound");
    exit();
}

$titulo = "Reactivar categoría | Administración";
$bodyClass = "admin-body";
$paginaAdmin = "categorias";

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/menu-admin.php';
?>

<main class="admin-main">
    <header class="admin-topbar">
        <div>
            <h2>Reactivar categoría</h2>
            <p>Inicio › Categorías › Reactivar</p>
        </div>
        <div class="admin-usuario">Administrador</div>
    </header>

    <section class="admin-contenido">
        <div class="modal-confirmacion-overlay">
            <div class="modal-confirmacion-card">
                <div class="modal-confirmacion-icono">↺</div>
                <h3>¿Reactivar esta categoría?</h3>
                <p>
                    Vas a reactivar <strong><?php echo htmlspecialchars($categoria->getNombre()); ?></strong>.
                    Volverá a estar visible en la tienda.
                </p>
                
                <form
                    action="<?php echo BASE_URL; ?>/admin/categorias/reactivar/<?php echo $categoria->getIdCategoria(); ?>"
                    method="post"
                >

                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <div class="acciones-formulario">
                        <a href="<?php echo BASE_URL; ?>/admin/categorias" class="boton cancelar">Cancelar</a>
                        <button type="submit" class="boton principal">Sí, reactivar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../../includes/footer-simple.php'; ?>