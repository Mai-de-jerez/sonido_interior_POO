<?php
// ============================================
// DASHBOARD ADMIN
// ============================================

$totalProductos = $data['totalProductos'] ?? 0;
$totalActivos = $data['totalActivos'] ?? 0;

$titulo = "Panel | Administración";
$bodyClass = "admin-body";
$paginaAdmin = "dashboard";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/menu-admin.php';
?>

<main class="admin-main">
    <header class="admin-topbar">
        <div>
            <h2>Panel de administración</h2>
            <p>Inicio</p>
        </div>
        <div class="admin-usuario">
            Hola, <?php echo htmlspecialchars($_SESSION['usuario'] ?? ''); ?>
        </div>
    </header>

    <section class="admin-contenido">
        <div class="dos-columnas-admin">
            <div class="tabla-card" style="padding: 28px;">
                <h3 style="margin-bottom: 6px;">Productos</h3>
                <p style="color: #8a735f; margin: 0 0 18px;">Resumen rápido del catálogo</p>
                <p style="font-size: 34px; font-weight: bold; color: #8f5c20; margin: 0;">
                    <?php echo $totalProductos; ?>
                </p>
                <p style="color: #6d5b4e; margin: 4px 0 20px;">producto(s) en total, <?php echo $totalActivos; ?> activo(s)</p>
                <a href="<?php echo BASE_URL; ?>/admin/productos" class="boton principal">Ver listado de productos</a>
            </div>

            <div class="tabla-card" style="padding: 28px;">
                <h3 style="margin-bottom: 6px;">Acciones rápidas</h3>
                <p style="color: #8a735f; margin: 0 0 18px;">Ir directo a lo más habitual</p>
                <a href="<?php echo BASE_URL; ?>/admin/productos/crear" class="boton secundario bloque" style="margin-bottom: 12px;">+ Añadir producto</a>
                <a href="<?php echo BASE_URL; ?>/admin/productos" class="boton cancelar bloque">Ver listado</a>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer-simple.php'; ?>