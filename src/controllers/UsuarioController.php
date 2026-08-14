<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;
use SonidoInteriorPoo\interfaces\UsuarioServiceInterface;
use SonidoInteriorPoo\interfaces\PasswordResetServiceInterface;
use SonidoInteriorPoo\validators\UsuarioValidator;
use SonidoInteriorPoo\core\Session;

class UsuarioController extends Controller {
    private UsuarioServiceInterface $usuarioService;
    private PasswordResetServiceInterface $passwordResetService;
    private UsuarioValidator $usuarioValidator;

    public function __construct(
        UsuarioServiceInterface $usuarioService,
        PasswordResetServiceInterface $passwordResetService,
        UsuarioValidator $usuarioValidator
    ) {
        $this->usuarioService = $usuarioService;
        $this->passwordResetService = $passwordResetService;
        $this->usuarioValidator = $usuarioValidator;
    }

    // ============================================================
    // PROCESAR LOGIN
    // ============================================================
    public function procesarLogin(): void {

        if (!$this->validarCsrf()) {
            $this->setFlash('mensaje_error', 'Token de seguridad inválido. Por favor, recarga la página e inténtalo de nuevo.');
            $this->redirigir('login');
            return;
        }

        $errores = $this->usuarioValidator->validarLogin($_POST);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->setSession('form_old', ['usuario' => $_POST['usuario'] ?? '']);
            $this->redirigir('login');
        }

        $usuarioData = $this->usuarioService->login($_POST['usuario'], $_POST['password']);

        if (!$usuarioData) {
            $this->setFlash('mensaje_error', 'Usuario o contraseña incorrectos.');
            $this->redirigir('login');
        }

        // Limpiar datos anteriores de sesión y regenerar el ID
        Session::clear();
        Session::regenerate(); 

        $this->setSession('id_usuario', $usuarioData['id_usuario']);
        $this->setSession('usuario', $usuarioData['usuario']);
        $this->setSession('rol', $usuarioData['rol']);
        $this->setSession('cantidades_carrito', $usuarioData['cantidades_carrito']);

        if ($usuarioData['rol'] === 'ADMIN') {
            $this->redirigir('admin/dashboard');
        } else {
            $this->redirigir('');
        }
    }

    // ============================================================
    // PROCESAR REGISTRO
    // ============================================================
    public function procesarRegistro(): void {

        if (!$this->validarCsrf()) {
            $this->setFlash('mensaje_error', 'Token de seguridad inválido. Por favor, recarga la página e inténtalo de nuevo.');
            $this->redirigir('registro');
            return;
        }

        $errores = $this->usuarioValidator->validarRegistro($_POST);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->setSession('form_old', [
                'usuario' => $_POST['usuario'] ?? '',
                'email' => $_POST['email'] ?? ''
            ]);
            $this->redirigir('registro');
        }

        $registrado = $this->usuarioService->registrar(
            trim($_POST['usuario']),
            trim($_POST['email']),
            $_POST['password']
        );

        if (!$registrado) {
            $this->setFlash('mensaje_error', 'El usuario o email ya existe.');
            $this->setSession('form_old', [
                'usuario' => $_POST['usuario'] ?? '',
                'email' => $_POST['email'] ?? ''
            ]);
            $this->redirigir('registro');
        }

        $this->setFlash('mensaje_exito', 'Usuario registrado con éxito. Ahora puedes iniciar sesión.');
        $this->redirigir('login');
    }

    // ============================================================
    // MOSTRAR FORMULARIO DE SOLICITUD DE RECUPERACIÓN
    // ============================================================
    public function mostrarRecuperar(): void {
        $this->renderizar('public/recuperar-password', [
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // PROCESAR SOLICITUD DE RECUPERACIÓN
    // ============================================================
    public function procesarRecuperar(): void {
        
        if (!$this->validarCsrf()) {
            $this->setFlash('mensaje_error', 'Token de seguridad inválido. Por favor, recarga la página e inténtalo de nuevo.');
            $this->redirigir('recuperar-password');
            return;
        }

        $errores = $this->usuarioValidator->validarRecuperacion($_POST);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->setSession('form_old', ['email' => $_POST['email'] ?? '']);
            $this->redirigir('recuperar-password');
        }

        $this->passwordResetService->solicitarRecuperacion(trim($_POST['email']));

        $this->setFlash('mensaje_exito', 'Si el correo introducido está registrado, recibirás las instrucciones en tu bandeja de entrada.');
        $this->redirigir('recuperar-password');
    }

    // ============================================================
    // MOSTRAR FORMULARIO DE RESTABLECER CONTRASEÑA
    // ============================================================
    public function mostrarRestablecer(): void {
        $token = $_GET['token'] ?? '';

        if ($token === '') {
            $this->redirigir('recuperar-password');
        }

        $this->renderizar('public/restablecer-password', [
            'token' => $token,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // PROCESAR RESTABLECIMIENTO DE CONTRASEÑA
    // ============================================================
    public function procesarRestablecer(): void {
        
        if (!$this->validarCsrf()) {
            $this->setFlash('mensaje_error', 'Token de seguridad inválido. Por favor, recarga la página e inténtalo de nuevo.');
            $token = $_POST['token'] ?? '';
            $this->redirigir('restablecer-password?token=' . urlencode($token));
            return;
        }

        $token = $_POST['token'] ?? '';

        if ($token === '') {
            $this->redirigir('recuperar-password');
        }

        $errores = $this->usuarioValidator->validarNuevaPassword($_POST);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->redirigir('restablecer-password?token=' . urlencode($token));
        }

        $actualizado = $this->passwordResetService->actualizarPasswordPorToken($token, $_POST['password']);

        if ($actualizado) {
            $this->setFlash('mensaje_exito', '¡Contraseña cambiada con éxito! Ya puedes acceder.');
            $this->redirigir('login');
        } else {
            $this->setFlash('mensaje_error', 'El enlace ha caducado o es inválido. Solicita uno nuevo.');
            $this->redirigir('recuperar-password');
        }
    }

    // ============================================================
    // CERRAR SESIÓN
    // ============================================================
    public function logout(): void {
        Session::destroy();
        $this->redirigir('');
    }
}