<?php
if (!isset($paginaAdmin)) {
    $paginaAdmin = "";
}
?>
<aside class="sidebar">
    <div class="logo admin-logo">
        <div class="logo-icono">◉</div>
        <div>
            <h1>Sonido Interior</h1>
            <p>Admin</p>
        </div>
    </div>

    <nav class="menu-admin">
        <a href="views/admin/dashboard.php">⌂ Panel</a>
        <a href="views/admin/productos/admin-listado-productos.php" class="<?php echo ($paginaAdmin == 'productos') ? 'activo' : ''; ?>">▣ Productos</a>
        <a href="views/admin/productos/admin-alta-producto.php" class="sub <?php echo ($paginaAdmin == 'alta-producto') ? 'activo-sub' : ''; ?>">Añadir producto</a>
        <a href="views/admin/categorias/admin-listado-categorias.php" class="<?php echo ($paginaAdmin == 'categorias') ? 'activo' : ''; ?>">◇ Categorías</a>
        <a href="views/admin/categorias/admin-alta-categoria.php" class="sub <?php echo ($paginaAdmin == 'alta-categoria') ? 'activo-sub' : ''; ?>">Añadir categoría</a>
        <a href="views/admin/#">✉ Mensajes</a>
        <a href="views/admin/#">⚙ Configuración</a>
        <a href="controllers/auth/logout.php">↩ Cerrar sesión</a>
    </nav>
</aside> 
