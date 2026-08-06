<?php
session_start();
 
// Borra todas las variables de la sesión actual
session_unset();
 
// Destruye la sesión por completo
session_destroy();
 
// Redirigimos al usuario a la página de inicio después de cerrar sesión
header("Location: ../../views/public/index.php");
exit();
?>