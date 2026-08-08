<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\interfaces\UsuarioServiceInterface;

class UsuarioController {

    private UsuarioServiceInterface $usuarioService;

    public function __construct(
        UsuarioServiceInterface $usuarioService  
    ) {
        $this->usuarioService = $usuarioService;
    }


    // ============================================================
    // PROCESAR LOGIN
    // ============================================================
    public function procesarLogin(): void {
        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validar campos vacíos
        $errores = [];
        if ($usuario === '') {
            $errores['usuario'] = "Introduce tu usuario.";
        }
        if ($password === '') {
            $errores['password'] = "Introduce tu contraseña.";
        }

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['form_old'] = ['usuario' => $usuario];
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        // Intentar login
        $usuarioData = $this->usuarioService->login($usuario, $password);

        if (!$usuarioData) {
            $_SESSION['mensaje_error'] = "Usuario o contraseña incorrectos.";
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        // Limpiar sesión y regenerar ID
        session_unset();
        session_regenerate_id(true);

        $_SESSION['id_usuario'] = $usuarioData['id_usuario'];
        $_SESSION['usuario'] = $usuarioData['usuario'];
        $_SESSION['rol'] = $usuarioData['rol'];

        // Redirigir según rol
        if ($usuarioData['rol'] === 'ADMIN') {
            header("Location: " . BASE_URL . "/admin/dashboard");
        } else {
            header("Location: " . BASE_URL . "/");
        }
        exit();
    }       

    // ============================================================
    // PROCESAR REGISTRO
    // ============================================================
    public function procesarRegistro(): void {
        $usuario = trim($_POST['usuario'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        $errores = [];

        if ($usuario === '') {
            $errores['usuario'] = "El usuario es obligatorio.";
        } elseif (strlen($usuario) < 3 || strlen($usuario) > 50) {
            $errores['usuario'] = "El usuario debe tener entre 3 y 50 caracteres.";
        }

        if ($email === '') {
            $errores['email'] = "El email es obligatorio.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = "El email no es válido.";
        }

        if ($password === '') {
            $errores['password'] = "La contraseña es obligatoria.";
        } elseif (strlen($password) < 8) {
            $errores['password'] = "La contraseña debe tener al menos 8 caracteres.";
        }

        if ($password !== $passwordConfirm) {
            $errores['password_confirm'] = "Las contraseñas no coinciden.";
        }

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['form_old'] = ['usuario' => $usuario, 'email' => $email];
            header("Location: " . BASE_URL . "/registro");
            exit();
        }

        $registrado = $this->usuarioService->registrar($usuario, $email, $password);

        if (!$registrado) {
            $_SESSION['mensaje_error'] = "El usuario o email ya existe.";
            $_SESSION['form_old'] = ['usuario' => $usuario, 'email' => $email];
            header("Location: " . BASE_URL . "/registro");
            exit();
        }

        $_SESSION['mensaje_exito'] = "Usuario registrado con éxito. Ahora puedes iniciar sesión.";
        header("Location: " . BASE_URL . "/login");
        exit();
    }

    // ============================================================
    // MOSTRAR FORMULARIO DE SOLICITUD DE RECUPERACIÓN
    // ============================================================
    public function mostrarRecuperar(): void {
        require __DIR__ . '/../views/public/recuperar-password.php';
    }

    // ============================================================
    // PROCESAR SOLICITUD DE RECUPERACIÓN
    // ============================================================
    public function procesarRecuperar(): void {
        $email = trim($_POST['email'] ?? '');
        $errores = [];

        if ($email === '') {
            $errores['email'] = "El correo es obligatorio.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = "Introduce un email válido.";
        }

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['form_old'] = ['email' => $email];
            header("Location: " . BASE_URL . "/recuperar-password");
            exit();
        }

        $this->usuarioService->solicitarRecuperacion($email);

        $_SESSION['mensaje_exito'] = "Si el correo introducido está registrado, recibirás las instrucciones en tu bandeja de entrada.";
        header("Location: " . BASE_URL . "/recuperar-password");
        exit();
    }

    // ============================================================
    // MOSTRAR FORMULARIO DE RESTABLECER CONTRASEÑA
    // ============================================================
    public function mostrarRestablecer(): void {
        $token = $_GET['token'] ?? '';

        if ($token === '') {
            header("Location: " . BASE_URL . "/recuperar-password");
            exit();
        }

        $data = ['token' => $token];
        extract($data);
        require __DIR__ . '/../views/public/restablecer-password.php';
    }

    // ============================================================
    // PROCESAR RESTABLECIMIENTO DE CONTRASEÑA
    // ============================================================
    public function procesarRestablecer(): void {
        $token = $_POST['token'] ?? '';

        if ($token === '') {
            header("Location: " . BASE_URL . "/recuperar-password");
            exit();
        }

        $errores = $this->usuarioService->validarNuevaPassword($_POST);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            header("Location: " . BASE_URL . "/restablecer-password?token=" . urlencode($token));
            exit();
        }

        $email = $this->usuarioService->obtenerEmailPorToken($token);

        if (!$email) {
            $_SESSION['mensaje_error'] = "El enlace ha caducado o es inválido. Solicita uno nuevo.";
            header("Location: " . BASE_URL . "/recuperar-password");
            exit();
        }

        $actualizado = $this->usuarioService->actualizarPasswordConToken($email, $_POST['password']);

        if ($actualizado) {
            $_SESSION['mensaje_exito'] = "¡Contraseña cambiada con éxito! Ya puedes acceder.";
            header("Location: " . BASE_URL . "/login");
        } else {
            $_SESSION['mensaje_error'] = "Error al actualizar la contraseña. Inténtalo de nuevo.";
            header("Location: " . BASE_URL . "/restablecer-password?token=" . urlencode($token));
        }
        exit();
    }

    // ============================================================
    // CERRAR SESIÓN
    // ============================================================
    public function logout(): void {
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "/");
        exit();
    }
}