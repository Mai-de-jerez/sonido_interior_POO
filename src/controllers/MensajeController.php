<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\interfaces\MensajeServiceInterface;
use SonidoInteriorPoo\middleware\AuthMiddleware;

class MensajeController {
    private MensajeServiceInterface $mensajeService;

    public function __construct(MensajeServiceInterface $mensajeService) {
        $this->mensajeService = $mensajeService;
    }

    // ============================================================
    // PROCESAR FORMULARIO DE CONTACTO (PÚBLICO)
    // ============================================================
    public function procesarContacto(): void {
        $errores = $this->mensajeService->validar($_POST);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['form_old'] = $_POST;
            header("Location: " . BASE_URL . "/contacto");
            exit();
        }

        $creado = $this->mensajeService->crear($_POST);

        if ($creado) {
            $_SESSION['mensaje_exito'] = "Mensaje enviado correctamente. Te responderemos lo antes posible.";
        } else {
            $_SESSION['mensaje_error'] = "Ha habido un problema al enviar el mensaje. Inténtalo de nuevo.";
        }

        header("Location: " . BASE_URL . "/contacto");
        exit();
    }

    // ============================================================
    // MÉTODOS ADMIN - CON SEGURIDAD
    // ============================================================

    public function listar(): void {
        AuthMiddleware::verificarAdmin();

        $mensajes = $this->mensajeService->obtenerTodosAdmin();
        $data = ['mensajes' => $mensajes];
        $paginaAdmin = "mensajes";

        extract($data);
        require_once __DIR__ . '/../views/admin/mensajes/admin-listado-mensajes.php';
    }

    public function marcarLeido(): void {
        AuthMiddleware::verificarAdmin();

        $idMensaje = (isset($_POST['id_mensaje']) && ctype_digit($_POST['id_mensaje']))
            ? (int) $_POST['id_mensaje']
            : null;

        if ($idMensaje === null) {
            header("Location: " . BASE_URL . "/admin/mensajes");
            exit();
        }

        try {
            $this->mensajeService->marcarComoLeido($idMensaje);
        } catch (\RuntimeException $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            header("Location: " . BASE_URL . "/admin/mensajes?status=error");
            exit();
        }

        header("Location: " . BASE_URL . "/admin/mensajes");
        exit();
    }

    public function eliminar(): void {
        AuthMiddleware::verificarAdmin();

        $idMensaje = (isset($_POST['id_mensaje']) && ctype_digit($_POST['id_mensaje']))
            ? (int) $_POST['id_mensaje']
            : null;

        if ($idMensaje === null) {
            header("Location: " . BASE_URL . "/admin/mensajes");
            exit();
        }

        try {
            $eliminado = $this->mensajeService->eliminar($idMensaje);
        } catch (\RuntimeException $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            header("Location: " . BASE_URL . "/admin/mensajes?status=error");
            exit();
        }

        if ($eliminado) {
            $_SESSION['mensaje_exito'] = "Mensaje eliminado correctamente.";
            header("Location: " . BASE_URL . "/admin/mensajes?status=deleted");
        } else {
            $_SESSION['mensaje_error'] = "No se pudo eliminar el mensaje.";
            header("Location: " . BASE_URL . "/admin/mensajes?status=error");
        }
        exit();
    }
}