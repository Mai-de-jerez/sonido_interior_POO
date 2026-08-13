<?php
namespace SonidoInteriorPoo\core;

use PDO;
use PDOException;
use SonidoInteriorPoo\interfaces\TransactionManagerInterface;

class Conexion implements TransactionManagerInterface {
    private string $host;
    private string $db;
    private string $user;
    private string $pass;
    private ?PDO $pdo = null;

    public function __construct() {
        $this->host = DB_HOST;
        $this->db = DB_NAME;
        $this->user = DB_USER;
        $this->pass = DB_PASS;
    }

    public function getPdo(): PDO {
        if ($this->pdo === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";
                $this->pdo = new PDO($dsn, $this->user, $this->pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                throw new \Exception("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
        return $this->pdo;
    }

    public function transaction(callable $fn): mixed {
        $pdo = $this->getPdo();
        $pdo->beginTransaction();
        try {
            $resultado = $fn();
            $pdo->commit();
            return $resultado;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}