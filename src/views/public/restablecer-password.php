<?php

$token = $token ?? '';
$csrf_token = $csrf_token ?? '';
$titulo = "Sonido Interior | Nueva Contraseña";
$pagina = "restablecer";


include __DIR__ . '/../includes/header.php';
include __DIR__ . "/../includes/menu-login.php";

$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);
?>

<main class="contenedor">
    <div class="encabezado-pagina">
        <h2>Crear Nueva Contraseña</h2>
        <div class="linea-adorno-centro"></div>
    </div>

    <div style="max-width: 450px; margin: 0 auto;">
        <?php if (empty($token)): ?>
            <p style="text-align: center; color: #b03030;">El enlace de recuperación no es válido.</p>
        <?php else: ?>
            <section class="tarjeta-beneficio">
                <form class="formulario-restablecer" action="<?php echo BASE_URL; ?>/restablecer-password" method="POST" autocomplete="off">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">

                    <div class="campo">
                        <label for="password">Nueva contraseña *</label>
                        <input type="password" id="password" name="password" autocomplete="new-password" required>
                        <span class="mensaje-error" id="error-password"><?= isset($errores['password']) ? htmlspecialchars($errores['password']) : '' ?></span>
                    </div>

                    <div class="campo">
                        <label for="confirm_password">Confirmar nueva contraseña *</label>
                        <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" required>
                        <span class="mensaje-error" id="error-confirm_password"><?= isset($errores['confirm_password']) ? htmlspecialchars($errores['confirm_password']) : '' ?></span>
                    </div>

                    <button type="submit" class="boton principal bloque">Guardar nueva contraseña</button>
                </form>
            </section>
        <?php endif; ?>
    </div>
</main>

<script>
function validarPassword() {
    const input = document.getElementById('password');
    const valor = input.value;
    if (valor.length < 6 || valor.length > 72) {
        marcarError(input, 'error-password', 'La contraseña debe tener entre 6 y 72 caracteres.');
        return false;
    }
    if (!/[A-Z]/.test(valor) || !/[a-z]/.test(valor) || !/[0-9]/.test(valor)) {
        marcarError(input, 'error-password', 'Debe incluir mayúscula, minúscula y número.');
        return false;
    }
    limpiarError(input, 'error-password');
    return true;
}

function validarConfirmPassword() {
    const password = document.getElementById('password').value;
    const input2 = document.getElementById('confirm_password');
    if (input2.value !== password) {
        marcarError(input2, 'error-confirm_password', 'Las contraseñas no coinciden.');
        return false;
    }
    limpiarError(input2, 'error-confirm_password');
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

function enganchar(idInput, validador) {
    const input = document.getElementById(idInput);
    input.addEventListener('blur', validador);
    input.addEventListener('input', () => {
        if (input.classList.contains('input-error')) validador();
    });
}

const formRestablecer = document.querySelector('.formulario-restablecer');
if (formRestablecer) {
    enganchar('password', validarPassword);
    enganchar('confirm_password', validarConfirmPassword);

    formRestablecer.addEventListener('submit', (e) => {
        const ok1 = validarPassword();
        const ok2 = validarConfirmPassword();
        if (!ok1 || !ok2) {
            e.preventDefault();
        }
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>