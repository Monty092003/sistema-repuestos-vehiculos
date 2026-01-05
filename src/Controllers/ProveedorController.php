<?php
/**
 * ProveedorController - Sistema de Repuestos de Vehículos
 * Capa de Presentación - CRUD de proveedores
 */

namespace App\Controllers;

use App\Services\ProveedorService;
use App\Controllers\AuthController;
use App\Core\Flash;
use App\Core\Csrf;

class ProveedorController {
    private $proveedorService;
    private $authController;
    
    public function __construct() {
        $this->proveedorService = new ProveedorService();
        $this->authController = new AuthController();
    }
    
    /**
     * Listar proveedores (RF11)
     */
    public function index() {
        // Verificar autenticación
        if (!$this->authController->isLoggedIn()) {
            Flash::error('Debes iniciar sesión para acceder a esta sección');
            header('Location: /login');
            exit;
        }

        // Verificar autorización (solo admin y gerente)
        if (!$this->authController->hasPermission('view_proveedores')) {
            Flash::error('No tienes permisos para acceder a esta sección');
            header('Location: /dashboard');
            exit;
        }
        
        try {
            // Verificar si se solicita vista de estadísticas
            $view = $_GET['view'] ?? 'list';
            
            if ($view === 'stats') {
                // Obtener estadísticas detalladas
                $stats = $this->proveedorService->getProveedorStats();
                $this->render('proveedores/stats', [
                    'title' => 'Estadísticas de Proveedores',
                    'stats' => $stats,
                    'success' => Flash::get('success'),
                    'error' => Flash::get('error')
                ]);
                return;
            }
            
            // Parámetros de búsqueda y paginación
            $search = $_GET['search'] ?? '';
            $page = max(1, intval($_GET['page'] ?? 1));
            
            // Obtener proveedores con paginación
            if (!empty($search)) {
                $data = $this->proveedorService->searchProveedores($search, $page);
            } else {
                $data = $this->proveedorService->getAllProveedores($page);
            }
            
            $this->render('proveedores/index', [
                'title' => 'Gestión de Proveedores',
                'proveedores' => $data['proveedores'],
                'total' => $data['total'],
                'pages' => $data['pages'],
                'current_page' => $data['current_page'],
                'search_term' => $search,
                'success' => Flash::get('success'),
                'error' => Flash::get('error')
            ]);
            
        } catch (\Exception $e) {
            Flash::error('Error al cargar proveedores: ' . $e->getMessage());
            $this->render('proveedores/index', [
                'title' => 'Gestión de Proveedores',
                'proveedores' => [],
                'total' => 0,
                'pages' => 0,
                'current_page' => 1,
                'search_term' => '',
                'view' => 'list',
                'stats' => [],
                'mas_utilizados' => [],
                'success' => '',
                'error' => Flash::get('error')
            ]);
        }
    }
    
    /**
     * Mostrar formulario de creación (RF11)
     */
    public function create() {
        // Verificar autenticación
        if (!$this->authController->isLoggedIn()) {
            Flash::error( 'Debes iniciar sesión para acceder a esta sección');
            header('Location: /login');
            exit;
        }

        // Verificar autorización (solo admin y gerente)
        if (!$this->authController->hasPermission('create_proveedores')) {
            Flash::error( 'No tienes permisos para acceder a esta sección');
            header('Location: /dashboard');
            exit;
        }
        
        $this->render('proveedores/create', [
            'title' => 'Crear Proveedor',
            'error' => Flash::get('error'),
            'success' => Flash::get('success')
        ]);
    }
    
    /**
     * Procesar creación de proveedor (RF11)
     */
    public function store() {
        // Verificar autenticación
        if (!$this->authController->isLoggedIn()) {
            Flash::error( 'Debes iniciar sesión para acceder a esta sección');
            header('Location: /login');
            exit;
        }

        // Verificar autorización (solo admin y gerente)
        if (!$this->authController->hasPermission('create_proveedores')) {
            Flash::error( 'No tienes permisos para acceder a esta sección');
            header('Location: /dashboard');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /proveedores');
            return;
        }
        
        // CSRF
        if (!Csrf::validateFromRequest()) {
            Flash::error( 'Token CSRF inválido o expirado');
            header('Location: /proveedores/crear');
            return;
        }
        
        try {
            $data = [
                'nombre' => $_POST['nombre'] ?? '',
                'contacto' => $_POST['contacto'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'email' => $_POST['email'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'activo' => isset($_POST['activo'])
            ];
            
            $proveedor = $this->proveedorService->createProveedor($data);
            
            Flash::success( 'Proveedor creado correctamente');
            header('Location: /proveedores');
            
        } catch (\Exception $e) {
            Flash::error( $e->getMessage());
            header('Location: /proveedores/crear');
        }
    }
    
    /**
     * Mostrar proveedor específico (RF11)
     */
    public function show($id) {
        // Verificar autenticación
        if (!$this->authController->isLoggedIn()) {
            Flash::error( 'Debes iniciar sesión para acceder a esta sección');
            header('Location: /login');
            exit;
        }

        // Verificar autorización (solo admin y gerente)
        if (!$this->authController->hasPermission('view_proveedores')) {
            Flash::error( 'No tienes permisos para acceder a esta sección');
            header('Location: /dashboard');
            exit;
        }
        
        try {
            $proveedor = $this->proveedorService->getProveedorById($id);
            
            if (!$proveedor) {
                Flash::error( 'Proveedor no encontrado');
                header('Location: /proveedores');
                return;
            }
            
            $this->render('proveedores/show', [
                'title' => 'Detalles del Proveedor',
                'proveedor' => $proveedor,
                'success' => Flash::get('success'),
                'error' => Flash::get('error')
            ]);
            
        } catch (\Exception $e) {
            Flash::error( 'Error al cargar proveedor: ' . $e->getMessage());
            header('Location: /proveedores');
        }
    }
    
    /**
     * Mostrar formulario de edición (RF11)
     */
    public function edit($id) {
        // Verificar autenticación
        if (!$this->authController->isLoggedIn()) {
            Flash::error( 'Debes iniciar sesión para acceder a esta sección');
            header('Location: /login');
            exit;
        }

        // Verificar autorización (solo admin y gerente)
        if (!$this->authController->hasPermission('edit_proveedores')) {
            Flash::error( 'No tienes permisos para acceder a esta sección');
            header('Location: /dashboard');
            exit;
        }
        
        try {
            $proveedor = $this->proveedorService->getProveedorById($id);
            
            if (!$proveedor) {
                Flash::error( 'Proveedor no encontrado');
                header('Location: /proveedores');
                return;
            }
            
            $this->render('proveedores/edit', [
                'title' => 'Editar Proveedor',
                'proveedor' => $proveedor,
                'error' => Flash::get('error'),
                'success' => Flash::get('success')
            ]);
            
        } catch (\Exception $e) {
            Flash::error( 'Error al cargar proveedor: ' . $e->getMessage());
            header('Location: /proveedores');
        }
    }
    
    /**
     * Procesar actualización de proveedor (RF11)
     */
    public function update($id) {
        // Verificar autenticación
        if (!$this->authController->isLoggedIn()) {
            Flash::error( 'Debes iniciar sesión para acceder a esta sección');
            header('Location: /login');
            exit;
        }

        // Verificar autorización (solo admin y gerente)
        if (!$this->authController->hasPermission('edit_proveedores')) {
            Flash::error( 'No tienes permisos para acceder a esta sección');
            header('Location: /dashboard');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /proveedores');
            return;
        }
        
        // CSRF
        if (!Csrf::validateFromRequest()) {
            Flash::error( 'Token CSRF inválido o expirado');
            header("Location: /proveedores/{$id}/editar");
            return;
        }
        
        try {
            $data = [
                'nombre' => $_POST['nombre'] ?? '',
                'contacto' => $_POST['contacto'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'email' => $_POST['email'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'activo' => isset($_POST['activo'])
            ];
            
            $this->proveedorService->updateProveedor($id, $data);
            
            Flash::success( 'Proveedor actualizado correctamente');
            header("Location: /proveedores/{$id}");
            
        } catch (\Exception $e) {
            Flash::error( $e->getMessage());
            header("Location: /proveedores/{$id}/editar");
        }
    }
    
    /**
     * Eliminar proveedor (RF11)
     */
    public function destroy($id) {
        // Verificar autenticación
        if (!$this->authController->isLoggedIn()) {
            Flash::error( 'Debes iniciar sesión para acceder a esta sección');
            header('Location: /login');
            exit;
        }

        // Verificar autorización (solo admin y gerente)
        if (!$this->authController->hasPermission('delete_proveedores')) {
            Flash::error( 'No tienes permisos para acceder a esta sección');
            header('Location: /dashboard');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /proveedores');
            return;
        }
        
        // CSRF
        if (!Csrf::validateFromRequest()) {
            Flash::error( 'Token CSRF inválido o expirado');
            header('Location: /proveedores');
            return;
        }
        
        try {
            $proveedor = $this->proveedorService->getProveedorById($id);
            
            if (!$proveedor) {
                Flash::error( 'Proveedor no encontrado');
                header('Location: /proveedores');
                return;
            }
            
            $this->proveedorService->deleteProveedor($id);
            
            Flash::success( 'Proveedor eliminado correctamente');
            header('Location: /proveedores');
            
        } catch (\Exception $e) {
            Flash::error( $e->getMessage());
            header('Location: /proveedores');
        }
    }
    
    /**
     * Mostrar historial de repuestos de un proveedor
     */
    public function historial($id) {
        // Verificar autenticación
        if (!$this->authController->isLoggedIn()) {
            Flash::error( 'Debes iniciar sesión para acceder a esta sección');
            header('Location: /login');
            exit;
        }

        // Verificar autorización (solo admin y gerente)
        if (!$this->authController->hasPermission('view_proveedores')) {
            Flash::error( 'No tienes permisos para acceder a esta sección');
            header('Location: /dashboard');
            exit;
        }
        
        try {
            $proveedor = $this->proveedorService->getProveedorById($id);
            
            if (!$proveedor) {
                Flash::error( 'Proveedor no encontrado');
                header('Location: /proveedores');
                return;
            }
            
            // Parámetros de paginación
            $page = max(1, intval($_GET['page'] ?? 1));
            
            // Obtener historial completo paginado usando el método que existe
            $result = $this->proveedorService->getHistorialCompras($id, $page);
            
            $this->render('proveedores/historial', [
                'title' => 'Historial de Compras - ' . $proveedor->getNombre(),
                'proveedor' => $result['proveedor'],
                'movimientos' => $result['movimientos'],
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current_page' => $result['current_page'],
                'success' => Flash::get('success'),
                'error' => Flash::get('error')
            ]);
            
        } catch (\Exception $e) {
            Flash::error( 'Error al cargar historial: ' . $e->getMessage());
            header('Location: /proveedores');
        }
    }
    
    /**
     * Renderizar vista
     */
    private function render($view, $data = []) {
        extract($data);
        $viewPath = SRC_PATH . '/Views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Vista no encontrada: {$view}";
        }
    }
    
    /**
     * Redireccionar a URL
     */
    private function redirect($url) {
        header("Location: " . BASE_URL . ltrim($url, '/'));
        exit;
    }
}
