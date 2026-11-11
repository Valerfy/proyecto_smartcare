<?php// Inicia la sesión para mantener los datos del usuario
//estoy estudiando el codigo, son unos apuntes mios//
session_start(); // Define la ruta principal del proyecto para facilitar
// el acceso a los archivos de configuración y módulos.

define('ROOT', dirname(__DIR__));// Carga la conexión a la base de datos

require ROOT . '/config/conexion.php';// Permite cargar automáticamente las clases del proyecto

spl_autoload_register(function ($class) {
    $paths = [ROOT . '/models/', ROOT . '/controllers/'];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';

        // Si encuentra el archivo correspondiente a la clase,
        // lo carga y evita continuar buscando en otras carpetas.
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

// Obtiene el controlador y la acción solicitados desde la URL.
$controller = $_GET['controller'] ?? 'auth';
$action     = $_GET['action']     ?? 'index';

// Relaciona los nombres utilizados en la URL con las clases
// encargadas de gestionar cada módulo del sistema.
$controllerMap = [
    'auth'      => 'AuthController',
    'paciente'  => 'PacienteController',
    'medico'    => 'MedicoController',
    'admin'     => 'AdminController',
    'helper'    => 'HelperController',
    'chatbot'   => 'ChatbotController',
];

// Comprueba si el controlador solicitado está registrado.
$controllerClass = $controllerMap[$controller] ?? 'AuthController';

// Verifica que la clase del controlador esté disponible
if (!class_exists($controllerClass)) {
    http_response_code(404);
    die("Controlador no encontrado.");
}

// Crea el objeto del controlador que atenderá la solicitud.
$ctrl = new $controllerClass();

// Comprueba que la acción solicitada exista dentro del controlador.
if (!method_exists($ctrl, $action)) {
    http_response_code(404);
    die("Acción no encontrada.");
}

// Ejecuta la acción correspondiente para atender la solicitud
// y mostrar el contenido solicitado por el usuario.
$ctrl->$action();
