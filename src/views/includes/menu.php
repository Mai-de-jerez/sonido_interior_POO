<?php

use SonidoInteriorPoo\core\Session;

if (!isset($pagina)) {
    $pagina = "";
}

// Leemos las unidades del carrito usando el método estático get()
$cantidadesCarrito = Session::get('cantidades_carrito', 0);

?>

<header class="cabecera">

    <div class="logo">
        <div class="logo-icono">◉</div>
        <div>
            <h1>Sonido Interior</h1>
            <p>Cuencos Tibetanos</p>
        </div>
    </div>

    <nav class="menu">

        <a href="<?php echo BASE_URL; ?>/"
           class="<?php echo ($pagina == 'inicio') ? 'activo' : ''; ?>">
            Inicio
        </a>

        <a href="<?php echo BASE_URL; ?>/catalogo"
           class="<?php echo ($pagina == 'catalogo') ? 'activo' : ''; ?>">
            Catálogo
        </a>

        <a href="<?php echo BASE_URL; ?>/sonoterapia"
           class="<?php echo ($pagina == 'sonoterapia') ? 'activo' : ''; ?>">
            Sonoterapia
        </a>

        <a href="<?php echo BASE_URL; ?>/sobre-nosotros"
           class="<?php echo ($pagina == 'nosotros') ? 'activo' : ''; ?>">
            Sobre nosotros
        </a>

        <a href="<?php echo BASE_URL; ?>/contacto"
           class="<?php echo ($pagina == 'contacto') ? 'activo' : ''; ?>">
            Contacto
        </a>

    </nav>

    <div class="acciones-header">

        <a href="#">🔍</a>

        <?php if (Session::isLoggedIn()): ?>

            <a href="<?php echo BASE_URL; ?>/logout"
               title="Cerrar sesión">
                ⏻
            </a>

            <a href="<?php echo BASE_URL; ?>/carrito"
               title="Mi carrito"
               class="btn-carrito-header">

                🛒

                <?php if ($cantidadesCarrito > 0): ?>
                    <span class="badge-carrito">
                        <?php echo $cantidadesCarrito; ?>
                    </span>
                <?php endif; ?>

            </a>

        <?php else: ?>

            <a href="<?php echo BASE_URL; ?>/login"
               title="Iniciar sesión">
                👤
            </a>

        <?php endif; ?>

    </div>

</header>





