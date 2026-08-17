<?php
namespace SonidoInteriorPoo\exceptions;

class BusinessRuleException extends AppException {
    public function getStatusCode(): int {
        return 400;
    }
}