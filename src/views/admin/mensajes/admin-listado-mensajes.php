<?php
// ============================================
// LISTADO DE MENSAJES - ADMIN (POO)
// ============================================

$mensajes = $mensajes ?? [];
$csrf_token = $csrf_token ?? '';

$titulo = "Mensajes | Administración";
$bodyClass = "admin-body";
$paginaAdmin = "mensajes";

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/menu-admin.php';
?>

<main class="admin-main"> 

    <header class="admin-topbar">
        <div>
            <h2>Mensajes de contacto</h2>
            <p>Inicio › Mensajes › Listado</p>
        </div>
    </header>

    <section class="admin-contenido">

        <div class="tabla-card">

            <table class="tabla-admin">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Motivo</th>
                        <th>Mensaje</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($mensajes)): ?>

                        <tr>
                            <td colspan="9" style="text-align: center; padding: 20px;">
                                No hay mensajes recibidos.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($mensajes as $mensaje): ?>

                            <tr>

                                <td>
                                    <?php echo $mensaje->getIdMensaje(); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($mensaje->getNombre()); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($mensaje->getEmail()); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($mensaje->getTelefono() ?? ''); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($mensaje->getMotivo() ?? ''); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($mensaje->getMensaje()); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($mensaje->getFechaEnvio()); ?>
                                </td>

                                <td>
                                    <?php if ($mensaje->isLeido()): ?>

                                        <span class="estado activo">
                                            Leído
                                        </span>

                                    <?php else: ?>

                                        <span class="estado inactivo">
                                            No leído
                                        </span>

                                    <?php endif; ?>
                                </td>

                                <td class="acciones-tabla">

                                    <?php if (!$mensaje->isLeido()): ?>

                                        <form
                                            method="POST"
                                            action="<?php echo BASE_URL; ?>/admin/mensajes/marcar-leido/<?php echo $mensaje->getIdMensaje(); ?>"
                                            style="display: inline;"
                                        >
                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?php echo htmlspecialchars($csrf_token); ?>"
                                            >

                                            <button type="submit" title="Marcar como leído">
                                                ✓
                                            </button>
                                        </form>

                                    <?php endif; ?>

                                    <form
                                        method="POST"
                                        action="<?php echo BASE_URL; ?>/admin/mensajes/eliminar/<?php echo $mensaje->getIdMensaje(); ?>"
                                        style="display: inline;"
                                    >
                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?php echo htmlspecialchars($csrf_token); ?>"
                                        >

                                        <button type="submit" title="Eliminar">
                                            🗑
                                        </button>
                                    </form>

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