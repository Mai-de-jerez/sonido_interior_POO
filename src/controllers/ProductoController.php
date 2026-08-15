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

    // ============================================================
    // HOME
    // ============================================================

    public function home(): Response
    {
        $productos = $this->productoService
            ->obtenerUltimosProductosInicio();

        return Response::view('public/index', [
            'productos' => $productos
        ]);
    }

    // ============================================================
    // DASHBOARD
    // ============================================================

    public function dashboard(): Response
    {
        return Response::view('admin/dashboard', [
            'totalProductos' => $this->productoService
                ->obtenerTotalProductosAdmin(),

            'totalActivos' => $this->productoService
                ->obtenerTotalActivosAdmin()
        ]);
    }

    // ============================================================
    // FORMULARIO NUEVO PRODUCTO
    // ============================================================

    public function nuevo(): Response
    {
        $categorias = $this->categoriaService->obtenerActivas();

        return Response::view('admin/productos/admin-alta-producto', [
            'producto' => null,
            'categorias' => $categorias,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // FORMULARIO EDITAR PRODUCTO
    // ============================================================

    public function editar(int $id): Response
    {
        $producto = $this->productoService
            ->obtenerPorIdAdmin($id);

        if ($producto === null) {
            return Response::redirect(
                'admin/productos?status=notfound'
            );
        }

        $categorias = $this->categoriaService->obtenerActivas();

        return Response::view('admin/productos/admin-alta-producto', [
            'producto' => $producto,
            'categorias' => $categorias,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // CONFIRMAR ELIMINAR
    // ============================================================

    public function confirmarEliminar(int $id): Response
    {
        $producto = $this->productoService
            ->obtenerPorIdAdmin($id);

        if ($producto === null) {
            return Response::redirect(
                'admin/productos?status=notfound'
            );
        }

        return Response::view(
            'admin/productos/admin-confirmar-eliminar',
            [
                'producto' => $producto,
                'csrf_token' => $this->csrfToken()
            ]
        );
    }

    // ============================================================
    // CONFIRMAR REACTIVAR
    // ============================================================

    public function confirmarReactivar(int $id): Response
    {
        $producto = $this->productoService
            ->obtenerPorIdAdmin($id);

        if ($producto === null) {
            return Response::redirect(
                'admin/productos?status=notfound'
            );
        }

        return Response::view(
            'admin/productos/admin-confirmar-reactivar',
            [
                'producto' => $producto,
                'csrf_token' => $this->csrfToken()
            ]
        );
    }

    // ============================================================
    // DETALLE PRODUCTO
    // ============================================================

    public function detalle(int $id): Response
    {
        $producto = $this->productoService
            ->obtenerPorId($id);

        if ($producto === null) {
            return Response::redirect(
                'catalogo?error=producto_no_encontrado'
            );
        }

        return Response::view('public/detalle-producto', [
            'producto' => $producto,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // CATÁLOGO
    // ============================================================

    public function catalogo(Request $request): Response
    {
        $categoria = $request->get('categoria');

        $idCategoria = ctype_digit((string) $categoria)
            ? (int) $categoria
            : null;

        $orden = $request->get('orden', 'recientes');

        $pag = $request->get('pag');

        $pagina = ctype_digit((string) $pag)
            ? (int) $pag
            : 1;

        $porPagina = 8;

        $productos = $this->productoService
            ->obtenerProductosCatalogo(
                $idCategoria,
                $orden,
                $pagina,
                $porPagina
            );

        $totalProductos = $this->productoService
            ->contarProductosCatalogo($idCategoria);

        $totalPaginas = (int) ceil(
            $totalProductos / $porPagina
        );

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

    // ============================================================
    // LISTAR PRODUCTOS
    // ============================================================

    public function listar(): Response
    {
        $productos = $this->productoService
            ->obtenerProductosAdmin();

        return Response::view(
            'admin/productos/admin-listado-productos',
            [
                'productos' => $productos
            ]
        );
    }

    // ============================================================
    // CREAR PRODUCTO
    // ============================================================

    public function crear(Request $request): Response
    {
        $datos = $request->allPost();
        $archivos = $request->allFiles();

        if (empty($request->post('nombre'))) {
            return Response::redirect('admin/productos');
        }

        $errores = array_merge(
            $this->productoValidator->validar(
                $datos,
                esEdicion: false
            ),
            $this->productoService->validarCategoria($datos)
        );

        if (!empty($errores)) {
            $this->setSession('errores', $errores);

            return Response::redirect(
                'admin/productos/crear'
            );
        }

        try {
            $creado = $this->productoService->crear(
                $datos,
                $archivos
            );
        } catch (\RuntimeException $e) {
            $this->setFlash(
                'mensaje_error',
                $e->getMessage()
            );

            return Response::redirect(
                'admin/productos/crear'
            );
        }

        if ($creado) {
            $this->setFlash(
                'mensaje_exito',
                'Producto guardado con éxito.'
            );

            return Response::redirect(
                'admin/productos'
            );
        }

        $this->setFlash(
            'mensaje_error',
            'Error al guardar el producto.'
        );

        return Response::redirect(
            'admin/productos/crear'
        );
    }

    // ============================================================
    // ACTUALIZAR PRODUCTO
    // ============================================================

    public function actualizar(
        Request $request,
        int $id
    ): Response {
        $datos = $request->allPost();
        $archivos = $request->allFiles();

        $urlVuelta = 'admin/productos/editar/' . $id;

        if (empty($request->post('nombre'))) {
            return Response::redirect(
                'admin/productos'
            );
        }

        $errores = array_merge(
            $this->productoValidator->validar(
                $datos,
                esEdicion: true
            ),
            $this->productoService->validarCategoria($datos)
        );

        if (!empty($errores)) {
            $this->setSession('errores', $errores);

            return Response::redirect($urlVuelta);
        }

        try {
            $actualizado = $this->productoService->actualizar(
                $id,
                $datos,
                $archivos
            );
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
                'Producto actualizado con éxito.'
            );

            return Response::redirect(
                'admin/productos'
            );
        }

        $this->setFlash(
            'mensaje_error',
            'Error al actualizar el producto.'
        );

        return Response::redirect($urlVuelta);
    }

    // ============================================================
    // ELIMINAR PRODUCTO
    // ============================================================

    public function eliminar(int $id): Response
    {
        try {
            $eliminado = $this->productoService
                ->eliminarLogico($id);
        } catch (\RuntimeException $e) {
            $this->setFlash(
                'mensaje_error',
                $e->getMessage()
            );

            return Response::redirect(
                'admin/productos?status=error'
            );
        }

        if ($eliminado) {
            $this->setFlash(
                'mensaje_exito',
                'Producto eliminado correctamente.'
            );

            return Response::redirect(
                'admin/productos?status=deleted'
            );
        }

        $this->setFlash(
            'mensaje_error',
            'No se pudo eliminar el producto.'
        );

        return Response::redirect(
            'admin/productos?status=error'
        );
    }

    // ============================================================
    // REACTIVAR PRODUCTO
    // ============================================================

    public function reactivar(int $id): Response
    {
        try {
            $reactivado = $this->productoService
                ->reactivar($id);
        } catch (\RuntimeException $e) {
            $this->setFlash(
                'mensaje_error',
                $e->getMessage()
            );

            return Response::redirect(
                'admin/productos?status=error'
            );
        }

        if ($reactivado) {
            $this->setFlash(
                'mensaje_exito',
                'Producto reactivado correctamente.'
            );

            return Response::redirect(
                'admin/productos?status=reactivated'
            );
        }

        $this->setFlash(
            'mensaje_error',
            'No se pudo reactivar el producto. Por favor, inténtalo de nuevo.'
        );

        return Response::redirect(
            'admin/productos?status=error'
        );
    }
}