<?php
/**
 * Ejecuta TODAS las pruebas unitarias del proyecto.
 *
 * Desde la carpeta del proyecto:   php tests/run_all.php
 * (en XAMPP:  C:\xampp\php\php.exe tests\run_all.php)
 */
require_once __DIR__ . '/Prueba.php';

require __DIR__ . '/test_registro_paciente.php';
require __DIR__ . '/test_login.php';
require __DIR__ . '/test_filtros_y_horarios.php';

exit(Prueba::resumen());
