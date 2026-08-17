<?php
namespace SonidoInteriorPoo\exceptions;

class ValidationException extends AppException {
    public function getStatusCode(): int {
        return 422;
    }
}