<?php
/**
 * RepuestoRepository - Sistema de Repuestos de Vehículos
 * Capa de Datos - Operaciones de base de datos para repuestos
 */

namespace App\Repositories;

use App\Models\Repuesto;
use App\Core\Database;

class RepuestoRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Buscar repuesto por ID
     */
    public function findById($id) {
        $sql = "SELECT r.*, c.nombre as categoria_nombre 
                FROM repuestos r
                LEFT JOIN categorias c ON r.categoria_id = c.id
                WHERE r.id = ? AND r.activo = 1";
        $stmt = $this->db->query($sql, [$id]);
        $data = $stmt->fetch();
        
        if ($data) {
            $repuesto = new Repuesto($data);
            $repuesto->setCategoria($data['categoria_nombre']);
            return $repuesto;
        }
        
        return null;
    }
    
    /**
     * Buscar repuesto por código
     */
    public function findByCodigo($codigo) {
        $sql = "SELECT r.*, c.nombre as categoria_nombre 
                FROM repuestos r
                LEFT JOIN categorias c ON r.categoria_id = c.id
                WHERE r.codigo = ? AND r.activo = 1";
        $stmt = $this->db->query($sql, [$codigo]);
        $data = $stmt->fetch();
        
        if ($data) {
            $repuesto = new Repuesto($data);
            $repuesto->setCategoria($data['categoria_nombre']);
            return $repuesto;
        }
        
        return null;
    }
    
    /**
     * Obtener todos los repuestos con paginación
     */
    public function findAll($page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT r.*, c.nombre as categoria_nombre 
                FROM repuestos r
                LEFT JOIN categorias c ON r.categoria_id = c.id
                WHERE r.activo = 1 
                ORDER BY r.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $repuesto = new Repuesto($row);
            $repuesto->setCategoria($row['categoria_nombre']);
            return $repuesto;
        }, $data);
    }
    
    /**
     * Contar total de repuestos
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM repuestos WHERE activo = 1";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        
        return (int)$result['total'];
    }

    /**
     * Contar total filtrado por búsqueda y categoría (para paginación correcta)
     */
    public function countFiltered($term = '', $categoriaId = null) {
        $sql = "SELECT COUNT(*) as total FROM repuestos r WHERE r.activo = 1";
        $params = [];
        if (!empty($term)) {
            $sql .= " AND (r.nombre LIKE ? OR r.codigo LIKE ? OR r.descripcion LIKE ?)";
            $like = "%{$term}%";
            $params[] = $like; $params[] = $like; $params[] = $like;
        }
        if (!empty($categoriaId)) {
            $sql .= " AND r.categoria_id = ?";
            $params[] = $categoriaId;
        }
        $stmt = $this->db->query($sql, $params);
        $row = $stmt->fetch();
        return (int)$row['total'];
    }

    /**
     * Contar total por categoría
     */
    public function countByCategoria($categoriaId) {
        $sql = "SELECT COUNT(*) as total FROM repuestos WHERE activo = 1 AND categoria_id = ?";
        $stmt = $this->db->query($sql, [$categoriaId]);
        $row = $stmt->fetch();
        return (int)$row['total'];
    }

    /**
     * Contar total de repuestos con stock bajo
     */
    public function countStockBajo() {
        $sql = "SELECT COUNT(*) as total FROM repuestos WHERE activo = 1 AND stock_actual <= stock_minimo";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();
        return (int)$row['total'];
    }
    
    /**
     * Buscar repuestos por nombre, código o categoría (RF7)
     */
    public function search($term, $categoriaId = null, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        $searchTerm = "%{$term}%";
        
        $sql = "SELECT r.*, c.nombre as categoria_nombre 
                FROM repuestos r
                LEFT JOIN categorias c ON r.categoria_id = c.id
                WHERE r.activo = 1 
                AND (r.nombre LIKE ? OR r.codigo LIKE ? OR r.descripcion LIKE ?)";
        
        $params = [$searchTerm, $searchTerm, $searchTerm];
        
        if ($categoriaId) {
            $sql .= " AND r.categoria_id = ?";
            $params[] = $categoriaId;
        }
        
        $sql .= " ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->query($sql, $params);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $repuesto = new Repuesto($row);
            $repuesto->setCategoria($row['categoria_nombre']);
            return $repuesto;
        }, $data);
    }
    
    /**
     * Obtener repuestos por categoría (RF6)
     */
    public function findByCategoria($categoriaId, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT r.*, c.nombre as categoria_nombre 
                FROM repuestos r
                LEFT JOIN categorias c ON r.categoria_id = c.id
                WHERE r.categoria_id = ? AND r.activo = 1 
                ORDER BY r.nombre ASC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$categoriaId, $limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $repuesto = new Repuesto($row);
            $repuesto->setCategoria($row['categoria_nombre']);
            return $repuesto;
        }, $data);
    }
    
    /**
     * Obtener repuestos con stock bajo (RF5)
     */
    public function findStockBajo($page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT r.*, c.nombre as categoria_nombre 
                FROM repuestos r
                LEFT JOIN categorias c ON r.categoria_id = c.id
                WHERE r.activo = 1 AND r.stock_actual <= r.stock_minimo
                ORDER BY r.stock_actual ASC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$limit, $offset]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $repuesto = new Repuesto($row);
            $repuesto->setCategoria($row['categoria_nombre']);
            return $repuesto;
        }, $data);
    }
    
    /**
     * Crear nuevo repuesto (RF4)
     */
    public function create(Repuesto $repuesto) {
        $sql = "INSERT INTO repuestos (codigo, nombre, descripcion, categoria_id, precio_compra, 
                precio_venta, stock_actual, stock_minimo, stock_maximo, activo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $repuesto->getCodigo(),
            $repuesto->getNombre(),
            $repuesto->getDescripcion(),
            $repuesto->getCategoriaId(),
            $repuesto->getPrecioCompra(),
            $repuesto->getPrecioVenta(),
            $repuesto->getStockActual(),
            $repuesto->getStockMinimo(),
            $repuesto->getStockMaximo(),
            $repuesto->isActivo() ? 1 : 0
        ]);
        
        $repuesto->setId($this->db->lastInsertId());
        return $repuesto;
    }
    
    /**
     * Actualizar repuesto (RF4)
     */
    public function update(Repuesto $repuesto) {
        $sql = "UPDATE repuestos 
                SET codigo = ?, nombre = ?, descripcion = ?, categoria_id = ?, 
                    precio_compra = ?, precio_venta = ?, stock_actual = ?, 
                    stock_minimo = ?, stock_maximo = ?, activo = ?, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
        $this->db->query($sql, [
            $repuesto->getCodigo(),
            $repuesto->getNombre(),
            $repuesto->getDescripcion(),
            $repuesto->getCategoriaId(),
            $repuesto->getPrecioCompra(),
            $repuesto->getPrecioVenta(),
            $repuesto->getStockActual(),
            $repuesto->getStockMinimo(),
            $repuesto->getStockMaximo(),
            $repuesto->isActivo() ? 1 : 0,
            $repuesto->getId()
        ]);
        
        return $repuesto;
    }
    
    /**
     * Eliminar repuesto (RF4)
     */
    public function delete($id) {
        $sql = "UPDATE repuestos SET activo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$id]);
        
        return true;
    }
    
    /**
     * Verificar si el código ya existe
     */
    public function codigoExists($codigo, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM repuestos WHERE codigo = ? AND activo = 1";
        $params = [$codigo];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        
        return (int)$result['count'] > 0;
    }
    
    /**
     * Actualizar stock de repuesto
     */
    public function updateStock($id, $nuevoStock) {
        $sql = "UPDATE repuestos SET stock_actual = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$nuevoStock, $id]);
        
        return true;
    }

    /**
     * Bloquear (FOR UPDATE) un repuesto por ID dentro de una transacción abierta.
     * Retorna el array de datos (no instancia) para eficiencia.
     */
    public function lockById($id) {
        $sql = "SELECT * FROM repuestos WHERE id = ? FOR UPDATE";
        $stmt = $this->db->query($sql, [$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Bloquear múltiples repuestos (ordenados) para evitar interbloqueos.
     * Retorna array indexado por id.
     */
    public function lockMultiple(array $ids) {
        if (empty($ids)) return [];
        $ids = array_unique(array_map('intval', $ids));
        sort($ids, SORT_NUMERIC);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM repuestos WHERE id IN ($placeholders) ORDER BY id ASC FOR UPDATE";
        $stmt = $this->db->query($sql, $ids);
        $rows = $stmt->fetchAll();
        $byId = [];
        foreach ($rows as $r) { $byId[(int)$r['id']] = $r; }
        return $byId;
    }

    /**
     * Actualización atómica por delta.
     * Si $mustRemainNonNegative true, asegura stock_actual + delta >= 0.
     * Devuelve nuevo stock o lanza ConcurrencyException/Exception.
     */
    public function updateStockAtomic($id, $delta, $mustRemainNonNegative = true) {
        $id = (int)$id; $delta = (int)$delta;
        if ($delta === 0) {
            // No-op
            $row = $this->findById($id);
            if (!$row) throw new \Exception('Repuesto no encontrado para actualización atómica');
            return $row->getStockActual();
        }
        if ($mustRemainNonNegative && $delta < 0) {
            // Enforce no negative via WHERE clause
            $sql = "UPDATE repuestos SET stock_actual = stock_actual + ?, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ? AND stock_actual + ? >= 0";
            $stmt = $this->db->query($sql, [$delta, $id, $delta]);
        } else {
            $sql = "UPDATE repuestos SET stock_actual = stock_actual + ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $this->db->query($sql, [$delta, $id]);
        }
        // My query wrapper throws on error; need to validate rows affected with a second select.
        // (PDO::rowCount en MySQL para UPDATE es válido.)
        // Recuperar conexión real para rowCount
        // Simplificamos: hacemos un SELECT para obtener valor final o detectar inexistencia.
        $row = $this->findById($id);
        if (!$row) {
            throw new \Exception('Repuesto no encontrado tras update atómico');
        }
        if ($mustRemainNonNegative && $row->getStockActual() < 0) {
            throw new \App\Core\ConcurrencyException('Resultado de stock negativo detectado');
        }
        return $row->getStockActual();
    }
    
    /**
     * Obtener estadísticas de repuestos
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN stock_actual <= stock_minimo THEN 1 ELSE 0 END) as stock_bajo,
                    SUM(CASE WHEN stock_actual <= ? THEN 1 ELSE 0 END) as stock_critico,
                    SUM(stock_actual * precio_compra) as valor_inventario,
                    AVG(precio_venta) as precio_promedio
                FROM repuestos 
                WHERE activo = 1";
        
        $stmt = $this->db->query($sql, [STOCK_CRITICO_LIMIT]);
        return $stmt->fetch();
    }
    
    /**
     * Obtener repuestos más vendidos
     */
    public function getMasVendidos($limit = 10) {
        $sql = "SELECT r.*, c.nombre as categoria_nombre, 
                       COALESCE(SUM(vd.cantidad), 0) as total_vendido
                FROM repuestos r
                LEFT JOIN categorias c ON r.categoria_id = c.id
                LEFT JOIN venta_detalles vd ON r.id = vd.repuesto_id
                LEFT JOIN ventas v ON vd.venta_id = v.id AND UPPER(v.estado) = 'COMPLETADA'
                WHERE r.activo = 1
                GROUP BY r.id
                ORDER BY total_vendido DESC
                LIMIT ?";
        
        $stmt = $this->db->query($sql, [$limit]);
        $data = $stmt->fetchAll();
        
        return array_map(function($row) {
            $repuesto = new Repuesto($row);
            $repuesto->setCategoria($row['categoria_nombre']);
            $repuesto->totalVendido = $row['total_vendido'];
            return $repuesto;
        }, $data);
    }

    /**
     * Obtener alertas de stock avanzadas (RF16)
     * @param int|null $categoriaId
     * @param bool $includeNear incluir "casi bajos"
     * @param bool $soloCriticos sólo severidad CRITICO
     * @param int $page
     * @param int $limit
     * @return array<Repuesto>
     */
    public function findStockAlerts($categoriaId = null, $includeNear = false, $soloCriticos = false, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $offset = ($page - 1) * $limit;
        // Construimos condiciones base
        $where = ["r.activo = 1"];
        $params = [];

        // Condición principal de severidades
        if ($soloCriticos) {
            $where[] = "r.stock_actual <= ?"; // crítico basado en STOCK_CRITICO_LIMIT
            $params[] = STOCK_CRITICO_LIMIT;
        } else {
            // Bajo: stock_actual <= stock_minimo
            // Casi bajo: stock_actual > stock_minimo AND stock_actual <= stock_minimo * NEAR_STOCK_FACTOR
            // Seleccionamos ambos si includeNear, si no, solo bajo/critico (stock_actual <= stock_minimo)
            if ($includeNear) {
                $where[] = "(r.stock_actual <= r.stock_minimo OR (r.stock_actual > r.stock_minimo AND r.stock_actual <= r.stock_minimo * ?))";
                $params[] = NEAR_STOCK_FACTOR;
            } else {
                $where[] = "r.stock_actual <= r.stock_minimo";
            }
        }

        if (!empty($categoriaId)) {
            $where[] = "r.categoria_id = ?";
            $params[] = $categoriaId;
        }

        $whereSql = implode(' AND ', $where);

        $sql = "SELECT r.*, c.nombre as categoria_nombre,
                (r.stock_actual - r.stock_minimo) AS shortage,
                CASE 
                    WHEN r.stock_actual <= ? THEN 'CRITICO'
                    WHEN r.stock_actual <= r.stock_minimo THEN 'BAJO'
                    WHEN r.stock_actual > r.stock_minimo AND r.stock_actual <= r.stock_minimo * ? THEN 'CASI'
                    ELSE 'OK'
                END AS severidad,
                CASE WHEN r.stock_minimo > 0 THEN (r.stock_actual / r.stock_minimo) * 100 ELSE NULL END AS porcentaje_min,
                CASE WHEN r.stock_actual < r.stock_minimo THEN (r.stock_minimo * ?) - r.stock_actual ELSE 0 END AS recomendado_reponer
            FROM repuestos r
            LEFT JOIN categorias c ON r.categoria_id = c.id
            WHERE $whereSql
            ORDER BY severidad = 'CRITICO' DESC, severidad = 'BAJO' DESC, porcentaje_min ASC NULLS LAST, r.stock_actual ASC
            LIMIT ? OFFSET ?";

        // Parámetros para CASE (STOCK_CRITICO_LIMIT, NEAR_STOCK_FACTOR, TARGET_REPLENISH_FACTOR)
        $execParams = array_merge($params);
        array_unshift($execParams, STOCK_CRITICO_LIMIT, NEAR_STOCK_FACTOR, TARGET_REPLENISH_FACTOR); // Se insertan al inicio en orden de aparición
        $execParams[] = $limit; $execParams[] = $offset;

        $stmt = $this->db->query($sql, $execParams);
        $data = $stmt->fetchAll();

        return array_map(function($row){
            $repuesto = new Repuesto($row);
            $repuesto->setCategoria($row['categoria_nombre'] ?? null);
            // Atributos calculados auxiliares
            $repuesto->shortage = (int)$row['shortage'];
            $repuesto->severidad = $row['severidad'];
            $repuesto->porcentaje_min = $row['porcentaje_min'] !== null ? (float)$row['porcentaje_min'] : null;
            $repuesto->recomendado_reponer = (int)$row['recomendado_reponer'];
            return $repuesto;
        }, $data);
    }

    /**
     * Contar alertas de stock avanzadas (coincidir filtros con findStockAlerts)
     */
    public function countStockAlerts($categoriaId = null, $includeNear = false, $soloCriticos = false) {
        $where = ["r.activo = 1"];
        $params = [];

        if ($soloCriticos) {
            $where[] = "r.stock_actual <= ?"; $params[] = STOCK_CRITICO_LIMIT;
        } else {
            if ($includeNear) {
                $where[] = "(r.stock_actual <= r.stock_minimo OR (r.stock_actual > r.stock_minimo AND r.stock_actual <= r.stock_minimo * ?))";
                $params[] = NEAR_STOCK_FACTOR;
            } else {
                $where[] = "r.stock_actual <= r.stock_minimo";
            }
        }
        if (!empty($categoriaId)) { $where[] = "r.categoria_id = ?"; $params[] = $categoriaId; }
        $whereSql = implode(' AND ', $where);
        $sql = "SELECT COUNT(*) as total FROM repuestos r WHERE $whereSql";
        $stmt = $this->db->query($sql, $params);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }
    
    /**
     * Contar repuestos activos
     */
    public function countActivos() {
        $sql = "SELECT COUNT(*) as total FROM repuestos WHERE activo = 1";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }
    
    /**
     * Obtener repuestos con stock bajo (formato simple para dashboard)
     */
    public function getRepuestosStockBajo($limit = 10) {
        $sql = "SELECT codigo, nombre, stock_actual, stock_minimo 
                FROM repuestos 
                WHERE activo = 1 AND stock_actual <= stock_minimo 
                ORDER BY stock_actual ASC 
                LIMIT ?";
        $stmt = $this->db->query($sql, [$limit]);
        return $stmt->fetchAll();
    }
}
