<?php
//==============================================
// ----------CONSULTAS DE CHECKOUT Y PEDIDOS----
//==============================================

/**
 * Consulta y bloquea la fila del producto (FOR UPDATE) dentro de una transacción.
 * Devuelve un array asociativo con el stock y el nombre, o null si no existe.
 */
function obtenerStockProductoParaUpdate(mysqli $conexion, int $idProducto): ?array {
    $sql = "SELECT stock, nombre FROM productos WHERE id_producto = ? FOR UPDATE";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProducto);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $producto = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    return $producto ?: null;
}

/**
 * Registra la cabecera del pedido en la tabla `pedidos`.
 * Devuelve el id_pedido recién insertado.
 */
function crearPedido(mysqli $conexion, int $idUsuario, float $total, string $direccionEnvio): int {
    $sql = "INSERT INTO pedidos (id_usuario, estado, total, direccion_envio) 
            VALUES (?, 'PAGADO', ?, ?)";
    
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ids", $idUsuario, $total, $direccionEnvio);
    mysqli_stmt_execute($stmt);
    $idPedido = mysqli_insert_id($conexion);
    mysqli_stmt_close($stmt);

    return (int) $idPedido;
} 

/**
 * Registra una línea individual de producto comprada en `detalle_pedido`.
 */
function crearDetallePedido(mysqli $conexion, int $idPedido, int $idProducto, int $cantidad, float $precioUnitario): bool {
    $subtotal = $cantidad * $precioUnitario;
    $sql = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario, subtotal) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "iiidd", $idPedido, $idProducto, $cantidad, $precioUnitario, $subtotal);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $resultado;
}

/**
 * Resta las unidades compradas del stock del producto y, si se agota,
 * lo desactiva automáticamente para que desaparezca del catálogo.
 */
function descontarStockProducto(mysqli $conexion, int $idProducto, int $cantidad): bool {
    $sql = "UPDATE productos 
            SET stock = stock - ?, 
                activo = CASE WHEN stock - ? <= 0 THEN 0 ELSE activo END 
            WHERE id_producto = ?";
    
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $cantidad, $cantidad, $idProducto);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $resultado;
}