<?php
$titulo = "Crear cuenta | Sonido Interior";
$bodyClass = "fondo-login";
include __DIR__ . "/../../includes/header.php";
include __DIR__ . "/../../includes/menu-login.php";

// Recogemos errores y datos previos, y los limpiamos para que no se queden pegados en la sesión
$errores = $_SESSION['errores'] ?? [];
$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['errores'], $_SESSION['form_old']);
?>

<main class="registro-contenedor">
    <section class="registro-card">
        <div class="simbolo-registro">☯</div>
        <h2>Sonido Interior</h2>
        <p class="subtitulo-registro">Crear cuenta</p>

        <h3>Únete a Sonido Interior</h3>
        <p>Rellena tus datos para crear tu cuenta</p>

        <?php if (isset($errores['general'])): ?>
            <p style="color: #b03030; text-align: center;"><?= htmlspecialchars($errores['general']) ?></p>
        <?php endif; ?>

        <form class="formulario-registro" action="controllers/auth/procesar-registro.php" method="post">           

            <div class="campo">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" placeholder="Elige un nombre de usuario" 
                       value="<?= htmlspecialchars($old['usuario'] ?? '') ?>">
                <span class="mensaje-error" id="error-usuario"><?= isset($errores['usuario']) ? htmlspecialchars($errores['usuario']) : '' ?></span>
            </div>

            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Introduce tu email"
                       value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                <span class="mensaje-error" id="error-email"><?= isset($errores['email']) ? htmlspecialchars($errores['email']) : '' ?></span>
            </div>

            <div class="agrupacion-passwords">
                <div class="campo">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Elige una contraseña" autocomplete="new-password">
                    <span class="mensaje-error" id="error-password"><?= isset($errores['password']) ? htmlspecialchars($errores['password']) : '' ?></span>
                </div>                

                <div class="campo">
                    <label for="password2">Repite la contraseña</label>
                    <input type="password" id="password2" name="password2" placeholder="Repite la contraseña" autocomplete="new-password">
                    <span class="mensaje-error" id="error-password2"><?= isset($errores['password2']) ? htmlspecialchars($errores['password2']) : '' ?></span>
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

function validarPassword2() {
    const password = document.getElementById('password').value;
    const input2 = document.getElementById('password2');
    if (input2.value !== password) {
        marcarError(input2, 'error-password2', 'Las contraseñas no coinciden.');
        return false;
    }
    limpiarError(input2, 'error-password2');
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

enganchar('usuario', validarUsuario);
enganchar('email', validarEmail);
enganchar('password', validarPassword);
enganchar('password2', validarPassword2);

document.querySelector('.formulario-registro').addEventListener('submit', (e) => {
    const ok1 = validarUsuario();
    const ok2 = validarEmail();
    const ok3 = validarPassword();
    const ok4 = validarPassword2();
    if (!ok1 || !ok2 || !ok3 || !ok4) {
        e.preventDefault();
    }
});
</script>

<?php include __DIR__ . "/../../includes/footer.php"; ?>