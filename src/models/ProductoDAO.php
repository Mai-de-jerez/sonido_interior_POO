<?php
namespace SonidoInteriorPoo\models;

use SonidoInteriorPoo\interfaces\ProductoDAOInterface;
use SonidoInteriorPoo\dto\ProductoAdminDTO;
use SonidoInteriorPoo\dto\ProductoTarjetaDTO;
use SonidoInteriorPoo\dto\ProductoStockDTO;
use SonidoInteriorPoo\core\Conexion;

class ProductoDAO implements ProductoDAOInterface {
    private Conexion $conexion;

    public function __construct(Conexion $conexion) {
        $this->conexion = $conexion;
    }

    // Obtener todos los productos para el admin (con categoría)
    public function obtenerProductosAdmin(): array {
        $pdo = $this->conexion->getPdo();
        
        $sql = "SELECT p.id_producto, p.imagen, p.nombre, p.precio, p.stock, 
                    p.nota_musical, p.activo, c.nombre AS nombre_categoria
                FROM productos p
                INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                ORDER BY p.id_producto DESC";

        $filas = $pdo->query($sql)->fetchAll();

        return array_map(
            fn($fila) => new ProductoAdminDTO(
                (int) $fila['id_producto'],
                $fila['imagen'] ?? '',
                $fila['nombre'],
                $fila['nombre_categoria'] ?? '',
                (float) $fila['precio'],
                (int) $fila['stock'],
                $fila['nota_musical'],
                (int) $fila['activo']
            ),
            $filas
        );
    }

    // Obtener los últimos 4 productos para el inicio
    public function obtenerUltimosProductosInicio(): array {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT id_producto, imagen, nombre, precio
                FROM productos
                WHERE activo = 1
                ORDER BY id_producto DESC
                LIMIT 4";

        $filas = $pdo->query($sql)->fetchAll();

        return array_map(
            fn($fila) => new ProductoTarjetaDTO(
                (int) $fila['id_producto'],
                $fila['nombre'],
                (float) $fila['precio'],
                $fila['imagen'] ?? ''
            ),
            $filas
        );
    }

    // Obtener productos para el catálogo público
    public function obtenerProductosCatalogo(?int $idCategoria = null, string $orden = 'recientes', int $pagina = 1, int $porPagina = 12): array {
        $pdo = $this->conexion->getPdo();
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT id_producto, nombre, precio, imagen
                FROM productos
                WHERE activo = 1";

        $params = [];

        if ($idCategoria !== null) {
            $sql .= " AND id_categoria = ?";
            $params[] = $idCategoria;
        }

        switch ($orden) {
            case 'precio_asc':
                $sql .= " ORDER BY precio ASC";
                break;
            case 'precio_desc':
                $sql .= " ORDER BY precio DESC";
                break;
            default:
                $sql .= " ORDER BY id_producto DESC";
        }

        $sql .= " LIMIT " . (int) $porPagina . " OFFSET " . (int) $offset;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $filas = $stmt->fetchAll();

        return array_map(
            fn($fila) => new ProductoTarjetaDTO(
                (int) $fila['id_producto'],
                $fila['nombre'],
                (float) $fila['precio'],
                $fila['imagen'] ?? ''
            ),
            $filas
        );
    }

    // Contar productos activos para la paginación
    public function contarProductosCatalogo(?int $idCategoria = null): int {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT COUNT(*) AS total FROM productos WHERE activo = 1";
        $params = [];

        if ($idCategoria !== null) {
            $sql .= " AND id_categoria = ?";
            $params[] = $idCategoria;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $fila = $stmt->fetch();

        return (int) ($fila['total'] ?? 0);
    }

    // Contar el total absoluto de productos (activos e inactivos) para el admin
    public function contarTodosAdmin(): int {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT COUNT(*) AS total FROM productos";
        $stmt = $pdo->query($sql);
        $fila = $stmt->fetch();

        return (int) ($fila['total'] ?? 0);
    }

    // Contar el total de productos activos para el admin
    public function contarActivosAdmin(): int {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT COUNT(*) AS total FROM productos WHERE activo = 1";
        $stmt = $pdo->query($sql);
        $fila = $stmt->fetch();

        return (int) ($fila['total'] ?? 0);
    }

    // Obtener un producto activo por ID (público)
    public function obtenerPorId(int $idProducto): ?Producto {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.stock, p.imagen,
                       p.diametro, p.peso, p.material, p.nota_musical, p.procedencia,
                       p.id_categoria, p.activo, p.fecha_alta
                FROM productos p
                WHERE p.id_producto = ? AND p.activo = 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idProducto]);
        $fila = $stmt->fetch();

        return $fila ? Producto::fromArray($fila) : null;
    }

    // Obtener producto por ID sin importar si está activo (admin)
    public function obtenerPorIdAdmin(int $idProducto): ?Producto {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.stock, p.imagen,
                       p.diametro, p.peso, p.material, p.nota_musical, p.procedencia,
                       p.id_categoria, p.activo, p.fecha_alta
                FROM productos p
                WHERE p.id_producto = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idProducto]);
        $fila = $stmt->fetch();

        return $fila ? Producto::fromArray($fila) : null;
    }

    // Consulta y bloquea la fila del producto (FOR UPDATE)
    public function obtenerStockParaUpdate(int $idProducto): ?ProductoStockDTO {
        $pdo = $this->conexion->getPdo();
        $sql = "SELECT stock, nombre FROM productos WHERE id_producto = ? FOR UPDATE";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idProducto]);
        $fila = $stmt->fetch();

        if (!$fila) {
            return null;
        }

        return new ProductoStockDTO(
            (int) $fila['stock'],
            $fila['nombre']
        );
    }

    // Descuenta stock tras una compra; si llega a 0, desactiva el producto automáticamente
    public function descontarStock(int $idProducto, int $cantidad): bool {
        $pdo = $this->conexion->getPdo();
        $sql = "UPDATE productos
                SET stock = stock - ?,
                    activo = CASE WHEN stock - ? <= 0 THEN 0 ELSE activo END
                WHERE id_producto = ? AND stock >= ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$cantidad, $cantidad, $idProducto, $cantidad]);
    }

    // Insertar nuevo producto
    public function insertar(Producto $producto): void {
        $pdo = $this->conexion->getPdo();
        $sql = "INSERT INTO productos
                    (nombre, id_categoria, precio, stock, diametro, peso, material, procedencia, descripcion, imagen, nota_musical)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $producto->getNombre(),
            $producto->getIdCategoria(),
            $producto->getPrecio(),
            $producto->getStock(),
            $producto->getDiametro(),
            $producto->getPeso(),
            $producto->getMaterial(),
            $producto->getProcedencia(),
            $producto->getDescripcion(),
            $producto->getImagen(),
            $producto->getNotaMusical(),
        ]);
    }

    // Actualizar producto
    public function actualizar(Producto $producto): void {
        $pdo = $this->conexion->getPdo();
        $sql = "UPDATE productos SET
                    nombre = ?, id_categoria = ?, precio = ?, stock = ?, diametro = ?, peso = ?,
                    material = ?, procedencia = ?, descripcion = ?, imagen = ?, nota_musical = ?
                WHERE id_producto = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $producto->getNombre(),
            $producto->getIdCategoria(),
            $producto->getPrecio(),
            $producto->getStock(),
            $producto->getDiametro(),
            $producto->getPeso(),
            $producto->getMaterial(),
            $producto->getProcedencia(),
            $producto->getDescripcion(),
            $producto->getImagen(),
            $producto->getNotaMusical(),
            $producto->getIdProducto(),
        ]);
    }

    // Borrado lógico (activo = 0)
    public function eliminarLogico(int $idProducto): void {
        $pdo = $this->conexion->getPdo();
        $stmt = $pdo->prepare("UPDATE productos SET activo = 0 WHERE id_producto = ?");
        $stmt->execute([$idProducto]);
    }

    // Reactivar producto (activo = 1)
    public function reactivar(int $idProducto): void {
        $pdo = $this->conexion->getPdo();
        $stmt = $pdo->prepare("UPDATE productos SET activo = 1 WHERE id_producto = ?");
        $stmt->execute([$idProducto]);
    }
}


