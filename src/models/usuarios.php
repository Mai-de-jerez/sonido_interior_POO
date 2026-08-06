<?php
//================================================
// ----------CONSULTAS A USUARIOS-------------
//================================================

function obtenerUsuarioPorUsername(mysqli $conexion, string $usuario): ?array {
    $sql = "SELECT id_usuario, usuario, password, rol FROM usuarios WHERE usuario = ?";
    
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);
    
    $resultado = mysqli_stmt_get_result($stmt);
    $usuarioEncontrado = mysqli_fetch_assoc($resultado);
    
    mysqli_stmt_close($stmt);
    
    return $usuarioEncontrado; 
}

function obtenerUsuarioPorEmail(mysqli $conexion, string $email): ?array {
    $sql = "SELECT id_usuario, usuario, email, password, rol FROM usuarios WHERE email = ?";
    
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    
    $resultado = mysqli_stmt_get_result($stmt);
    $usuarioEncontrado = mysqli_fetch_assoc($resultado);
    
    mysqli_stmt_close($stmt);
    
    return $usuarioEncontrado; 
}

function registroUsuario(mysqli $conexion, string $usuario, string $email, string $passwordHash): bool {
    // Creamos usuarios solo con rol CLIENTE y sin nombre ya que el nombre se puede agregar después en el perfil del usuario
    $sql = "INSERT INTO usuarios (usuario, email, password) VALUES (?, ?, ?)";
 
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $usuario, $email, $passwordHash);
    $resultado = mysqli_stmt_execute($stmt);
 
    mysqli_stmt_close($stmt);
 
    return $resultado;
}

?>