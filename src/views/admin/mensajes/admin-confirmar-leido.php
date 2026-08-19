<?php

$mensaje = $mensaje ?? null;
$csrf_token = $csrf_token ?? '';

$titulo = "Marcar mensaje como leído | Administración";
$bodyClass = "admin-body";
$paginaAdmin = "mensajes";

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/menu-admin.php';
?>

<main class="admin-main">
    <header class="admin-topbar">
        <div>
            <h2>Marcar mensaje como leído</h2>
            <p>Inicio › Mensajes › Marcar como leído</p>
        </div>
        <div class="admin-usuario">Administrador</div>
    </header>

    <section class="admin-contenido">
        <div class="modal-confirmacion-overlay">
            <div class="modal-confirmacion-card">
                <div class="modal-confirmacion-icono">✓</div>
                <h3>¿Marcar este mensaje como leído?</h3>
                <p>
                    Vas a marcar como leído el mensaje de 
                    <strong><?php echo $mensaje !== null ? htmlspecialchars($mensaje->getNombre()) : 'Mensaje no encontrado'; ?></strong>.
                </p>
                <form action="<?php echo BASE_URL; ?>/admin/mensajes/marcar-leido/<?php echo $mensaje !== null ? $mensaje->getIdMensaje() : ''; ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <div class="acciones-formulario">
                        <a href="<?php echo BASE_URL; ?>/admin/mensajes" class="boton cancelar">Cancelar</a>
                        <button type="submit" class="boton principal">Sí, marcar como leído</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../../includes/footer-simple.php'; ?>