<?php
/**
 * UserService - Sistema de Repuestos de Vehículos
 * Capa de Negocio - Lógica de negocio para usuarios
 */

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService {
    private $userRepository;
    
    public function __construct() {
        $this->userRepository = new UserRepository();
    }
    
    /**
     * Obtener todos los usuarios con paginación
     */
    public function getAllUsers($page = 1, $limit = ITEMS_POR_PAGINA) {
        $users = $this->userRepository->findAll($page, $limit);
        $total = $this->userRepository->count();
        
        return [
            'users' => $users,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ];
    }
    
    /**
     * Buscar usuarios
     */
    public function searchUsers($term, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $users = $this->userRepository->search($term, $page, $limit);
        $total = $this->userRepository->count();
        
        return [
            'users' => $users,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page,
            'search_term' => $term
        ];
    }
    
    /**
     * Obtener usuario por ID
     */
    public function getUserById($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new \Exception('ID de usuario inválido');
        }
        
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new \Exception('Usuario no encontrado');
        }
        
        return $user;
    }
    
    /**
     * Autenticar usuario por email y contraseña
     */
    public function authenticateUser($email, $password) {
        // Validar datos
        if (empty($email) || empty($password)) {
            throw new \Exception('Email y contraseña son requeridos');
        }
        
        // Normalizar email
        $email = strtolower(trim($email));
        
        // Buscar usuario por email
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            throw new \Exception('Credenciales incorrectas');
        }
        
        // Verificar password
        if (!$user->verifyPassword($password)) {
            throw new \Exception('Credenciales incorrectas');
        }
        
        // Verificar que el usuario esté activo
        if (!$user->isActivo()) {
            throw new \Exception('Su cuenta está desactivada');
        }
        
        return $user;
    }
    
    /**
     * Crear nuevo usuario
     */
    public function createUser($data) {
        // Validar datos requeridos
        if (empty($data['nombre']) || empty($data['email']) || empty($data['password'])) {
            throw new \Exception('Nombre, email y password son requeridos');
        }

        // Validar longitud mínima de password
        if (strlen($data['password']) < 6) {
            throw new \Exception('La contraseña debe tener al menos 6 caracteres');
        }

        // Si se proporciona confirmación de password, validar coincidencia
        if (isset($data['password_confirmation']) && $data['password'] !== $data['password_confirmation']) {
            throw new \Exception('La confirmación de contraseña no coincide');
        }
        
        // Verificar si el email ya existe
        if ($this->userRepository->emailExists($data['email'])) {
            throw new \Exception('El email ya está registrado');
        }

        // Normalizar email antes de continuar
        $data['email'] = strtolower(trim($data['email']));
        
        // Crear instancia del usuario
        $user = new User();
        $user->setNombre($data['nombre'])
             ->setEmail($data['email'])
             ->setPassword($data['password'])
             ->setRol($data['rol'] ?? ROLE_EMPLOYEE)
             ->setActivo($data['activo'] ?? true);
        
        // Validar el usuario
        $errors = $user->validate();
        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }
        
        // Guardar en la base de datos
        return $this->userRepository->create($user);
    }
    
    /**
     * Actualizar usuario
     */
    public function updateUser($id, $data) {
        // Obtener usuario existente
        $user = $this->getUserById($id);
        
        // Actualizar datos
        if (isset($data['nombre'])) {
            $user->setNombre($data['nombre']);
        }
        
        if (isset($data['email'])) {
            // Verificar si el nuevo email ya existe (excluyendo el usuario actual)
            if ($data['email'] !== $user->getEmail() && 
                $this->userRepository->emailExists($data['email'], $id)) {
                throw new \Exception('El email ya está registrado');
            }
            $user->setEmail($data['email']);
        }
        
        if (isset($data['rol'])) {
            $user->setRol($data['rol']);
        }
        
        if (isset($data['activo'])) {
            $user->setActivo($data['activo']);
        }
        
        // Validar el usuario actualizado
        $errors = $user->validate();
        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }
        
        // Guardar cambios
        return $this->userRepository->update($user);
    }
    
    /**
     * Cambiar password de usuario
     */
    public function changePassword($id, $currentPassword, $newPassword) {
        $user = $this->getUserById($id);
        
        // Verificar password actual
        if (!$user->verifyPassword($currentPassword)) {
            throw new \Exception('La contraseña actual es incorrecta');
        }
        
        // Validar nueva password
        if (strlen($newPassword) < 6) {
            throw new \Exception('La nueva contraseña debe tener al menos 6 caracteres');
        }
        
        // Actualizar password
        $this->userRepository->updatePassword($id, $newPassword);
        
        return true;
    }
    
    /**
     * Eliminar usuario
     */
    public function deleteUser($id) {
        $user = $this->getUserById($id);
        
        // No permitir eliminar el último administrador
        if ($user->isAdmin()) {
            $adminCount = $this->userRepository->findByRol(ROLE_ADMIN, 1, 1000);
            if (count($adminCount) <= 1) {
                throw new \Exception('No se puede eliminar el último administrador');
            }
        }
        
        return $this->userRepository->delete($id);
    }
    
    /**
     * Obtener estadísticas de usuarios
     */
    public function getUserStats() {
        return $this->userRepository->getStats();
    }
    
    /**
     * Obtener usuarios por rol
     */
    public function getUsersByRole($role, $page = 1, $limit = ITEMS_POR_PAGINA) {
        if (!in_array($role, [ROLE_ADMIN, ROLE_EMPLOYEE])) {
            throw new \Exception('Rol inválido');
        }
        
        $users = $this->userRepository->findByRol($role, $page, $limit);
        $total = $this->userRepository->count();
        
        return [
            'users' => $users,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page,
            'role' => $role
        ];
    }
    
    /**
     * Verificar permisos de usuario
     */
    public function hasPermission($userId, $action) {
        $user = $this->getUserById($userId);
        
        // Los administradores tienen todos los permisos
        if ($user->isAdmin()) {
            return true;
        }
        
        // Definir permisos por rol
        $permissions = [
            ROLE_EMPLOYEE => [
                'view_users', 'view_repuestos', 'view_inventario', 
                'view_ventas', 'create_ventas', 'view_reportes'
            ],
            ROLE_ADMIN => [
                'all' // Todos los permisos
            ]
        ];
        
        $userPermissions = $permissions[$user->getRol()] ?? [];
        
        return in_array($action, $userPermissions) || in_array('all', $userPermissions);
    }
}
