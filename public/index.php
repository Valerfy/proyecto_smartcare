<?php

session_start();

define('ROOT', dirname(__DIR__));

require ROOT . '/config/conexion.php';

spl_autoload_register(function ($class) {
    $paths = [ROOT . '/models/', ROOT . '/controllers/'];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

$controller = $_GET['controller'] ?? 'auth';
$action     = $_GET['action']     ?? 'index';

if (!is_string($controller) || !is_string($action)) {
    http_response_code(400);
    die("Solicitud inválida.");
}

$controllerMap = [
    'auth'      => 'AuthController',
    'paciente'  => 'PacienteController',
    'medico'    => 'MedicoController',
    'admin'     => 'AdminController',
    'helper'    => 'HelperController',
    'chatbot'   => 'ChatbotController',
];

$controllerClass = $controllerMap[$controller] ?? 'AuthController';

if (!class_exists($controllerClass)) {
    http_response_code(404);
    die("Controlador no encontrado.");
}

if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $action) || !method_exists($controllerClass, $action)) {
    http_response_code(404);
    die("Acción no encontrada.");
}

$metodo = new ReflectionMethod($controllerClass, $action);

if (!$metodo->isPublic() || $metodo->isConstructor() || $metodo->isAbstract()) {
    http_response_code(404);
    die("Acción no encontrada.");
}

if ($metodo->isStatic()) {
    $controllerClass::$action();
} else {
    $ctrl = new $controllerClass();
    $ctrl->$action();
}