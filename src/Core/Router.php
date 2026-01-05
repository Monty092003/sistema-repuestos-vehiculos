<?php
/**
 * Router Core - Sistema de Repuestos de Vehículos
 * Maneja el enrutamiento de peticiones HTTP
 */

namespace App\Core;

use Exception;

class Router {
    private $routes = [];
    private $middlewares = [];
    
    public function __construct() {
        $this->loadRoutes();
    }
    
    /**
     * Cargar las rutas de la aplicación
     */
    private function loadRoutes() {
        // Rutas principales
        $this->addRoute('GET', '/', 'HomeController@index');
    // Fallback común cuando el servidor embebido incluye index.php en la URI
    $this->addRoute('GET', '/index.php', 'HomeController@index');
        $this->addRoute('GET', '/dashboard', 'DashboardController@index');
        
        // Rutas de autenticación
        $this->addRoute('GET', '/login', 'AuthController@showLogin');
        $this->addRoute('POST', '/login', 'AuthController@login');
        $this->addRoute('GET', '/logout', 'AuthController@logout');
        $this->addRoute('POST', '/logout', 'AuthController@logout');
        
        // Rutas de usuarios (RF1-RF3)
        $this->addRoute('GET', '/usuarios', 'UserController@index');
        $this->addRoute('GET', '/usuarios/crear', 'UserController@create');
        $this->addRoute('POST', '/usuarios', 'UserController@store');
        $this->addRoute('GET', '/usuarios/{id}', 'UserController@show');
        $this->addRoute('GET', '/usuarios/{id}/editar', 'UserController@edit');
        $this->addRoute('GET', '/usuarios/{id}/cambiar-password', 'UserController@changePassword');
        $this->addRoute('PUT', '/usuarios/{id}', 'UserController@update');
        $this->addRoute('POST', '/usuarios/{id}', 'UserController@update'); // Agregar POST para formularios HTML
        $this->addRoute('POST', '/usuarios/{id}/cambiar-password', 'UserController@updatePassword');
        $this->addRoute('DELETE', '/usuarios/{id}', 'UserController@destroy');
        
        // Rutas de repuestos (RF4-RF7)
        $this->addRoute('GET', '/repuestos', 'RepuestoController@index');
        $this->addRoute('GET', '/repuestos/crear', 'RepuestoController@create');
        $this->addRoute('POST', '/repuestos', 'RepuestoController@store');
        $this->addRoute('GET', '/repuestos/{id}', 'RepuestoController@show');
        $this->addRoute('GET', '/repuestos/{id}/editar', 'RepuestoController@edit');
    // Se usan POST para update y delete debido a formularios HTML sin override de método
    $this->addRoute('POST', '/repuestos/{id}', 'RepuestoController@update');
    $this->addRoute('POST', '/repuestos/{id}/eliminar', 'RepuestoController@destroy');
        $this->addRoute('GET', '/repuestos/buscar', 'RepuestoController@search');
        
        // Rutas de inventario (RF8-RF10)
        $this->addRoute('GET', '/inventario', 'InventarioController@index');
        $this->addRoute('GET', '/inventario/entradas', 'InventarioController@entradas');
        $this->addRoute('POST', '/inventario/entradas', 'InventarioController@storeEntrada');
        $this->addRoute('GET', '/inventario/salidas', 'InventarioController@salidas');
        $this->addRoute('POST', '/inventario/salidas', 'InventarioController@storeSalida');
        $this->addRoute('GET', '/inventario/ajustes', 'InventarioController@ajustes');
        $this->addRoute('POST', '/inventario/ajustes', 'InventarioController@storeAjuste');
        $this->addRoute('GET', '/inventario/movimientos', 'InventarioController@movimientos');
        
        // Rutas de proveedores (RF11-RF12)
        $this->addRoute('GET', '/proveedores', 'ProveedorController@index');
        $this->addRoute('GET', '/proveedores/crear', 'ProveedorController@create');
        $this->addRoute('POST', '/proveedores', 'ProveedorController@store');
        $this->addRoute('GET', '/proveedores/{id}', 'ProveedorController@show');
        $this->addRoute('GET', '/proveedores/{id}/editar', 'ProveedorController@edit');
        $this->addRoute('GET', '/proveedores/{id}/historial', 'ProveedorController@historial');
    // Se reemplaza PUT/DELETE por POST por limitaciones de formularios HTML sin override
    $this->addRoute('POST', '/proveedores/{id}', 'ProveedorController@update');
    $this->addRoute('POST', '/proveedores/{id}/eliminar', 'ProveedorController@destroy');
        
        // Rutas de ventas (RF13-RF15)
        $this->addRoute('GET', '/ventas', 'VentaController@index');
        $this->addRoute('GET', '/ventas/crear', 'VentaController@create');
        $this->addRoute('POST', '/ventas', 'VentaController@store');
        $this->addRoute('GET', '/ventas/{id}', 'VentaController@show');
        $this->addRoute('GET', '/ventas/{id}/comprobante', 'VentaController@comprobante');
    $this->addRoute('POST', '/ventas/{id}/anular', 'VentaController@anular');
        
        // Rutas de reportes (RF16-RF18)
        $this->addRoute('GET', '/reportes', 'ReporteController@index');
        $this->addRoute('GET', '/reportes/stock-bajo', 'ReporteController@stockBajo');
        $this->addRoute('GET', '/reportes/movimientos', 'ReporteController@movimientos');
        $this->addRoute('GET', '/reportes/ventas', 'ReporteController@ventas');
    }
    
    /**
     * Agregar una ruta
     */
    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    /**
     * Manejar la petición HTTP
     */
    public function handleRequest($uri, $method) {
        $method = strtoupper($method);
        
        // Buscar ruta coincidente
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchRoute($route['path'], $uri)) {
                $this->executeHandler($route['handler'], $uri);
                return;
            }
        }
        
        // Si no se encuentra la ruta, mostrar 404
        $this->handle404();
    }
    
    /**
     * Verificar si la ruta coincide con la URI
     */
    private function matchRoute($routePath, $uri) {
        // Convertir parámetros de ruta {id} a regex
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $uri);
    }
    
    /**
     * Extraer parámetros de la URI
     */
    private function extractParams($routePath, $uri) {
        $pattern = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $uri, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }
        
        return [];
    }
    
    /**
     * Ejecutar el controlador y método correspondiente
     */
    private function executeHandler($handler, $uri) {
        list($controllerName, $methodName) = explode('@', $handler);
        
        // Agregar namespace
        $controllerClass = "App\\Controllers\\{$controllerName}";
        
        // Verificar que la clase existe
        if (!class_exists($controllerClass)) {
            throw new Exception("Controlador no encontrado: {$controllerClass}");
        }
        
        // Crear instancia del controlador
        $controller = new $controllerClass();
        
        // Verificar que el método existe
        if (!method_exists($controller, $methodName)) {
            throw new Exception("Método no encontrado: {$methodName} en {$controllerClass}");
        }
        
        // Extraer parámetros de la URI si los hay
        $params = $this->extractParams($this->getCurrentRoutePath($uri), $uri);
        
        // Ejecutar el método
        call_user_func_array([$controller, $methodName], $params);
    }
    
    /**
     * Obtener la ruta actual (para extraer parámetros)
     */
    private function getCurrentRoutePath($uri) {
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route['path'], $uri)) {
                return $route['path'];
            }
        }
        return $uri;
    }
    
    /**
     * Manejar error 404
     */
    private function handle404() {
        http_response_code(404);
        echo "Página no encontrada - Error 404";
    }
    
    /**
     * Agregar middleware
     */
    public function addMiddleware($middleware) {
        $this->middlewares[] = $middleware;
    }
}
