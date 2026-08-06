<?php
// configuramos los datos para hablar con mi base de datos de mysql
$servidor = getenv('DB_HOST') ?: "127.0.0.1";
$usuario = "root";
$password = getenv('PASSWORD_DB');
$baseDatos = "sonido_interior";

$conexion = mysqli_connect($servidor, $usuario, $password, $baseDatos);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
// Establecemos el conjunto de caracteres a UTF-8 para evitar problemas con acentos y caracteres especiales
// Además asi es como está configurada la base de datos y las tablas, para que no haya problemas de compatibilidad
mysqli_set_charset($conexion, "utf8mb4");
?>