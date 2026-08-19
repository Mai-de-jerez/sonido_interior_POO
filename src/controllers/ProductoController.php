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
        
        return Response::view(
            'admin/productos/admin-listado-productos', 
            ['productos' => $productos]);
    }

    // ============================================================
    // MÉTODOS POST 
    // ============================================================

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

        $this->productoService->crear($datos, $archivos);
        
        $this->setFlash('mensaje_exito', 'Producto guardado con éxito.');
        return Response::redirect('admin/productos');
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

        $this->productoService->actualizar($id, $datos, $archivos);
        
        $this->setFlash('mensaje_exito', 'Producto actualizado con éxito.');
        return Response::redirect('admin/productos');
    }

    public function eliminar(int $id): Response
    {
        $this->productoService->eliminarLogico($id);
        
        $this->setFlash('mensaje_exito', 'Producto eliminado correctamente.');
        return Response::redirect('admin/productos?status=deleted');
    }

    public function reactivar(int $id): Response
    {
        $this->productoService->reactivar($id);
        
        $this->setFlash('mensaje_exito', 'Producto reactivado correctamente.');
        return Response::redirect('admin/productos?status=reactivated');
    }
}