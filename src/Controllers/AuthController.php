<?php
/**
 * AuthController - Sistema de Repuestos de Vehículos
 * Capa de Presentación - Manejo de autenticación
 */

namespace App\Controllers;

use App\Services\UserService;
use App\Models\User;
use App\Core\Flash;

class AuthController {
    private $userService;
    
    public function __construct() {
        $this->userService = new UserService();
    }
    
    /**
     * Mostrar formulario de login
     */
    public function showLogin() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        // Si ya está logueado, redirigir al dashboard
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
            return;
        }
        
        $this->render('auth/login', [
            'title' => 'Iniciar Sesión',
            'error' => Flash::get('error')
        ]);
    }
    
    /**
     * Procesar login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }
        if (!\App\Core\Csrf::validateFromRequest()) {
            Flash::error('Token CSRF inválido o expirado');
            $this->redirect('/login');
            return;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        try {
            // Autenticar usuario usando el servicio (respetando arquitectura en capas)
            $user = $this->userService->authenticateUser($email, $password);
            
            // Iniciar sesión de forma segura
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            session_regenerate_id(true); // Mitigar session fixation
            $this->startSession($user);
            
            // Redirigir según el rol
            $redirectUrl = $user->isAdmin() ? '/dashboard' : '/dashboard';
            $this->redirect($redirectUrl);
            
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/login');
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        // No requiere CSRF estricto pero se podría exigir POST; opcional.
        session_destroy();
        
        // Redirigir al login
        $this->redirect('/login');
    }
    
    /**
     * Iniciar sesión de usuario
     */
    private function startSession(User $user) {
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_name'] = $user->getNombre();
        $_SESSION['user_email'] = $user->getEmail();
        $_SESSION['user_role'] = $user->getRol();
        $_SESSION['login_time'] = time();
    }
    
    /**
     * Verificar si el usuario está logueado
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && 
               isset($_SESSION['login_time']) && 
               (time() - $_SESSION['login_time']) < SESSION_TIMEOUT;
    }
    
    /**
     * Obtener usuario actual
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            return $this->userService->getUserById($_SESSION['user_id']);
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Verificar permisos del usuario actual
     */
    public function hasPermission($action) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        return $this->userService->hasPermission($_SESSION['user_id'], $action);
    }
    
    /**
     * Middleware de autenticación
     */
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            Flash::error(MSG_LOGIN_REQUIRED);
            $this->redirect('/login');
            exit;
        }
    }
    
    /**
     * Middleware de permisos
     */
    public function requirePermission($action) {
        $this->requireAuth();
        
        if (!$this->hasPermission($action)) {
            http_response_code(403);
            Flash::error('Acceso denegado');
            $this->redirect('/login');
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
