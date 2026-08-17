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

    public function nuevo(): Response
    {
        return Response::view('admin/categorias/admin-alta-categoria', [
            'categoria' => null,
            'csrf_token' => $this->csrfToken(),
            'errores' => $this->getFlash('errores', []),
            'old' => $this->getFlash('form_old', []),
        ]);
    }

    public function editar(int $id): Response
    {
        $categoria = $this->categoriaService->obtenerPorId($id);

        if ($categoria === null) {
            return Response::redirect('admin/categorias?status=notfound');
        }

        return Response::view('admin/categorias/admin-alta-categoria', [
            'categoria' => $categoria,
            'csrf_token' => $this->csrfToken(),
            'errores' => $this->getFlash('errores', []),
            'old' => $this->getFlash('form_old', []),
        ]);
    }

    public function listar(): Response
    {
        $categorias = $this->categoriaService->obtenerTodasAdmin();

        return Response::view('admin/categorias/admin-listado-categorias', [
            'categorias' => $categorias,
            'paginaAdmin' => 'categorias'
        ]);
    }

    public function crear(Request $request): Response
    {
        $datos = $request->allPost();

        $errores = $this->categoriaValidator->validar($datos);

        if (!empty($errores)) {
            $this->setFlash('errores', $errores);
            $this->setFlash('form_old', $datos);
            return Response::redirect('admin/categorias/crear');
        }

        $creado = $this->categoriaService->crear($datos);

        if ($creado) {
            $this->setFlash('mensaje_exito', 'Categoría guardada con éxito.');
            return Response::redirect('admin/categorias');
        }

        $this->setFlash('mensaje_error', 'Error al guardar la categoría.');
        return Response::redirect('admin/categorias/crear');
    }

    public function actualizar(Request $request, int $id): Response
    {
        $datos = $request->allPost();
        $urlVuelta = 'admin/categorias/editar/' . $id;

        $errores = $this->categoriaValidator->validar($datos);

        if (!empty($errores)) {
            $this->setFlash('errores', $errores);
            $this->setFlash('form_old', $datos);
            return Response::redirect($urlVuelta);
        }

        $actualizado = $this->categoriaService->actualizar($id, $datos);

        if ($actualizado) {
            $this->setFlash('mensaje_exito', 'Categoría actualizada con éxito.');
            return Response::redirect('admin/categorias');
        }

        $this->setFlash('mensaje_error', 'Error al actualizar la categoría.');
        return Response::redirect($urlVuelta);
    }

    public function eliminar(int $id): Response
    {
        $eliminado = $this->categoriaService->eliminarLogica($id);

        if ($eliminado) {
            $this->setFlash('mensaje_exito', 'Categoría eliminada correctamente.');
        } else {
            $this->setFlash('mensaje_error', 'No se pudo eliminar la categoría.');
        }

        return Response::redirect('admin/categorias');
    }

    public function confirmarEliminar(int $id): Response
    {
        $categoria = $this->categoriaService->obtenerPorId($id);

        if ($categoria === null) {
            return Response::redirect('admin/categorias?status=notfound');
        }

        return Response::view('admin/categorias/admin-confirmar-eliminar', [
            'categoria' => $categoria,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    public function confirmarReactivar(int $id): Response
    {
        $categoria = $this->categoriaService->obtenerPorId($id);

        if ($categoria === null) {
            return Response::redirect('admin/categorias?status=notfound');
        }

        return Response::view('admin/categorias/admin-confirmar-reactivar', [
            'categoria' => $categoria,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    public function reactivar(int $id): Response
    {
        $reactivado = $this->categoriaService->reactivar($id);

        if ($reactivado) {
            $this->setFlash('mensaje_exito', 'Categoría reactivada correctamente.');
        } else {
            $this->setFlash('mensaje_error', 'No se pudo reactivar la categoría.');
        }

        return Response::redirect('admin/categorias');
    }
}
