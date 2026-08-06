<?php
$rolNecesario = 'ADMIN';
require_once __DIR__ . '/../../../includes/seguridad.php';
require_once __DIR__ . '/../../../includes/conexion.php';
require_once __DIR__ . '/../../../models/categorias.php';

$categoria = null;
$esEdicion = false;

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id_categoria = (int) $_GET['id'];
    $categoria = obtenerCategoriaPorId($conexion, $id_categoria);
    if ($categoria) {
        $esEdicion = true;
    }
}

$titulo = $esEdicion ? "Editar categoría | Administración" : "Añadir categoría | Administración";
$bodyClass = "admin-body";
$paginaAdmin = "categorias";
include __DIR__ . '/../../../includes/header.php';
include __DIR__ . '/../../../includes/menu-admin.php';

$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);
?>

<main class="admin-main">
    <header class="admin-topbar">
        <div>
            <h2><?php echo $esEdicion ? "Editar categoría" : "Añadir nueva categoría"; ?></h2>
            <p>Inicio › Categorías › <?php echo $esEdicion ? "Editar" : "Añadir"; ?></p>
        </div>
        <div class="admin-usuario">Administrador</div>
    </header>

    <section class="admin-contenido">
        <form class="formulario-admin" action="controllers/categorias/guardar-categoria.php" method="post">
            
            <?php if ($esEdicion): ?>
                <input type="hidden" name="id_categoria" value="<?php echo $categoria['id_categoria']; ?>">
            <?php endif; ?>

            <div class="form-grid">
                <div class="campo ancho-completo">
                    <label for="nombre">Nombre de la categoría *</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej: Cuencos grandes" value="<?php echo $esEdicion ? htmlspecialchars($categoria['nombre']) : ''; ?>">
                    <span class="mensaje-error" id="error-nombre"><?= isset($errores['nombre']) ? htmlspecialchars($errores['nombre']) : '' ?></span>
                </div>

                <div class="campo ancho-completo">
                    <label for="descripcion">Descripción *</label>
                    <textarea id="descripcion" name="descripcion" placeholder="Describe brevemente esta categoría..."><?php echo $esEdicion ? htmlspecialchars($categoria['descripcion']) : ''; ?></textarea>
                    <span class="mensaje-error" id="error-descripcion"><?= isset($errores['descripcion']) ? htmlspecialchars($errores['descripcion']) : '' ?></span>
                </div>
            </div>

            <div class="acciones-formulario">
                <a href="views/admin/categorias/admin-listado-categorias.php" class="boton cancelar">Cancelar</a>
                <button type="submit" class="boton principal"><?php echo $esEdicion ? "Actualizar categoría" : "Guardar categoría"; ?></button>
            </div>
        </form>
    </section>
</main>

<script>
const formCategoria = document.querySelector('.formulario-admin');
if (formCategoria) {

    function marcarError(input, idSpan, mensaje) {
        input.classList.add('input-error');
        document.getElementById(idSpan).textContent = mensaje;
    }
    function limpiarError(input, idSpan) {
        input.classList.remove('input-error');
        document.getElementById(idSpan).textContent = '';
    }

    function validarNombre() {
        const input = document.getElementById('nombre');
        const valor = input.value.trim();
        if (valor === '') {
            marcarError(input, 'error-nombre', 'El nombre es obligatorio.');
            return false;
        }
        if (valor.length < 3 || valor.length > 50) {
            marcarError(input, 'error-nombre', 'El nombre debe tener entre 3 y 50 caracteres.');
            return false;
        }
        limpiarError(input, 'error-nombre');
        return true;
    }

    function validarDescripcion() {
        const input = document.getElementById('descripcion');
        const valor = input.value.trim();
        if (valor === '') {
            marcarError(input, 'error-descripcion', 'La descripción es obligatoria.');
            return false;
        }
        if (valor.length < 15 || valor.length > 300) {
            marcarError(input, 'error-descripcion', 'La descripción debe tener entre 15 y 300 caracteres.');
            return false;
        }
        limpiarError(input, 'error-descripcion');
        return true;
    }

    function enganchar(idInput, validador) {
        const input = document.getElementById(idInput);
        input.addEventListener('blur', validador);
        input.addEventListener('input', () => {
            if (input.classList.contains('input-error')) validador();
        });
    }

    enganchar('nombre', validarNombre);
    enganchar('descripcion', validarDescripcion);

    formCategoria.addEventListener('submit', (e) => {
        const ok1 = validarNombre();
        const ok2 = validarDescripcion();
        if (!ok1 || !ok2) {
            e.preventDefault();
        }
    });
}
</script>

<?php include __DIR__ . "/../../../includes/footer-simple.php"; ?>