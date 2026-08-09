<?php
namespace SonidoInteriorPoo\services;


use SonidoInteriorPoo\models\Producto;
use SonidoInteriorPoo\models\ProductoDAO;
use SonidoInteriorPoo\models\CategoriaDAO;
use SonidoInteriorPoo\utils\ArchivosHelper;
use SonidoInteriorPoo\interfaces\ProductoServiceInterface;

class ProductoService implements ProductoServiceInterface {
    private ProductoDAO $productoDAO;
    private CategoriaDAO $categoriaDAO;

    public function __construct(ProductoDAO $productoDAO, CategoriaDAO $categoriaDAO) {
        $this->productoDAO = $productoDAO;
        $this->categoriaDAO = $categoriaDAO;
    }

    // ---------- VALIDACIÓN DE NEGOCIO (requiere BD) ----------
    public function validarCategoria(array $datos): array {
        $errores = [];
        $idCategoriaPost = $datos['id_categoria'] ?? '';

        $categoriasActivas = $this->categoriaDAO->obtenerActivas();
        $idsValidos = array_map(fn($c) => $c->getIdCategoria(), $categoriasActivas);

        if ($idCategoriaPost === '' || !in_array((int) $idCategoriaPost, $idsValidos)) {
            $errores['id_categoria'] = "Selecciona una categoría válida.";
        }

        return $errores;
    }

    // ---------- OBTENER PRODUCTOS ----------
    public function obtenerPorIdAdmin(int $idProducto): ?Producto {
        return $this->productoDAO->obtenerPorIdAdmin($idProducto);
    }

    public function obtenerPorId(int $idProducto): ?Producto {
        return $this->productoDAO->obtenerPorId($idProducto);
    }

    public function obtenerProductosAdmin(): array {
        return $this->productoDAO->obtenerProductosAdmin();
    }

    public function obtenerUltimosProductosInicio(): array {
        return $this->productoDAO->obtenerUltimosProductosInicio();
    }

    public function obtenerProductosCatalogo(?int $idCategoria = null, string $orden = 'recientes', int $pagina = 1, int $porPagina = 12): array {
        return $this->productoDAO->obtenerProductosCatalogo($idCategoria, $orden, $pagina, $porPagina);
    }

    public function contarProductosCatalogo(?int $idCategoria = null): int {
        return $this->productoDAO->contarProductosCatalogo($idCategoria);
    }

    // ---------- DASHBOARD / ESTADÍSTICAS ----------
    public function obtenerTotalProductosAdmin(): int {
        return $this->productoDAO->contarTodosAdmin();
    }

    public function obtenerTotalActivosAdmin(): int {
        return $this->productoDAO->contarActivosAdmin();
    }

    // ---------- CREAR ----------
    public function crear(array $datos, array $ficheros): bool {
        $imagen = ArchivosHelper::subirFoto($ficheros['imagen'], trim($datos['nombre']), 10000000);
        $nota = ArchivosHelper::subirMP3($ficheros['nota'], trim($datos['nombre']));

        if ($imagen === false || $nota === false) {
            throw new \RuntimeException("Error con el archivo de imagen o la melodía (formato no válido o peso superior al permitido).");
        }

        $diametro = trim($datos['diametro'] ?? '');

        $producto = new Producto(
            0,
            (int) $datos['id_categoria'],
            trim($datos['nombre']),
            trim($datos['descripcion']),
            (float) $datos['precio'],
            (int) $datos['stock'],
            $imagen,
            $diametro !== '' ? (float) $diametro : null,
            (float) $datos['peso'],
            trim($datos['material']),
            $nota,
            trim($datos['procedencia'])
        );

        return $this->productoDAO->insertar($producto);
    }

    // ---------- ACTUALIZAR ----------
    public function actualizar(int $idProducto, array $datos, array $ficheros): bool {
        $productoActual = $this->productoDAO->obtenerPorIdAdmin($idProducto);

        if ($productoActual === null) {
            throw new \RuntimeException("Producto no encontrado.");
        }

        if ($ficheros['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            $imagen = $productoActual->getImagen();
        } else {
            $imagen = ArchivosHelper::subirFoto($ficheros['imagen'], trim($datos['nombre']), 10000000);
            if ($imagen === false) {
                throw new \RuntimeException("Error con el archivo de imagen (formato no válido o peso superior al permitido).");
            }
        }

        if ($ficheros['nota']['error'] === UPLOAD_ERR_NO_FILE) {
            $nota = $productoActual->getNotaMusical();
        } else {
            $nota = ArchivosHelper::subirMP3($ficheros['nota'], trim($datos['nombre']));
            if ($nota === false) {
                throw new \RuntimeException("Error con el archivo de melodía (formato no válido o peso superior al permitido).");
            }
        }

        $diametro = trim($datos['diametro'] ?? '');

        $producto = new Producto(
            $idProducto,
            (int) $datos['id_categoria'],
            trim($datos['nombre']),
            trim($datos['descripcion']),
            (float) $datos['precio'],
            (int) $datos['stock'],
            $imagen,
            $diametro !== '' ? (float) $diametro : null,
            (float) $datos['peso'],
            trim($datos['material']),
            $nota,
            trim($datos['procedencia'])
        );

        return $this->productoDAO->actualizar($producto);
    }

    // ---------- ELIMINAR / REACTIVAR ----------
    public function eliminarLogico(int $idProducto): bool {
        $producto = $this->productoDAO->obtenerPorIdAdmin($idProducto);

        if ($producto === null) {
            throw new \RuntimeException("El producto no existe.");
        }

        return $this->productoDAO->eliminarLogico($idProducto);
    }

    public function reactivar(int $idProducto): bool {
        $producto = $this->productoDAO->obtenerPorIdAdmin($idProducto);

        if ($producto === null) {
            throw new \RuntimeException("El producto no existe.");
        }

        return $this->productoDAO->reactivar($idProducto);
    }
}