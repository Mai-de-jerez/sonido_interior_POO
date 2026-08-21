<?php
namespace SonidoInteriorPoo\dto;

class ResumenCheckoutDTO {
    public function __construct(
        private readonly array $lineas,
        private readonly float $total
    ) {}

    public function getLineas(): array { return $this->lineas; }
    public function getTotal(): float { return $this->total; }
}