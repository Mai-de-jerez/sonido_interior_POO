<?php
$rolNecesario = 'ADMIN';
require_once __DIR__ . '/../../../includes/seguridad.php';
require_once __DIR__ . '/../../../models/categorias.php';
require_once __DIR__ . '/../../../includes/conexion.php';

$idCategoria = (isset($_GET['id']) && ctype_digit($_GET['id'])) ? (int) $_GET['id'] : null;
$categoria = $idCategoria !== null ? obtenerCategoriaPorId($conexion, $idCategoria) : null;
mysqli_close($conexion);

if (!$categoria) {
    header("Location: ../../../views/admin/categorias/admin-listado-categorias.php?status=notfound");
    exit();
}

$titulo = "Eliminar categoría | Administración";
$bodyClass = "admin-body";
$paginaAdmin = "categorias";
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/menu-admin.php';
?>

<main class="admin-main">
    <header class="admin-topbar">
        <div>
            <h2>Baja categoría</h2>
            <p>Inicio › Categorías › Eliminar</p>
        </div>
        <div class="admin-usuario">Administrador</div>
    </header>

    <section class="admin-contenido">
        <div class="modal-confirmacion-overlay">
            <div class="modal-confirmacion-card">
                <div class="modal-confirmacion-icono">✕</div>
                <h3>¿Dar de baja esta categoría?</h3>
                <p>
                    Vas a desactivar <strong><?php echo htmlspecialchars($categoria['nombre']); ?></strong>.
                    Dejará de verse en la tienda, pero sus datos no se borrarán.
                </p>

                <form action="controllers/categorias/eliminar-categoria.php" method="post">
                    <input type="hidden" name="id_categoria" value="<?php echo $categoria['id_categoria']; ?>">

                    <div class="acciones-formulario">
                        <a href="views/admin/categorias/admin-listado-categorias.php" class="boton cancelar">Cancelar</a>
                        <button type="submit" class="boton principal">Sí, desactivar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
<?php include __DIR__ . "/../../../includes/footer-simple.php"; ?>