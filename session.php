<?php
// Cargar la sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inspección de Sesión</title>
    <style>
        body {
            font-family: monospace;
            background-color: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
        }
        .card {
            background-color: #252526;
            border: 1px solid #454545;
            border-radius: 6px;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        h2 {
            margin-top: 0;
            color: #569cd6;
        }
        pre {
            background-color: #1e1e1e;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #333;
            color: #ce9178;
            overflow-x: auto;
        }
        .info {
            color: #b5cea8;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Estado de la Sesión PHP</h2>
        <div class="info"><strong>Session ID:</strong> <?= session_id() ?: 'Ninguna sesión activa' ?></div>
        
        <h3>Contenido de $_SESSION:</h3>
        <?php if (!empty($_SESSION)): ?>
            <pre><?php print_r($_SESSION); ?></pre>
        <?php else: ?>
            <p style="color: #f44747;">La sesión está vacía ($_SESSION está vacío).</p>
        <?php endif; ?>
    </div>
</body>
</html>