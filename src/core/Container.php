<?php

namespace SonidoInteriorPoo\core;

use SonidoInteriorPoo\core\Conexion;

use SonidoInteriorPoo\models\CategoriaDAO;
use SonidoInteriorPoo\models\ProductoDAO;
use SonidoInteriorPoo\models\UsuarioDAO;
use SonidoInteriorPoo\models\MensajeDAO;
use SonidoInteriorPoo\models\CarritoDAO;
use SonidoInteriorPoo\models\PedidoDAO;

use SonidoInteriorPoo\services\CategoriaService;
use SonidoInteriorPoo\services\ProductoService;
use SonidoInteriorPoo\services\UsuarioService;
use SonidoInteriorPoo\services\MensajeService;
use SonidoInteriorPoo\services\CarritoService;

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
    private Conexion $conexion;

    public function __construct()
    {
        $this->conexion = new Conexion();
    }

    public function getCategoriaController(): CategoriaController
    {
        $categoriaDAO = new CategoriaDAO($this->conexion);

        $categoriaService = new CategoriaService($categoriaDAO);

        $categoriaValidator = new CategoriaValidator();

        return new CategoriaController(
            $categoriaService,
            $categoriaValidator
        );
    }

    public function getProductoController(): ProductoController
    {
        $categoriaDAO = new CategoriaDAO($this->conexion);
        $productoDAO = new ProductoDAO($this->conexion);

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
        $usuarioDAO = new UsuarioDAO($this->conexion);

        $carritoDAO = new CarritoDAO($this->conexion);
        $productoDAO = new ProductoDAO($this->conexion);
        $pedidoDAO = new PedidoDAO($this->conexion);

        $carritoService = new CarritoService(
            $this->conexion,
            $carritoDAO,
            $productoDAO,
            $pedidoDAO
        );

        $usuarioService = new UsuarioService(
            $usuarioDAO,
            $carritoService
        );

        $usuarioValidator = new UsuarioValidator();

        return new UsuarioController(
            $usuarioService,
            $usuarioValidator    
        );
    }

    public function getStaticPagesController(): StaticPagesController
    {
        return new StaticPagesController();
    }

    public function getMensajeController(): MensajeController
    {
        $mensajeDAO = new MensajeDAO($this->conexion);

        $mensajeService = new MensajeService($mensajeDAO);

        $mensajeValidator = new MensajeValidator();

        return new MensajeController(
            $mensajeService,
            $mensajeValidator
        );
    }

    public function getCarritoController(): CarritoController
    {
        $carritoDAO = new CarritoDAO($this->conexion);
        $productoDAO = new ProductoDAO($this->conexion);
        $pedidoDAO = new PedidoDAO($this->conexion);

        $carritoService = new CarritoService(
            $this->conexion,
            $carritoDAO,
            $productoDAO,
            $pedidoDAO
        );

        $checkoutValidator = new CheckoutValidator();

        return new CarritoController(
            $carritoService,
            $checkoutValidator
        );
    }
}