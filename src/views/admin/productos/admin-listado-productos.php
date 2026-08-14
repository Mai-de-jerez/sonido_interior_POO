<?php
// ============================================
// LISTADO DE PRODUCTOS - ADMIN (POO)
// ============================================

// Datos que llegan del controlador
$productos = $data['productos'] ?? [];

$titulo = "Listado de productos | Administración";
$bodyClass = "admin-body";
$paginaAdmin = "productos";

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/menu-admin.php';
?>

<main class="admin-main">
    <header class="admin-topbar">
        <div>
            <h2>Listado de productos</h2>
            <p>Inicio › Productos › Listado</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin/productos/crear" class="boton principal">+ Añadir producto</a>
    </header>

    <section class="admin-contenido">
        <div class="tabla-card">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Melodía</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 20px;">No hay productos guardados en la base de datos.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($productos as $dto): ?>
                            <?php $prod = $dto->getProducto(); ?>
                            <tr>
                                <td><?php echo $prod->getIdProducto(); ?></td>
                                <td>
                                    <?php if (!empty($prod->getImagen())): ?>
                                        <img src="<?php echo BASE_URL; ?>/public/img/productos/<?php echo htmlspecialchars($prod->getImagen()); ?>" alt="<?php echo htmlspecialchars($prod->getNombre()); ?>">
                                    <?php else: ?>
                                        <img src="<?php echo BASE_URL; ?>/public/img/cuenco-12.svg" alt="Por defecto">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($prod->getNombre()); ?></td>
                                <td><?php echo htmlspecialchars($dto->getNombreCategoria()); ?></td>
                                <td><?php echo number_format($prod->getPrecio(), 2, ',', '.'); ?> €</td>
                                <td><?php echo $prod->getStock(); ?></td>
                                <td>
                                    <?php if (!empty($prod->getNotaMusical())): ?>
                                        <audio controls style="width: 120px; height: 30px;">
                                            <source src="<?php echo BASE_URL; ?>/public/sonidos/<?php echo htmlspecialchars($prod->getNotaMusical()); ?>" type="audio/mpeg">
                                            Tu navegador no soporta audio.
                                        </audio>
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 0.9em;">Sin sonido</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($prod->isActivo()): ?>
                                        <span class="estado activo">Activo</span>
                                    <?php else: ?>
                                        <span class="estado inactivo">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="acciones-tabla">
                                    <a href="<?php echo BASE_URL; ?>/admin/productos/editar/<?php echo $prod->getIdProducto(); ?>">✎</a>                                
                                    <?php if ($prod->isActivo()): ?>
                                        <a href="<?php echo BASE_URL; ?>/admin/productos/eliminar/<?php echo $prod->getIdProducto(); ?>">🗑</a>
                                    <?php else: ?>
                                        <a href="<?php echo BASE_URL; ?>/admin/productos/reactivar/<?php echo $prod->getIdProducto(); ?>">↺</a>
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