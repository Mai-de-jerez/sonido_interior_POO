<?php
require_once __DIR__ . '/../../includes/seguridad.php';
require_once __DIR__ . '/../../models/carrito.php';
require_once __DIR__ . '/../../includes/conexion.php';

$idCarritoProducto = (isset($_POST['id_carrito_producto']) && ctype_digit($_POST['id_carrito_producto'])) ? (int) $_POST['id_carrito_producto'] : null;

if ($idCarritoProducto === null) {
    mysqli_close($conexion);
    header("Location: ../../views/public/carrito.php");
    exit();
}

// Comprobamos que esa línea es de verdad del carrito del usuario logueado
if (!lineaPerteneceAUsuario($conexion, $idCarritoProducto, $_SESSION['id_usuario'])) {
    mysqli_close($conexion);
    header("Location: ../../views/public/carrito.php?status=error");
    exit();
}

// Buscamos cuántas unidades tiene esta fila concreta antes de eliminarla
$idCarrito = obtenerOCrearCarrito($conexion, $_SESSION['id_usuario']);
$lineas = obtenerProductosCarrito($conexion, $idCarrito);

$cantidadAEliminar = 0;
foreach ($lineas as $linea) {
    if ((int)$linea['id_carrito_producto'] === $idCarritoProducto) {
        $cantidadAEliminar = (int)$linea['cantidad'];
        break;
    }
}

// Borramos la fila de la base de datos
eliminarProductoDelCarrito($conexion, $idCarritoProducto);
mysqli_close($conexion);

// Restamos esa cantidad de la sesión para el número del carrito que se muestra en la cabecera
if ($cantidadAEliminar > 0) {
    $_SESSION['cantidades_carrito'] = max(0, ($_SESSION['cantidades_carrito'] ?? 0) - $cantidadAEliminar);
}

header("Location: ../../views/public/carrito.php");
exit();
?>