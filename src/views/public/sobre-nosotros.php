<?php
// Si necesitas conexión o modelos para esta vista, los requieres aquí arriba
$titulo = "Sonido Interior | Sobre nosotros";
$pagina = "nosotros";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/menu.php';
?>

<main class="contenedor-nosotros">

    <!-- SECCIÓN 1: SOBRE NOSOTROS -->
    <section class="nosotros-hero">
        <div class="nosotros-imagen">
            <img src="public/img/tibet.jpg" alt="Estupa en Nepal">
        </div>
        <div class="nosotros-texto">
            <h2>Sobre nosotros</h2>
            <div class="linea-adorno"></div>
            <p>En <strong>Sonido Interior</strong> creemos en el poder transformador del sonido. Por eso, seleccionamos cuidadosamente cada cuenco tibetano en talleres familiares de Nepal e India, donde la tradición y el respeto por los materiales se transmiten de generación en generación.</p>
            <p>Nuestro propósito es acompañarte en tu camino de bienestar con instrumentos auténticos que elevan tu práctica y tu conexión interior.</p>
        </div>
        <div class="nosotros-puntos">
            <div class="punto-item">
                <span class="punto-icono">⛰️</span>
                <div>
                    <h4>Origen auténtico</h4>
                    <p>Nepal e India</p>
                </div>
            </div>
            <div class="punto-item">
                <span class="punto-icono">🤲</span>
                <div>
                    <h4>Comercio justo</h4>
                    <p>Apoyo a artesanos locales</p>
                </div>
            </div>
            <div class="punto-item">
                <span class="punto-icono">🌿</span>
                <div>
                    <h4>Sostenible</h4>
                    <p>Materiales naturales y duraderos</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN 2: BENEFICIOS -->
    <section class="nosotros-beneficios">
        <h2>Beneficios del sonido</h2>
        <div class="linea-adorno-centro"></div>
        <div class="grid-beneficios">
            <article class="tarjeta-beneficio">
                <div class="icono-beneficio">🧘</div>
                <h3>Meditación profunda</h3>
                <p>Facilita la concentración, calma la mente y te ayuda a conectar con tu esencia.</p>
            </article>
            <article class="tarjeta-beneficio">
                <div class="icono-beneficio">〰️</div>
                <h3>Relajación y alivio del estrés</h3>
                <p>Las vibraciones del cuenco reducen la tensión, promueven la relajación y mejoran el descanso.</p>
            </article>
            <article class="tarjeta-beneficio">
                <div class="icono-beneficio">🪷</div>
                <h3>Equilibrio y bienestar</h3>
                <p>Armoniza tu energía, mejora el bienestar emocional y favorece la salud integral.</p>
            </article>
        </div>
    </section>

    <!-- SECCIÓN 3: BANNER CTA -->
    <section class="banner-cta">
        <div class="banner-contenido">
            <h2>Descubre el cuenco ideal para tu práctica</h2>
            <p>Explora nuestro catálogo y encuentra el sonido que resuena contigo.</p>
        </div>
        <a href="catalogo" class="boton principal cta-btn">Ver catálogo completo &rarr;</a>
    </section>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>