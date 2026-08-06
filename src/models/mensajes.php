<?php
//==============================================
// ----------CONSULTAS A MENSAJES-------------
//==============================================
 
// Guarda un mensaje de contacto en la base de datos.
function guardarMensaje(mysqli $conexion, string $nombre, string $email, ?string $telefono, ?string $motivo, string $mensaje): bool {

    $sql = "INSERT INTO mensajes (nombre, email, telefono, motivo, mensaje) VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $nombre, $email, $telefono, $motivo, $mensaje);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $resultado;
}
?>