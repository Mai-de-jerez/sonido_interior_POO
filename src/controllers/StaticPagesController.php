<?php
namespace SonidoInteriorPoo\controllers;

class StaticPagesController {
    
    public function login(): void {
        require __DIR__ . '/../views/public/login.php';
    }
    
    public function registro(): void {
        require __DIR__ . '/../views/public/registro.php';
    }    
    
    public function sonoterapia(): void {
        require __DIR__ . '/../views/public/sonoterapia.php';
    }
    
    public function sobreNosotros(): void {
        require __DIR__ . '/../views/public/sobre-nosotros.php';
    }
    
    public function contacto(): void {
        require __DIR__ . '/../views/public/contacto.php';
    }
}