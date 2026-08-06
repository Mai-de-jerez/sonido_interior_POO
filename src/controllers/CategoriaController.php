<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\interfaces\CategoriaServiceInterface;
use SonidoInteriorPoo\middleware\AuthMiddleware;

class CategoriaController {
    private CategoriaServiceInterface $categoriaService;

    public function __construct(CategoriaServiceInterface $categoriaService) {
        $this->categoriaService = $categoriaService;
    }

    public function crear(): void {
        AuthMiddleware::verificarAdmin();

        if (empty($_POST['nombre'])) {
            header("Location: /admin/categorias");
            exit();
        }

        $errores = $this->categoriaService->validar($_POST);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            header("Location: /admin/categorias/nueva");
            exit();
        }

        $creado = $this->categoriaService->crear($_POST);

        if ($creado) {
            $_SESSION['mensaje_exito'] = "Categoría guardada con éxito.";
            header("Location: /admin/categorias");
        } else {
            $_SESSION['mensaje_error'] = "Error al guardar la categoría.";
            header("Location: /admin/categorias/nueva");
        }
        exit();
    }

    public function actualizar(): void {
        AuthMiddleware::verificarAdmin();

        $idCategoria = (isset($_POST['id_categoria']) && ctype_digit($_POST['id_categoria']))
            ? (int) $_POST['id_categoria']
            : null;

        if ($idCategoria === null || empty($_POST['nombre'])) {
            header("Location: /admin/categorias");
            exit();
        }

        $urlVuelta = "/admin/categorias/editar?id=" . $idCategoria;

        $errores = $this->categoriaService->validar($_POST);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            header("Location: " . $urlVuelta);
            exit();
        }

        try {
            $actualizado = $this->categoriaService->actualizar($idCategoria, $_POST);
        } catch (\RuntimeException $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            header("Location: " . $urlVuelta);
            exit();
        }

        if ($actualizado) {
            $_SESSION['mensaje_exito'] = "Categoría actualizada con éxito.";
            header("Location: /admin/categorias");
        } else {
            $_SESSION['mensaje_error'] = "Error al actualizar la categoría.";
            header("Location: " . $urlVuelta);
        }
        exit();
    }

    public function eliminar(): void {
        AuthMiddleware::verificarAdmin();

        $idCategoria = (isset($_POST['id_categoria']) && ctype_digit($_POST['id_categoria']))
            ? (int) $_POST['id_categoria']
            : null;

        if ($idCategoria === null) {
            header("Location: /admin/categorias");
            exit();
        }

        try {
            $eliminado = $this->categoriaService->eliminarLogica($idCategoria);
        } catch (\RuntimeException $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            header("Location: /admin/categorias?status=error");
            exit();
        }

        if ($eliminado) {
            $_SESSION['mensaje_exito'] = "Categoría eliminada correctamente.";
            header("Location: /admin/categorias?status=deleted");
        } else {
            $_SESSION['mensaje_error'] = "No se pudo eliminar la categoría.";
            header("Location: /admin/categorias?status=error");
        }
        exit();
    }

    public function reactivar(): void {
        AuthMiddleware::verificarAdmin();

        $idCategoria = (isset($_GET['id']) && ctype_digit($_GET['id']))
            ? (int) $_GET['id']
            : null;

        if ($idCategoria === null) {
            header("Location: /admin/categorias");
            exit();
        }

        try {
            $reactivado = $this->categoriaService->reactivar($idCategoria);
        } catch (\RuntimeException $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            header("Location: /admin/categorias?status=error");
            exit();
        }

        if ($reactivado) {
            $_SESSION['mensaje_exito'] = "Categoría reactivada correctamente.";
            header("Location: /admin/categorias?status=reactivated");
        } else {
            $_SESSION['mensaje_error'] = "No se pudo reactivar la categoría. Por favor, inténtalo de nuevo.";
            header("Location: /admin/categorias?status=error");
        }
        exit();
    }
}