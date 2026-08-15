<?php

namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;
use SonidoInteriorPoo\core\Request;
use SonidoInteriorPoo\core\Response;
use SonidoInteriorPoo\interfaces\CategoriaServiceInterface;
use SonidoInteriorPoo\validators\CategoriaValidator;

class CategoriaController extends Controller
{
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

    public function nuevo(): Response
    {
        return Response::view(
            'admin/categorias/admin-alta-categoria',
            [
                'categoria' => null,
                'csrf_token' => $this->csrfToken()
            ]
        );
    }

    // ============================================================
    // MOSTRAR FORMULARIO EDITAR
    // ============================================================

    public function editar(int $id): Response
    {
        $categoria = $this->categoriaService
            ->obtenerPorId($id);

        if ($categoria === null) {
            return Response::redirect(
                'admin/categorias?status=notfound'
            );
        }

        return Response::view(
            'admin/categorias/admin-alta-categoria',
            [
                'categoria' => $categoria,
                'csrf_token' => $this->csrfToken()
            ]
        );
    }

    // ============================================================
    // LISTAR CATEGORÍAS
    // ============================================================

    public function listar(): Response
    {
        $categorias = $this->categoriaService
            ->obtenerTodasAdmin();

        return Response::view(
            'admin/categorias/admin-listado-categorias',
            [
                'categorias' => $categorias,
                'paginaAdmin' => 'categorias'
            ]
        );
    }

    // ============================================================
    // CREAR CATEGORÍA
    // ============================================================

    public function crear(Request $request): Response
    {
        $datos = $request->allPost();

        $errores = $this->categoriaValidator
            ->validar($datos);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->setSession('form_old', $datos);

            return Response::redirect(
                'admin/categorias/crear'
            );
        }

        try {
            $creado = $this->categoriaService
                ->crear($datos);
        } catch (\RuntimeException $e) {
            $this->setFlash(
                'mensaje_error',
                $e->getMessage()
            );

            return Response::redirect(
                'admin/categorias/crear'
            );
        }

        if ($creado) {
            $this->setFlash(
                'mensaje_exito',
                'Categoría guardada con éxito.'
            );

            return Response::redirect(
                'admin/categorias'
            );
        }

        $this->setFlash(
            'mensaje_error',
            'Error al guardar la categoría.'
        );

        return Response::redirect(
            'admin/categorias/crear'
        );
    }

    // ============================================================
    // ACTUALIZAR CATEGORÍA
    // ============================================================

    public function actualizar(Request $request, int $id): Response {
        
        $datos = $request->allPost();
        $urlVuelta = 'admin/categorias/editar/' . $id;

        $errores = $this->categoriaValidator
            ->validar($datos);

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->setSession('form_old', $datos);

            return Response::redirect($urlVuelta);
        }

        try {
            $actualizado = $this->categoriaService
                ->actualizar($id, $datos);

        } catch (\RuntimeException $e) {
            $this->setFlash(
                'mensaje_error',
                $e->getMessage()
            );

            return Response::redirect($urlVuelta);
        }

        if ($actualizado) {
            $this->setFlash(
                'mensaje_exito',
                'Categoría actualizada con éxito.'
            );

            return Response::redirect(
                'admin/categorias'
            );
        }

        $this->setFlash(
            'mensaje_error',
            'Error al actualizar la categoría.'
        );

        return Response::redirect($urlVuelta);
    }

    // ============================================================
    // ELIMINAR CATEGORÍA
    // ============================================================

    public function eliminar(int $id): Response
    {
        try {
            $eliminado = $this->categoriaService->eliminarLogica($id);

        } catch (\RuntimeException $e) {
            $this->setFlash(
                'mensaje_error',
                $e->getMessage()
            );

            return Response::redirect(
                'admin/categorias'
            );
        }

        if ($eliminado) {
            $this->setFlash(
                'mensaje_exito',
                'Categoría eliminada correctamente.'
            );
        } else {
            $this->setFlash(
                'mensaje_error',
                'No se pudo eliminar la categoría.'
            );
        }

        return Response::redirect(
            'admin/categorias'
        );
    }

    // ============================================================
    // CONFIRMAR ELIMINAR
    // ============================================================

    public function confirmarEliminar(int $id): Response
    {
        $categoria = $this->categoriaService
            ->obtenerPorId($id);

        if ($categoria === null) {
            return Response::redirect(
                'admin/categorias?status=notfound'
            );
        }

        return Response::view(
            'admin/categorias/admin-confirmar-eliminar',
            [
                'categoria' => $categoria,
                'csrf_token' => $this->csrfToken()
            ]
        );
    }

    // ============================================================
    // CONFIRMAR REACTIVAR
    // ============================================================

    public function confirmarReactivar(int $id): Response
    {
        $categoria = $this->categoriaService
            ->obtenerPorId($id);

        if ($categoria === null) {
            return Response::redirect(
                'admin/categorias?status=notfound'
            );
        }

        return Response::view(
            'admin/categorias/admin-confirmar-reactivar',
            [
                'categoria' => $categoria,
                'csrf_token' => $this->csrfToken()
            ]
        );
    }

    // ============================================================
    // REACTIVAR CATEGORÍA
    // ============================================================

    public function reactivar(int $id): Response
    {
        try {
            $reactivado = $this->categoriaService
                ->reactivar($id);

        } catch (\RuntimeException $e) {
            $this->setFlash(
                'mensaje_error',
                $e->getMessage()
            );

            return Response::redirect(
                'admin/categorias'
            );
        }

        if ($reactivado) {
            $this->setFlash(
                'mensaje_exito',
                'Categoría reactivada correctamente.'
            );
        } else {
            $this->setFlash(
                'mensaje_error',
                'No se pudo reactivar la categoría.'
            );
        }

        return Response::redirect(
            'admin/categorias'
        );
    }
}
