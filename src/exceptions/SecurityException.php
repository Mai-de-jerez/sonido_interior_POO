<?php
namespace SonidoInteriorPoo\exceptions;

class SecurityException extends AppException {
    public function getStatusCode(): int {
        return 403;
    }
}