<?php
/**
 * UserController - Sistema de Repuestos de Vehículos
 * Capa de Presentación - CRUD de usuarios
 */

namespace App\Controllers;

use App\Services\UserService;
use App\Controllers\AuthController;
use App\Core\Flash;
use App\Core\Csrf;

class UserController {
    private $userService;
    private $authController;
    
    public function __construct() {
        $this->userService = new UserService();
        $this->authController = new AuthController();
    }
    
    /**
     * Listar usuarios (RF1)
     */
    public function index() {
        $this->authController->requirePermission('view_users');
        
        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        
        try {
            if (!empty($search)) {
                $result = $this->userService->searchUsers($search, $page);
            } else {
                $result = $this->userService->getAllUsers($page);
            }
            
            $this->render('users/index', [
                'title' => 'Gestión de Usuarios',
                'users' => $result['users'],
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current_page' => $result['current_page'],
                'search_term' => $search,
                'success' => Flash::get('success'),
                'error' => Flash::get('error')
            ]);
            
        } catch (\Exception $e) {
            $this->render('users/index', [
                'title' => 'Gestión de Usuarios',
                'users' => [],
                'total' => 0,
                'pages' => 0,
                'current_page' => 1,
                'search_term' => $search,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mostrar formulario de creación (RF1)
     */
    public function create() {
        $this->authController->requirePermission('create_users');
        
        $this->render('users/create', [
            'title' => 'Crear Usuario',
            'roles' => [
                ROLE_ADMIN => 'Administrador',
                ROLE_EMPLOYEE => 'Empleado'
            ],
            'error' => Flash::get('error')
        ]);
    }
    
    /**
     * Procesar creación de usuario (RF1)
     */
    public function store() {
        $this->authController->requirePermission('create_users');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios');
            return;
        }
        if (!Csrf::validateFromRequest()) {
            Flash::error('Token CSRF inválido o expirado');
            $this->redirect('/usuarios/crear');
            return;
        }
        
        try {
            $data = [
                'nombre' => $_POST['nombre'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'rol' => $_POST['rol'] ?? ROLE_EMPLOYEE,
                'activo' => isset($_POST['activo'])
            ];
            
            $user = $this->userService->createUser($data);
            
            Flash::success('Usuario creado correctamente');
            $this->redirect('/usuarios');
            
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/usuarios/crear');
        }
    }
    
    /**
     * Mostrar usuario específico (RF1)
     */
    public function show($id) {
        $this->authController->requirePermission('view_users');
        
        try {
            $user = $this->userService->getUserById($id);
            
            $this->render('users/show', [
                'title' => 'Detalles del Usuario',
                'user' => $user,
                'error' => Flash::get('error')
            ]);
            
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/usuarios');
        }
    }
    
    /**
     * Mostrar formulario de edición (RF1)
     */
    public function edit($id) {
        $this->authController->requirePermission('edit_users');
        
        try {
            $user = $this->userService->getUserById($id);
            
            $this->render('users/edit', [
                'title' => 'Editar Usuario',
                'user' => $user,
                'roles' => [
                    ROLE_ADMIN => 'Administrador',
                    ROLE_EMPLOYEE => 'Empleado'
                ],
                'error' => Flash::get('error')
            ]);
            
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/usuarios');
        }
    }
    
    /**
     * Procesar actualización de usuario (RF1)
     */
    public function update($id) {
        $this->authController->requirePermission('edit_users');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios');
            return;
        }
        if (!Csrf::validateFromRequest()) {
            Flash::error('Token CSRF inválido o expirado');
            $this->redirect("/usuarios/{$id}/editar");
            return;
        }
        
        try {
            $data = [
                'nombre' => $_POST['nombre'] ?? '',
                'email' => $_POST['email'] ?? '',
                'rol' => $_POST['rol'] ?? ROLE_EMPLOYEE,
                'activo' => isset($_POST['activo'])
            ];
            
            $this->userService->updateUser($id, $data);
            
            Flash::success('Usuario actualizado correctamente');
            $this->redirect('/usuarios');
            
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect("/usuarios/{$id}/editar");
        }
    }
    
    /**
     * Eliminar usuario (RF1)
     */
    public function destroy($id) {
        $this->authController->requirePermission('delete_users');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios');
            return;
        }
        if (!Csrf::validateFromRequest()) {
            Flash::error('Token CSRF inválido o expirado');
            $this->redirect('/usuarios');
            return;
        }
        
        try {
            $this->userService->deleteUser($id);
            
            Flash::success('Usuario eliminado correctamente');
            $this->redirect('/usuarios');
            
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/usuarios');
        }
    }
    
    /**
     * Mostrar formulario de cambio de contraseña
     */
    public function changePassword($id) {
        $this->authController->requireAuth();
        
        // Solo puede cambiar su propia contraseña o ser admin
        $currentUser = $this->authController->getCurrentUser();
        if ($currentUser->getId() != $id && !$currentUser->isAdmin()) {
            Flash::error('No tiene permisos para cambiar esta contraseña');
            $this->redirect('/usuarios');
            return;
        }
        
        try {
            $user = $this->userService->getUserById($id);
            
            $this->render('users/change-password', [
                'title' => 'Cambiar Contraseña',
                'user' => $user,
                'error' => Flash::get('error')
            ]);
            
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/usuarios');
        }
    }
    
    /**
     * Procesar cambio de contraseña
     */
    public function updatePassword($id) {
        $this->authController->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios');
            return;
        }
        if (!Csrf::validateFromRequest()) {
            Flash::error('Token CSRF inválido o expirado');
            $this->redirect("/usuarios/{$id}/cambiar-password");
            return;
        }
        
        try {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if ($newPassword !== $confirmPassword) {
                throw new \Exception('Las contraseñas no coinciden');
            }
            
            $this->userService->changePassword($id, $currentPassword, $newPassword);
            
            Flash::success('Contraseña actualizada correctamente');
            $this->redirect('/usuarios');
            
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect("/usuarios/{$id}/cambiar-password");
        }
    }
    
    /**
     * Redirigir a una URL
     */
    private function redirect($url) {
        header("Location: " . BASE_URL . ltrim($url, '/'));
        exit;
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
}
