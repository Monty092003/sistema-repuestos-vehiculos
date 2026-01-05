<?php
/**
 * ProveedorRepository - Sistema de Repuestos de Vehículos
 * Capa de Datos - Operaciones de base de datos para proveedores
 */

namespace App\Repositories;

use App\Models\Proveedor;
use App\Core\Database;

class ProveedorRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Buscar proveedor por ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM proveedores WHERE id = ? AND activo = 1";
        $stmt = $this->db->query($sql, [$id]);
        $data = $stmt->fetch();
        
        return $data ? new Proveedor($data) : null;
    }
    
    /**
     * Obtener todos los proveedores con paginación
     */
    public function findAll($page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT * FROM proveedores WHERE activo = 1 ORDER BY nombre ASC LIMIT ? OFFSET ?";
        $stmt = $this->db->query($sql, [$limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            return new Proveedor($row);
        }, $data);
    }
    
    /**
     * Contar total de proveedores
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM proveedores WHERE activo = 1";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        
        return (int)$result['total'];
    }
    
    /**
     * Buscar proveedores por nombre
     */
    public function search($term, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        $searchTerm = "%{$term}%";
        
        $sql = "SELECT * FROM proveedores 
                WHERE activo = 1 
                AND (nombre LIKE ? OR contacto LIKE ? OR email LIKE ?) 
                ORDER BY nombre ASC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$searchTerm, $searchTerm, $searchTerm, $limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            return new Proveedor($row);
        }, $data);
    }
    
    /**
     * Crear nuevo proveedor (RF11)
     */
    public function create(Proveedor $proveedor) {
        $sql = "INSERT INTO proveedores (nombre, contacto, telefono, email, direccion, activo) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $proveedor->getNombre(),
            $proveedor->getContacto(),
            $proveedor->getTelefono(),
            $proveedor->getEmail(),
            $proveedor->getDireccion(),
            $proveedor->isActivo() ? 1 : 0
        ]);
        
        $proveedor->setId($this->db->lastInsertId());
        return $proveedor;
    }
    
    /**
     * Actualizar proveedor (RF11)
     */
    public function update(Proveedor $proveedor) {
        $sql = "UPDATE proveedores 
                SET nombre = ?, contacto = ?, telefono = ?, email = ?, direccion = ?, 
                    activo = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
        $this->db->query($sql, [
            $proveedor->getNombre(),
            $proveedor->getContacto(),
            $proveedor->getTelefono(),
            $proveedor->getEmail(),
            $proveedor->getDireccion(),
            $proveedor->isActivo() ? 1 : 0,
            $proveedor->getId()
        ]);
        
        return $proveedor;
    }
    
    /**
     * Eliminar proveedor (RF11) - Soft delete
     */
    public function delete($id) {
        // Verificar si hay movimientos de inventario asociados
        $sql = "SELECT COUNT(*) as count FROM movimientos_inventario WHERE proveedor_id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            throw new \Exception('No se puede eliminar el proveedor porque tiene movimientos de inventario asociados');
        }
        
        $sql = "UPDATE proveedores SET activo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$id]);
        
        return true;
    }
    
    /**
     * Verificar si el nombre ya existe
     */
    public function nombreExists($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM proveedores WHERE LOWER(nombre) = LOWER(?) AND activo = 1";
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
     * Verificar si el email ya existe
     */
    public function emailExists($email, $excludeId = null) {
        if (empty($email)) return false;
        
        $sql = "SELECT COUNT(*) as count FROM proveedores WHERE LOWER(email) = LOWER(?) AND activo = 1";
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
     * Obtener proveedores con estadísticas de compras
     */
    public function findAllWithStats($page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT p.*, 
                       COUNT(m.id) as total_movimientos,
                       SUM(CASE WHEN m.tipo = 'entrada' THEN m.cantidad ELSE 0 END) as total_entradas,
                       MAX(m.fecha_movimiento) as ultima_compra
                FROM proveedores p
                LEFT JOIN movimientos_inventario m ON p.id = m.proveedor_id
                WHERE p.activo = 1
                GROUP BY p.id
                ORDER BY p.nombre ASC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $proveedor = new Proveedor($row);
            // Retornar array con proveedor y estadísticas para evitar propiedades dinámicas
            return [
                'proveedor' => $proveedor,
                'total_movimientos' => $row['total_movimientos'],
                'total_entradas' => $row['total_entradas'],
                'ultima_compra' => $row['ultima_compra']
            ];
        }, $data);
    }
    
    /**
     * Obtener estadísticas de proveedores
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN activo = 0 THEN 1 ELSE 0 END) as inactivos,
                    SUM(CASE WHEN email IS NOT NULL AND email != '' THEN 1 ELSE 0 END) as con_email,
                    SUM(CASE WHEN telefono IS NOT NULL AND telefono != '' THEN 1 ELSE 0 END) as con_telefono
                FROM proveedores";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
    
    /**
     * Obtener proveedores más utilizados
     */
    public function getMasUtilizados($limit = 10) {
        $sql = "SELECT p.*, COUNT(m.id) as total_movimientos
                FROM proveedores p
                LEFT JOIN movimientos_inventario m ON p.id = m.proveedor_id
                WHERE p.activo = 1
                GROUP BY p.id
                ORDER BY total_movimientos DESC
                LIMIT ?";
        
        $stmt = $this->db->query($sql, [$limit]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $proveedor = new Proveedor($row);
            // Retornar array con proveedor y estadísticas para evitar propiedades dinámicas
            return [
                'proveedor' => $proveedor,
                'total_movimientos' => $row['total_movimientos']
            ];
        }, $data);
    }

    /**
     * Contar resultados de búsqueda (para paginación exacta)
     */
    public function countSearch($term) {
        $searchTerm = "%{$term}%";
        $sql = "SELECT COUNT(*) as total FROM proveedores 
                WHERE activo = 1 
                  AND (nombre LIKE ? OR contacto LIKE ? OR email LIKE ?)";
        $stmt = $this->db->query($sql, [$searchTerm, $searchTerm, $searchTerm]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Obtener estadísticas detalladas de proveedores
     */
    public function getDetailedStats() {
        // Estadísticas generales
        $generalSql = "SELECT 
            COUNT(*) as total_proveedores,
            COUNT(CASE WHEN activo = 1 THEN 1 END) as activos,
            COUNT(CASE WHEN activo = 0 THEN 1 END) as inactivos
            FROM proveedores";
        $generalStmt = $this->db->query($generalSql);
        $general = $generalStmt->fetch();
        
        // Top 5 proveedores con más compras
        $topSql = "SELECT p.nombre, COUNT(mi.id) as total_movimientos,
                   SUM(CASE WHEN mi.tipo = 'entrada' THEN mi.cantidad ELSE 0 END) as total_compras
                   FROM proveedores p
                   LEFT JOIN movimientos_inventario mi ON p.id = mi.proveedor_id
                   WHERE p.activo = 1
                   GROUP BY p.id, p.nombre
                   ORDER BY total_compras DESC, total_movimientos DESC
                   LIMIT 5";
        $topStmt = $this->db->query($topSql);
        $topProveedores = $topStmt->fetchAll();
        
        // Estadísticas por mes (últimos 6 meses)
        $monthsSql = "SELECT 
                      DATE_FORMAT(mi.fecha_movimiento, '%Y-%m') as mes,
                      COUNT(DISTINCT p.id) as proveedores_activos,
                      COUNT(mi.id) as total_movimientos,
                      SUM(mi.cantidad) as total_cantidad
                      FROM movimientos_inventario mi
                      JOIN proveedores p ON mi.proveedor_id = p.id
                      WHERE mi.fecha_movimiento >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                        AND mi.tipo = 'entrada'
                      GROUP BY DATE_FORMAT(mi.fecha_movimiento, '%Y-%m')
                      ORDER BY mes DESC";
        $monthsStmt = $this->db->query($monthsSql);
        $porMes = $monthsStmt->fetchAll();
        
        return [
            'general' => $general,
            'top_proveedores' => $topProveedores,
            'por_mes' => $porMes
        ];
    }
}
