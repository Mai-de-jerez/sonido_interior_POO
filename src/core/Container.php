<?php

namespace SonidoInteriorPoo\core;

use SonidoInteriorPoo\core\Conexion;

use SonidoInteriorPoo\models\CategoriaDAO;
use SonidoInteriorPoo\models\ProductoDAO;
use SonidoInteriorPoo\models\UsuarioDAO;
use SonidoInteriorPoo\models\MensajeDAO;
use SonidoInteriorPoo\models\CarritoDAO;
use SonidoInteriorPoo\models\PedidoDAO;
use SonidoInteriorPoo\models\PasswordResetDAO;

use SonidoInteriorPoo\services\CategoriaService;
use SonidoInteriorPoo\services\ProductoService;
use SonidoInteriorPoo\services\UsuarioService;
use SonidoInteriorPoo\services\PasswordResetService;
use SonidoInteriorPoo\services\MensajeService;
use SonidoInteriorPoo\services\CarritoService;
use SonidoInteriorPoo\services\CheckoutService;
use SonidoInteriorPoo\services\PedidoService;

use SonidoInteriorPoo\controllers\CategoriaController;
use SonidoInteriorPoo\controllers\ProductoController;
use SonidoInteriorPoo\controllers\UsuarioController;
use SonidoInteriorPoo\controllers\StaticPagesController;
use SonidoInteriorPoo\controllers\MensajeController;
use SonidoInteriorPoo\controllers\CarritoController;

use SonidoInteriorPoo\validators\CheckoutValidator;
use SonidoInteriorPoo\validators\CategoriaValidator;
use SonidoInteriorPoo\validators\MensajeValidator;
use SonidoInteriorPoo\validators\UsuarioValidator;
use SonidoInteriorPoo\validators\ProductoValidator;

class Container
{
    private ?Conexion $conexion = null;

    // Conexión diferida (Lazy Connection)
    private function getConexion(): Conexion
    {
        if ($this->conexion === null) {
            $this->conexion = new Conexion();
        }
        return $this->conexion;
    }

    // Método que llama el Router para obtener la instancia bajo demanda
    public function get(string $class): object
    {
        return match ($class) {
            CategoriaController::class => $this->getCategoriaController(),
            ProductoController::class => $this->getProductoController(),
            UsuarioController::class => $this->getUsuarioController(),
            StaticPagesController::class => $this->getStaticPagesController(),
            MensajeController::class => $this->getMensajeController(),
            CarritoController::class => $this->getCarritoController(),
            default => throw new \InvalidArgumentException("Controlador no registrado en el contenedor: $class"),
        };
    }

    public function getCategoriaController(): CategoriaController
    {
        $categoriaDAO = new CategoriaDAO($this->getConexion());

        $categoriaService = new CategoriaService($categoriaDAO);

        $categoriaValidator = new CategoriaValidator();

        return new CategoriaController(
            $categoriaService,
            $categoriaValidator
        );
    }

    public function getProductoController(): ProductoController
    {
        $categoriaDAO = new CategoriaDAO($this->getConexion());
        $productoDAO = new ProductoDAO($this->getConexion());

        $categoriaService = new CategoriaService($categoriaDAO);
        
        $productoService = new ProductoService(
            $productoDAO,
            $categoriaDAO
        );

        $productoValidator = new ProductoValidator();

        return new ProductoController(
            $productoService,
            $categoriaService,
            $productoValidator
        );
    } 


    public function getUsuarioController(): UsuarioController
    {
        $usuarioDAO = new UsuarioDAO($this->getConexion());
        $carritoDAO = new CarritoDAO($this->getConexion());
        $productoDAO = new ProductoDAO($this->getConexion());
        $passwordResetDAO = new PasswordResetDAO($this->getConexion());

        $carritoService = new CarritoService(
            $carritoDAO,
            $productoDAO
        );

        $usuarioService = new UsuarioService(
            $usuarioDAO,
            $carritoService
        );

        $passwordResetService = new PasswordResetService(
            $usuarioDAO,
            $passwordResetDAO,
            $this->getConexion()
        );

        $usuarioValidator = new UsuarioValidator();

        return new UsuarioController(
            $usuarioService,
            $passwordResetService,
            $usuarioValidator
        );
    }

    public function getStaticPagesController(): StaticPagesController
    {
        return new StaticPagesController();
    }

    public function getMensajeController(): MensajeController
    {
        $mensajeDAO = new MensajeDAO($this->getConexion());

        $mensajeService = new MensajeService($mensajeDAO);

        $mensajeValidator = new MensajeValidator();

        return new MensajeController(
            $mensajeService,
            $mensajeValidator
        );
    }

    public function getCarritoController(): CarritoController
    {
        $carritoDAO = new CarritoDAO($this->getConexion());
        $productoDAO = new ProductoDAO($this->getConexion());
        $pedidoDAO = new PedidoDAO($this->getConexion());

        $carritoService = new CarritoService(
            $carritoDAO,
            $productoDAO
        );

        $pedidoService = new PedidoService(
            $pedidoDAO
        );

        $checkoutService = new CheckoutService(
            $this->getConexion(), 
            $carritoDAO,
            $productoDAO,
            $pedidoService
        );

        $checkoutValidator = new CheckoutValidator();

        return new CarritoController(
            $carritoService,
            $checkoutService,
            $checkoutValidator
        );
    }
}