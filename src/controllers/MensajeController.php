<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;
use SonidoInteriorPoo\interfaces\MensajeServiceInterface;
use SonidoInteriorPoo\validators\MensajeValidator;
use SonidoInteriorPoo\middleware\AuthMiddleware;

class MensajeController extends Controller {
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
    public function procesarContacto(): void {
        $errores = $this->mensajeValidator->validar($_POST);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->setSession('form_old', $_POST);
            $this->redirigir('contacto');
        }

        $creado = $this->mensajeService->crear($_POST);

        if ($creado) {
            $this->setFlash('mensaje_exito', 'Mensaje enviado correctamente. Te responderemos lo antes posible.');
        } else {
            $this->setFlash('mensaje_error', 'Ha habido un problema al enviar el mensaje. Inténtalo de nuevo.');
        }

        $this->redirigir('contacto');
    }

    // ============================================================
    // LISTAR MENSAJES (ADMIN)
    // ============================================================
    public function listar(): void {
        AuthMiddleware::verificarAdmin();

        $mensajes = $this->mensajeService->obtenerTodosAdmin();

        $this->renderizar('admin/mensajes/admin-listado-mensajes', [
            'mensajes' => $mensajes,
            'paginaAdmin' => 'mensajes'
        ]);
    }

    // ============================================================
    // MARCAR MENSAJE COMO LEÍDO (ADMIN)
    // ============================================================
    public function marcarLeido(): void {
        AuthMiddleware::verificarAdmin();

        $idMensaje = (isset($_POST['id_mensaje']) && ctype_digit($_POST['id_mensaje']))
            ? (int) $_POST['id_mensaje']
            : null;

        if ($idMensaje === null) {
            $this->redirigir('admin/mensajes');
        }

        try {
            $this->mensajeService->marcarComoLeido($idMensaje);
            $this->setFlash('mensaje_exito', 'Mensaje marcado como leído.');
        } catch (\RuntimeException $e) {
            $this->setFlash('mensaje_error', $e->getMessage());
        }

        $this->redirigir('admin/mensajes');
    }

    // ============================================================
    // ELIMINAR MENSAJE (ADMIN)
    // ============================================================
    public function eliminar(): void {
        AuthMiddleware::verificarAdmin();

        $idMensaje = (isset($_POST['id_mensaje']) && ctype_digit($_POST['id_mensaje']))
            ? (int) $_POST['id_mensaje']
            : null;

        if ($idMensaje === null) {
            $this->redirigir('admin/mensajes');
        }

        try {
            $eliminado = $this->mensajeService->eliminar($idMensaje);
            if ($eliminado) {
                $this->setFlash('mensaje_exito', 'Mensaje eliminado correctamente.');
            } else {
                $this->setFlash('mensaje_error', 'No se pudo eliminar el mensaje.');
            }
        } catch (\RuntimeException $e) {
            $this->setFlash('mensaje_error', $e->getMessage());
        }

        $this->redirigir('admin/mensajes');
    }
}