<?php
// comprobamos el rol del usuario logueado antes de mostrar la página
$rolNecesario = 'ADMIN';
require_once __DIR__ . '/../../../includes/seguridad.php';
// llamamos a la conexion y al modelo de categorias para obtener el listado
require_once __DIR__ . '/../../../models/categorias.php';
require_once __DIR__ . '/../../../includes/conexion.php';

$listado_categorias = obtenerCategoriasAdmin($conexion);
mysqli_close($conexion);

$titulo = "Listado de categorías | Administración";
$bodyClass = "admin-body";
$paginaAdmin = "categorias";
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/menu-admin.php';
?>

<main class="admin-main">
    <header class="admin-topbar">
        <div>
            <h2>Listado de categorías</h2>
            <p>Inicio › Categorías › Listado</p>
        </div>
        <a href="views/admin/categorias/admin-alta-categoria.php" class="boton principal">+ Añadir categoría</a>
    </header>

    <section class="admin-contenido">
        <div class="tabla-card">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listado_categorias)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">No hay categorías guardadas en la base de datos.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listado_categorias as $cat): ?>
                            <tr>
                                <td><?php echo $cat['id_categoria']; ?></td>
                                <td><?php echo htmlspecialchars($cat['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($cat['descripcion'] ?? ''); ?></td>
                                <td>
                                    <?php if ($cat['activo'] == 1): ?>
                                        <span class="estado activo">Activo</span>
                                    <?php else: ?>
                                        <span class="estado inactivo">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="acciones-tabla">
                                    <a href="views/admin/categorias/admin-alta-categoria.php?id=<?php echo $cat['id_categoria']; ?>">✎</a>
                                    <?php if ($cat['activo'] == 1): ?>
                                        <a href="views/admin/categorias/admin-eliminar-categoria.php?id=<?php echo $cat['id_categoria']; ?>">🗑</a>
                                    <?php else: ?>
                                        <a href="controllers/categorias/reactivar-categoria.php?id=<?php echo $cat['id_categoria']; ?>">↺</a>
                                    <?php endif; ?>
                                </td> 
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../../../includes/footer-simple.php'; ?>