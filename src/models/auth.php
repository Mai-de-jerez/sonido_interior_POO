<?php
//==============================================================================
// ----------CONSULTAS PARA AUTENTICACION Y RECUPERACIÓN DE PASSWORD------------
//==============================================================================

// Guarda el token generado con un tiempo de expiración (p. ej. 30 min)
function guardarTokenRecuperacion(mysqli $conexion, string $email, string $token): bool {
    // Borramos solicitudes anteriores del mismo email para no acumular basura
    $sqlDelete = "DELETE FROM password_resets WHERE email = ?";
    $stmtDelete = mysqli_prepare($conexion, $sqlDelete);
    mysqli_stmt_bind_param($stmtDelete, "s", $email);
    mysqli_stmt_execute($stmtDelete);
    mysqli_stmt_close($stmtDelete);

    // Insertamos el nuevo token con validez de 30 minutos
    $sql = "INSERT INTO password_resets (email, token, expira) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $email, $token);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $resultado;
}

// Comprueba que el token existe y NO ha caducado
function obtenerEmailPorToken(mysqli $conexion, string $token): ?string {
    $sql = "SELECT email FROM password_resets WHERE token = ? AND expira > NOW() LIMIT 1";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultado)) {
        mysqli_stmt_close($stmt);
        return $row['email'];
    }

    mysqli_stmt_close($stmt);
    return null;
}

// Actualiza la contraseña en la BD y borra el token usado
function actualizarPasswordYBorrarToken(mysqli $conexion, string $email, string $nuevaPasswordHash): bool {
    // Iniciar transacción
    mysqli_begin_transaction($conexion);

    try {
        // Actualizamos en la tabla de usuarios
        $sqlUser = "UPDATE usuarios SET password = ? WHERE email = ?";
        $stmtUser = mysqli_prepare($conexion, $sqlUser);
        mysqli_stmt_bind_param($stmtUser, "ss", $nuevaPasswordHash, $email);
        mysqli_stmt_execute($stmtUser);
        mysqli_stmt_close($stmtUser);

        // Borramos el token usado
        $sqlToken = "DELETE FROM password_resets WHERE email = ?";
        $stmtToken = mysqli_prepare($conexion, $sqlToken);
        mysqli_stmt_bind_param($stmtToken, "s", $email);
        mysqli_stmt_execute($stmtToken);
        mysqli_stmt_close($stmtToken);

        mysqli_commit($conexion);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conexion);
        return false;
    }
}