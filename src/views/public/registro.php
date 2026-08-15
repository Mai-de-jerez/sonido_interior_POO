<?php
$titulo = "Crear cuenta | Sonido Interior";
$bodyClass = "fondo-login";
include __DIR__ . "/../includes/header.php";
include __DIR__ . "/../includes/menu-login.php";

// Recogemos los errores y mensajes que genera tu UsuarioController
$errores = $_SESSION['errores'] ?? [];
$mensajeError = $_SESSION['mensaje_error'] ?? null;
$old = $_SESSION['form_old'] ?? [];
$csrf_token = $csrf_token ?? '';

// Limpiamos la sesión tras leerlos
unset($_SESSION['errores'], $_SESSION['mensaje_error'], $_SESSION['form_old']);
?>

<main class="registro-contenedor">
    <section class="registro-card">
        <div class="simbolo-registro">☯</div>
        <h2>Sonido Interior</h2>
        <p class="subtitulo-registro">Crear cuenta</p>

        <h3>Únete a Sonido Interior</h3>
        <p>Rellena tus datos para crear tu cuenta</p>

        <?php if ($mensajeError): ?>
            <p style="color: #b03030; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($mensajeError) ?></p>
        <?php endif; ?>

        <form class="formulario-registro" action="<?php echo BASE_URL; ?>/registro" method="post"> 
            
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">          

            <div class="campo">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" placeholder="Elige un nombre de usuario" 
                       value="<?= htmlspecialchars($old['usuario'] ?? '') ?>">
                <span class="mensaje-error" id="error-usuario"><?= htmlspecialchars($errores['usuario'] ?? '') ?></span>
            </div>

            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Introduce tu email"
                       value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                <span class="mensaje-error" id="error-email"><?= htmlspecialchars($errores['email'] ?? '') ?></span>
            </div>

            <div class="agrupacion-passwords">
                <div class="campo">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Elige una contraseña" autocomplete="new-password">
                    <span class="mensaje-error" id="error-password"><?= htmlspecialchars($errores['password'] ?? '') ?></span>
                </div>                

                <div class="campo">
                    <label for="password_confirm">Repite la contraseña</label>
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="Repite la contraseña" autocomplete="new-password">
                    <span class="mensaje-error" id="error-password_confirm"><?= htmlspecialchars($errores['password_confirm'] ?? '') ?></span>
                </div>
            </div>

            <button type="submit" class="boton principal bloque">Crear cuenta</button>
        </form>
    </section>
</main>

<script>
function validarUsuario() {
    const input = document.getElementById('usuario');
    const valor = input.value.trim();
    if (valor.length < 3 || valor.length > 50) {
        marcarError(input, 'error-usuario', 'El usuario debe tener entre 3 y 50 caracteres.');
        return false;
    }
    if (!/^[A-Za-zÀ-ÿñÑ0-9_ ]+$/.test(valor)) {
        marcarError(input, 'error-usuario', 'Solo letras, números, espacios y guión bajo.');
        return false;
    }
    limpiarError(input, 'error-usuario');
    return true;
}

function validarEmail() {
    const input = document.getElementById('email');
    const valor = input.value.trim();
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regex.test(valor)) {
        marcarError(input, 'error-email', 'Introduce un email válido.');
        return false;
    }
    limpiarError(input, 'error-email');
    return true;
}

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

function validarPasswordConfirm() {
    const password = document.getElementById('password').value;
    const input2 = document.getElementById('password_confirm');
    if (input2.value !== password) {
        marcarError(input2, 'error-password_confirm', 'Las contraseñas no coinciden.');
        return false;
    }
    limpiarError(input2, 'error-password_confirm');
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
    if (!input) return;
    input.addEventListener('blur', validador);
    input.addEventListener('input', () => {
        if (input.classList.contains('input-error')) validador();
    });
}

enganchar('usuario', validarUsuario);
enganchar('email', validarEmail);
enganchar('password', validarPassword);
enganchar('password_confirm', validarPasswordConfirm);

document.querySelector('.formulario-registro').addEventListener('submit', (e) => {
    const ok1 = validarUsuario();
    const ok2 = validarEmail();
    const ok3 = validarPassword();
    const ok4 = validarPasswordConfirm();
    if (!ok1 || !ok2 || !ok3 || !ok4) {
        e.preventDefault();
    }
});
</script>

<?php include __DIR__ . "/../includes/footer.php"; ?>