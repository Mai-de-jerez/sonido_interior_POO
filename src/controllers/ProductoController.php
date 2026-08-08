<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\interfaces\ProductoServiceInterface;
use SonidoInteriorPoo\interfaces\CategoriaServiceInterface;
use SonidoInteriorPoo\middleware\AuthMiddleware;

class ProductoController {
    private ProductoServiceInterface $productoService;
    private CategoriaServiceInterface $categoriaService;

    public function __construct(
        ProductoServiceInterface $productoService,
        CategoriaServiceInterface $categoriaService
    ) {
        $this->productoService = $productoService;
        $this->categoriaService = $categoriaService;
    }

    // listar en la home últimos productos
    public function home(): void {
        $productos = $this->productoService->obtenerUltimosProductosInicio();
        $data = ['productos' => $productos];
        $pagina = "inicio";
        
        extract($data);
        require_once __DIR__ . '/../views/public/index.php';
    }

    // dashboard con estadísticas para la zona admin
    public function dashboard(): void {
        AuthMiddleware::verificarAdmin();
        
        $productos = $this->productoService->obtenerProductosAdmin();
        $totalProductos = count($productos);
        $totalActivos = count(array_filter($productos, fn($dto) => $dto->getProducto()->isActivo()));
        
        $categorias = $this->categoriaService->obtenerTodasAdmin();
        $totalCategorias = count($categorias);
        $categoriasActivas = count(array_filter($categorias, fn($c) => $c->isActivo()));
        
        $data = [
            'totalProductos' => $totalProductos,
            'totalActivos' => $totalActivos,
            'totalCategorias' => $totalCategorias,
            'categoriasActivas' => $categoriasActivas,
            'ultimosProductos' => array_slice($productos, 0, 5) 
        ];
        
        extract($data);
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    // ============================================================
    // MÉTODOS QUE MUESTRAN FORMULARIOS (GET) - CON SEGURIDAD
    // ============================================================

    public function nuevo(): void {
        AuthMiddleware::verificarAdmin();

        $categorias = $this->categoriaService->obtenerActivas();
        $data = [
            'producto' => null,
            'categorias' => $categorias
        ];

        extract($data);
        require_once __DIR__ . '/../views/admin/productos/admin-alta-producto.php';
    }

    public function editar(): void {
        AuthMiddleware::verificarAdmin();

        $idProducto = isset($_GET['id']) && ctype_digit($_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($idProducto === 0) {
            header("Location: " . BASE_URL . "/admin/productos?status=notfound");
            exit();
        }

        $producto = $this->productoService->obtenerPorIdAdmin($idProducto);
        $categorias = $this->categoriaService->obtenerActivas();

        if ($producto === null) {
            header("Location: " . BASE_URL . "/admin/productos?status=notfound");
            exit();
        }

        $data = [
            'producto' => $producto,
            'categorias' => $categorias
        ];

        extract($data);
        require_once __DIR__ . '/../views/admin/productos/admin-alta-producto.php';
    }

    public function confirmarEliminar(): void {
        AuthMiddleware::verificarAdmin();

        $idProducto = isset($_GET['id']) && ctype_digit($_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($idProducto === 0) {
            header("Location: " . BASE_URL . "/admin/productos?status=notfound");
            exit();
        }

        $producto = $this->productoService->obtenerPorIdAdmin($idProducto);

        if ($producto === null) {
            header("Location: " . BASE_URL . "/admin/productos?status=notfound");
            exit();
        }

        $data = ['producto' => $producto];
        extract($data);
        require_once __DIR__ . '/../views/admin/productos/admin-confirmar-eliminar.php';
    }

    public function confirmarReactivar(): void {
        AuthMiddleware::verificarAdmin();

        $idProducto = isset($_GET['id']) && ctype_digit($_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($idProducto === 0) {
            header("Location: " . BASE_URL . "/admin/productos?status=notfound");
            exit();
        }

        $producto = $this->productoService->obtenerPorIdAdmin($idProducto);

        if ($producto === null) {
            header("Location: " . BASE_URL . "/admin/productos?status=notfound");
            exit();
        }

        $data = ['producto' => $producto];
        extract($data);
        require_once __DIR__ . '/../views/admin/productos/admin-confirmar-reactivar.php';
    }

    public function detalle(): void {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id === 0) {
            header("Location: " . BASE_URL . "/catalogo?error=producto_no_encontrado");
            exit();
        }

        $producto = $this->productoService->obtenerPorId($id);

        if ($producto === null) {
            header("Location: " . BASE_URL . "/catalogo?error=producto_no_encontrado");
            exit();
        }

        $data = ['producto' => $producto];
        extract($data);
        require_once __DIR__ . '/../views/public/detalle-producto.php';
    }

    // ============================================================
    // MÉTODOS PÚBLICOS (CATÁLOGO) - SIN SEGURIDAD
    // ============================================================

    public function catalogo(): void {
        $idCategoria = isset($_GET['categoria']) && ctype_digit($_GET['categoria']) 
            ? (int) $_GET['categoria'] 
            : null;
        
        $orden = $_GET['orden'] ?? 'recientes';
        $pagina = isset($_GET['pag']) && ctype_digit($_GET['pag']) 
            ? (int) $_GET['pag'] 
            : 1;
        $porPagina = 8;
        
        $productos = $this->productoService->obtenerProductosCatalogo($idCategoria, $orden, $pagina, $porPagina);
        $totalProductos = $this->productoService->contarProductosCatalogo($idCategoria);        
        $totalPaginas = (int) ceil($totalProductos / $porPagina);
        $categorias = $this->categoriaService->obtenerActivas();
        
        $data = [
            'productos' => $productos,
            'categorias' => $categorias,
            'idCategoria' => $idCategoria,
            'orden' => $orden,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalProductos' => $totalProductos
        ];
        
        $pagina = "catalogo";  
        extract($data);
        require_once __DIR__ . '/../views/public/catalogo.php';
    }

    // ============================================================
    // MÉTODOS ADMIN (LISTADO) - CON SEGURIDAD
    // ============================================================

    public function listar(): void {
        AuthMiddleware::verificarAdmin();
        
        $productos = $this->productoService->obtenerProductosAdmin();
        $data = ['productos' => $productos];
        $paginaAdmin = "productos";
        
        extract($data);
        require_once __DIR__ . '/../views/admin/productos/admin-listado-productos.php';
    }

    // ============================================================
    // MÉTODOS QUE PROCESAN FORMULARIOS (POST) - CON SEGURIDAD
    // ============================================================

    public function crear(): void {
        AuthMiddleware::verificarAdmin();

        if (empty($_POST['nombre'])) {
            header("Location: " . BASE_URL . "/admin/productos");
            exit();
        }

        $errores = $this->productoService->validar($_POST, esEdicion: false);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            header("Location: " . BASE_URL . "/admin/productos/crear");
            exit();
        }

        try {
            $creado = $this->productoService->crear($_POST, $_FILES);
        } catch (\RuntimeException $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            header("Location: " . BASE_URL . "/admin/productos/crear");
            exit();
        }

        if ($creado) {
            $_SESSION['mensaje_exito'] = "Producto guardado con éxito.";
            header("Location: " . BASE_URL . "/admin/productos");
        } else {
            $_SESSION['mensaje_error'] = "Error al guardar el producto.";
            header("Location: " . BASE_URL . "/admin/productos/crear");
        }
        exit();
    }

    public function actualizar(): void {
        AuthMiddleware::verificarAdmin();

        $idProducto = (isset($_POST['id_producto']) && ctype_digit($_POST['id_producto']))
            ? (int) $_POST['id_producto']
            : null;

        if ($idProducto === null || empty($_POST['nombre'])) {
            header("Location: " . BASE_URL . "/admin/productos");
            exit();
        }

        $urlVuelta = BASE_URL . "/admin/productos/editar?id=" . $idProducto;

        $errores = $this->productoService->validar($_POST, esEdicion: true);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            header("Location: " . $urlVuelta);
            exit();
        }

        try {
            $actualizado = $this->productoService->actualizar($idProducto, $_POST, $_FILES);
        } catch (\RuntimeException $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            header("Location: " . $urlVuelta);
            exit();
        }

        if ($actualizado) {
            $_SESSION['mensaje_exito'] = "Producto actualizado con éxito.";
            header("Location: " . BASE_URL . "/admin/productos");
        } else {
            $_SESSION['mensaje_error'] = "Error al actualizar el producto.";
            header("Location: " . $urlVuelta);
        }
        exit();
    }

    public function eliminar(): void {
        AuthMiddleware::verificarAdmin();

        $idProducto = (isset($_POST['id_producto']) && ctype_digit($_POST['id_producto']))
            ? (int) $_POST['id_producto']
            : null;

        if ($idProducto === null) {
            header("Location: " . BASE_URL . "/admin/productos");
            exit();
        }

        try {
            $eliminado = $this->productoService->eliminarLogico($idProducto);
        } catch (\RuntimeException $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            header("Location: " . BASE_URL . "/admin/productos?status=error");
            exit();
        }

        if ($eliminado) {
            $_SESSION['mensaje_exito'] = "Producto eliminado correctamente.";
            header("Location: " . BASE_URL . "/admin/productos?status=deleted");
        } else {
            $_SESSION['mensaje_error'] = "No se pudo eliminar el producto.";
            header("Location: " . BASE_URL . "/admin/productos?status=error");
        }
        exit();
    }

    public function reactivar(): void {
        AuthMiddleware::verificarAdmin();

        $idProducto = (isset($_POST['id_producto']) && ctype_digit($_POST['id_producto']))
            ? (int) $_POST['id_producto']
            : null;

        if ($idProducto === null) {
            header("Location: " . BASE_URL . "/admin/productos");
            exit();
        }

        try {
            $reactivado = $this->productoService->reactivar($idProducto);
        } catch (\RuntimeException $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            header("Location: " . BASE_URL . "/admin/productos?status=error");
            exit();
        }

        if ($reactivado) {
            $_SESSION['mensaje_exito'] = "Producto reactivado correctamente.";
            header("Location: " . BASE_URL . "/admin/productos?status=reactivated");
        } else {
            $_SESSION['mensaje_error'] = "No se pudo reactivar el producto. Por favor, inténtalo de nuevo.";
            header("Location: " . BASE_URL . "/admin/productos?status=error");
        }
        exit();
    }
}