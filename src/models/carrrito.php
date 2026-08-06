<?php
//==============================================
// ----------CONSULTAS A CARRITO-------------
//==============================================

// Busca el carrito de un usuario. Si no tiene ninguno todavía, se lo crea.
// Siempre devuelve un id_carrito válido.
function obtenerOCrearCarrito(mysqli $conexion, int $idUsuario): int {

    $sql = "SELECT id_carrito FROM carrito WHERE id_usuario = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idUsuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $carrito = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    if ($carrito) {
        return (int) $carrito['id_carrito'];
    }

    // No tenía carrito todavía: se lo creamos
    $sqlInsert = "INSERT INTO carrito (id_usuario) VALUES (?)";
    $stmtInsert = mysqli_prepare($conexion, $sqlInsert);
    mysqli_stmt_bind_param($stmtInsert, "i", $idUsuario);
    mysqli_stmt_execute($stmtInsert);
    $idCarritoNuevo = mysqli_insert_id($conexion);
    mysqli_stmt_close($stmtInsert);

    return (int) $idCarritoNuevo;
}

// Añade un producto al carrito. Si ya estaba, suma la cantidad en vez de duplicar la fila
function agregarProductoAlCarrito(mysqli $conexion, int $idCarrito, int $idProducto, int $cantidad, float $precioUnitario): bool {

    $sql = "INSERT INTO carrito_producto (id_carrito, id_producto, cantidad, precio_unitario)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE cantidad = cantidad + VALUES(cantidad)";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "iiid", $idCarrito, $idProducto, $cantidad, $precioUnitario);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $resultado;
}

// Trae todas las líneas del carrito de un usuario, con los datos del producto ya unidos 
function obtenerProductosCarrito(mysqli $conexion, int $idCarrito): array {

    $sql = "SELECT cp.id_carrito_producto, cp.cantidad, cp.precio_unitario,
                   p.id_producto, p.nombre, p.imagen, p.stock
            FROM carrito_producto cp
            INNER JOIN productos p ON cp.id_producto = p.id_producto
            WHERE cp.id_carrito = ?
            ORDER BY cp.id_carrito_producto DESC";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCarrito);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $productos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $productos[] = $fila;
    }
    mysqli_stmt_close($stmt);

    return $productos;
}

// Cambia la cantidad de una línea concreta del carrito
function actualizarCantidadCarrito(mysqli $conexion, int $idCarritoProducto, int $cantidad): bool {

    $sql = "UPDATE carrito_producto SET cantidad = ? WHERE id_carrito_producto = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $cantidad, $idCarritoProducto);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $resultado;
}

// Quita una línea concreta del carrito
function eliminarProductoDelCarrito(mysqli $conexion, int $idCarritoProducto): bool {

    $sql = "DELETE FROM carrito_producto WHERE id_carrito_producto = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCarritoProducto);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $resultado;
}

// Vacía el carrito entero (se usará tras el checkout, una vez copiado todo a detalle_pedido)
function vaciarCarrito(mysqli $conexion, int $idCarrito): bool {

    $sql = "DELETE FROM carrito_producto WHERE id_carrito = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCarrito);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $resultado;
}

// Cuenta cuántas líneas distintas hay en el carrito (útil para un contador en el icono del menú)
function contarLineasCarrito(mysqli $conexion, int $idCarrito): int {

    $sql = "SELECT COUNT(*) AS total FROM carrito_producto WHERE id_carrito = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCarrito);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    return (int) $fila['total'];
}

// Comprueba que una línea de carrito_producto pertenece de verdad al carrito de ese usuario,
// para que nadie pueda tocar líneas de otro carrito cambiando el id a mano en el formulario
function lineaPerteneceAUsuario(mysqli $conexion, int $idCarritoProducto, int $idUsuario): bool {
 
    $sql = "SELECT cp.id_carrito_producto
            FROM carrito_producto cp
            INNER JOIN carrito c ON cp.id_carrito = c.id_carrito
            WHERE cp.id_carrito_producto = ? AND c.id_usuario = ?";
 
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idCarritoProducto, $idUsuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
 
    return $fila !== null;
}

// Cuenta el número total de unidades sumando las cantidades de todas las líneas del carrito
function contarUnidadesCarrito(mysqli $conexion, int $idCarrito): int {

    $sql = "SELECT COALESCE(SUM(cantidad), 0) AS total FROM carrito_producto WHERE id_carrito = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCarrito);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    return (int) $fila['total'];
}

// Obtiene el stock actual de un producto específico desde la base de datos
function obtenerStockProducto(mysqli $conexion, int $idProducto): int {
    $sql = "SELECT stock FROM productos WHERE id_producto = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProducto);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $producto = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    return $producto ? (int) $producto['stock'] : 0;
}

?>