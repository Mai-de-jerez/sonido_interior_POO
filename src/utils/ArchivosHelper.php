<?php
namespace SonidoInteriorPoo\utils;

class ArchivosHelper {

    public static function ver(mixed $dato): void {
        echo "<pre>";
        print_r($dato);
        echo "</pre>";
    }

    public static function subirFoto(array $foto, string $nombreProducto = "", int $pesoMaximo = 5000000): string|false {

        // 1. Si la subida dio error en el servidor, frenamos
        if (!isset($foto["error"]) || $foto["error"] !== UPLOAD_ERR_OK) {
            return false;
        }

        // 2. Comprobar peso
        if ($foto["size"] > $pesoMaximo) {
            return false;
        }

        // 3. Obtener el tipo real del archivo con finfo
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $tipoReal = $finfo->file($foto["tmp_name"]);

        $extensiones = [
            "image/png"  => ".png",
            "image/jpeg" => ".jpg",
            "image/webp" => ".webp"
        ];

        if (!isset($extensiones[$tipoReal])) {
            return false;
        }

        $extension = $extensiones[$tipoReal];

        // 4. Nombre base
        if ($nombreProducto != "") {
            $nombreArchivo = self::limpiarCaracteresEspeciales($nombreProducto);
        } else {
            $nombreArchivo = self::limpiarCaracteresEspeciales($foto["name"]);
            $nombreArchivo = self::cortarCadenaFinal($nombreArchivo, ".");
        }

        if (empty($nombreArchivo)) {
            $nombreArchivo = "producto";
        }

        // 5. Gestión de nombre único si ya existe
        $directorioDestino = __DIR__ . "/../../public/img/productos/";
        $nombreFinal = $nombreArchivo . $extension;

        if (file_exists($directorioDestino . $nombreFinal)) {
            $random = time();
            $nombreFinal = $nombreArchivo . $random . $extension;
        }

        // 6. Mover archivo
        if (!move_uploaded_file($foto["tmp_name"], $directorioDestino . $nombreFinal)) {
            return false;
        }

        return $nombreFinal;
    }

    public static function subirMP3(array $nota, string $nombreProducto = "", int $pesoMaximo = 5000000): string|false|null {

        // 1. Si no se ha subido ningún archivo, no es un error: el campo es opcional
        if (!isset($nota["error"]) || $nota["error"] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        // 2. Si la subida dio error en el servidor, frenamos
        if ($nota["error"] !== UPLOAD_ERR_OK) {
            return false;
        }

        // 3. Comprobar peso
        if ($nota["size"] > $pesoMaximo) {
            return false;
        }

        // 4. Obtener el tipo real del archivo con finfo
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $tipoReal = $finfo->file($nota["tmp_name"]);

        if ($tipoReal !== "audio/mpeg") {
            return false;
        }

        $extension = ".mp3";

        // 5. Nombre base
        if ($nombreProducto != "") {
            $nombreArchivo = self::limpiarCaracteresEspeciales($nombreProducto);
        } else {
            $nombreArchivo = self::limpiarCaracteresEspeciales($nota["name"]);
            $nombreArchivo = self::cortarCadenaFinal($nombreArchivo, ".");
        }

        if (empty($nombreArchivo)) {
            $nombreArchivo = "producto";
        }

        // 6. Gestión de nombre único si ya existe
        $directorioDestino = __DIR__ . "/../../public/sonidos/";
        $nombreFinal = $nombreArchivo . $extension;

        if (file_exists($directorioDestino . $nombreFinal)) {
            $random = time();
            $nombreFinal = $nombreArchivo . $random . $extension;
        }

        // 7. Mover archivo
        if (!move_uploaded_file($nota["tmp_name"], $directorioDestino . $nombreFinal)) {
            return false;
        }

        return $nombreFinal;
    }

    public static function limpiarCaracteresEspeciales(string $cadena): string {

        $cadena = str_replace(['?', '¿'], ['_', '_'], $cadena);
        $cadena = str_replace([' '], ['_'], $cadena);

        $cadena = str_replace(
            ['á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'],
            ['a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'],
            $cadena
        );

        $cadena = str_replace(
            ['é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'],
            ['e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'],
            $cadena
        );

        $cadena = str_replace(
            ['í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'],
            ['i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'],
            $cadena
        );

        $cadena = str_replace(
            ['ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'],
            ['o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'],
            $cadena
        );

        $cadena = str_replace(
            ['ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'],
            ['u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'],
            $cadena
        );

        $cadena = str_replace(
            ['ñ', 'Ñ', 'ç', 'Ç'],
            ['n', 'N', 'c', 'C'],
            $cadena
        );

        return $cadena;
    }

    public static function cortarCadenaFinal(string $cadena, string $caracter = "."): string {
        $posicionSubcadena = strrpos($cadena, $caracter);
        return substr($cadena, 0, $posicionSubcadena);
    }
}