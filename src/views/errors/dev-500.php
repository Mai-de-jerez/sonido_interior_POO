<?php
$mensaje = $mensaje ?? 'Error desconocido';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error (modo desarrollo)</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #f66; padding: 30px; }
        h1 { color: #ff8080; }
        pre { background: #2d2d2d; color: #ddd; padding: 15px; border-radius: 6px; overflow-x: auto; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>⚠ Error no controlado (solo visible en development)</h1>
    <pre><?php echo htmlspecialchars($mensaje); ?></pre>
    <p>Revisa <code>logs/error.log</code> para la traza completa.</p>
</body>
</html>