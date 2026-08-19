<?php
$titulo = "Login Administración | Sonido Interior";
$bodyClass = "fondo-login";

$errores = $errores ?? [];
$mensajeError = $mensajeError ?? null;
$old = $old ?? [];
$csrf_token = $csrf_token ?? '';

include __DIR__ . "/../includes/header.php";
include __DIR__ . "/../includes/menu-login.php";
?>

<main class="login-contenedor">
    <section class="login-card">
        <div class="simbolo-login">☯</div>
        <h2>Sonido Interior</h2>
        <p class="subtitulo-login">Administración</p>

        <h3>Acceso al panel de administración</h3>
        <p>Introduce tus credenciales para continuar</p>

        <!-- FORMULARIO -->
        <form class="formulario-login" action="<?php echo BASE_URL; ?>/login" method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
            <div class="campo">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" placeholder="Introduce tu usuario"
                       value="<?= htmlspecialchars($old['usuario'] ?? '') ?>">
                <span class="mensaje-error" id="error-usuario"><?= isset($errores['usuario']) ? htmlspecialchars($errores['usuario']) : '' ?></span>
            </div>

            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Introduce tu contraseña" autocomplete="current-password">
                <span class="mensaje-error" id="error-password"><?= isset($errores['password']) ? htmlspecialchars($errores['password']) : '' ?></span>
            </div>

            <button type="submit" class="boton principal bloque">Entrar</button>

            <div class="opciones-login">
                <a href="registro">¿No tienes una cuenta? Crea aquí una</a>
                <a href="recuperar-password">¿Has olvidado tu contraseña?</a>
            </div>
        </form>
    </section>
</main>

<script>
function limpiarErroresLogin() {
    document.querySelectorAll('.formulario-login input').forEach(input => {
        input.classList.remove('input-error');
    });
    document.querySelectorAll('.formulario-login .mensaje-error').forEach(span => {
        span.textContent = '';
    });
    const errorGeneral = document.getElementById('error-general');
    if (errorGeneral) errorGeneral.style.display = 'none';
}

document.querySelectorAll('.formulario-login input').forEach(input => {
    input.addEventListener('input', limpiarErroresLogin);
});

document.querySelector('.formulario-login').addEventListener('submit', (e) => {
    const usuario = document.getElementById('usuario').value.trim();
    const password = document.getElementById('password').value;

    if (usuario === '' || password === '') {
        e.preventDefault();
        if (usuario === '') {
            marcarErrorLogin('usuario', 'error-usuario', 'Introduce tu usuario.');
        }
        if (password === '') {
            marcarErrorLogin('password', 'error-password', 'Introduce tu contraseña.');
        }
    }
});

function marcarErrorLogin(idInput, idSpan, mensaje) {
    document.getElementById(idInput).classList.add('input-error');
    document.getElementById(idSpan).textContent = mensaje;
}
</script>

<?php include __DIR__ . "/../includes/footer.php"; ?>