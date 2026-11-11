<?php
if (!function_exists('mb_strtolower')) {
    function mb_strtolower(string $texto, ?string $codificacion = null): string
    {
        return strtolower($texto);
    }
}

if (!function_exists('mb_substr')) {
    function mb_substr(string $texto, int $inicio, ?int $longitud = null, ?string $codificacion = null): string
    {
        return $longitud === null ? substr($texto, $inicio) : substr($texto, $inicio, $longitud);
    }
}

require_once __DIR__ . '/PruebasFunciones.php';

$pruebas = new PruebasFunciones();
$pruebas->ejecutar();
