<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de seguridad aplicada ANTES de iniciar sesión
session_set_cookie_params([
    'lifetime' => 0,                            // Expira al cerrar el navegador
    'path'     => '/',                          // Disponible en todo el dominio
    'domain'   => '',                           // Dominio actual
    'secure'   => isset($_SERVER['HTTPS']),     // true si hay certificado SSL/HTTPS activo
    'httponly' => true,                         // Impide acceso desde JS (Protección contra XSS)
    'samesite' => 'Lax'                         // Protección contra CSRF
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Contador de recargas para probar la persistencia entre peticiones
$_SESSION['debug_counter'] = ($_SESSION['debug_counter'] ?? 0) + 1;

// 2. Extracción de cookies de sesión para validar directivas de seguridad
$sessionCookieName = session_name();
$sessionCookieParams = session_get_cookie_params();
$hasSessionCookie = isset($_COOKIE[$sessionCookieName]);

// 3. Comprobación rápida de autenticación específica de tu app
$isLoggedIn = isset($_SESSION['id_usuario']) || isset($_SESSION['usuario']);

// 4. Cálculo del número total de artículos en el carrito
$totalArticulosCarrito = 0;
if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $totalArticulosCarrito += $item['cantidad'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inspección y Diagnóstico de Sesión</title>
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
            max-width: 900px;
            margin: 0 auto;
        }
        h2, h3 {
            margin-top: 20px;
            color: #569cd6;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
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
            margin-bottom: 8px;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        .badge-success { background-color: #0e639c; color: #fff; }
        .badge-danger { background-color: #f44747; color: #fff; }
        .badge-warning { background-color: #cca700; color: #000; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #333;
        }
        th { color: #4ec9b0; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Inspección de Sesión PHP</h2>

        <!-- Estado de Autenticación General -->
        <div class="info">
            <strong>Estado del Login:</strong>
            <?php if ($isLoggedIn): ?>
                <span class="badge badge-success">SESIÓN INICIADA</span>
            <?php else: ?>
                <span class="badge badge-danger">USUARIO NO AUTENTICADO</span>
            <?php endif; ?>
        </div>

        <div class="info">
            <strong>Session ID:</strong> <?= session_id() ?: 'Ninguna sesión activa' ?>
        </div>
        
        <div class="info">
            <strong>Contador de Persistencia (Recargas):</strong> 
            <span class="badge badge-success"><?= $_SESSION['debug_counter'] ?></span>
            <small style="color: #888;">(Si recargas la página y este número sube, la sesión persiste correctamente)</small>
        </div>

        <!-- Información del Carrito de Juana -->
        <div class="info">
            <strong>Artículos en el Carrito:</strong> 
            <span class="badge badge-warning"><?= $totalArticulosCarrito ?></span>
        </div>

        <!-- 1. Configuración y Seguridad de la Cookie -->
        <h3>Configuración de Seguridad de la Cookie</h3>
        <table>
            <tr>
                <th>Propiedad</th>
                <th>Valor Actual</th>
                <th>Recomendado</th>
            </tr>
            <tr>
                <td>Nombre de Cookie</td>
                <td><?= session_name() ?></td>
                <td>PHPSESSID (o personalizado)</td>
            </tr>
            <tr>
                <td>Cookie Recibida del Navegador</td>
                <td><?= $hasSessionCookie ? 'Sí (' . htmlspecialchars($_COOKIE[$sessionCookieName]) . ')' : 'No' ?></td>
                <td>Sí</td>
            </tr>
            <tr>
                <td>HttpOnly</td>
                <td><?= $sessionCookieParams['httponly'] ? '✅ true' : '❌ false (Riesgo XSS)' ?></td>
                <td>true</td>
            </tr>
            <tr>
                <td>Secure (HTTPS)</td>
                <td><?= $sessionCookieParams['secure'] ? '✅ true' : '⚠️ false (Solo OK en HTTP local)' ?></td>
                <td>true (en Producción)</td>
            </tr>
            <tr>
                <td>SameSite</td>
                <td><?= !empty($sessionCookieParams['samesite']) ? $sessionCookieParams['samesite'] : 'Sin definir' ?></td>
                <td>Lax / Strict</td>
            </tr>
            <tr>
                <td>Lifetime / Expiración</td>
                <td><?= $sessionCookieParams['lifetime'] === 0 ? 'Al cerrar el navegador (0)' : $sessionCookieParams['lifetime'] . ' seg' ?></td>
                <td>Depende del sistema</td>
            </tr>
        </table>

        <!-- 2. Información del Servidor/Entorno -->
        <h3>Información del Servidor</h3>
        <div class="info"><strong>Save Path (Dónde se guardan los archivos de sesión):</strong> <?= session_save_path() ?: 'Defecto del sistema' ?></div>
        <div class="info"><strong>¿Es escribible la ruta de sesiones?:</strong> 
            <?= is_writable(session_save_path() ?: sys_get_temp_dir()) ? '✅ Sí' : '❌ No (Error de permisos)' ?>
        </div>

        <!-- 3. Contenido Completo del Array $_SESSION -->
        <h3>Contenido Completo de $_SESSION:</h3>
        <?php if (!empty($_SESSION)): ?>
            <pre><?php print_r($_SESSION); ?></pre>
        <?php else: ?>
            <p style="color: #f44747;">La sesión está vacía ($_SESSION está vacío).</p>
        <?php endif; ?>

        <!-- 4. Contenido del Array $_COOKIE -->
        <h3>Cookies Recibidas ($_COOKIE):</h3>
        <pre><?php print_r($_COOKIE); ?></pre>
    </div>
</body>
</html>