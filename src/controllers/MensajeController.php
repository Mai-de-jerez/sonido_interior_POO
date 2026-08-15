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
            $this->setSession('errores', $errores);
            $this->setSession('form_old', $request->allPost());

            return Response::redirect('contacto');
        }

        $creado = $this->mensajeService->crear($datos);

        if ($creado) {
            $this->setFlash(
                'mensaje_exito',
                'Mensaje enviado correctamente. Te responderemos lo antes posible.'
            );
        } else {
            $this->setFlash(
                'mensaje_error',
                'Ha habido un problema al enviar el mensaje. Inténtalo de nuevo.'
            );
        }

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

    // ============================================================
    // MARCAR MENSAJE COMO LEÍDO (ADMIN)
    // ============================================================

    public function marcarLeido(int $id): Response
    {
        try {
            $this->mensajeService->marcarComoLeido($id);

            $this->setFlash(
                'mensaje_exito',
                'Mensaje marcado como leído.'
            );
        } catch (\RuntimeException $e) {
            $this->setFlash(
                'mensaje_error',
                $e->getMessage()
            );
        }

        return Response::redirect('admin/mensajes');
    }

    // ============================================================
    // ELIMINAR MENSAJE (ADMIN)
    // ============================================================

    public function eliminar(int $id): Response
    {
        try {
            $eliminado = $this->mensajeService->eliminar($id);

            if ($eliminado) {
                $this->setFlash(
                    'mensaje_exito',
                    'Mensaje eliminado correctamente.'
                );
            } else {
                $this->setFlash(
                    'mensaje_error',
                    'No se pudo eliminar el mensaje.'
                );
            }
        } catch (\RuntimeException $e) {
            $this->setFlash(
                'mensaje_error',
                $e->getMessage()
            );
        }

        return Response::redirect('admin/mensajes');
    }
}