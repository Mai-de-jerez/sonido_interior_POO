<?php
$titulo = "Sonido Interior | Recuperar Contraseña";
$pagina = "recuperar";

include __DIR__ . '/../includes/header.php';
include __DIR__ . "/../includes/menu-login.php";

$errores = $_SESSION['errores'] ?? [];
$old = $_SESSION['form_old'] ?? [];
$csrf_token = $data['csrf_token'] ?? '';
unset($_SESSION['errores'], $_SESSION['form_old']);
?>

<main class="contenedor">
    <div class="encabezado-pagina">
        <h2>Recuperar Contraseña</h2>
        <div class="linea-adorno-centro"></div>
        <p>Introduce tu correo electrónico y te enviaremos las instrucciones.</p>
    </div>

    <div style="max-width: 450px; margin: 0 auto;">
        <section class="tarjeta-beneficio">
            <form class="formulario-recuperar" action="<?php echo BASE_URL; ?>/recuperar-password" method="POST" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
                <div class="campo">
                    <label for="email">Correo electrónico *</label>
                    <input type="email" id="email" name="email" placeholder="tu@email.com"
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                    <span class="mensaje-error" id="error-email"><?= isset($errores['email']) ? htmlspecialchars($errores['email']) : '' ?></span>
                </div>

                <button type="submit" class="boton principal bloque">Enviar enlace</button>
            </form>
        </section>
    </div>
</main>

<script>
function validarEmail() {
    const input = document.getElementById('email');
    const valor = input.value.trim();
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (valor === '') {
        marcarError(input, 'error-email', 'El correo es obligatorio.');
        return false;
    }
    if (!regex.test(valor)) {
        marcarError(input, 'error-email', 'Introduce un email válido.');
        return false;
    }
    limpiarError(input, 'error-email');
    return true;
}

function marcarError(input, idSpan, mensaje) {
    input.classList.add('input-error');
    document.getElementById(idSpan).textContent = mensaje;
}

function limpiarError(input, idSpan) {
    input.classList.remove('input-error');
    document.getElementById(idSpan).textContent = '';
}

const formRecuperar = document.querySelector('.formulario-recuperar');
if (formRecuperar) {
    const inputEmail = document.getElementById('email');
    inputEmail.addEventListener('blur', validarEmail);
    inputEmail.addEventListener('input', () => {
        if (inputEmail.classList.contains('input-error')) validarEmail();
    });

    formRecuperar.addEventListener('submit', (e) => {
        if (!validarEmail()) {
            e.preventDefault();
        }
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>