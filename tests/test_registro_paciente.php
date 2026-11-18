<?php
/**
 * PRUEBA UNITARIA — Requerimiento: Registro de nuevos pacientes (validación de datos)
 *
 * Reproduce las MISMAS reglas de validación de AuthController::register()
 * (nombre, CI, celular, edad mínima de 18 años, correo, contraseña, términos)
 * en una función aparte, para probarlas sin navegador ni base de datos.
 *
 * Ejecutar:  php tests/test_registro_paciente.php
 */
require_once __DIR__ . '/Prueba.php';

const REGEX_NOMBRE = '/^[A-Za-zÁÉÍÓÚÜáéíóúüÑñ]+(?: [A-Za-zÁÉÍÓÚÜáéíóúüÑñ]+)*$/u';
const DOMINIOS_BLOQUEADOS = ['@cns.com', '@cns.org.bo', '@cns.rec.bo'];

/** Misma lógica que AuthController::register(). Devuelve [campo => mensaje]. */
function validarRegistro(array $post): array {
    $errores = [];
    $old = [];
    foreach (['nombre', 'apellido', 'ci', 'telefono', 'correo', 'fecha_nac', 'direccion', 'ocupacion', 'matricula'] as $c) {
        $old[$c] = trim($post[$c] ?? '');
    }
    $old['nombre']   = preg_replace('/\s+/', ' ', $old['nombre']);
    $old['apellido'] = preg_replace('/\s+/', ' ', $old['apellido']);
    $old['ci']       = strtoupper($old['ci']);
    $old['terminos'] = !empty($post['terminos']) ? '1' : '';

    $clave  = $post['clave']  ?? '';
    $clave2 = $post['clave2'] ?? '';
    extract($old, EXTR_SKIP);

    foreach (['nombre' => 'Nombre', 'apellido' => 'Apellido'] as $k => $et) {
        $len = mb_strlen($old[$k]);
        if ($old[$k] === '')                               $errores[$k] = "Ingresa tu " . strtolower($et) . ".";
        elseif ($len < 2 || $len > 40)                     $errores[$k] = "$et: entre 2 y 40 caracteres.";
        elseif (!preg_match(REGEX_NOMBRE, $old[$k]))       $errores[$k] = "$et: solo letras y espacios.";
    }

    if ($ci === '')                                        $errores['ci'] = 'Ingresa tu CI.';
    elseif (!preg_match('/^\d{5,9}(-[0-9A-Z]{1,2})?$/', $ci))
                                                           $errores['ci'] = 'CI inválido (5 a 9 dígitos, complemento opcional: 1234567-1A).';

    if ($telefono === '')                                  $errores['telefono'] = 'Ingresa tu celular.';
    elseif (!preg_match('/^[67]\d{7}$/', $telefono))       $errores['telefono'] = 'Celular inválido (8 dígitos, empieza con 6 o 7).';

    if ($fecha_nac === '') {
        $errores['fecha_nac'] = 'Ingresa tu fecha de nacimiento.';
    } else {
        $f = DateTime::createFromFormat('Y-m-d', $fecha_nac);
        if (!$f || $f->format('Y-m-d') !== $fecha_nac)     $errores['fecha_nac'] = 'Fecha de nacimiento inválida.';
        elseif ($f > new DateTime())                       $errores['fecha_nac'] = 'La fecha no puede ser futura.';
        else {
            $edad = (new DateTime())->diff($f)->y;
            if ($edad < 18)                                $errores['fecha_nac'] = 'Debes ser mayor de 18 años.';
            elseif ($edad > 110)                           $errores['fecha_nac'] = 'Fecha de nacimiento no válida.';
        }
    }

    $lo = mb_strlen($ocupacion);
    if ($ocupacion === '')                                 $errores['ocupacion'] = 'Ingresa tu ocupación.';
    elseif ($lo < 3 || $lo > 50 || !preg_match('/^[A-Za-zÁÉÍÓÚÜáéíóúüÑñ .,\-]+$/u', $ocupacion))
                                                           $errores['ocupacion'] = 'Ocupación inválida (3 a 50 letras).';
    $ld = mb_strlen($direccion);
    if ($direccion === '')                                 $errores['direccion'] = 'Ingresa tu dirección.';
    elseif ($ld < 5 || $ld > 150)                          $errores['direccion'] = 'La dirección debe tener entre 5 y 150 caracteres.';
    if ($matricula === '')                                 $errores['matricula'] = 'Ingresa tu número de matrícula.';
    elseif (!preg_match('/^[A-Za-z0-9\-]{3,20}$/', $matricula))
                                                           $errores['matricula'] = 'Matrícula inválida (3 a 20 letras, números o guiones).';

    if ($correo === '')                                    $errores['correo'] = 'Ingresa tu correo.';
    elseif (mb_strlen($correo) > 120 || !filter_var($correo, FILTER_VALIDATE_EMAIL))
                                                           $errores['correo'] = 'Correo electrónico inválido.';
    else {
        foreach (DOMINIOS_BLOQUEADOS as $dom) {
            if (str_ends_with(strtolower($correo), $dom)) {
                $errores['correo'] = 'No puedes usar un correo institucional.';
                break;
            }
        }
    }

    if (strlen($clave) < 8 || strlen($clave) > 72)         $errores['clave'] = 'La contraseña debe tener entre 8 y 72 caracteres.';
    elseif (!preg_match('/[A-Za-z]/', $clave) || !preg_match('/\d/', $clave))
                                                           $errores['clave'] = 'La contraseña debe incluir al menos una letra y un número.';
    if ($clave2 === '' || $clave !== $clave2)              $errores['clave2'] = 'Las contraseñas no coinciden.';

    if ($old['terminos'] !== '1')                          $errores['terminos'] = 'Debes aceptar los términos y condiciones.';

    return $errores;
}

/** Datos de un paciente válido; cada prueba cambia solo el campo que quiere probar. */
function datosValidos(array $cambios = []): array {
    return array_merge([
        'nombre'    => 'Carlos',
        'apellido'  => 'Mamani Quispe',
        'ci'        => '1234567',
        'telefono'  => '71234567',
        'correo'    => 'carlos.mamani@gmail.com',
        'fecha_nac' => '1998-05-14',
        'direccion' => 'Calle Murillo 123, La Paz',
        'ocupacion' => 'Estudiante',
        'matricula' => 'MAT-12345',
        'clave'     => 'Demo1234',
        'clave2'    => 'Demo1234',
        'terminos'  => '1',
    ], $cambios);
}

Prueba::titulo('REGISTRO DE PACIENTE - validación de datos (AuthController::register)');

Prueba::caso('Paciente con todos los datos correctos no tiene errores',
    [], validarRegistro(datosValidos()));

Prueba::caso('CI con complemento (1234567-1A) es válido',
    [], validarRegistro(datosValidos(['ci' => '1234567-1a'])));

Prueba::caso('Nombre con tildes y ñ es válido',
    [], validarRegistro(datosValidos(['nombre' => 'José Ñusta', 'apellido' => 'Núñez Ibáñez'])));

Prueba::caso('Nombre vacío devuelve error',
    ['nombre' => 'Ingresa tu nombre.'], validarRegistro(datosValidos(['nombre' => ''])));

Prueba::caso('Nombre con números devuelve error',
    ['nombre' => 'Nombre: solo letras y espacios.'], validarRegistro(datosValidos(['nombre' => 'Juan3'])));

Prueba::caso('Apellido de 1 letra devuelve error de longitud',
    ['apellido' => 'Apellido: entre 2 y 40 caracteres.'], validarRegistro(datosValidos(['apellido' => 'A'])));

Prueba::caso('CI de 3 dígitos es inválido',
    ['ci' => 'CI inválido (5 a 9 dígitos, complemento opcional: 1234567-1A).'], validarRegistro(datosValidos(['ci' => '123'])));

Prueba::caso('CI de 10 dígitos es inválido',
    ['ci' => 'CI inválido (5 a 9 dígitos, complemento opcional: 1234567-1A).'], validarRegistro(datosValidos(['ci' => '1234567890'])));

Prueba::caso('Celular que no empieza con 6 o 7 es inválido',
    ['telefono' => 'Celular inválido (8 dígitos, empieza con 6 o 7).'], validarRegistro(datosValidos(['telefono' => '51234567'])));

Prueba::caso('Celular de menos de 8 dígitos es inválido',
    ['telefono' => 'Celular inválido (8 dígitos, empieza con 6 o 7).'], validarRegistro(datosValidos(['telefono' => '712345'])));

$hace10 = (new DateTime())->modify('-10 years')->format('Y-m-d');
Prueba::caso("Menor de edad (nacido el $hace10) es rechazado",
    ['fecha_nac' => 'Debes ser mayor de 18 años.'], validarRegistro(datosValidos(['fecha_nac' => $hace10])));

$futura = (new DateTime())->modify('+1 year')->format('Y-m-d');
Prueba::caso("Fecha futura ($futura) es rechazada",
    ['fecha_nac' => 'La fecha no puede ser futura.'], validarRegistro(datosValidos(['fecha_nac' => $futura])));

Prueba::caso('Fecha inexistente (31 de febrero) es rechazada',
    ['fecha_nac' => 'Fecha de nacimiento inválida.'], validarRegistro(datosValidos(['fecha_nac' => '1998-02-31'])));

Prueba::caso('Correo sin formato válido es rechazado',
    ['correo' => 'Correo electrónico inválido.'], validarRegistro(datosValidos(['correo' => 'carlos@@gmail'])));

Prueba::caso('Correo institucional (@cns.com) no se puede usar',
    ['correo' => 'No puedes usar un correo institucional.'], validarRegistro(datosValidos(['correo' => 'carlos@cns.com'])));

Prueba::caso('Contraseña de menos de 8 caracteres es rechazada',
    ['clave' => 'La contraseña debe tener entre 8 y 72 caracteres.'],
    validarRegistro(datosValidos(['clave' => 'Ab1', 'clave2' => 'Ab1'])));

Prueba::caso('Contraseña sin número es rechazada',
    ['clave' => 'La contraseña debe incluir al menos una letra y un número.'],
    validarRegistro(datosValidos(['clave' => 'SoloLetras', 'clave2' => 'SoloLetras'])));

Prueba::caso('Contraseñas que no coinciden son rechazadas',
    ['clave2' => 'Las contraseñas no coinciden.'], validarRegistro(datosValidos(['clave2' => 'Otra1234'])));

Prueba::caso('No aceptar términos y condiciones es rechazado',
    ['terminos' => 'Debes aceptar los términos y condiciones.'], validarRegistro(datosValidos(['terminos' => ''])));

Prueba::caso('Formulario completamente vacío marca los 12 campos obligatorios',
    ['nombre', 'apellido', 'ci', 'telefono', 'fecha_nac', 'ocupacion', 'direccion', 'matricula', 'correo', 'clave', 'clave2', 'terminos'],
    array_keys(validarRegistro([])));

if (Prueba::esEjecucionDirecta(__FILE__)) exit(Prueba::resumen());
