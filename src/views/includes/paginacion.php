
<?php
// Si no hay más de 1 página, no pintamos nada
// isset comprueba que la variable está definida y no es null
if (isset($totalPaginas) && $totalPaginas > 1): 
    
    // Guardamos los filtros que traiga la URL para no perderlos al cambiar de página
    $params = $_GET; 
    $actual = isset($paginaActual) ? (int)$paginaActual : 1; 
    $ruta = $rutaPaginador ?? 'catalogo';
?>
    <nav class="paginador">      
        <!-- Botón ANTERIOR -->
        <?php if ($actual > 1): 
            $params['pag'] = $actual - 1;
        ?>
            <a href="<?php echo $ruta; ?>?<?php echo http_build_query($params); ?>">&laquo; Anterior</a>
        <?php endif; ?>

        <!-- Primera página -->
        <?php $params['pag'] = 1; $activa = ($actual === 1) ? 'class="pagina-activa"' : ''; ?>
        <a href="<?php echo $ruta; ?>?<?php echo http_build_query($params); ?>" <?php echo $activa; ?>>1</a>

        <!-- Puntos si hay hueco entre la primera y la actual -->
        <?php if ($actual > 3): ?>
            <span class="paginador-puntos">…</span>
        <?php endif; ?>

        <!-- Página actual, solo si no es ni la primera ni la última (para no repetirla) -->
        <?php if ($actual !== 1 && $actual !== $totalPaginas): 
            $params['pag'] = $actual;
        ?>
            <a href="<?php echo $ruta; ?>?<?php echo http_build_query($params); ?>" class="pagina-activa"><?php echo $actual; ?></a>
        <?php endif; ?>

        <!-- Puntos si hay hueco entre la actual y la última -->
        <?php if ($actual < $totalPaginas - 2): ?>
            <span class="paginador-puntos">…</span>
        <?php endif; ?>

        <!-- Última página, solo si es distinta de la primera -->
        <?php if ($totalPaginas > 1): 
            $params['pag'] = $totalPaginas;
            $activa = ($actual === $totalPaginas) ? 'class="pagina-activa"' : '';
        ?>
            <a href="<?php echo $ruta; ?>?<?php echo http_build_query($params); ?>" <?php echo $activa; ?>><?php echo $totalPaginas; ?></a>
        <?php endif; ?>

        <!-- Botón SIGUIENTE -->
        <?php if ($actual < $totalPaginas): 
            $params['pag'] = $actual + 1;
        ?>
            <a href="<?php echo $ruta; ?>?<?php echo http_build_query($params); ?>">Siguiente &raquo;</a>
        <?php endif; ?>

    </nav>
<?php endif; ?>