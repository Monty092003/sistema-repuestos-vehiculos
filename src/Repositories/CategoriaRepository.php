<?php
/**
 * CategoriaRepository - Sistema de Repuestos de Vehículos
 * Capa de Datos - Operaciones de base de datos para categorías
 */

namespace App\Repositories;

use App\Models\Categoria;
use App\Core\Database;

class CategoriaRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Buscar categoría por ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM categorias WHERE id = ? AND activa = 1";
        $stmt = $this->db->query($sql, [$id]);
        $data = $stmt->fetch();
        
        return $data ? new Categoria($data) : null;
    }
    
    /**
     * Obtener todas las categorías activas
     */
    public function findAll() {
        $sql = "SELECT * FROM categorias WHERE activa = 1 ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            return new Categoria($row);
        }, $data);
    }
    
    /**
     * Obtener todas las categorías (incluyendo inactivas)
     */
    public function findAllWithInactive() {
        $sql = "SELECT * FROM categorias ORDER BY activa DESC, nombre ASC";
        $stmt = $this->db->query($sql);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            return new Categoria($row);
        }, $data);
    }
    
    /**
     * Buscar categorías por nombre
     */
    public function search($term) {
        $searchTerm = "%{$term}%";
        
        $sql = "SELECT * FROM categorias 
                WHERE activa = 1 
                AND (nombre LIKE ? OR descripcion LIKE ?) 
                ORDER BY nombre ASC";
        
        $stmt = $this->db->query($sql, [$searchTerm, $searchTerm]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            return new Categoria($row);
        }, $data);
    }
    
    /**
     * Crear nueva categoría
     */
    public function create(Categoria $categoria) {
        $sql = "INSERT INTO categorias (nombre, descripcion, activa) VALUES (?, ?, ?)";
        
        $this->db->query($sql, [
            $categoria->getNombre(),
            $categoria->getDescripcion(),
            $categoria->isActiva() ? 1 : 0
        ]);
        
        $categoria->id = $this->db->lastInsertId();
        return $categoria;
    }
    
    /**
     * Actualizar categoría
     */
    public function update(Categoria $categoria) {
        $sql = "UPDATE categorias 
                SET nombre = ?, descripcion = ?, activa = ? 
                WHERE id = ?";
        
        $this->db->query($sql, [
            $categoria->getNombre(),
            $categoria->getDescripcion(),
            $categoria->isActiva() ? 1 : 0,
            $categoria->getId()
        ]);
        
        return $categoria;
    }
    
    /**
     * Eliminar categoría (soft delete)
     */
    public function delete($id) {
        // Verificar si hay repuestos asociados
        $sql = "SELECT COUNT(*) as count FROM repuestos WHERE categoria_id = ? AND activo = 1";
        $stmt = $this->db->query($sql, [$id]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            throw new \Exception('No se puede eliminar la categoría porque tiene repuestos asociados');
        }
        
        $sql = "UPDATE categorias SET activa = 0 WHERE id = ?";
        $this->db->query($sql, [$id]);
        
        return true;
    }
    
    /**
     * Verificar si el nombre de categoría ya existe
     */
    public function nombreExists($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM categorias WHERE nombre = ? AND activa = 1";
        $params = [$nombre];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        
        return (int)$result['count'] > 0;
    }
    
    /**
     * Obtener categorías con conteo de repuestos
     */
    public function findAllWithRepuestoCount() {
        $sql = "SELECT c.*, COUNT(r.id) as total_repuestos
                FROM categorias c
                LEFT JOIN repuestos r ON c.id = r.categoria_id AND r.activo = 1
                WHERE c.activa = 1
                GROUP BY c.id
                ORDER BY c.nombre ASC";
        
        $stmt = $this->db->query($sql);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $categoria = new Categoria($row);
            $categoria->totalRepuestos = $row['total_repuestos'];
            return $categoria;
        }, $data);
    }
    
    /**
     * Obtener estadísticas de categorías
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN activa = 1 THEN 1 ELSE 0 END) as activas,
                    SUM(CASE WHEN activa = 0 THEN 1 ELSE 0 END) as inactivas
                FROM categorias";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
}
