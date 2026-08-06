<?php
namespace SonidoInteriorPoo\models;

use PDO;
use PDOException;

class Conexion {
    private string $host;
    private string $db;
    private string $user;
    private string $pass;
    private ?PDO $pdo = null;

    public function __construct(
        string $host = '', 
        string $db = 'sonido_interior', 
        string $user = 'root', 
        string $pass = ''
    ) {
        // Si no le pasamos los datos, tira de tus variables de entorno o valores por defecto
        $this->host = $host !== '' ? $host : (getenv('DB_HOST') ?: '127.0.0.1');
        $this->db = $db;
        $this->user = $user;
        $this->pass = $pass !== '' ? $pass : (getenv('PASSWORD_DB') ?: '');
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
}