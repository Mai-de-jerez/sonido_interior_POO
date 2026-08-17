<?php

namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;
use SonidoInteriorPoo\core\Request;
use SonidoInteriorPoo\core\Response;
use SonidoInteriorPoo\interfaces\ProductoServiceInterface;
use SonidoInteriorPoo\interfaces\CategoriaServiceInterface;
use SonidoInteriorPoo\validators\ProductoValidator;

class ProductoController extends Controller
{
    private ProductoServiceInterface $productoService;
    private CategoriaServiceInterface $categoriaService;
    private ProductoValidator $productoValidator;

    public function __construct(
        ProductoServiceInterface $productoService,
        CategoriaServiceInterface $categoriaService,
        ProductoValidator $productoValidator
    ) {
        $this->productoService = $productoService;
        $this->categoriaService = $categoriaService;
        $this->productoValidator = $productoValidator;
    }

    public function home(): Response
    {
        $productos = $this->productoService->obtenerUltimosProductosInicio();
        return Response::view('public/index', ['productos' => $productos]);
    }

    public function dashboard(): Response
    {
        return Response::view('admin/dashboard', [
            'totalProductos' => $this->productoService->obtenerTotalProductosAdmin(),
            'totalActivos' => $this->productoService->obtenerTotalActivosAdmin()
        ]);
    }

    public function nuevo(): Response
    {
        $categorias = $this->categoriaService->obtenerActivas();

        return Response::view('admin/productos/admin-alta-producto', [
            'producto' => null,
            'categorias' => $categorias,
            'csrf_token' => $this->csrfToken(),
            'errores' => $this->getFlash('errores', []),
            'old' => $this->getFlash('form_old', [])
        ]);
    }

    public function editar(int $id): Response
    {
        $producto = $this->productoService->obtenerPorIdAdmin($id);

        if ($producto === null) {
            return Response::redirect('admin/productos?status=notfound');
        }

        $categorias = $this->categoriaService->obtenerActivas();

        return Response::view('admin/productos/admin-alta-producto', [
            'producto' => $producto,
            'categorias' => $categorias,
            'csrf_token' => $this->csrfToken(),
            'errores' => $this->getFlash('errores', []),
            'old' => $this->getFlash('form_old', [])
        ]);
    }

    public function confirmarEliminar(int $id): Response
    {
        $producto = $this->productoService->obtenerPorIdAdmin($id);

        if ($producto === null) {
            return Response::redirect('admin/productos?status=notfound');
        }

        return Response::view('admin/productos/admin-confirmar-eliminar', [
            'producto' => $producto,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    public function confirmarReactivar(int $id): Response
    {
        $producto = $this->productoService->obtenerPorIdAdmin($id);

        if ($producto === null) {
            return Response::redirect('admin/productos?status=notfound');
        }

        return Response::view('admin/productos/admin-confirmar-reactivar', [
            'producto' => $producto,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    public function detalle(int $id): Response
    {
        $producto = $this->productoService->obtenerPorId($id);

        if ($producto === null) {
            return Response::redirect('catalogo?error=producto_no_encontrado');
        }

        return Response::view('public/detalle-producto', [
            'producto' => $producto,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    public function catalogo(Request $request): Response
    {
        $categoria = $request->get('categoria');
        $idCategoria = ctype_digit((string) $categoria) ? (int) $categoria : null;
        $orden = $request->get('orden', 'recientes');
        $pag = $request->get('pag');
        $pagina = ctype_digit((string) $pag) ? (int) $pag : 1;
        $porPagina = 8;

        $productos = $this->productoService->obtenerProductosCatalogo($idCategoria, $orden, $pagina, $porPagina);
        $totalProductos = $this->productoService->contarProductosCatalogo($idCategoria);
        $totalPaginas = (int) ceil($totalProductos / $porPagina);
        $categorias = $this->categoriaService->obtenerActivas();

        return Response::view('public/catalogo', [
            'productos' => $productos,
            'categorias' => $categorias,
            'idCategoria' => $idCategoria,
            'orden' => $orden,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalProductos' => $totalProductos,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    public function listar(): Response
    {
        $productos = $this->productoService->obtenerProductosAdmin();
        return Response::view('admin/productos/admin-listado-productos', ['productos' => $productos]);
    }

    public function crear(Request $request): Response
    {
        $datos = $request->allPost();
        $archivos = $request->allFiles();

        if (empty($request->post('nombre'))) {
            return Response::redirect('admin/productos');
        }

        $errores = array_merge(
            $this->productoValidator->validar($datos, esEdicion: false),
            $this->productoService->validarCategoria($datos)
        );

        if (!empty($errores)) {
            $this->setFlash('errores', $errores);
            $this->setFlash('form_old', $datos);
            return Response::redirect('admin/productos/crear');
        }

        $creado = $this->productoService->crear($datos, $archivos);

        if ($creado) {
            $this->setFlash('mensaje_exito', 'Producto guardado con éxito.');
            return Response::redirect('admin/productos');
        }

        $this->setFlash('mensaje_error', 'Error al guardar el producto.');
        return Response::redirect('admin/productos/crear');
    }

    public function actualizar(Request $request, int $id): Response
    {
        $datos = $request->allPost();
        $archivos = $request->allFiles();
        $urlVuelta = 'admin/productos/editar/' . $id;

        if (empty($request->post('nombre'))) {
            return Response::redirect('admin/productos');
        }

        $errores = array_merge(
            $this->productoValidator->validar($datos, esEdicion: true),
            $this->productoService->validarCategoria($datos)
        );

        if (!empty($errores)) {
            $this->setFlash('errores', $errores);
            $this->setFlash('form_old', $datos);
            return Response::redirect($urlVuelta);
        }

        $actualizado = $this->productoService->actualizar($id, $datos, $archivos);

        if ($actualizado) {
            $this->setFlash('mensaje_exito', 'Producto actualizado con éxito.');
            return Response::redirect('admin/productos');
        }

        $this->setFlash('mensaje_error', 'Error al actualizar el producto.');
        return Response::redirect($urlVuelta);
    }

    public function eliminar(int $id): Response
    {
        $eliminado = $this->productoService->eliminarLogico($id);

        if ($eliminado) {
            $this->setFlash('mensaje_exito', 'Producto eliminado correctamente.');
            return Response::redirect('admin/productos?status=deleted');
        }

        $this->setFlash('mensaje_error', 'No se pudo eliminar el producto.');
        return Response::redirect('admin/productos?status=error');
    }

    public function reactivar(int $id): Response
    {
        $reactivado = $this->productoService->reactivar($id);

        if ($reactivado) {
            $this->setFlash('mensaje_exito', 'Producto reactivado correctamente.');
            return Response::redirect('admin/productos?status=reactivated');
        }

        $this->setFlash('mensaje_error', 'No se pudo reactivar el producto. Por favor, inténtalo de nuevo.');
        return Response::redirect('admin/productos?status=error');
    }
}