<?php

namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;
use SonidoInteriorPoo\core\Request;
use SonidoInteriorPoo\core\Response;
use SonidoInteriorPoo\core\Session;
use SonidoInteriorPoo\interfaces\UsuarioServiceInterface;
use SonidoInteriorPoo\interfaces\PasswordResetServiceInterface;
use SonidoInteriorPoo\validators\UsuarioValidator;

class UsuarioController extends Controller
{
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

    public function procesarLogin(Request $request): Response
    {
        $datos = $request->allPost();

        $errores = $this->usuarioValidator->validarLogin($datos);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);

            $this->setSession('form_old', [
                'usuario' => $request->post('usuario', '')
            ]);

            return Response::redirect('login');
        }

        $usuarioData = $this->usuarioService->login(
            trim($request->post('usuario', '')),
            $request->post('password', '')
        );

        if (!$usuarioData) {
            $this->setFlash(
                'mensaje_error',
                'Usuario o contraseña incorrectos.'
            );

            return Response::redirect('login');
        }

        Session::clear();
        Session::regenerate();

        $this->setSession(
            'id_usuario',
            $usuarioData['id_usuario']
        );

        $this->setSession(
            'usuario',
            $usuarioData['usuario']
        );

        $this->setSession(
            'rol',
            $usuarioData['rol']
        );

        $this->setSession(
            'cantidades_carrito',
            $usuarioData['cantidades_carrito']
        );

        if ($usuarioData['rol'] === 'ADMIN') {
            return Response::redirect('admin/dashboard');
        }

        return Response::redirect('');
    }

    // ============================================================
    // PROCESAR REGISTRO
    // ============================================================

    public function procesarRegistro(Request $request): Response
    {
        $datos = $request->allPost();

        $errores = $this->usuarioValidator->validarRegistro($datos);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);

            $this->setSession('form_old', [
                'usuario' => $request->post('usuario', ''),
                'email' => $request->post('email', '')
            ]);

            return Response::redirect('registro');
        }

        $registrado = $this->usuarioService->registrar(
            trim($request->post('usuario', '')),
            trim($request->post('email', '')),
            $request->post('password', '')
        );

        if (!$registrado) {
            $this->setFlash(
                'mensaje_error',
                'El usuario o email ya existe.'
            );

            $this->setSession('form_old', [
                'usuario' => $request->post('usuario', ''),
                'email' => $request->post('email', '')
            ]);

            return Response::redirect('registro');
        }

        $this->setFlash(
            'mensaje_exito',
            'Usuario registrado con éxito. Ahora puedes iniciar sesión.'
        );

        return Response::redirect('login');
    }

    // ============================================================
    // MOSTRAR RECUPERACIÓN
    // ============================================================

    public function mostrarRecuperar(): Response
    {
        return Response::view('public/recuperar-password', [
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // PROCESAR RECUPERACIÓN
    // ============================================================

    public function procesarRecuperar(Request $request): Response
    {
        $datos = $request->allPost();

        $errores = $this->usuarioValidator->validarRecuperacion($datos);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);

            $this->setSession('form_old', [
                'email' => $request->post('email', '')
            ]);

            return Response::redirect('recuperar-password');
        }

        $this->passwordResetService->solicitarRecuperacion(
            trim($request->post('email', ''))
        );

        $this->setFlash(
            'mensaje_exito',
            'Si el correo introducido está registrado, recibirás las instrucciones en tu bandeja de entrada.'
        );

        return Response::redirect('recuperar-password');
    }

    // ============================================================
    // MOSTRAR RESTABLECER CONTRASEÑA
    // ============================================================

    public function mostrarRestablecer(Request $request): Response
    {
        $token = $request->get('token', '');

        if ($token === '') {
            return Response::redirect('recuperar-password');
        }

        return Response::view('public/restablecer-password', [
            'token' => $token,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // PROCESAR RESTABLECIMIENTO
    // ============================================================

    public function procesarRestablecer(Request $request): Response
    {
        $datos = $request->allPost();

        $token = $request->post('token', '');

        if ($token === '') {
            return Response::redirect('recuperar-password');
        }

        $errores = $this->usuarioValidator->validarNuevaPassword($datos);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);

            return Response::redirect(
                'restablecer-password?token=' . urlencode($token)
            );
        }

        $actualizado = $this->passwordResetService
            ->actualizarPasswordPorToken(
                $token,
                $request->post('password', '')
            );

        if ($actualizado) {
            $this->setFlash(
                'mensaje_exito',
                '¡Contraseña cambiada con éxito! Ya puedes acceder.'
            );

            return Response::redirect('login');
        }

        $this->setFlash(
            'mensaje_error',
            'El enlace ha caducado o es inválido. Solicita uno nuevo.'
        );

        return Response::redirect('recuperar-password');
    }

    // ============================================================
    // CERRAR SESIÓN
    // ============================================================

    public function logout(): Response
    {
        Session::destroy();

        return Response::redirect('');
    }
}



