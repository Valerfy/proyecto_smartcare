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

$controllerMap = [
    'auth'      => 'AuthController',
    'paciente'  => 'PacienteController',
    'medico'    => 'MedicoController',
    'admin'     => 'AdminController',
    'helper'    => 'HelperController',
    'chatbot'  => 'ChatbotController',
];

$controllerClass = $controllerMap[$controller] ?? 'AuthController';

if (!class_exists($controllerClass)) {
    http_response_code(404);
    die("Controlador no encontrado.");
}

$ctrl = new $controllerClass();

if (!method_exists($ctrl, $action)) {
    http_response_code(404);
    die("Acción no encontrada.");
}

$ctrl->$action();
