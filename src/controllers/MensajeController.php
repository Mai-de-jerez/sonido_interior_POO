<?php

namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;
use SonidoInteriorPoo\core\Request;
use SonidoInteriorPoo\core\Response;
use SonidoInteriorPoo\interfaces\MensajeServiceInterface;
use SonidoInteriorPoo\validators\MensajeValidator;

class MensajeController extends Controller
{
    private MensajeServiceInterface $mensajeService;
    private MensajeValidator $mensajeValidator;

    public function __construct(
        MensajeServiceInterface $mensajeService,
        MensajeValidator $mensajeValidator
    ) {
        $this->mensajeService = $mensajeService;
        $this->mensajeValidator = $mensajeValidator;
    }

    // ============================================================
    // PROCESAR FORMULARIO DE CONTACTO
    // ============================================================

    public function procesarContacto(Request $request): Response
    {
        $datos = $request->allPost();

        $errores = $this->mensajeValidator->validar($datos);

        if (!empty($errores)) {
            $this->setFlash('errores', $errores);
            $this->setFlash('form_old', $datos);
            return Response::redirect('contacto');
        }

        $this->mensajeService->crear($datos);

        $this->setFlash(
            'mensaje_exito',
            'Mensaje enviado correctamente. Te responderemos lo antes posible.'
        );

        return Response::redirect('contacto');
    }

    // ============================================================
    // LISTAR MENSAJES (ADMIN)
    // ============================================================

    public function listar(): Response
    {
        $mensajes = $this->mensajeService
            ->obtenerTodosAdmin();

        return Response::view(
            'admin/mensajes/admin-listado-mensajes',
            [
                'mensajes' => $mensajes,
                'paginaAdmin' => 'mensajes',
                'csrf_token' => $this->csrfToken()
            ]
        );
    }

    public function confirmarMarcarLeido(int $id): Response
    {
        $mensaje = $this->mensajeService->obtenerPorId($id);

        if ($mensaje === null) {
            return Response::redirect('admin/mensajes?status=notfound');
        }

        return Response::view('admin/mensajes/admin-confirmar-leido', [
            'mensaje' => $mensaje,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    public function confirmarEliminar(int $id): Response
    {
        $mensaje = $this->mensajeService->obtenerPorId($id);

        if ($mensaje === null) {
            return Response::redirect('admin/mensajes?status=notfound');
        }

        return Response::view('admin/mensajes/admin-confirmar-eliminar', [
            'mensaje' => $mensaje,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // MARCAR MENSAJE COMO LEÍDO (ADMIN)
    // ============================================================
    public function marcarLeido(int $id): Response
    {
        $this->mensajeService->marcarComoLeido($id);

        $this->setFlash(
            'mensaje_exito',
            'Mensaje marcado como leído.'
        );

        return Response::redirect('admin/mensajes');
    }

    // ============================================================
    // ELIMINAR MENSAJE (ADMIN)
    // ============================================================
    public function eliminar(int $id): Response
    {
        $this->mensajeService->eliminar($id);

        $this->setFlash(
            'mensaje_exito',
            'Mensaje eliminado correctamente.'
        );

        return Response::redirect('admin/mensajes');
    }

}