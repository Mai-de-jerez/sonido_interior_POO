<?php
namespace SonidoInteriorPoo\exceptions;

abstract class AppException extends \RuntimeException {
    abstract public function getStatusCode(): int;
}