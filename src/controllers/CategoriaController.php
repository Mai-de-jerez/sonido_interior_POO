<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;
use SonidoInteriorPoo\interfaces\CategoriaServiceInterface;
use SonidoInteriorPoo\validators\CategoriaValidator;

class CategoriaController extends Controller {
    private CategoriaServiceInterface $categoriaService;
    private CategoriaValidator $categoriaValidator;

    public function __construct(
        CategoriaServiceInterface $categoriaService,
        CategoriaValidator $categoriaValidator
    ) {
        $this->categoriaService = $categoriaService;
        $this->categoriaValidator = $categoriaValidator;
    }

    // ============================================================
    // MOSTRAR FORMULARIO NUEVO
    // ============================================================
    public function nuevo(): void {

        $this->renderizar('admin/categorias/admin-alta-categoria', [
            'categoria' => null,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // MOSTRAR FORMULARIO EDITAR
    // ============================================================
    public function editar(): void {

        $idCategoria = isset($_GET['id']) && ctype_digit($_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($idCategoria === 0) {
            $this->redirigir('admin/categorias?status=notfound');
        }

        $categoria = $this->categoriaService->obtenerPorId($idCategoria);

        if ($categoria === null) {
            $this->redirigir('admin/categorias?status=notfound');
        }

        $this->renderizar('admin/categorias/admin-alta-categoria', [
            'categoria' => $categoria,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // LISTAR CATEGORÍAS (ADMIN)
    // ============================================================
    public function listar(): void {

        $categorias = $this->categoriaService->obtenerTodasAdmin();

        $this->renderizar('admin/categorias/admin-listado-categorias', [
            'categorias' => $categorias,
            'paginaAdmin' => 'categorias'
        ]);
    }

    // ============================================================
    // CREAR CATEGORÍA
    // ============================================================
    public function crear(): void {

        if (!$this->validarCsrf()) {
            $this->setFlash('mensaje_error', 'Token de seguridad inválido. Por favor, recarga la página e inténtalo de nuevo.');
            $this->redirigir('admin/categorias/crear');
            return;
        }

        $errores = $this->categoriaValidator->validar($_POST);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->setSession('form_old', $_POST);
            $this->redirigir('admin/categorias/crear');
        }

        $creado = $this->categoriaService->crear($_POST);

        if ($creado) {
            $this->setFlash('mensaje_exito', 'Categoría guardada con éxito.');
            $this->redirigir('admin/categorias');
        } else {
            $this->setFlash('mensaje_error', 'Error al guardar la categoría.');
            $this->redirigir('admin/categorias/crear');
        }
    }

    // ============================================================
    // ACTUALIZAR CATEGORÍA
    // ============================================================
    public function actualizar(): void {
        // Validar token CSRF
        if (!$this->validarCsrf()) {
            $this->setFlash('mensaje_error', 'Token de seguridad inválido. Por favor, recarga la página e inténtalo de nuevo.');
            $idCategoria = $_POST['id_categoria'] ?? 0;
            $this->redirigir('admin/categorias/editar?id=' . $idCategoria);
            return;
        }

        $idCategoria = (isset($_POST['id_categoria']) && ctype_digit($_POST['id_categoria']))
            ? (int) $_POST['id_categoria']
            : null;

        if ($idCategoria === null) {
            $this->redirigir('admin/categorias');
        }

        $urlVuelta = 'admin/categorias/editar?id=' . $idCategoria;

        $errores = $this->categoriaValidator->validar($_POST);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->setSession('form_old', $_POST);
            $this->redirigir($urlVuelta);
        }

        try {
            $actualizado = $this->categoriaService->actualizar($idCategoria, $_POST);
            if ($actualizado) {
                $this->setFlash('mensaje_exito', 'Categoría actualizada con éxito.');
                $this->redirigir('admin/categorias');
            } else {
                $this->setFlash('mensaje_error', 'Error al actualizar la categoría.');
                $this->redirigir($urlVuelta);
            }
        } catch (\RuntimeException $e) {
            $this->setFlash('mensaje_error', $e->getMessage());
            $this->redirigir($urlVuelta);
        }
    }

    // ============================================================
    // ELIMINAR CATEGORÍA (BORRADO LÓGICO)
    // ============================================================
    public function eliminar(): void {
        // Validar token CSRF
        if (!$this->validarCsrf()) {
            $this->setFlash('mensaje_error', 'Token de seguridad inválido.');
            $this->redirigir('admin/categorias');
            return;
        }
        $idCategoria = (isset($_POST['id_categoria']) && ctype_digit($_POST['id_categoria']))
            ? (int) $_POST['id_categoria']
            : null;

        if ($idCategoria === null) {
            $this->redirigir('admin/categorias');
        }

        try {
            $eliminado = $this->categoriaService->eliminarLogica($idCategoria);
            if ($eliminado) {
                $this->setFlash('mensaje_exito', 'Categoría eliminada correctamente.');
            } else {
                $this->setFlash('mensaje_error', 'No se pudo eliminar la categoría.');
            }
        } catch (\RuntimeException $e) {
            $this->setFlash('mensaje_error', $e->getMessage());
        }

        $this->redirigir('admin/categorias');
    }

    // ============================================================
    // CONFIRMAR ELIMINAR
    // ============================================================
    public function confirmarEliminar(): void {

        $idCategoria = isset($_GET['id']) && ctype_digit($_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($idCategoria === 0) {
            $this->redirigir('admin/categorias?status=notfound');
        }

        $categoria = $this->categoriaService->obtenerPorId($idCategoria);

        if ($categoria === null) {
            $this->redirigir('admin/categorias?status=notfound');
        }

        $this->renderizar('admin/categorias/admin-confirmar-eliminar', [
            'categoria' => $categoria,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // CONFIRMAR REACTIVAR
    // ============================================================
    public function confirmarReactivar(): void {

        $idCategoria = isset($_GET['id']) && ctype_digit($_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($idCategoria === 0) {
            $this->redirigir('admin/categorias?status=notfound');
        }

        $categoria = $this->categoriaService->obtenerPorId($idCategoria);

        if ($categoria === null) {
            $this->redirigir('admin/categorias?status=notfound');
        }

        $this->renderizar('admin/categorias/admin-confirmar-reactivar', [
            'categoria' => $categoria,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // REACTIVAR CATEGORÍA
    // ============================================================
    public function reactivar(): void {
        // Validar token CSRF
        if (!$this->validarCsrf()) {
            $this->setFlash('mensaje_error', 'Token de seguridad inválido.');
            $this->redirigir('admin/categorias');
            return;
        }
        $idCategoria = (isset($_POST['id_categoria']) && ctype_digit($_POST['id_categoria']))
            ? (int) $_POST['id_categoria']
            : null;

        if ($idCategoria === null) {
            $this->redirigir('admin/categorias');
        }

        try {
            $reactivado = $this->categoriaService->reactivar($idCategoria);
            if ($reactivado) {
                $this->setFlash('mensaje_exito', 'Categoría reactivada correctamente.');
            } else {
                $this->setFlash('mensaje_error', 'No se pudo reactivar la categoría.');
            }
        } catch (\RuntimeException $e) {
            $this->setFlash('mensaje_error', $e->getMessage());
        }

        $this->redirigir('admin/categorias');
    }
}