<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\interfaces\CategoriaServiceInterface;
use SonidoInteriorPoo\validators\CategoriaValidator;
use SonidoInteriorPoo\middleware\AuthMiddleware;

class CategoriaController {
    private CategoriaServiceInterface $categoriaService;
    private CategoriaValidator $categoriaValidator;

    public function __construct(
        CategoriaServiceInterface $categoriaService,
        CategoriaValidator $categoriaValidator
    ) {
        $this->categoriaService = $categoriaService;
        $this->categoriaValidator = $categoriaValidator;
    }

    public function crear(): void {
        AuthMiddleware::verificarAdmin();

        $errores = $this->categoriaValidator->validar($_POST);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['form_old'] = $_POST;
            header("Location: " . BASE_URL . "/admin/categorias/nueva");
            exit();
        }

        $creado = $this->categoriaService->crear($_POST);

        if ($creado) {
            $_SESSION['mensaje_exito'] = "Categoría guardada con éxito.";
            header("Location: " . BASE_URL . "/admin/categorias");
        } else {
            $_SESSION['mensaje_error'] = "Error al guardar la categoría.";
            header("Location: " . BASE_URL . "/admin/categorias/nueva");
        }
        exit();
    }

    public function actualizar(): void {
        AuthMiddleware::verificarAdmin();

        $idCategoria = (isset($_POST['id_categoria']) && ctype_digit($_POST['id_categoria']))
            ? (int) $_POST['id_categoria']
            : null;

        if ($idCategoria === null) {
            header("Location: " . BASE_URL . "/admin/categorias");
            exit();
        }

        $urlVuelta = BASE_URL . "/admin/categorias/editar?id=" . $idCategoria;

        $errores = $this->categoriaValidator->validar($_POST);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['form_old'] = $_POST;
            header("Location: " . $urlVuelta);
            exit();
        }

        try {
            $actualizado = $this->categoriaService->actualizar($idCategoria, $_POST);
            if ($actualizado) {
                $_SESSION['mensaje_exito'] = "Categoría actualizada con éxito.";
                header("Location: " . BASE_URL . "/admin/categorias");
            } else {
                $_SESSION['mensaje_error'] = "Error al actualizar la categoría.";
                header("Location: " . $urlVuelta);
            }
        } catch (\RuntimeException $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
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
            header("Location: " . BASE_URL . "/admin/categorias");
            exit();
        }

        try {
            $eliminado = $this->categoriaService->eliminarLogica($idCategoria);
            if ($eliminado) {
                $_SESSION['mensaje_exito'] = "Categoría eliminada correctamente.";
            } else {
                $_SESSION['mensaje_error'] = "No se pudo eliminar la categoría.";
            }
        } catch (\RuntimeException $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
        }

        header("Location: " . BASE_URL . "/admin/categorias");
        exit();
    }

    public function reactivar(): void {
        AuthMiddleware::verificarAdmin();

        $idCategoria = (isset($_GET['id']) && ctype_digit($_GET['id']))
            ? (int) $_GET['id']
            : null;

        if ($idCategoria === null) {
            header("Location: " . BASE_URL . "/admin/categorias");
            exit();
        }

        try {
            $reactivado = $this->categoriaService->reactivar($idCategoria);
            if ($reactivado) {
                $_SESSION['mensaje_exito'] = "Categoría reactivada correctamente.";
            } else {
                $_SESSION['mensaje_error'] = "No se pudo reactivar la categoría. Por favor, inténtalo de nuevo.";
            }
        } catch (\RuntimeException $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
        }

        header("Location: " . BASE_URL . "/admin/categorias");
        exit();
    }
}