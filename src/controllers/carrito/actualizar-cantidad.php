<?php
require_once __DIR__ . '/../../includes/seguridad.php';
require_once __DIR__ . '/../../models/carrito.php';
require_once __DIR__ . '/../../includes/conexion.php';

$idCarritoProducto = (isset($_POST['id_carrito_producto']) && ctype_digit($_POST['id_carrito_producto'])) ? (int) $_POST['id_carrito_producto'] : null;
$accion = $_POST['accion'] ?? null;

// Validar que tengamos un ID válido y la acción sea 'sumar' o 'restar'
if ($idCarritoProducto === null || !in_array($accion, ['sumar', 'restar'], true)) {
    mysqli_close($conexion);
    header("Location: ../../views/public/carrito.php");
    exit();
}

// Seguridad: Comprobar pertenencia al usuario
if (!lineaPerteneceAUsuario($conexion, $idCarritoProducto, $_SESSION['id_usuario'])) {
    mysqli_close($conexion);
    header("Location: ../../views/public/carrito.php?status=error");
    exit();
}

// Buscar la línea actual para obtener cantidad y el id_producto asociado
$idCarrito = obtenerOCrearCarrito($conexion, $_SESSION['id_usuario']);
$lineas = obtenerProductosCarrito($conexion, $idCarrito);

$lineaEncontrada = null;
foreach ($lineas as $linea) {
    if ((int)$linea['id_carrito_producto'] === $idCarritoProducto) {
        $lineaEncontrada = $linea;
        break;
    }
}

if (!$lineaEncontrada) {
    mysqli_close($conexion);
    header("Location: ../../views/public/carrito.php");
    exit();
}

$cantidadActual = (int)$lineaEncontrada['cantidad'];
$idProducto = (int)$lineaEncontrada['id_producto'];

// Calcular el nuevo valor, validando stock real mediante el modelo si se trata de sumar
if ($accion === 'sumar') {
    $stockReal = obtenerStockProducto($conexion, $idProducto);
    $nuevaCantidad = $cantidadActual + 1;

    // Solo actualizamos si la nueva cantidad no supera el stock real
    if ($nuevaCantidad <= $stockReal) {
        actualizarCantidadCarrito($conexion, $idCarritoProducto, $nuevaCantidad);
        $_SESSION['cantidades_carrito'] = ($_SESSION['cantidades_carrito'] ?? 0) + 1;
    } else {
        $_SESSION['mensaje_error'] = "No hay más stock disponible para este producto.";
    }

// Si la acción es restar, solo permitimos restar si la cantidad actual es mayor a 1
} elseif ($accion === 'restar' && $cantidadActual > 1) {
    $nuevaCantidad = $cantidadActual - 1;
    actualizarCantidadCarrito($conexion, $idCarritoProducto, $nuevaCantidad);
    $_SESSION['cantidades_carrito'] = max(0, ($_SESSION['cantidades_carrito'] ?? 0) - 1);
}

mysqli_close($conexion);
header("Location: ../../views/public/carrito.php");
exit();
?>