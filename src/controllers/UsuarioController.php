<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\middleware\AuthMiddleware;
use SonidoInteriorPoo\interfaces\ProductoServiceInterface;
use SonidoInteriorPoo\interfaces\CategoriaServiceInterface;
use SonidoInteriorPoo\interfaces\UsuarioServiceInterface;

class UsuarioController {

    private ProductoServiceInterface $productoService;
    private CategoriaServiceInterface $categoriaService;
    private UsuarioServiceInterface $usuarioService;

    public function __construct(
        UsuarioServiceInterface $usuarioService,     
        ProductoServiceInterface $productoService,   
        CategoriaServiceInterface $categoriaService  
    ) {
        $this->usuarioService = $usuarioService;
        $this->productoService = $productoService;
        $this->categoriaService = $categoriaService;
    }

    //=====================================
    // DASHBOARD
    //=====================================
    public function dashboard(): void {
        AuthMiddleware::verificarAdmin();
        
        $productos = $this->productoService->obtenerProductosAdmin();
        $totalProductos = count($productos);
        $totalActivos = count(array_filter($productos, fn($p) => $p->isActivo()));
        
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
    // PROCESAR LOGIN
    // ============================================================
    public function procesarLogin(): void {
        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validar campos vacíos
        $errores = [];
        if ($usuario === '') {
            $errores['usuario'] = "Introduce tu usuario.";
        }
        if ($password === '') {
            $errores['password'] = "Introduce tu contraseña.";
        }

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['form_old'] = ['usuario' => $usuario];
            header("Location: /login");
            exit();
        }

        // Intentar login
        $usuarioData = $this->usuarioService->login($usuario, $password);

        if (!$usuarioData) {
            $_SESSION['mensaje_error'] = "Usuario o contraseña incorrectos.";
            header("Location: /login");
            exit();
        }

        // Limpiar sesión y regenerar ID
        session_unset();
        session_regenerate_id(true);

        $_SESSION['id_usuario'] = $usuarioData['id_usuario'];
        $_SESSION['usuario'] = $usuarioData['usuario'];
        $_SESSION['rol'] = $usuarioData['rol'];

        // Redirigir según rol
        if ($usuarioData['rol'] === 'ADMIN') {
            header("Location: admin/dashboard");
        } else {
            header("Location: /");
        }
        exit();
    }

    // ============================================================
    // PROCESAR REGISTRO
    // ============================================================
    public function procesarRegistro(): void {
        $usuario = trim($_POST['usuario'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        $errores = [];

        if ($usuario === '') {
            $errores['usuario'] = "El usuario es obligatorio.";
        } elseif (strlen($usuario) < 3 || strlen($usuario) > 50) {
            $errores['usuario'] = "El usuario debe tener entre 3 y 50 caracteres.";
        }

        if ($email === '') {
            $errores['email'] = "El email es obligatorio.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = "El email no es válido.";
        }

        if ($password === '') {
            $errores['password'] = "La contraseña es obligatoria.";
        } elseif (strlen($password) < 8) {
            $errores['password'] = "La contraseña debe tener al menos 8 caracteres.";
        }

        if ($password !== $passwordConfirm) {
            $errores['password_confirm'] = "Las contraseñas no coinciden.";
        }

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['form_old'] = ['usuario' => $usuario, 'email' => $email];
            header("Location: /registro");
            exit();
        }

        $registrado = $this->usuarioService->registrar($usuario, $email, $password);

        if (!$registrado) {
            $_SESSION['mensaje_error'] = "El usuario o email ya existe.";
            $_SESSION['form_old'] = ['usuario' => $usuario, 'email' => $email];
            header("Location: /registro");
            exit();
        }

        $_SESSION['mensaje_exito'] = "Usuario registrado con éxito. Ahora puedes iniciar sesión.";
        header("Location: /login");
        exit();
    }

    // ============================================================
    // CERRAR SESIÓN
    // ============================================================
    public function logout(): void {
        session_unset();
        session_destroy();
        header("Location: /");
        exit();
    }
}