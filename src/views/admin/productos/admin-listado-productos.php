<?php
// comprobamos el rol del usuario logueado antes de mostrar la página
$rolNecesario = 'ADMIN';
require_once __DIR__ . '/../../../includes/seguridad.php';
// llamamos a la conexion y al modelo de productos para obtener el listado de productos
require_once __DIR__ . '/../../../models/productos.php';
require_once __DIR__ . '/../../../includes/conexion.php'; 

$listado_productos = obtenerProductosAdmin($conexion); 
mysqli_close($conexion); 

$titulo = "Listado de productos | Administración";
$bodyClass = "admin-body";
$paginaAdmin = "productos";
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/menu-admin.php';
?>

<main class="admin-main">
    <header class="admin-topbar">
        <div>
            <h2>Listado de productos</h2>
            <p>Inicio › Productos › Listado</p>
        </div>
        <a href="views/admin/productos/admin-alta-producto.php" class="boton principal">+ Añadir producto</a>
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
                    <?php if (empty($listado_productos)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 20px;">No hay productos guardados en la base de datos.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listado_productos as $prod): ?>
                            <tr>
                                <td><?php echo $prod['id_producto']; ?></td>
                                <td>
                                    <!-- Si el producto tiene imagen subida la muestra de la carpeta cuencos, si no, una por defecto -->
                                    <?php if (!empty($prod['imagen'])): ?>
                                        <img src="img/productos/<?php echo htmlspecialchars($prod['imagen']); ?>" alt="<?php echo htmlspecialchars($prod['nombre']); ?>">
                                    <?php else: ?>
                                        <img src="img/cuenco-12.svg" alt="Por defecto">
                                    <?php endif; ?>
                                </td>

                                <td><?php echo htmlspecialchars($prod['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($prod['nombre_categoria']); ?></td>
                                <!-- Formateamos el precio para que luzca con su coma y símbolo de euro -->
                                <td><?php echo number_format($prod['precio'], 2, ',', '.'); ?> €</td>
                                <td><?php echo $prod['stock']; ?></td>
                                <td>
                                    <?php if (!empty($prod['nota_musical'])): ?>
                                        <!-- Reproductor compacto nativo -->
                                        <audio controls style="width: 120px; height: 30px;">
                                            <source src="sonidos/<?php echo htmlspecialchars($prod['nota_musical']); ?>" type="audio/mpeg">
                                            Tu navegador no soporta audio.
                                        </audio>
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 0.9em;">Sin sonido</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($prod['activo'] == 1): ?>
                                        <span class="estado activo">Activo</span>
                                    <?php else: ?>
                                        <span class="estado inactivo">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="acciones-tabla">
                                    <a href="views/admin/productos/admin-alta-producto.php?id=<?php echo $prod['id_producto']; ?>">✎</a>
                                    <?php if ($prod['activo'] == 1): ?>
                                        <a href="views/admin/productos/admin-eliminar-producto.php?id=<?php echo $prod['id_producto']; ?>">🗑</a>
                                    <?php else: ?>
                                        <a href="controllers/productos/reactivar-producto.php?id=<?php echo $prod['id_producto']; ?>">↺</a>
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
