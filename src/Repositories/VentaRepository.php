<?php
namespace App\Repositories;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Core\Database;

class VentaRepository {
    private $db;
    public function __construct(){ $this->db = Database::getInstance(); }

    public function generateNumeroVenta() {
        $prefix = 'V-' . date('Ymd') . '-';
        $sql = "SELECT numero_venta FROM ventas WHERE numero_venta LIKE ? ORDER BY numero_venta DESC LIMIT 1";
        $stmt = $this->db->query($sql, [$prefix . '%']);
        $row = $stmt->fetch();
        if ($row) {
            $lastSeq = (int)substr($row['numero_venta'], -5);
            $next = str_pad($lastSeq + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $next = '00001';
        }
        return $prefix . $next;
    }

    public function create(Venta $venta) {
        $sql = "INSERT INTO ventas (numero_venta, cliente_nombre, cliente_documento, cliente_telefono, subtotal, descuento, total, estado, usuario_id) VALUES (?,?,?,?,?,?,?,?,?)";
        $this->db->query($sql, [
            $venta->getNumeroVenta(),
            $venta->getClienteNombre(),
            $venta->getClienteDocumento(),
            $venta->getClienteTelefono(),
            $venta->getSubtotal(),
            $venta->getDescuento(),
            $venta->getTotal(),
            $venta->getEstado(),
            $venta->getUsuarioId()
        ]);
        $id = $this->db->lastInsertId();
        return $id;
    }

    public function bulkInsertDetalles($ventaId, array $detalles) {
        $sql = "INSERT INTO venta_detalles (venta_id, repuesto_id, cantidad, precio_unitario, subtotal) VALUES (?,?,?,?,?)";
        foreach ($detalles as $d) {
            $this->db->query($sql, [
                $ventaId,
                $d->getRepuestoId(),
                $d->getCantidad(),
                $d->getPrecioUnitario(),
                $d->getSubtotal()
            ]);
        }
    }

    public function findById($id) {
        $sql = "SELECT * FROM ventas WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $ventaData = $stmt->fetch();
        if (!$ventaData) return null;
        $venta = new Venta($ventaData);
        $venta->fill($ventaData);

        $dsql = "SELECT vd.*, r.codigo, r.nombre FROM venta_detalles vd INNER JOIN repuestos r ON r.id = vd.repuesto_id WHERE vd.venta_id = ?";
        $dst = $this->db->query($dsql, [$id]);
        $rows = $dst->fetchAll();
        foreach ($rows as $r) {
            $venta->addDetalle(new VentaDetalle($r));
        }
        return $venta;
    }

    public function findAll($page = 1, $limit = ITEMS_POR_PAGINA, $filters = []) {
        $offset = ($page - 1) * $limit;
        $where = [];
        $params = [];
        if (!empty($filters['numero'])) { $where[] = 'numero_venta LIKE ?'; $params[] = '%' . $filters['numero'] . '%'; }
        if (!empty($filters['cliente'])) { $where[] = 'cliente_nombre LIKE ?'; $params[] = '%' . $filters['cliente'] . '%'; }
    if (!empty($filters['estado'])) { $where[] = 'UPPER(estado) = ?'; $params[] = strtoupper($filters['estado']); }
        if (!empty($filters['fecha_inicio']) && !empty($filters['fecha_fin'])) { $where[] = 'DATE(created_at) BETWEEN ? AND ?'; $params[] = $filters['fecha_inicio']; $params[] = $filters['fecha_fin']; }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $sql = "SELECT * FROM ventas $whereSql ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $paramsLimit = array_merge($params, [$limit, $offset]);
        $stmt = $this->db->query($sql, $paramsLimit);
        $data = $stmt->fetchAll();
        return $data;
    }

    public function count($filters = []) {
        $where = [];
        $params = [];
        if (!empty($filters['numero'])) { $where[] = 'numero_venta LIKE ?'; $params[] = '%' . $filters['numero'] . '%'; }
        if (!empty($filters['cliente'])) { $where[] = 'cliente_nombre LIKE ?'; $params[] = '%' . $filters['cliente'] . '%'; }
    if (!empty($filters['estado'])) { $where[] = 'UPPER(estado) = ?'; $params[] = strtoupper($filters['estado']); }
        if (!empty($filters['fecha_inicio']) && !empty($filters['fecha_fin'])) { $where[] = 'DATE(created_at) BETWEEN ? AND ?'; $params[] = $filters['fecha_inicio']; $params[] = $filters['fecha_fin']; }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $sql = "SELECT COUNT(*) as total FROM ventas $whereSql";
        $stmt = $this->db->query($sql, $params);
        $row = $stmt->fetch();
        return (int)$row['total'];
    }

    public function updateEstado($ventaId, $estado) {
        $sql = "UPDATE ventas SET estado = ? WHERE id = ?";
        $this->db->query($sql, [$estado, $ventaId]);
    }

    public function getDetallesVenta($ventaId) {
        $sql = "SELECT vd.*, r.stock_actual FROM venta_detalles vd INNER JOIN repuestos r ON r.id = vd.repuesto_id WHERE vd.venta_id = ?";
        $stmt = $this->db->query($sql, [$ventaId]);
        return $stmt->fetchAll();
    }

    public function resumenDiario($fechaInicio, $fechaFin) {
        $sql = "SELECT DATE(created_at) as fecha, COUNT(*) as cantidad, SUM(total) as monto, SUM(subtotal) as subtotal, SUM(descuento) as descuentos FROM ventas WHERE DATE(created_at) BETWEEN ? AND ? AND estado != 'ANULADA' GROUP BY DATE(created_at) ORDER BY fecha DESC";
        $stmt = $this->db->query($sql, [$fechaInicio, $fechaFin]);
        return $stmt->fetchAll();
    }

    public function resumenSemanal($semanas = 6) {
        $sql = "SELECT YEARWEEK(created_at, 1) as semana, MIN(DATE(created_at)) as desde, MAX(DATE(created_at)) as hasta, COUNT(*) as cantidad, SUM(total) as monto FROM ventas WHERE estado != 'ANULADA' GROUP BY YEARWEEK(created_at,1) ORDER BY semana DESC LIMIT ?";
        $stmt = $this->db->query($sql, [$semanas]);
        return $stmt->fetchAll();
    }
    
    /**
     * Contar ventas por rango de fechas
     */
    public function countByDateRange($fechaInicio, $fechaFin) {
        $sql = "SELECT COUNT(*) as total FROM ventas WHERE DATE(created_at) BETWEEN ? AND ? AND estado != 'ANULADA'";
        $stmt = $this->db->query($sql, [$fechaInicio, $fechaFin]);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }
    
    /**
     * Obtener ventas recientes para dashboard
     */
    public function getVentasRecientes($limit = 5) {
        $sql = "SELECT id, numero_venta, cliente_nombre, total, estado, created_at 
                FROM ventas 
                ORDER BY created_at DESC 
                LIMIT ?";
        $stmt = $this->db->query($sql, [$limit]);
        return $stmt->fetchAll();
    }
}
