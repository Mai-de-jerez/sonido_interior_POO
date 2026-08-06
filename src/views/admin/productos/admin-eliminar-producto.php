<?php
$rolNecesario = 'ADMIN';
require_once __DIR__ . '/../../../includes/seguridad.php';
require_once __DIR__ . '/../../../models/productos.php';
require_once __DIR__ . '/../../../includes/conexion.php';

$idProducto = (isset($_GET['id']) && ctype_digit($_GET['id'])) ? (int) $_GET['id'] : null;
$producto = $idProducto !== null ? obtenerProductoPorIdAdmin($conexion, $idProducto) : null;
mysqli_close($conexion);

if (!$producto) {
    header("Location: ../../../views/admin/productos/admin-listado-productos.php?status=notfound");
    exit();
}

$titulo = "Eliminar producto | Administración";
$bodyClass = "admin-body";
$paginaAdmin = "productos";
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/menu-admin.php';
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
                    Vas a desactivar <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong>.
                    Dejará de verse en la tienda, pero sus datos no se borrarán.
                </p>

                <form action="controllers/productos/eliminar-producto.php" method="post">
                    <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">

                    <div class="acciones-formulario">
                        <a href="views/admin/productos/admin-listado-productos.php" class="boton cancelar">Cancelar</a>
                        <button type="submit" class="boton principal">Sí, desactivar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
<?php include __DIR__ . "/../../../includes/footer-simple.php"; ?>