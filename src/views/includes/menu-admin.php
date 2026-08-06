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
        <a href="admin/dashboard" class="<?php echo ($paginaAdmin == 'dashboard') ? 'activo' : ''; ?>">⌂ Panel</a>
        <a href="admin/productos" class="<?php echo ($paginaAdmin == 'productos') ? 'activo' : ''; ?>">▣ Productos</a>
        <a href="admin/productos/crear" class="sub <?php echo ($paginaAdmin == 'alta-producto') ? 'activo-sub' : ''; ?>">Añadir producto</a>
        <a href="admin/categorias" class="<?php echo ($paginaAdmin == 'categorias') ? 'activo' : ''; ?>">◇ Categorías</a>
        <a href="admin/categorias/crear" class="sub <?php echo ($paginaAdmin == 'alta-categoria') ? 'activo-sub' : ''; ?>">Añadir categoría</a>
        <a href="admin/mensajes" class="<?php echo ($paginaAdmin == 'mensajes') ? 'activo' : ''; ?>">✉ Mensajes</a>
        <a href="admin/configuracion" class="<?php echo ($paginaAdmin == 'configuracion') ? 'activo' : ''; ?>">⚙ Configuración</a>
        <a href="logout">↩ Cerrar sesión</a>
    </nav>
</aside> 
