<?php
namespace SonidoInteriorPoo\services;

use SonidoInteriorPoo\models\Producto;
use SonidoInteriorPoo\interfaces\ProductoDAOInterface;
use SonidoInteriorPoo\interfaces\CategoriaDAOInterface;
use SonidoInteriorPoo\interfaces\ProductoServiceInterface;
use SonidoInteriorPoo\utils\ArchivosHelper;
use SonidoInteriorPoo\exceptions\NotFoundException;
use SonidoInteriorPoo\exceptions\BusinessRuleException;

class ProductoService implements ProductoServiceInterface {
    private ProductoDAOInterface $productoDAO;
    private CategoriaDAOInterface $categoriaDAO;

    public function __construct(ProductoDAOInterface $productoDAO, CategoriaDAOInterface $categoriaDAO) {
        $this->productoDAO = $productoDAO;
        $this->categoriaDAO = $categoriaDAO;
    }

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
    
    // ============================================================
    // FUNCIONES DE LECTURA
    // ============================================================
    
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

    public function obtenerTotalProductosAdmin(): int {
        return $this->productoDAO->contarTodosAdmin();
    }

    public function obtenerTotalActivosAdmin(): int {
        return $this->productoDAO->contarActivosAdmin();
    }

    // ============================================================
    // FUNCIONES DE ESCRITURA (VOID + EXCEPCIONES)
    // ============================================================
    
    public function crear(array $datos, array $ficheros): void {
        $imagen = ArchivosHelper::subirFoto($ficheros['imagen'], trim($datos['nombre']), 10000000);
        $nota = ArchivosHelper::subirMP3($ficheros['nota'], trim($datos['nombre']));

        if ($imagen === false || $nota === false) {
            throw new BusinessRuleException("Error con el archivo de imagen o la melodía (formato no válido o peso superior al permitido).");
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

        if (!$this->productoDAO->insertar($producto)) {
            throw new BusinessRuleException("Error al guardar el producto en la base de datos.");
        }
    }

    public function actualizar(int $idProducto, array $datos, array $ficheros): void {
        $productoActual = $this->productoDAO->obtenerPorIdAdmin($idProducto);

        if ($productoActual === null) {
            throw new NotFoundException("Producto no encontrado.");
        }

        if ($ficheros['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            $imagen = $productoActual->getImagen();
        } else {
            $imagen = ArchivosHelper::subirFoto($ficheros['imagen'], trim($datos['nombre']), 10000000);
            if ($imagen === false) {
                throw new BusinessRuleException("Error con el archivo de imagen (formato no válido o peso superior al permitido).");
            }
        }

        if ($ficheros['nota']['error'] === UPLOAD_ERR_NO_FILE) {
            $nota = $productoActual->getNotaMusical();
        } else {
            $nota = ArchivosHelper::subirMP3($ficheros['nota'], trim($datos['nombre']));
            if ($nota === false) {
                throw new BusinessRuleException("Error con el archivo de melodía (formato no válido o peso superior al permitido).");
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

        if (!$this->productoDAO->actualizar($producto)) {
            throw new BusinessRuleException("Error al actualizar el producto en la base de datos.");
        }
    }

    public function eliminarLogico(int $idProducto): void {
        $producto = $this->productoDAO->obtenerPorIdAdmin($idProducto);

        if ($producto === null) {
            throw new NotFoundException("El producto no existe.");
        }

        if (!$this->productoDAO->eliminarLogico($idProducto)) {
            throw new BusinessRuleException("No se pudo eliminar el producto.");
        }
    }

    public function reactivar(int $idProducto): void {
        $producto = $this->productoDAO->obtenerPorIdAdmin($idProducto);

        if ($producto === null) {
            throw new NotFoundException("El producto no existe.");
        }

        if (!$this->productoDAO->reactivar($idProducto)) {
            throw new BusinessRuleException("No se pudo reactivar el producto.");
        }
    }
}