<?php
//==============================================
// ----- CONSULTAS DE LECTURA A CATEGORIAS -----
//==============================================

// Función para obtener una categoría por su ID
function obtenerCategoriaPorId(mysqli $conexion, int $id_categoria): ?array {
    $sql = "SELECT id_categoria, nombre, descripcion FROM categorias WHERE id_categoria = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_categoria);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    $categoria = null;
    if ($fila = mysqli_fetch_assoc($resultado)) {
        $categoria = $fila;
    }
    
    mysqli_stmt_close($stmt);
    return $categoria;
}

// Función para obtener las categorías activas desde la base de datos
function obtenerCategoriasActivas(mysqli $conexion) {
    $sql = "SELECT id_categoria, nombre FROM categorias WHERE activo = 1";
    $resultado = mysqli_query($conexion, $sql);
    
    $categorias = [];
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $categorias[] = $fila;
        }
        mysqli_free_result($resultado);
    }
    
    return $categorias;
}

// Función para obtener TODAS las categorías (activas e inactivas) para el listado de administración
function obtenerCategoriasAdmin(mysqli $conexion) {
    $sql = "SELECT id_categoria, nombre, descripcion, activo FROM categorias ORDER BY id_categoria DESC";
    $resultado = mysqli_query($conexion, $sql);

    $categorias = [];
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $categorias[] = $fila;
        }
        mysqli_free_result($resultado);
    }

    return $categorias;
}

//==============================================
// ----- CONSULTAS DE ESCRITURA A CATEGORIAS -----
//==============================================

// Función para crear una nueva categoría
function crearCategoria(mysqli $conexion, string $nombre, ?string $descripcion): bool {
    $sql = "INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)";
 
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $nombre, $descripcion);
    $resultado = mysqli_stmt_execute($stmt);
 
    mysqli_stmt_close($stmt);
 
    return $resultado;
}

// Función para actualizar una categoría existente
function actualizarCategoria(mysqli $conexion, int $id_categoria, string $nombre, ?string $descripcion): bool {
    $sql = "UPDATE categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $nombre, $descripcion, $id_categoria);
    $resultado = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    return $resultado;
}

// Función para hacer un borrado LÓGICO de una categoría:
// solo la marca como inactiva para que deje de aparecer en la parte pública
function eliminarCategoriaLogica(mysqli $conexion, int $idCategoria): bool {
    $sql = "UPDATE categorias SET activo = 0 WHERE id_categoria = ?";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCategoria);
    $resultado = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $resultado;
}

// Función para reactivar una categoría que estaba desactivada (proceso inverso al borrado lógico)
function reactivarCategoria(mysqli $conexion, int $idCategoria): bool {
    $sql = "UPDATE categorias SET activo = 1 WHERE id_categoria = ?";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCategoria);
    $resultado = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $resultado;
}
?>
