<?php
require_once __DIR__ . '/../../includes/seguridad.php';
require_once __DIR__ . '/../../models/carrito.php';
require_once __DIR__ . '/../../models/productos.php';
require_once __DIR__ . '/../../includes/conexion.php';

$idProducto = (isset($_POST['id_producto']) && ctype_digit($_POST['id_producto'])) ? (int) $_POST['id_producto'] : null;
$cantidadAAnadir = (isset($_POST['cantidad']) && ctype_digit($_POST['cantidad'])) ? (int) $_POST['cantidad'] : 1;

if ($idProducto === null) {
    mysqli_close($conexion);
    header("Location: ../../views/public/productos/catalogo.php");
    exit();
}

$producto = obtenerProductoPorId($conexion, $idProducto);

if (!$producto) {
    mysqli_close($conexion);
    header("Location: ../../views/public/productos/catalogo.php?status=error");
    exit();
}

$stockReal = (int) $producto['stock'];
$idCarrito = obtenerOCrearCarrito($conexion, $_SESSION['id_usuario']);

// Consultar la única fuente de la verdad: la tabla carrito_prodcuto
$lineasCarrito = obtenerProductosCarrito($conexion, $idCarrito);
$cantidadEnCarrito = 0;

foreach ($lineasCarrito as $linea) {
    if ((int)$linea['id_producto'] === $idProducto) {
        $cantidadEnCarrito = (int)$linea['cantidad'];
        break;
    }
}

// Sumar lo que ya tiene más lo que pretende añadir para saber si supera el stock
$cantidadTotalFutura = $cantidadEnCarrito + $cantidadAAnadir;

// Comprobar contra el stock real de la tabla productos
if ($cantidadTotalFutura > $stockReal) {
    mysqli_close($conexion);
    $_SESSION['mensaje_error'] = "No puedes añadir tantas unidades. Stock disponible: $stockReal (Ya tienes $cantidadEnCarrito en el carrito).";
    $origen = $_SERVER['HTTP_REFERER'] ?? '../../views/public/productos/catalogo.php';
    header("Location: " . $origen);
    exit();
}

// solo entonces añadimos el producto al carrito
agregarProductoAlCarrito($conexion, $idCarrito, $idProducto, $cantidadAAnadir, $producto['precio']);
mysqli_close($conexion);

// Va a la sesión inmediatamente
$_SESSION['cantidades_carrito'] = ($_SESSION['cantidades_carrito'] ?? 0) + $cantidadAAnadir;

// Guardamos el aviso para la vista
$_SESSION['mensaje_exito'] = "¡Producto añadido al carrito correctamente!";

$origen = $_SERVER['HTTP_REFERER'] ?? '../../views/public/productos/catalogo.php';
header("Location: " . $origen);
exit();
?>