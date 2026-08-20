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

    public function crear(array $datos, array $ficheros): void {
        $nombre = trim($datos['nombre']);

        $imagen = ArchivosHelper::subirFoto($ficheros['imagen'], $nombre, 10000000);
        $nota = ArchivosHelper::subirMP3($ficheros['nota'], $nombre);

        if ($imagen === false || $nota === false) {
            // Si una subida falla pero la otra funcionó, limpiamos el archivo subido
            if ($imagen !== false) $this->borrarImagen($imagen);
            if ($nota !== false && $nota !== null) $this->borrarAudio($nota);

            throw new BusinessRuleException("Error con el archivo de imagen o la melodía (formato no válido o peso superior al permitido).");
        }

        $diametro = trim($datos['diametro'] ?? '');

        $producto = new Producto(
            0,
            (int) $datos['id_categoria'],
            $nombre,
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

        try {
            $this->productoDAO->insertar($producto);
        } catch (\Throwable $e) {
            // Si la BBDD falla, no dejamos huérfanos
            $this->borrarImagen($imagen);
            $this->borrarAudio($nota);
            throw $e;
        }
    }

    public function actualizar(int $idProducto, array $datos, array $ficheros): void {
        $productoActual = $this->productoDAO->obtenerPorIdAdmin($idProducto);

        if ($productoActual === null) {
            throw new NotFoundException("Producto no encontrado.");
        }

        $nombre = trim($datos['nombre']);
        $nuevaImagen = null;
        $nuevaNota = null;

        if (isset($ficheros['imagen']) && $ficheros['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $nuevaImagen = ArchivosHelper::subirFoto($ficheros['imagen'], $nombre, 10000000);
            if ($nuevaImagen === false) {
                throw new BusinessRuleException("Error con el archivo de imagen (formato no válido o peso superior al permitido).");
            }
        }

        if (isset($ficheros['nota']) && $ficheros['nota']['error'] !== UPLOAD_ERR_NO_FILE) {
            $nuevaNota = ArchivosHelper::subirMP3($ficheros['nota'], $nombre);
            if ($nuevaNota === false) {
                if ($nuevaImagen !== null) $this->borrarImagen($nuevaImagen);
                throw new BusinessRuleException("Error con el archivo de melodía (formato no válido o peso superior al permitido).");
            }
        }

        $imagenFinal = $nuevaImagen ?? $productoActual->getImagen();
        $notaFinal = $nuevaNota ?? $productoActual->getNotaMusical();
        $diametro = trim($datos['diametro'] ?? '');

        $producto = new Producto(
            $idProducto,
            (int) $datos['id_categoria'],
            $nombre,
            trim($datos['descripcion']),
            (float) $datos['precio'],
            (int) $datos['stock'],
            $imagenFinal,
            $diametro !== '' ? (float) $diametro : null,
            (float) $datos['peso'],
            trim($datos['material']),
            $notaFinal,
            trim($datos['procedencia'])
        );

        try {
            $this->productoDAO->actualizar($producto);

            // Si la BBDD actualiza bien y se subieron archivos nuevos, borramos los antiguos
            if ($nuevaImagen !== null) {
                $this->borrarImagen($productoActual->getImagen());
            }
            if ($nuevaNota !== null) {
                $this->borrarAudio($productoActual->getNotaMusical());
            }
        } catch (\Throwable $e) {
            // Si falla la BBDD, deshacemos las subidas nuevas
            if ($nuevaImagen !== null) $this->borrarImagen($nuevaImagen);
            if ($nuevaNota !== null) $this->borrarAudio($nuevaNota);
            throw $e;
        }
    }

    public function eliminarLogico(int $idProducto): void {
        $producto = $this->productoDAO->obtenerPorIdAdmin($idProducto);

        if ($producto === null) {
            throw new NotFoundException("El producto no existe.");
        }

        $this->productoDAO->eliminarLogico($idProducto);
    }

    public function reactivar(int $idProducto): void {
        $producto = $this->productoDAO->obtenerPorIdAdmin($idProducto);

        if ($producto === null) {
            throw new NotFoundException("El producto no existe.");
        }

        $this->productoDAO->reactivar($idProducto);
    }

    // Metodos privados
    private function borrarImagen(?string $nombreArchivo): void {
        if ($nombreArchivo !== null && $nombreArchivo !== '') {
            $ruta = __DIR__ . '/../../public/img/productos/' . $nombreArchivo;
            if (file_exists($ruta)) {
                @unlink($ruta);
            }
        }
    }

    private function borrarAudio(?string $nombreArchivo): void {
        if ($nombreArchivo !== null && $nombreArchivo !== '') {
            $ruta = __DIR__ . '/../../public/sonidos/' . $nombreArchivo;
            if (file_exists($ruta)) {
                @unlink($ruta);
            }
        }
    }
}