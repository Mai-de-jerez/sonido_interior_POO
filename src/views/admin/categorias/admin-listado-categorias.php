<?php
// ============================================
// LISTADO DE CATEGORÍAS - ADMIN (POO)
// ============================================

// Datos que llegan del controlador
$categorias = $data['categorias'] ?? [];

$titulo = "Listado de categorías | Administración";
$bodyClass = "admin-body";
$paginaAdmin = "categorias";

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/menu-admin.php';
?>

<main class="admin-main">
    <header class="admin-topbar">
        <div>
            <h2>Listado de categorías</h2>
            <p>Inicio › Categorías › Listado</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin/categorias/crear" class="boton principal">+ Añadir categoría</a>
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
                    <?php if (empty($categorias)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">No hay categorías guardadas en la base de datos.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categorias as $cat): ?>
                            <tr>
                                <td><?php echo $cat->getIdCategoria(); ?></td>
                                <td><?php echo htmlspecialchars($cat->getNombre()); ?></td>
                                <td><?php echo htmlspecialchars($cat->getDescripcion() ?? ''); ?></td>
                                <td>
                                    <?php if ($cat->isActivo()): ?>
                                        <span class="estado activo">Activo</span>
                                    <?php else: ?>
                                        <span class="estado inactivo">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="acciones-tabla">
                                    <a href="<?php echo BASE_URL; ?>/admin/categorias/editar?id=<?php echo $cat->getIdCategoria(); ?>">✎</a>
                                    <?php if ($cat->isActivo()): ?>
                                        <a href="<?php echo BASE_URL; ?>/admin/categorias/eliminar?id=<?php echo $cat->getIdCategoria(); ?>">🗑</a>
                                    <?php else: ?>
                                        <a href="<?php echo BASE_URL; ?>/admin/categorias/reactivar?id=<?php echo $cat->getIdCategoria(); ?>">↺</a>
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

<?php include __DIR__ . '/../../includes/footer-simple.php'; ?>