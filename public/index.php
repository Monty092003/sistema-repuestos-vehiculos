<?php
/**
 * cd public && C:\xampp\php\php.exe -S localhost:8000
 * Router Principal - Sistema de Repuestos de Vehículos
 * Punto de entrada único de la aplicación
 */

// Incluir constantes primero (necesarias para rutas usadas en el autoloader)
require_once __DIR__ . '/../config/constants.php';

// Configuración de errores según el entorno (después de cargar constants.php)
if (defined('APP_DEBUG') && APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Incluir autoloader (usa SRC_PATH definido en constants)
require_once __DIR__ . '/../src/autoloader.php';

// Incluir configuración de base de datos (clase Database)
require_once __DIR__ . '/../config/database.php';

// Iniciar sesión
session_start();

// Obtener la ruta solicitada
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// Remover el directorio base si existe
$basePath = dirname($scriptName);
if ($basePath !== '/') {
    $requestUri = substr($requestUri, strlen($basePath));
}

// Limpiar la ruta
$requestUri = parse_url($requestUri, PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/');
// Asegurar que siempre comience con /
if (empty($requestUri) || $requestUri[0] !== '/') {
    $requestUri = '/' . $requestUri;
}
// Normalizar posibles variantes
if ($requestUri === '/index.php') {
    $requestUri = '/';
}

// Router simple
try {
    $router = new \App\Core\Router();
    $router->handleRequest($requestUri, $_SERVER['REQUEST_METHOD']);
} catch (Exception $e) {
    // Manejo de errores
    http_response_code(500);
    echo "Error: " . $e->getMessage();
    
    // En desarrollo, mostrar más detalles
    if (APP_DEBUG) {
        echo "<br>Archivo: " . $e->getFile();
        echo "<br>Línea: " . $e->getLine();
        echo "<br>Trace: <pre>" . $e->getTraceAsString() . "</pre>";
    }
}
