<?php
namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;

class StaticPagesController extends Controller {
    
    public function login(): void {
        $this->renderizar('public/login', [
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function registro(): void {
        $this->renderizar('public/registro', [
            'csrf_token' => $this->csrfToken()
        ]);
    }    
    
    public function sonoterapia(): void {
        $this->renderizar('public/sonoterapia');
    }
    
    public function sobreNosotros(): void {
        $this->renderizar('public/sobre-nosotros');
    }
    
    public function contacto(): void {
        $this->renderizar('public/contacto', [
            'csrf_token' => $this->csrfToken()
        ]);
    }
}