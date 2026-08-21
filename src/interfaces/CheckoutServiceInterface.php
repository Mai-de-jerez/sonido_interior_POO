<?php
namespace SonidoInteriorPoo\interfaces;

interface CheckoutServiceInterface {
    public function procesarCheckout(int $idUsuario, string $direccionEnvio): int;
}