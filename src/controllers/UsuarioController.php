<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;
use SonidoInteriorPoo\interfaces\UsuarioServiceInterface;
use SonidoInteriorPoo\validators\UsuarioValidator;

class UsuarioController extends Controller {
    private UsuarioServiceInterface $usuarioService;
    private UsuarioValidator $usuarioValidator;

    public function __construct(
        UsuarioServiceInterface $usuarioService,
        UsuarioValidator $usuarioValidator
    ) {
        $this->usuarioService = $usuarioService;
        $this->usuarioValidator = $usuarioValidator;
    }

    // ============================================================
    // PROCESAR LOGIN
    // ============================================================
    public function procesarLogin(): void {
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

        // Limpiar y regenerar sesión
        session_unset();
        session_regenerate_id(true);

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
        $this->renderizar('public/recuperar-password');
    }

    // ============================================================
    // PROCESAR SOLICITUD DE RECUPERACIÓN
    // ============================================================
    public function procesarRecuperar(): void {
        $errores = $this->usuarioValidator->validarRecuperacion($_POST);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->setSession('form_old', ['email' => $_POST['email'] ?? '']);
            $this->redirigir('recuperar-password');
        }

        $this->usuarioService->solicitarRecuperacion(trim($_POST['email']));

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
            'token' => $token
        ]);
    }

    // ============================================================
    // PROCESAR RESTABLECIMIENTO DE CONTRASEÑA
    // ============================================================
    public function procesarRestablecer(): void {
        $token = $_POST['token'] ?? '';

        if ($token === '') {
            $this->redirigir('recuperar-password');
        }

        $errores = $this->usuarioValidator->validarNuevaPassword($_POST);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->redirigir('restablecer-password?token=' . urlencode($token));
        }

        $actualizado = $this->usuarioService->actualizarPasswordPorToken($token, $_POST['password']);

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
        session_unset();
        session_destroy();
        $this->redirigir('');
    }
}