<?php
$titulo = "Sonido Interior | Sonoterapia";
$pagina = "sonoterapia";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/menu.php';
?>

<main class="contenedor">

    <!-- HERO: imagen + texto + puntos destacados -->
    <div class="nosotros-hero">
        <div class="nosotros-imagen">
            <img src="<?php echo BASE_URL; ?>/public/img/cuencos.jpg" alt="Cuenco tibetano usado en sonoterapia">
        </div>

        <div class="nosotros-texto">
            <h2>Sonoterapia</h2>
            <div class="linea-adorno"></div>
            <p>La sonoterapia, o terapia de sonido, es una práctica ancestral originaria de la región del Himalaya que utiliza las vibraciones de los cuencos tibetanos para favorecer estados profundos de relajación.</p>
            <p>Durante una sesión, la persona suele tumbarse cómodamente mientras el terapeuta hace sonar los cuencos alrededor o sobre el cuerpo, dejando que el sonido invite a la mente a desconectar del ruido cotidiano.</p>
        </div>

        <div class="nosotros-puntos">
            <div class="punto-item">
                <div class="punto-icono">🎋</div>
                <div>
                    <h4>Método ancestral</h4>
                    <p>Con siglos de tradición en el Himalaya</p>
                </div>
            </div>
            <div class="punto-item">
                <div class="punto-icono">🧘‍♀️</div>
                <div>
                    <h4>Sesiones guiadas</h4>
                    <p>Acompañadas por profesionales</p>
                </div>
            </div>
            <div class="punto-item">
                <div class="punto-icono">🕊</div>
                <div>
                    <h4>Espacio de calma</h4>
                    <p>Un paréntesis en tu rutina diaria</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CÓMO ES UNA SESIÓN -->
    <section class="nosotros-beneficios">
        <h2>¿Cómo es una sesión?</h2>
        <div class="linea-adorno-centro"></div>

        <div class="grid-beneficios">
            <div class="tarjeta-beneficio">
                <div class="icono-beneficio">🌿</div>
                <h3>1. Preparación</h3>
                <p>Te acomodas en un espacio tranquilo, en una posición relajada, y cierras los ojos.</p>
            </div>
            <div class="tarjeta-beneficio">
                <div class="icono-beneficio">🔔</div>
                <h3>2. Inmersión sonora</h3>
                <p>El terapeuta hace sonar los cuencos alrededor de tu cuerpo, dejando que las vibraciones te envuelvan.</p>
            </div>
            <div class="tarjeta-beneficio">
                <div class="icono-beneficio">🌤</div>
                <h3>3. Integración</h3>
                <p>Un momento final de silencio para asentar la sensación de calma antes de volver a tu día.</p>
            </div>
        </div>
    </section>

    <!-- BENEFICIOS -->
    <section class="nosotros-beneficios">
        <h2>Beneficios</h2>
        <div class="linea-adorno-centro"></div>

        <div class="grid-beneficios">
            <div class="tarjeta-beneficio">
                <div class="icono-beneficio">🧘</div>
                <h3>Bienestar físico</h3>
                <p>Ayuda a liberar tensión muscular y favorece un estado general de relajación corporal.</p>
            </div>
            <div class="tarjeta-beneficio">
                <div class="icono-beneficio">🌙</div>
                <h3>Calma mental</h3>
                <p>Muchas personas lo usan como apoyo para reducir el estrés, la ansiedad y mejorar la concentración.</p>
            </div>
            <div class="tarjeta-beneficio">
                <div class="icono-beneficio">☯</div>
                <h3>Conexión interior</h3>
                <p>Un espacio de pausa y silencio, alejado del ritmo diario, para reconectar contigo misma.</p>
            </div>
        </div>
    </section>

    <!-- DATOS RÁPIDOS, reaprovechando el grid de características de la ficha de producto -->
    <section class="detalle-producto-info">
        <h3>Un poco de historia</h3>
        <p class="descripcion">Los cuencos tibetanos, también llamados cuencos cantores, tienen su origen en la región del Himalaya, donde se han usado durante siglos en rituales y prácticas de meditación de las tradiciones budista y Bön. Aunque circula la creencia popular de que están hechos con una aleación de siete metales, no hay una base demostrada para ese dato — lo que sí es real es la larga tradición artesanal detrás de cada pieza.</p>

        <ul class="detalle-producto-caracteristicas">
            <li><strong>Origen</strong>Región del Himalaya (Tíbet, Nepal, India)</li>
            <li><strong>Tradición</strong>Budista y Bön</li>
            <li><strong>Uso habitual</strong>Meditación, relajación y rituales</li>
            <li><strong>Duración de una sesión</strong>Entre 30 y 60 minutos aprox.</li>
        </ul>
    </section>

    <!-- PARA QUIÉN -->
    <div class="nosotros-texto">
        <p><strong>¿Para quién está recomendada?</strong> La sonoterapia puede interesar a cualquier persona que atraviese periodos de estrés, tenga dificultades para conciliar el sueño, o simplemente busque un momento de calma en su rutina. También es habitual entre quienes practican meditación y quieren incorporar el sonido como apoyo a su práctica. No sustituye a ningún tratamiento médico: es una herramienta complementaria de bienestar.</p>
    </div>

    <!-- CTA -->
    <div class="banner-cta">
        <div class="banner-contenido">
            <h2>¿Tienes dudas o quieres más información?</h2>
            <p>Escríbenos y te contamos más sobre nuestros cuencos y cómo elegir el que mejor se adapte a lo que buscas.</p>
        </div>
        <a href="contacto" class="boton principal cta-btn">Contactar</a>
    </div>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>