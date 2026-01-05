<?php
/**
 * MovimientoInventarioRepository - Sistema de Repuestos de Vehículos
 * Capa de Datos - Operaciones de base de datos para movimientos de inventario
 */

namespace App\Repositories;

use App\Models\MovimientoInventario;
use App\Core\Database;

class MovimientoInventarioRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Buscar movimiento por ID
     */
    public function findById($id) {
        $sql = "SELECT m.*, r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                       p.nombre as proveedor_nombre, u.nombre as usuario_nombre
                FROM movimientos_inventario m
                LEFT JOIN repuestos r ON m.repuesto_id = r.id
                LEFT JOIN proveedores p ON m.proveedor_id = p.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                WHERE m.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $data = $stmt->fetch();
        
        if ($data) {
            $movimiento = new MovimientoInventario($data);
            $movimiento->setRepuesto($data['repuesto_nombre'] . ' (' . $data['repuesto_codigo'] . ')');
            $movimiento->setProveedor($data['proveedor_nombre']);
            $movimiento->setUsuario($data['usuario_nombre']);
            return $movimiento;
        }
        
        return null;
    }
    
    /**
     * Obtener todos los movimientos con paginación
     */
    public function findAll($page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT m.*, r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                       p.nombre as proveedor_nombre, u.nombre as usuario_nombre
                FROM movimientos_inventario m
                LEFT JOIN repuestos r ON m.repuesto_id = r.id
                LEFT JOIN proveedores p ON m.proveedor_id = p.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                ORDER BY m.fecha_movimiento DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $movimiento = new MovimientoInventario($row);
            $movimiento->setRepuesto($row['repuesto_nombre'] . ' (' . $row['repuesto_codigo'] . ')');
            $movimiento->setProveedor($row['proveedor_nombre']);
            $movimiento->setUsuario($row['usuario_nombre']);
            return $movimiento;
        }, $data);
    }
    
    /**
     * Contar total de movimientos
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM movimientos_inventario";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        
        return (int)$result['total'];
    }
    
    /**
     * Contar movimientos por repuesto
     */
    public function countByRepuesto($repuestoId) {
        $sql = "SELECT COUNT(*) as total FROM movimientos_inventario WHERE repuesto_id = ?";
        $stmt = $this->db->query($sql, [$repuestoId]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Contar movimientos por tipo
     */
    public function countByTipo($tipo) {
        $sql = "SELECT COUNT(*) as total FROM movimientos_inventario WHERE tipo = ?";
        $stmt = $this->db->query($sql, [$tipo]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Contar movimientos por rango de fechas (inclusive)
     */
    public function countByFechaRange($fechaInicio, $fechaFin) {
        $sql = "SELECT COUNT(*) as total FROM movimientos_inventario WHERE DATE(fecha_movimiento) BETWEEN ? AND ?";
        $stmt = $this->db->query($sql, [$fechaInicio, $fechaFin]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Contar movimientos por usuario
     */
    public function countByUsuario($usuarioId) {
        $sql = "SELECT COUNT(*) as total FROM movimientos_inventario WHERE usuario_id = ?";
        $stmt = $this->db->query($sql, [$usuarioId]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Contar movimientos por proveedor
     */
    public function countByProveedor($proveedorId) {
        $sql = "SELECT COUNT(*) as total FROM movimientos_inventario WHERE proveedor_id = ?";
        $stmt = $this->db->query($sql, [$proveedorId]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Obtener movimientos por repuesto
     */
    public function findByRepuesto($repuestoId, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT m.*, r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                       p.nombre as proveedor_nombre, u.nombre as usuario_nombre
                FROM movimientos_inventario m
                LEFT JOIN repuestos r ON m.repuesto_id = r.id
                LEFT JOIN proveedores p ON m.proveedor_id = p.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                WHERE m.repuesto_id = ?
                ORDER BY m.fecha_movimiento DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$repuestoId, $limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $movimiento = new MovimientoInventario($row);
            $movimiento->setRepuesto($row['repuesto_nombre'] . ' (' . $row['repuesto_codigo'] . ')');
            $movimiento->setProveedor($row['proveedor_nombre']);
            $movimiento->setUsuario($row['usuario_nombre']);
            return $movimiento;
        }, $data);
    }
    
    /**
     * Obtener movimientos por tipo
     */
    public function findByTipo($tipo, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT m.*, r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                       p.nombre as proveedor_nombre, u.nombre as usuario_nombre
                FROM movimientos_inventario m
                LEFT JOIN repuestos r ON m.repuesto_id = r.id
                LEFT JOIN proveedores p ON m.proveedor_id = p.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                WHERE m.tipo = ?
                ORDER BY m.fecha_movimiento DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$tipo, $limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $movimiento = new MovimientoInventario($row);
            $movimiento->setRepuesto($row['repuesto_nombre'] . ' (' . $row['repuesto_codigo'] . ')');
            $movimiento->setProveedor($row['proveedor_nombre']);
            $movimiento->setUsuario($row['usuario_nombre']);
            return $movimiento;
        }, $data);
    }
    
    /**
     * Obtener movimientos por rango de fechas
     */
    public function findByFechaRange($fechaInicio, $fechaFin, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT m.*, r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                       p.nombre as proveedor_nombre, u.nombre as usuario_nombre
                FROM movimientos_inventario m
                LEFT JOIN repuestos r ON m.repuesto_id = r.id
                LEFT JOIN proveedores p ON m.proveedor_id = p.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                WHERE DATE(m.fecha_movimiento) BETWEEN ? AND ?
                ORDER BY m.fecha_movimiento DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$fechaInicio, $fechaFin, $limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $movimiento = new MovimientoInventario($row);
            $movimiento->setRepuesto($row['repuesto_nombre'] . ' (' . $row['repuesto_codigo'] . ')');
            $movimiento->setProveedor($row['proveedor_nombre']);
            $movimiento->setUsuario($row['usuario_nombre']);
            return $movimiento;
        }, $data);
    }
    
    /**
     * Crear nuevo movimiento (RF8, RF9)
     */
    public function create(MovimientoInventario $movimiento) {
        $sql = "INSERT INTO movimientos_inventario 
                (repuesto_id, tipo, cantidad, motivo, proveedor_id, usuario_id, observaciones) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $movimiento->getRepuestoId(),
            $movimiento->getTipo(),
            $movimiento->getCantidad(),
            $movimiento->getMotivo(),
            $movimiento->getProveedorId(),
            $movimiento->getUsuarioId(),
            $movimiento->getObservaciones()
        ]);
        
        $movimiento->setId($this->db->lastInsertId());
        return $movimiento;
    }
    
    /**
     * Obtener estadísticas de movimientos
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total_movimientos,
                    SUM(CASE WHEN tipo = 'entrada' THEN 1 ELSE 0 END) as total_entradas,
                    SUM(CASE WHEN tipo = 'salida' THEN 1 ELSE 0 END) as total_salidas,
                    SUM(CASE WHEN tipo = 'ajuste' THEN 1 ELSE 0 END) as total_ajustes,
                    SUM(CASE WHEN tipo = 'entrada' THEN cantidad ELSE 0 END) as total_cantidad_entrada,
                    SUM(CASE WHEN tipo = 'salida' THEN cantidad ELSE 0 END) as total_cantidad_salida
                FROM movimientos_inventario";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
    
    /**
     * Obtener movimientos recientes
     */
    public function getRecientes($limit = 10) {
        $sql = "SELECT m.*, r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                       p.nombre as proveedor_nombre, u.nombre as usuario_nombre
                FROM movimientos_inventario m
                LEFT JOIN repuestos r ON m.repuesto_id = r.id
                LEFT JOIN proveedores p ON m.proveedor_id = p.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                ORDER BY m.fecha_movimiento DESC 
                LIMIT ?";
        
        $stmt = $this->db->query($sql, [$limit]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $movimiento = new MovimientoInventario($row);
            $movimiento->setRepuesto($row['repuesto_nombre'] . ' (' . $row['repuesto_codigo'] . ')');
            $movimiento->setProveedor($row['proveedor_nombre']);
            $movimiento->setUsuario($row['usuario_nombre']);
            return $movimiento;
        }, $data);
    }
    
    /**
     * Obtener movimientos por usuario
     */
    public function findByUsuario($usuarioId, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT m.*, r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                       p.nombre as proveedor_nombre, u.nombre as usuario_nombre
                FROM movimientos_inventario m
                LEFT JOIN repuestos r ON m.repuesto_id = r.id
                LEFT JOIN proveedores p ON m.proveedor_id = p.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                WHERE m.usuario_id = ?
                ORDER BY m.fecha_movimiento DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$usuarioId, $limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $movimiento = new MovimientoInventario($row);
            $movimiento->setRepuesto($row['repuesto_nombre'] . ' (' . $row['repuesto_codigo'] . ')');
            $movimiento->setProveedor($row['proveedor_nombre']);
            $movimiento->setUsuario($row['usuario_nombre']);
            return $movimiento;
        }, $data);
    }
    
    /**
     * Obtener movimientos por proveedor
     */
    public function findByProveedor($proveedorId, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT m.*, r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                       p.nombre as proveedor_nombre, u.nombre as usuario_nombre
                FROM movimientos_inventario m
                LEFT JOIN repuestos r ON m.repuesto_id = r.id
                LEFT JOIN proveedores p ON m.proveedor_id = p.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                WHERE m.proveedor_id = ?
                ORDER BY m.fecha_movimiento DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$proveedorId, $limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $movimiento = new MovimientoInventario($row);
            $movimiento->setRepuesto($row['repuesto_nombre'] . ' (' . $row['repuesto_codigo'] . ')');
            $movimiento->setProveedor($row['proveedor_nombre']);
            $movimiento->setUsuario($row['usuario_nombre']);
            return $movimiento;
        }, $data);
    }
    
    /**
     * Obtener movimientos recientes para dashboard
     */
    public function getMovimientosRecientes($limit = 5) {
        $sql = "SELECT m.tipo, m.cantidad, m.fecha_movimiento,
                       r.codigo as repuesto_codigo, r.nombre as repuesto_nombre
                FROM movimientos_inventario m
                LEFT JOIN repuestos r ON m.repuesto_id = r.id
                ORDER BY m.fecha_movimiento DESC 
                LIMIT ?";
        $stmt = $this->db->query($sql, [$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener historial de compras (entradas) de un proveedor
     */
    public function getHistorialComprasByProveedor($proveedorId, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT m.*, r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                       u.nombre as usuario_nombre
                FROM movimientos_inventario m
                LEFT JOIN repuestos r ON m.repuesto_id = r.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                WHERE m.proveedor_id = ? AND m.tipo = 'entrada'
                ORDER BY m.fecha_movimiento DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$proveedorId, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Contar movimientos de entrada por proveedor
     */
    public function countEntradasByProveedor($proveedorId) {
        $sql = "SELECT COUNT(*) as total FROM movimientos_inventario WHERE proveedor_id = ? AND tipo = 'entrada'";
        $stmt = $this->db->query($sql, [$proveedorId]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
}
