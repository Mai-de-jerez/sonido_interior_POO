<?php
namespace SonidoInteriorPoo\interfaces;

interface TransactionManagerInterface {
    public function transaction(callable $fn): mixed;
}