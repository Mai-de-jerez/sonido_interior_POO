<?php

use Dotenv\Dotenv;

// Cargar variables de entorno desde el archivo .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// URL base
define('BASE_URL', $_ENV['BASE_URL'] ?? '/sonido-interior-POO');

define(
    'SITE_URL',
    'http://' . $_SERVER['HTTP_HOST'] . BASE_URL
);

// Base de datos
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'sonido_interior');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// Entorno
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');

if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}