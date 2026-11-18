<?php
/**
 * Mini ayudante para pruebas unitarias (sin librerías externas).
 *
 * Cada prueba compara lo ESPERADO con lo que DEVUELVE la función y muestra
 * en la terminal si fue exitosa y los datos que devolvió.
 */
final class Prueba {

    private static int $exitosas = 0;
    private static int $fallidas = 0;

    public static function titulo(string $texto): void {
        echo PHP_EOL . str_repeat('=', 70) . PHP_EOL;
        echo " " . $texto . PHP_EOL;
        echo str_repeat('=', 70) . PHP_EOL;
    }

    /** Convierte cualquier valor a texto legible (una línea). */
    public static function ver($valor): string {
        return json_encode($valor, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    /**
     * @param string $nombre    Qué se está probando
     * @param mixed  $esperado  Lo que debería devolver la función
     * @param mixed  $obtenido  Lo que realmente devolvió
     */
    public static function caso(string $nombre, $esperado, $obtenido): void {
        $ok = ($esperado === $obtenido);
        if ($ok) {
            self::$exitosas++;
            echo PHP_EOL . "[OK]    PRUEBA EXITOSA: " . $nombre . PHP_EOL;
            echo "        Devuelve: " . self::ver($obtenido) . PHP_EOL;
        } else {
            self::$fallidas++;
            echo PHP_EOL . "[FALLO] PRUEBA FALLIDA: " . $nombre . PHP_EOL;
            echo "        Esperado: " . self::ver($esperado) . PHP_EOL;
            echo "        Obtenido: " . self::ver($obtenido) . PHP_EOL;
        }
    }

    /** Imprime el resumen y devuelve el código de salida (0 = todo bien). */
    public static function resumen(): int {
        $total = self::$exitosas + self::$fallidas;
        echo PHP_EOL . str_repeat('-', 70) . PHP_EOL;
        echo " RESULTADO: {$total} pruebas | " . self::$exitosas . " exitosas | " . self::$fallidas . " fallidas" . PHP_EOL;
        echo str_repeat('-', 70) . PHP_EOL;
        return self::$fallidas === 0 ? 0 : 1;
    }

    /** True si el archivo se ejecutó directamente (php tests/archivo.php). */
    public static function esEjecucionDirecta(string $archivo): bool {
        $script = $_SERVER['argv'][0] ?? '';
        return $script !== '' && realpath($script) === realpath($archivo);
    }
}
