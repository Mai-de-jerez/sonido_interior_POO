<?php
namespace SonidoInteriorPoo\core;

use SonidoInteriorPoo\exceptions\AppException;
use SonidoInteriorPoo\exceptions\NotFoundException;

class ExceptionMapper {

    // public static function map(\Throwable $e, Request $request): Response {
    //     self::logException($e);

    //     if ($e instanceof AppException) {
    //         return self::mapAppException($e, $request);
    //     }

    //     return self::mapExcepcionInesperada($e);
    // }
    public static function map(\Throwable $e, Request $request): Response {
        self::logException($e);

        if ($e instanceof AppException) {
            return self::mapAppException($e, $request);
        }

        if ($e instanceof \PDOException) {
            Session::setFlash('mensaje_error', 'Ha ocurrido un error al guardar los datos. Inténtalo de nuevo.');
            return Response::redirect(self::origenSeguro($request));
        }

        return self::mapExcepcionInesperada($e);
    }

    private static function mapAppException(AppException $e, Request $request): Response {
        if ($e instanceof NotFoundException) {
            return Response::notFound();
        }

        // Para otros errores de negocio, redirigimos al usuario a la página anterior con un mensaje de error.
        Session::setFlash('mensaje_error', $e->getMessage());
        return Response::redirect(self::origenSeguro($request));
    }

    private static function mapExcepcionInesperada(\Throwable $e): Response {
        $esDesarrollo = defined('APP_ENV') && APP_ENV === 'development';

        return Response::view(
            $esDesarrollo ? 'errors/dev-500' : 'errors/500',
            [
                'mensaje' => $esDesarrollo
                    ? $e->getMessage()
                    : 'Ha ocurrido un error inesperado. Por favor, inténtalo de nuevo más tarde.'
            ],
            500
        );
    }

    // Auditoría de errores de negocio esperados (no incidentes graves).
    private static function logException(\Throwable $e): void {
        $logFile = __DIR__ . '/../../logs/errors.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $type = $e instanceof AppException ? 'APP' : 'SYSTEM';

        $logEntry = sprintf(
            "[%s] [%s] %s en %s:%d\n%s\n---\n",
            date('Y-m-d H:i:s'),
            $type,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    // Devuelve la URL de origen segura para redirigir al usuario después de un error.
    private static function origenSeguro(Request $request): string {
        $referer = $request->referer();

        if ($referer !== null) {
            $hostReferer = parse_url($referer, PHP_URL_HOST);
            $hostActual = $request->host();

            if ($hostReferer === $hostActual) {
                return $referer;
            }
        }

        return BASE_URL . '/';
    }
}