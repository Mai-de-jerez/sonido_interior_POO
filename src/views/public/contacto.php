<?php
$titulo = "Sonido Interior | Contacto";
$pagina = "contacto";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/menu.php';

$errores = $_SESSION['errores'] ?? [];
$old = $_SESSION['form_old'] ?? [];
$csrf_token = $data['csrf_token'] ?? '';
unset($_SESSION['errores'], $_SESSION['form_old']);
?>

<main class="contenedor">

    <div class="encabezado-pagina">
        <h2>Contacto</h2>
        <div class="linea-adorno-centro"></div>
        <p>¿Tienes alguna duda sobre nuestros cuencos o necesitas asesoramiento? Escríbenos y te responderemos encantados.</p>
    </div>

    <div class="contacto-grid">
        
        <aside class="tarjeta-beneficio contacto-info">
            <h3>Información de contacto</h3>
            <p>Estamos al otro lado para resolver cualquier pregunta sobre nuestros instrumentos.</p>
            
            <div class="contacto-item">
                <div class="punto-icono">📍</div>
                <div>
                    <h4>Dirección</h4>
                    <p>C/ Armonía, 12 — Barcelona</p>
                </div>
            </div>

            <div class="contacto-item">
                <div class="punto-icono">📞</div>
                <div>
                    <h4>Teléfono</h4>
                    <p>+34 644 123 456</p>
                </div>
            </div>

            <div class="contacto-item">
                <div class="punto-icono">✉️</div>
                <div>
                    <h4>Email</h4>
                    <p>hola@sonidointerior.com</p>
                </div>
            </div>

            <div class="contacto-item">
                <div class="punto-icono">🕒</div>
                <div>
                    <h4>Horario</h4>
                    <p>Lunes a Viernes: 10:00 - 18:00</p>
                </div>
            </div>
        </aside>

        <section class="tarjeta-beneficio contacto-form">
            <form class="formulario-contacto" action="<?php echo BASE_URL; ?>/contacto" method="POST"> 
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
                <div class="campo-form">
                    <label for="nombre">Nombre completo *</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Tu nombre"
                           value="<?= htmlspecialchars($old['nombre'] ?? '') ?>">
                    <span class="mensaje-error" id="error-nombre"><?= isset($errores['nombre']) ? htmlspecialchars($errores['nombre']) : '' ?></span>
                </div>

                <div class="campo-form">
                    <label for="email">Correo electrónico *</label>
                    <input type="email" id="email" name="email" placeholder="tu@email.com"
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                    <span class="mensaje-error" id="error-email"><?= isset($errores['email']) ? htmlspecialchars($errores['email']) : '' ?></span>
                </div>

                <div class="campo-form">
                    <label for="telefono">Teléfono *</label>
                    <input type="tel" id="telefono" name="telefono" placeholder="600 123 456"
                           value="<?= htmlspecialchars($old['telefono'] ?? '') ?>">
                    <span class="mensaje-error" id="error-telefono"><?= isset($errores['telefono']) ? htmlspecialchars($errores['telefono']) : '' ?></span>
                </div>

                <div class="campo-form">
                    <label for="motivo">Asunto *</label>
                    <input type="text" id="motivo" name="motivo" placeholder="¿En qué te podemos ayudar?"
                           value="<?= htmlspecialchars($old['motivo'] ?? '') ?>">
                    <span class="mensaje-error" id="error-motivo"><?= isset($errores['motivo']) ? htmlspecialchars($errores['motivo']) : '' ?></span>
                </div>

                <div class="campo-form">
                    <label for="mensaje">Mensaje *</label>
                    <textarea id="mensaje" name="mensaje" placeholder="Escribe tu mensaje aquí..."><?= htmlspecialchars($old['mensaje'] ?? '') ?></textarea>
                    <span class="mensaje-error" id="error-mensaje"><?= isset($errores['mensaje']) ? htmlspecialchars($errores['mensaje']) : '' ?></span>
                </div>

                <button type="submit" class="boton principal bloque">Enviar mensaje</button>
            </form>
        </section>

    </div>

</main>

<script>
const formContacto = document.querySelector('.formulario-contacto');
if (formContacto) {

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

    function validarEmail() {
        const input = document.getElementById('email');
        const valor = input.value.trim();
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (valor === '') {
            marcarError(input, 'error-email', 'El email es obligatorio.');
            return false;
        }
        if (!regex.test(valor)) {
            marcarError(input, 'error-email', 'Introduce un email válido.');
            return false;
        }
        limpiarError(input, 'error-email');
        return true;
    }

    function validarTelefono() {
        const input = document.getElementById('telefono');
        const valor = input.value.trim();
        const regex = /^(\+34\s?)?[6789]\d{2}\s?\d{3}\s?\d{3}$/;
        if (valor === '') {
            marcarError(input, 'error-telefono', 'El teléfono es obligatorio.');
            return false;
        }
        if (!regex.test(valor)) {
            marcarError(input, 'error-telefono', 'Introduce un teléfono válido (ej: 600 123 456).');
            return false;
        }
        limpiarError(input, 'error-telefono');
        return true;
    }

    function validarAsunto() {
       const input = document.getElementById('motivo');
        const valor = input.value.trim();
        if (valor === '') {
            marcarError(input, 'error-motivo', 'El asunto es obligatorio.');
            return false;
        }
        if (valor.length < 3 || valor.length > 50) {
            marcarError(input, 'error-motivo', 'El asunto debe tener entre 3 y 50 caracteres.');
            return false;
        }
        limpiarError(input, 'error-motivo');
        return true;
    }

    function validarMensaje() {
        const input = document.getElementById('mensaje');
        const valor = input.value.trim();
        if (valor === '') {
            marcarError(input, 'error-mensaje', 'El mensaje es obligatorio.');
            return false;
        }
        if (valor.length < 30 || valor.length > 255) {
            marcarError(input, 'error-mensaje', 'El mensaje debe tener entre 30 y 255 caracteres.');
            return false;
        }
        limpiarError(input, 'error-mensaje');
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
    enganchar('email', validarEmail);
    enganchar('telefono', validarTelefono);
    enganchar('asunto', validarAsunto);
    enganchar('mensaje', validarMensaje);

    formContacto.addEventListener('submit', (e) => {
        const ok1 = validarNombre();
        const ok2 = validarEmail();
        const ok3 = validarTelefono();
        const ok4 = validarAsunto();
        const ok5 = validarMensaje();
        if (!ok1 || !ok2 || !ok3 || !ok4 || !ok5) {
            e.preventDefault();
        }
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>