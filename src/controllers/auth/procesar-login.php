<?php
session_start();
require_once __DIR__ . '/../../models/usuarios.php';
require_once __DIR__ . '/../../models/carrito.php';
require_once __DIR__ . '/../../includes/conexion.php';

$usuario = trim($_POST["usuario"] ?? '');
$password = $_POST["password"] ?? '';

// --- Campos vacíos: comprobamos antes de tocar la BD ---
if ($usuario === '' || $password === '') {
    mysqli_close($conexion);
    $errores = [];
    if ($usuario === '') {
        $errores['usuario'] = "Introduce tu usuario.";
    }
    if ($password === '') {
        $errores['password'] = "Introduce tu contraseña.";
    }
    $_SESSION['errores'] = $errores;
    $_SESSION['form_old'] = ['usuario' => $usuario];
    header("Location: ../../views/public/login.php");
    exit();
}

// usamos mi consulta para obtener el usuario por su nombre de usuario
$usuarioEncontrado = obtenerUsuarioPorUsername($conexion, $usuario);

// usamos password_verify para comparar la contraseña ingresada con la almacenada en la base de datos
if ($usuarioEncontrado && password_verify($password, $usuarioEncontrado["password"])) {

    // Antes de guardar sus cosas limpiamos la sesión para evitar que se mezclen datos de sesiones anteriores
    session_unset();
    session_regenerate_id(true); // Regeneramos el ID de sesión para evitar el ataque de fijación de sesión

    $_SESSION["id_usuario"] = $usuarioEncontrado["id_usuario"];
    $_SESSION["usuario"] = $usuarioEncontrado["usuario"];
    $_SESSION["rol"] = $usuarioEncontrado["rol"];

    // habrá que saber cuantos articulos tiene el carrito si los tiene para mostrar el número en el icono del carrito
    $idCarrito = obtenerOCrearCarrito($conexion, $usuarioEncontrado["id_usuario"]);
    $_SESSION['cantidades_carrito'] = contarUnidadesCarrito($conexion, $idCarrito);
    // Cerramos la conexión a la base de datos porque me da mal rollo tenerla abierta.
    mysqli_close($conexion);

    // Según el rol, mandamos a un sitio o a otro
    if ($usuarioEncontrado["rol"] === "ADMIN") {
        header("Location: ../../views/admin/dashboard.php");
    } else {
        header("Location: ../../views/public/index.php");
    }
    exit();
 
} else {
    mysqli_close($conexion);
    $_SESSION['mensaje_error'] = "Usuario o contraseña incorrectos.";
    header("Location: ../../views/public/login.php");
    exit();
}