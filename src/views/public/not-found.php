<?php
// ============================================
// VISTA: 404 - PÁGINA NO ENCONTRADA
// ============================================

$titulo = "Página no encontrada | Sonido Interior";
$pagina = "";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/menu.php';
?>

<main class="pagina-error">

    <section class="error-card">

        <div class="error-numero">
            404
        </div>

        <h2>Esta página no existe</h2>

        <p>
            Parece que el camino que buscas no lleva a ningún sitio.
            Quizá la página se haya movido, eliminado o la dirección no sea correcta.
        </p>

        <div class="error-acciones">

            <a href="<?= BASE_URL ?>/" class="boton principal">
                Volver al inicio
            </a>

        </div>

    </section>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>