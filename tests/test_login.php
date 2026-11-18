<?php
/**
 * PRUEBA UNITARIA — Requerimiento: Inicio de sesión con acceso por rol
 *
 * Reproduce la lógica de AuthController::login() y UsuarioModel::autenticar()
 * (validaciones, password_verify, usuario desactivado, bloqueo tras 5 intentos
 * y redirección según el rol) SIN base de datos: el "usuario de la BD" se simula
 * con un arreglo.
 *
 * Ejecutar:  php tests/test_login.php
 */
require_once __DIR__ . '/Prueba.php';

const MAX_INTENTOS = 5;
const BLOQUEO_SEG  = 60;

/** Igual que UsuarioModel::autenticar(): devuelve el usuario o false. */
function autenticar(?array $usuarioBD, string $clave) {
    if (!$usuarioBD || empty($usuarioBD['clave']) || !password_verify($clave, $usuarioBD['clave'])) {
        return false;
    }
    return $usuarioBD;
}

/** Igual que el switch de AuthController::login(). */
function destinoPorRol(string $rol): string {
    switch ($rol) {
        case 'admin':         return 'index.php?controller=admin';
        case 'medico':        return 'index.php?controller=medico';
        case 'recepcionista': return 'index.php?controller=helper';
        default:              return 'index.php?controller=paciente';
    }
}

/**
 * Igual que el flujo de AuthController::login().
 * @param array $sesion  se pasa por referencia, como $_SESSION
 * @return array ['error' => string, 'redirige' => string|null]
 */
function intentarLogin(string $correo, string $clave, ?array $usuarioBD, array &$sesion, int $ahora): array {
    $correo = trim($correo);
    $espera = ($sesion['login_bloqueo'] ?? 0) - $ahora;

    if ($espera > 0) {
        return ['error' => "Demasiados intentos fallidos. Intenta de nuevo en {$espera} segundos.", 'redirige' => null];
    }
    if ($correo === '' || $clave === '') {
        return ['error' => 'Todos los campos son obligatorios.', 'redirige' => null];
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        return ['error' => 'Correo inválido.', 'redirige' => null];
    }

    $usuario = autenticar($usuarioBD, $clave);
    if (!$usuario) {
        $sesion['login_fallos'] = ($sesion['login_fallos'] ?? 0) + 1;
        if ($sesion['login_fallos'] >= MAX_INTENTOS) {
            $sesion['login_bloqueo'] = $ahora + BLOQUEO_SEG;
            $sesion['login_fallos']  = 0;
        }
        return ['error' => 'Correo o contraseña incorrectos.', 'redirige' => null];
    }
    if ((int)$usuario['estado'] === 0) {
        return ['error' => 'Usuario desactivado.', 'redirige' => null];
    }

    $sesion = [
        'usuario_id' => $usuario['id'],
        'nombre'     => $usuario['nombre'],
        'apellido'   => $usuario['apellido'],
        'rol'        => $usuario['rol'],
    ];
    return ['error' => '', 'redirige' => destinoPorRol($usuario['rol'])];
}

function usuarioDePrueba(string $rol = 'paciente', int $estado = 1): array {
    return [
        'id' => 9, 'nombre' => 'Juan', 'apellido' => 'Pérez Gómez',
        'correo' => 'juan.perez@correo.com', 'rol' => $rol, 'estado' => $estado,
        'clave' => password_hash('Clave1234', PASSWORD_DEFAULT),
    ];
}

$ahora = 1_000_000;

Prueba::titulo('INICIO DE SESIÓN - AuthController::login / UsuarioModel::autenticar');

$ses = [];
Prueba::caso('Correo y contraseña vacíos devuelven error de campos obligatorios',
    ['error' => 'Todos los campos son obligatorios.', 'redirige' => null],
    intentarLogin('', '', usuarioDePrueba(), $ses, $ahora));

$ses = [];
Prueba::caso('Correo sin formato válido devuelve "Correo inválido."',
    ['error' => 'Correo inválido.', 'redirige' => null],
    intentarLogin('juan.perez', 'Clave1234', usuarioDePrueba(), $ses, $ahora));

$ses = [];
Prueba::caso('Contraseña incorrecta devuelve error y suma 1 fallo',
    ['error' => 'Correo o contraseña incorrectos.', 'fallos' => 1],
    (function () use (&$ses, $ahora) {
        $r = intentarLogin('juan.perez@correo.com', 'mala', usuarioDePrueba(), $ses, $ahora);
        return ['error' => $r['error'], 'fallos' => $ses['login_fallos']];
    })());

$ses = [];
Prueba::caso('Correo que no existe en la BD devuelve error genérico',
    ['error' => 'Correo o contraseña incorrectos.', 'redirige' => null],
    intentarLogin('nadie@correo.com', 'Clave1234', null, $ses, $ahora));

$ses = [];
Prueba::caso('Usuario desactivado (estado 0) no puede ingresar',
    ['error' => 'Usuario desactivado.', 'redirige' => null],
    intentarLogin('juan.perez@correo.com', 'Clave1234', usuarioDePrueba('paciente', 0), $ses, $ahora));

$ses = [];
Prueba::caso('Paciente con datos correctos entra y se guarda la sesión',
    ['error' => '', 'redirige' => 'index.php?controller=paciente',
     'sesion' => ['usuario_id' => 9, 'nombre' => 'Juan', 'apellido' => 'Pérez Gómez', 'rol' => 'paciente']],
    (function () use (&$ses, $ahora) {
        $r = intentarLogin('juan.perez@correo.com', 'Clave1234', usuarioDePrueba('paciente'), $ses, $ahora);
        return $r + ['sesion' => $ses];
    })());

Prueba::caso('Redirección por rol: admin, medico, recepcionista y paciente',
    [
        'admin'         => 'index.php?controller=admin',
        'medico'        => 'index.php?controller=medico',
        'recepcionista' => 'index.php?controller=helper',
        'paciente'      => 'index.php?controller=paciente',
    ],
    [
        'admin'         => destinoPorRol('admin'),
        'medico'        => destinoPorRol('medico'),
        'recepcionista' => destinoPorRol('recepcionista'),
        'paciente'      => destinoPorRol('paciente'),
    ]);

$ses = [];
Prueba::caso('Tras 5 intentos fallidos se bloquea el login 60 segundos',
    ['bloqueo_hasta' => $ahora + 60, 'fallos_reiniciados' => 0, 'mensaje' => 'Demasiados intentos fallidos. Intenta de nuevo en 60 segundos.'],
    (function () use (&$ses, $ahora) {
        for ($i = 0; $i < MAX_INTENTOS; $i++) {
            intentarLogin('juan.perez@correo.com', 'mala', usuarioDePrueba(), $ses, $ahora);
        }
        $r = intentarLogin('juan.perez@correo.com', 'Clave1234', usuarioDePrueba(), $ses, $ahora);
        return ['bloqueo_hasta' => $ses['login_bloqueo'], 'fallos_reiniciados' => $ses['login_fallos'], 'mensaje' => $r['error']];
    })());

Prueba::caso('Pasado el bloqueo (61 s después) vuelve a poder ingresar',
    ['error' => '', 'redirige' => 'index.php?controller=paciente'],
    intentarLogin('juan.perez@correo.com', 'Clave1234', usuarioDePrueba(), $ses, $ahora + 61));

if (Prueba::esEjecucionDirecta(__FILE__)) exit(Prueba::resumen());
