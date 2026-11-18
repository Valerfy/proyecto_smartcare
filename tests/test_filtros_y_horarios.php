<?php
/**
 * PRUEBA UNITARIA — Funciones reales de los modelos que NO necesitan base de datos:
 *   - ReporteRecepcionModel::filtrosDesdeGet()  (filtros del reporte de recepción)
 *   - MedicoModel::generarHoras()               (horarios de atención cada 30 min)
 *   - HistorialModel::separarLista()            (antecedentes separados por coma, ; o salto de línea)
 *
 * Aquí se llama directamente al código REAL de models/. Las funciones privadas
 * se invocan con Reflection.
 *
 * Ejecutar:  php tests/test_filtros_y_horarios.php
 */
require_once __DIR__ . '/Prueba.php';
require_once __DIR__ . '/../models/ReporteRecepcionModel.php';
require_once __DIR__ . '/../models/MedicoModel.php';
require_once __DIR__ . '/../models/HistorialModel.php';

/** Llama a un método privado estático de una clase real. */
function llamarPrivado(string $clase, string $metodo, array $args) {
    $m = new ReflectionMethod($clase, $metodo);
    return $m->invokeArgs(null, $args);
}

/* ---------------------------------------------------------------- filtros */
Prueba::titulo('REPORTE DE RECEPCIÓN - ReporteRecepcionModel::filtrosDesdeGet');

Prueba::caso('Sin parámetros devuelve filtros vacíos',
    ['q' => '', 'medico_id' => 0, 'fecha_desde' => '', 'fecha_hasta' => '', 'tipo' => ''],
    ReporteRecepcionModel::filtrosDesdeGet([]));

Prueba::caso('Filtros válidos se conservan (se recorta el texto y el médico pasa a entero)',
    ['q' => 'Perez', 'medico_id' => 4, 'fecha_desde' => '2026-01-01', 'fecha_hasta' => '2026-12-31', 'tipo' => 'Nuevo'],
    ReporteRecepcionModel::filtrosDesdeGet([
        'q' => '  Perez  ', 'medico_id' => '4', 'fecha_desde' => '2026-01-01',
        'fecha_hasta' => '2026-12-31', 'tipo' => 'Nuevo',
    ]));

Prueba::caso('Tipo distinto de Nuevo/Antiguo se descarta',
    '', ReporteRecepcionModel::filtrosDesdeGet(['tipo' => 'Otro'])['tipo']);

Prueba::caso('Tipo "Antiguo" es aceptado',
    'Antiguo', ReporteRecepcionModel::filtrosDesdeGet(['tipo' => 'Antiguo'])['tipo']);

Prueba::caso('Fechas con formato incorrecto se descartan',
    ['fecha_desde' => '', 'fecha_hasta' => ''],
    (function () {
        $f = ReporteRecepcionModel::filtrosDesdeGet(['fecha_desde' => '01/01/2026', 'fecha_hasta' => 'hoy']);
        return ['fecha_desde' => $f['fecha_desde'], 'fecha_hasta' => $f['fecha_hasta']];
    })());

Prueba::caso('Un medico_id que no es número se convierte en 0',
    0, ReporteRecepcionModel::filtrosDesdeGet(['medico_id' => 'abc'])['medico_id']);

/* ---------------------------------------------------------------- horarios */
Prueba::titulo('HORARIOS DEL MÉDICO - MedicoModel::generarHoras (citas de 30 min)');

Prueba::caso('Turno de 08:00 a 10:00 genera 4 horarios',
    ['08:00', '08:30', '09:00', '09:30'],
    llamarPrivado('MedicoModel', 'generarHoras', ['08:00:00', '10:00:00']));

Prueba::caso('Turno de 14:00 a 15:00 genera 2 horarios',
    ['14:00', '14:30'],
    llamarPrivado('MedicoModel', 'generarHoras', ['14:00:00', '15:00:00']));

Prueba::caso('Turno menor a 30 minutos no genera horarios',
    [], llamarPrivado('MedicoModel', 'generarHoras', ['08:00:00', '08:20:00']));

Prueba::caso('Horas inválidas devuelven lista vacía',
    [], llamarPrivado('MedicoModel', 'generarHoras', ['xx', 'yy']));

/* ---------------------------------------------------------------- listas */
Prueba::titulo('HISTORIAL CLÍNICO - HistorialModel::separarLista');

Prueba::caso('Separa por coma, punto y coma y salto de línea',
    ['Diabetes', 'Hipertensión', 'Asma'],
    llamarPrivado('HistorialModel', 'separarLista', ["Diabetes, Hipertensión;\nAsma", 100]));

Prueba::caso('Elimina elementos repetidos y vacíos',
    ['Asma', 'Gastritis'],
    llamarPrivado('HistorialModel', 'separarLista', ["Asma,, Asma ; Gastritis ;", 100]));

Prueba::caso('Recorta cada elemento al largo máximo',
    ['Diab'],
    llamarPrivado('HistorialModel', 'separarLista', ['Diabetes', 4]));

Prueba::caso('Texto vacío devuelve lista vacía',
    [], llamarPrivado('HistorialModel', 'separarLista', ['', 100]));

if (Prueba::esEjecucionDirecta(__FILE__)) exit(Prueba::resumen());
