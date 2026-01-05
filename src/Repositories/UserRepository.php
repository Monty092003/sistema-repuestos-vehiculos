<?php
/**
 * UserRepository - Sistema de Repuestos de Vehículos
 * Capa de Datos - Operaciones de base de datos para usuarios
 */

namespace App\Repositories;

use App\Models\User;
use App\Core\Database;

class UserRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Buscar usuario por ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ? AND activo = 1";
        $stmt = $this->db->query($sql, [$id]);
        $data = $stmt->fetch();
        
        return $data ? new User($data) : null;
    }
    
    /**
     * Buscar usuario por email
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = ? AND activo = 1";
        $stmt = $this->db->query($sql, [$email]);
        $data = $stmt->fetch();
        
        return $data ? new User($data) : null;
    }
    
    /**
     * Obtener todos los usuarios con paginación
     */
    public function findAll($page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT * FROM usuarios WHERE activo = 1 ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->query($sql, [$limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            return new User($row);
        }, $data);
    }
    
    /**
     * Contar total de usuarios
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE activo = 1";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        
        return (int)$result['total'];
    }
    
    /**
     * Buscar usuarios por nombre o email
     */
    public function search($term, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        $searchTerm = "%{$term}%";
        
        $sql = "SELECT * FROM usuarios 
                WHERE activo = 1 
                AND (nombre LIKE ? OR email LIKE ?) 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$searchTerm, $searchTerm, $limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            return new User($row);
        }, $data);
    }
    
    /**
     * Crear nuevo usuario
     */
    public function create(User $user) {
        $sql = "INSERT INTO usuarios (nombre, email, password, rol, activo) 
                VALUES (?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $user->getNombre(),
            $user->getEmail(),
            $user->getPasswordHash(), // Ya viene hasheado del modelo
            $user->getRol(),
            $user->isActivo() ? 1 : 0
        ]);
        
        $user->setId($this->db->lastInsertId());
        return $user;
    }
    
    /**
     * Actualizar usuario
     */
    public function update(User $user) {
        $sql = "UPDATE usuarios 
                SET nombre = ?, email = ?, rol = ?, activo = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
        $this->db->query($sql, [
            $user->getNombre(),
            $user->getEmail(),
            $user->getRol(),
            $user->isActivo() ? 1 : 0,
            $user->getId()
        ]);
        
        return $user;
    }
    
    /**
     * Actualizar password de usuario
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $sql = "UPDATE usuarios SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$hashedPassword, $userId]);
        
        return true;
    }
    
    /**
     * Eliminar usuario (soft delete)
     */
    public function delete($id) {
        $sql = "UPDATE usuarios SET activo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$id]);
        
        return true;
    }
    
    /**
     * Verificar si email ya existe
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM usuarios WHERE email = ? AND activo = 1";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        
        return (int)$result['count'] > 0;
    }
    
    /**
     * Obtener usuarios por rol
     */
    public function findByRol($rol, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT * FROM usuarios WHERE rol = ? AND activo = 1 ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->query($sql, [$rol, $limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            return new User($row);
        }, $data);
    }
    
    /**
     * Obtener estadísticas de usuarios
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN rol = 'administrador' THEN 1 ELSE 0 END) as administradores,
                    SUM(CASE WHEN rol = 'empleado' THEN 1 ELSE 0 END) as empleados,
                    SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as nuevos_mes
                FROM usuarios 
                WHERE activo = 1";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
}
