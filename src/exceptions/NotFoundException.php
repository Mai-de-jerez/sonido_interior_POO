<?php
namespace SonidoInteriorPoo\exceptions;

class NotFoundException extends AppException {
    public function getStatusCode(): int {
        return 404;
    }
}