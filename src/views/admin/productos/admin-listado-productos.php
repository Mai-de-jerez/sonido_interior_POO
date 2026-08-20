<?php
// Datos que llegan del controlador
$productos = $productos ?? [];

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
                            <tr>
                                <td><?php echo $dto->getIdProducto(); ?></td>
                                <td>
                                    <?php if (!empty($dto->getImagen())): ?>
                                        <img src="<?php echo BASE_URL; ?>/public/img/productos/<?php echo htmlspecialchars($dto->getImagen()); ?>" alt="<?php echo htmlspecialchars($dto->getNombre()); ?>">
                                    <?php else: ?> 
                                        <img src="<?php echo BASE_URL; ?>/public/img/cuenco-12.svg" alt="Por defecto">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($dto->getNombre()); ?></td>
                                <td><?php echo htmlspecialchars($dto->getNombreCategoria()); ?></td>
                                <td><?php echo number_format($dto->getPrecio(), 2, ',', '.'); ?> €</td>
                                <td><?php echo $dto->getStock(); ?></td>
                                <td>
                                    <?php if (!empty($dto->getNotaMusical())): ?>
                                        <audio controls style="width: 120px; height: 30px;">
                                            <source src="<?php echo BASE_URL; ?>/public/sonidos/<?php echo htmlspecialchars($dto->getNotaMusical()); ?>" type="audio/mpeg">
                                            Tu navegador no soporta audio.
                                        </audio>
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 0.9em;">Sin sonido</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($dto->isActivo()): ?>
                                        <span class="estado activo">Activo</span>
                                    <?php else: ?>
                                        <span class="estado inactivo">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="acciones-tabla">
                                    <a href="<?php echo BASE_URL; ?>/admin/productos/editar/<?php echo $dto->getIdProducto(); ?>">✎</a>                                
                                    <?php if ($dto->isActivo()): ?>
                                        <a href="<?php echo BASE_URL; ?>/admin/productos/eliminar/<?php echo $dto->getIdProducto(); ?>">🗑</a>
                                    <?php else: ?>
                                        <a href="<?php echo BASE_URL; ?>/admin/productos/reactivar/<?php echo $dto->getIdProducto(); ?>">↺</a>
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