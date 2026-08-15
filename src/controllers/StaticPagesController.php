<?php

namespace SonidoInteriorPoo\controllers;

use SonidoInteriorPoo\core\Controller;
use SonidoInteriorPoo\core\Response;

class StaticPagesController extends Controller
{
    public function login(): Response
    {
        return Response::view('public/login', [
            'csrf_token' => $this->csrfToken()
        ]);
    }

    public function registro(): Response
    {
        return Response::view('public/registro', [
            'csrf_token' => $this->csrfToken()
        ]);
    }

    public function sonoterapia(): Response
    {
        return Response::view('public/sonoterapia');
    }

    public function sobreNosotros(): Response
    {
        return Response::view('public/sobre-nosotros');
    }

    public function contacto(): Response
    {
        return Response::view('public/contacto', [
            'csrf_token' => $this->csrfToken()
        ]);
    }
}