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

        $this->categoriaService->crear($datos);

        $this->setFlash('mensaje_exito', 'Categoría guardada con éxito.');
        return Response::redirect('admin/categorias');
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

        $this->categoriaService->actualizar($id, $datos);

        $this->setFlash('mensaje_exito', 'Categoría actualizada con éxito.');
        return Response::redirect('admin/categorias');
    }

    public function eliminar(int $id): Response
    {
        $this->categoriaService->eliminarLogica($id);

        $this->setFlash('mensaje_exito', 'Categoría eliminada correctamente.');
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
        $this->categoriaService->reactivar($id);

        $this->setFlash('mensaje_exito', 'Categoría reactivada correctamente.');
        return Response::redirect('admin/categorias');
    }
}
