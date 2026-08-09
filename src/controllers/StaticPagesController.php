<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;

class StaticPagesController extends Controller {
    
    public function login(): void {
        $this->renderizar('public/login');
    }
    
    public function registro(): void {
        $this->renderizar('public/registro');
    }    
    
    public function sonoterapia(): void {
        $this->renderizar('public/sonoterapia');
    }
    
    public function sobreNosotros(): void {
        $this->renderizar('public/sobre-nosotros');
    }
    
    public function contacto(): void {
        $this->renderizar('public/contacto');
    }
}