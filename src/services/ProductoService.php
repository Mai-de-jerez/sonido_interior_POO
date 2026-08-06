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

    // ---------- VALIDACIÓN ----------
    public function validar(array $datos, bool $esEdicion): array {
        $errores = [];

        $nombre = trim($datos['nombre'] ?? '');
        $idCategoriaPost = $datos['id_categoria'] ?? '';
        $precio = trim($datos['precio'] ?? '');
        $stock = trim($datos['stock'] ?? '');
        $diametro = trim($datos['diametro'] ?? '');
        $peso = trim($datos['peso'] ?? '');
        $material = trim($datos['material'] ?? '');
        $procedencia = trim($datos['procedencia'] ?? '');
        $descripcion = trim($datos['descripcion'] ?? '');

        if ($nombre === '') {
            $errores['nombre'] = "El nombre es obligatorio.";
        } elseif (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 50) {
            $errores['nombre'] = "El nombre debe tener entre 3 y 50 caracteres.";
        }

        $categoriasActivas = $this->categoriaDAO->obtenerActivas();
        $idsValidos = array_map(fn($c) => $c->getIdCategoria(), $categoriasActivas);
        if ($idCategoriaPost === '' || !in_array((int) $idCategoriaPost, $idsValidos)) {
            $errores['id_categoria'] = "Selecciona una categoría válida.";
        }

        if ($precio === '') {
            $errores['precio'] = "El precio es obligatorio.";
        } elseif (!is_numeric($precio) || $precio <= 0 || $precio > 2000) {
            $errores['precio'] = "El precio debe ser mayor que 0 y no superar 2000€.";
        }

        if ($stock === '') {
            $errores['stock'] = "El stock es obligatorio.";
        } elseif (!ctype_digit($stock) || (int) $stock <= 0 || (int) $stock > 10000) {
            $errores['stock'] = "El stock debe ser mayor que 0 y no superar 10000 unidades.";
        }

        if ($peso === '') {
            $errores['peso'] = "El peso es obligatorio.";
        } elseif (!is_numeric($peso) || $peso <= 0 || $peso > 10000) {
            $errores['peso'] = "El peso debe ser mayor que 0 y no superar 10000g.";
        }

        if ($diametro !== '' && (!is_numeric($diametro) || $diametro <= 0 || $diametro > 100)) {
            $errores['diametro'] = "El diámetro debe ser mayor que 0 y no superar 100cm.";
        }

        if ($material === '') {
            $errores['material'] = "El material es obligatorio.";
        } elseif (mb_strlen($material) < 3 || mb_strlen($material) > 50) {
            $errores['material'] = "El material debe tener entre 3 y 50 caracteres.";
        } elseif (!preg_match('/^[A-Za-zÀ-ÿñÑ0-9\s\-]+$/u', $material)) {
            $errores['material'] = "El material contiene caracteres no válidos.";
        }

        if ($procedencia === '') {
            $errores['procedencia'] = "La procedencia es obligatoria.";
        } elseif (mb_strlen($procedencia) < 3 || mb_strlen($procedencia) > 50) {
            $errores['procedencia'] = "La procedencia debe tener entre 3 y 50 caracteres.";
        } elseif (!preg_match('/^[A-Za-zÀ-ÿñÑ0-9\s\-]+$/u', $procedencia)) {
            $errores['procedencia'] = "La procedencia contiene caracteres no válidos.";
        }

        if ($descripcion === '') {
            $errores['descripcion'] = "La descripción es obligatoria.";
        } elseif (mb_strlen($descripcion) < 15 || mb_strlen($descripcion) > 300) {
            $errores['descripcion'] = "La descripción debe tener entre 15 y 300 caracteres.";
        }

        if (!$esEdicion && (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE)) {
            $errores['imagen'] = "La imagen es obligatoria.";
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