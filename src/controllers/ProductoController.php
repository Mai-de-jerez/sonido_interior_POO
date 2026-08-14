<?php

namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;
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
    // HOME (ÚLTIMOS PRODUCTOS)
    // ============================================================
    public function home(): void
    {
        $productos = $this->productoService->obtenerUltimosProductosInicio();

        $this->renderizar('public/index', [
            'productos' => $productos
        ]);
    }

    // ============================================================
    // DASHBOARD (ADMIN)
    // ============================================================
    public function dashboard(): void
    {
        $this->renderizar('admin/dashboard', [
            'totalProductos' => $this->productoService->obtenerTotalProductosAdmin(),
            'totalActivos'   => $this->productoService->obtenerTotalActivosAdmin()
        ]);
    }

    // ============================================================
    // FORMULARIO NUEVO PRODUCTO
    // ============================================================
    public function nuevo(): void
    {
        $categorias = $this->categoriaService->obtenerActivas();

        $this->renderizar('admin/productos/admin-alta-producto', [
            'producto' => null,
            'categorias' => $categorias,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // FORMULARIO EDITAR PRODUCTO
    // ============================================================
    public function editar(int $id): void
    {
        $producto = $this->productoService->obtenerPorIdAdmin($id);

        if ($producto === null) {
            $this->redirigir('admin/productos?status=notfound');
            return;
        }

        $categorias = $this->categoriaService->obtenerActivas();

        $this->renderizar('admin/productos/admin-alta-producto', [
            'producto' => $producto,
            'categorias' => $categorias,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // CONFIRMAR ELIMINAR
    // ============================================================
    public function confirmarEliminar(int $id): void
    {
        $producto = $this->productoService->obtenerPorIdAdmin($id);

        if ($producto === null) {
            $this->redirigir('admin/productos?status=notfound');
            return;
        }

        $this->renderizar('admin/productos/admin-confirmar-eliminar', [
            'producto' => $producto,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // CONFIRMAR REACTIVAR
    // ============================================================
    public function confirmarReactivar(int $id): void
    {
        $producto = $this->productoService->obtenerPorIdAdmin($id);

        if ($producto === null) {
            $this->redirigir('admin/productos?status=notfound');
            return;
        }

        $this->renderizar('admin/productos/admin-confirmar-reactivar', [
            'producto' => $producto,
            'csrf_token' => $this->csrfToken()
        ]);
    }

    // ============================================================
    // DETALLE PRODUCTO (PÚBLICO)
    // ============================================================
    public function detalle(int $id): void
    {
        $producto = $this->productoService->obtenerPorId($id);

        if ($producto === null) {
            $this->redirigir('catalogo?error=producto_no_encontrado');
            return;
        }

        $this->renderizar('public/detalle-producto', [
            'producto' => $producto
        ]);
    }

    // ============================================================
    // CATÁLOGO (PÚBLICO)
    // ============================================================
    public function catalogo(): void
    {
        $idCategoria = isset($_GET['categoria']) && ctype_digit($_GET['categoria'])
            ? (int) $_GET['categoria']
            : null;

        $orden = $_GET['orden'] ?? 'recientes';

        $pagina = isset($_GET['pag']) && ctype_digit($_GET['pag'])
            ? (int) $_GET['pag']
            : 1;

        $porPagina = 8;

        $productos = $this->productoService->obtenerProductosCatalogo(
            $idCategoria,
            $orden,
            $pagina,
            $porPagina
        );

        $totalProductos = $this->productoService->contarProductosCatalogo(
            $idCategoria
        );

        $totalPaginas = (int) ceil($totalProductos / $porPagina);

        $categorias = $this->categoriaService->obtenerActivas();

        $this->renderizar('public/catalogo', [
            'productos' => $productos,
            'categorias' => $categorias,
            'idCategoria' => $idCategoria,
            'orden' => $orden,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalProductos' => $totalProductos
        ]);
    }

    // ============================================================
    // LISTAR PRODUCTOS (ADMIN)
    // ============================================================
    public function listar(): void
    {
        $productos = $this->productoService->obtenerProductosAdmin();

        $this->renderizar('admin/productos/admin-listado-productos', [
            'productos' => $productos
        ]);
    }

    // ============================================================
    // CREAR PRODUCTO (POST)
    // ============================================================
    public function crear(): void
    {
        // Validar CSRF
        if (!$this->validarCsrf()) {
            $this->setFlash(
                'mensaje_error',
                'Token de seguridad inválido. Por favor, recarga la página e inténtalo de nuevo.'
            );

            $this->redirigir('admin/productos/crear');
            return;
        }

        if (empty($_POST['nombre'])) {
            $this->redirigir('admin/productos');
            return;
        }

        $errores = array_merge(
            $this->productoValidator->validar($_POST, esEdicion: false),
            $this->productoService->validarCategoria($_POST)
        );

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->redirigir('admin/productos/crear');
            return;
        }

        try {
            $creado = $this->productoService->crear($_POST, $_FILES);
        } catch (\RuntimeException $e) {
            $this->setFlash('mensaje_error', $e->getMessage());
            $this->redirigir('admin/productos/crear');
            return;
        }

        if ($creado) {
            $this->setFlash(
                'mensaje_exito',
                'Producto guardado con éxito.'
            );

            $this->redirigir('admin/productos');
            return;
        }

        $this->setFlash(
            'mensaje_error',
            'Error al guardar el producto.'
        );

        $this->redirigir('admin/productos/crear');
    }

    // ============================================================
    // ACTUALIZAR PRODUCTO (POST)
    // ============================================================
    public function actualizar(int $id): void
    {
        // Validar CSRF
        if (!$this->validarCsrf()) {
            $this->setFlash(
                'mensaje_error',
                'Token de seguridad inválido. Por favor, recarga la página e inténtalo de nuevo.'
            );

            $this->redirigir('admin/productos/editar/' . $id);
            return;
        }

        if (empty($_POST['nombre'])) {
            $this->redirigir('admin/productos');
            return;
        }

        $urlVuelta = 'admin/productos/editar/' . $id;

        $errores = array_merge(
            $this->productoValidator->validar($_POST, esEdicion: true),
            $this->productoService->validarCategoria($_POST)
        );

        if (!empty($errores)) {
            $this->setSession('errores', $errores);
            $this->redirigir($urlVuelta);
            return;
        }

        try {
            $actualizado = $this->productoService->actualizar(
                $id,
                $_POST,
                $_FILES
            );
        } catch (\RuntimeException $e) {
            $this->setFlash(
                'mensaje_error',
                $e->getMessage()
            );

            $this->redirigir($urlVuelta);
            return;
        }

        if ($actualizado) {
            $this->setFlash(
                'mensaje_exito',
                'Producto actualizado con éxito.'
            );

            $this->redirigir('admin/productos');
            return;
        }

        $this->setFlash(
            'mensaje_error',
            'Error al actualizar el producto.'
        );

        $this->redirigir($urlVuelta);
    }

    // ============================================================
    // ELIMINAR PRODUCTO (POST)
    // ============================================================
    public function eliminar(int $id): void
    {
        // Validar CSRF
        if (!$this->validarCsrf()) {
            $this->setFlash(
                'mensaje_error',
                'Token de seguridad inválido.'
            );

            $this->redirigir('admin/productos');
            return;
        }

        try {
            $eliminado = $this->productoService->eliminarLogico($id);
        } catch (\RuntimeException $e) {
            $this->setFlash(
                'mensaje_error',
                $e->getMessage()
            );

            $this->redirigir('admin/productos?status=error');
            return;
        }

        if ($eliminado) {
            $this->setFlash(
                'mensaje_exito',
                'Producto eliminado correctamente.'
            );

            $this->redirigir('admin/productos?status=deleted');
            return;
        }

        $this->setFlash(
            'mensaje_error',
            'No se pudo eliminar el producto.'
        );

        $this->redirigir('admin/productos?status=error');
    }

    // ============================================================
    // REACTIVAR PRODUCTO (POST)
    // ============================================================
    public function reactivar(int $id): void
    {
        // Validar CSRF
        if (!$this->validarCsrf()) {
            $this->setFlash(
                'mensaje_error',
                'Token de seguridad inválido.'
            );

            $this->redirigir('admin/productos');
            return;
        }

        try {
            $reactivado = $this->productoService->reactivar($id);
        } catch (\RuntimeException $e) {
            $this->setFlash(
                'mensaje_error',
                $e->getMessage()
            );

            $this->redirigir('admin/productos?status=error');
            return;
        }

        if ($reactivado) {
            $this->setFlash(
                'mensaje_exito',
                'Producto reactivado correctamente.'
            );

            $this->redirigir('admin/productos?status=reactivated');
            return;
        }

        $this->setFlash(
            'mensaje_error',
            'No se pudo reactivar el producto. Por favor, inténtalo de nuevo.'
        );

        $this->redirigir('admin/productos?status=error');
    }
}