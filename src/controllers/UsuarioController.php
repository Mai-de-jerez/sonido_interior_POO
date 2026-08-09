<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\interfaces\UsuarioServiceInterface;
use SonidoInteriorPoo\validators\UsuarioValidator;

class UsuarioController {

    private UsuarioServiceInterface $usuarioService;
    private UsuarioValidator $usuarioValidator;

    public function __construct(UsuarioServiceInterface $usuarioService, UsuarioValidator $usuarioValidator) {
        $this->usuarioService = $usuarioService;
        $this->usuarioValidator = $usuarioValidator;
    }

    // ============================================================
    // PROCESAR LOGIN
    // ============================================================ 
    public function procesarLogin(): void {
        $errores = $this->usuarioValidator->validarLogin($_POST);  

        if (!empty($errores)) {   
            $_SESSION['errores'] = $errores;
            $_SESSION['form_old'] = ['usuario' => $_POST['usuario'] ?? ''];
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        $usuarioData = $this->usuarioService->login($_POST['usuario'], $_POST['password']);

        if (!$usuarioData) {
            $_SESSION['mensaje_error'] = "Usuario o contraseña incorrectos.";
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        session_unset();
        session_regenerate_id(true);

        $_SESSION['id_usuario']         = $usuarioData['id_usuario'];
        $_SESSION['usuario']            = $usuarioData['usuario'];
        $_SESSION['rol']                = $usuarioData['rol'];
        $_SESSION['cantidades_carrito'] = $usuarioData['cantidades_carrito'];

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
        $errores = $this->usuarioValidator->validarRegistro($_POST);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['form_old'] = [
                'usuario' => $_POST['usuario'] ?? '',
                'email' => $_POST['email'] ?? ''
            ];
            header("Location: " . BASE_URL . "/registro");
            exit();
        }

        $registrado = $this->usuarioService->registrar(
            trim($_POST['usuario']),
            trim($_POST['email']),
            $_POST['password']
        );

        if (!$registrado) {
            $_SESSION['mensaje_error'] = "El usuario o email ya existe.";
            $_SESSION['form_old'] = [
                'usuario' => $_POST['usuario'] ?? '',
                'email' => $_POST['email'] ?? ''
            ];
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
        $errores = $this->usuarioValidator->validarRecuperacion($_POST);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['form_old'] = ['email' => $_POST['email'] ?? ''];
            header("Location: " . BASE_URL . "/recuperar-password");
            exit();
        }

        $this->usuarioService->solicitarRecuperacion(trim($_POST['email']));

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

        $errores = $this->usuarioValidator->validarNuevaPassword($_POST);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            header("Location: " . BASE_URL . "/restablecer-password?token=" . urlencode($token));
            exit();
        }

        $actualizado = $this->usuarioService->actualizarPasswordPorToken($token, $_POST['password']);

        if ($actualizado) { 
            $_SESSION['mensaje_exito'] = "¡Contraseña cambiada con éxito! Ya puedes acceder.";
            header("Location: " . BASE_URL . "/login");
        } else {
            $_SESSION['mensaje_error'] = "El enlace ha caducado o es inválido. Solicita uno nuevo.";
            header("Location: " . BASE_URL . "/recuperar-password");
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