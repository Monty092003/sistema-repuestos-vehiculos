<?php
/**
 * InventarioService - Sistema de Repuestos de Vehículos
 * Capa de Negocio - Lógica de negocio para inventario
 */

namespace App\Services;

use App\Models\MovimientoInventario;
use App\Repositories\MovimientoInventarioRepository;
use App\Repositories\RepuestoRepository;
use App\Repositories\ProveedorRepository;

class InventarioService {
    private $movimientoRepository;
    private $repuestoRepository;
    private $proveedorRepository;
    
    public function __construct() {
        $this->movimientoRepository = new MovimientoInventarioRepository();
        $this->repuestoRepository = new RepuestoRepository();
        $this->proveedorRepository = new ProveedorRepository();
    }
    
    /**
     * Obtener todos los movimientos con paginación (RF10)
     */
    public function getAllMovimientos($page = 1, $limit = ITEMS_POR_PAGINA) {
        $movimientos = $this->movimientoRepository->findAll($page, $limit);
        $total = $this->movimientoRepository->count();
        
        return [
            'movimientos' => $movimientos,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ];
    }
    
    /**
     * Obtener movimientos por repuesto
     */
    public function getMovimientosByRepuesto($repuestoId, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $movimientos = $this->movimientoRepository->findByRepuesto($repuestoId, $page, $limit);
        $total = $this->movimientoRepository->countByRepuesto($repuestoId);
        
        $repuesto = $this->repuestoRepository->findById($repuestoId);
        
        return [
            'movimientos' => $movimientos,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page,
            'repuesto' => $repuesto
        ];
    }
    
    /**
     * Obtener movimientos por tipo
     */
    public function getMovimientosByTipo($tipo, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $movimientos = $this->movimientoRepository->findByTipo($tipo, $page, $limit);
        $total = $this->movimientoRepository->countByTipo($tipo);
        
        return [
            'movimientos' => $movimientos,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page,
            'tipo' => $tipo
        ];
    }
    
    /**
     * Obtener movimientos por rango de fechas
     */
    public function getMovimientosByFechaRange($fechaInicio, $fechaFin, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $movimientos = $this->movimientoRepository->findByFechaRange($fechaInicio, $fechaFin, $page, $limit);
        $total = $this->movimientoRepository->countByFechaRange($fechaInicio, $fechaFin);
        
        return [
            'movimientos' => $movimientos,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ];
    }
    
    /**
     * Registrar entrada de stock (RF8)
     */
    public function registrarEntrada($data) {
        // Validar datos requeridos
        if (empty($data['repuesto_id']) || empty($data['cantidad']) || empty($data['motivo'])) {
            throw new \Exception('Repuesto, cantidad y motivo son requeridos');
        }

        // Normalizar / validar cantidad numérica positiva
        if (!is_numeric($data['cantidad']) || $data['cantidad'] <= 0) {
            throw new \Exception('La cantidad debe ser un número positivo');
        }
        $data['cantidad'] = (int)$data['cantidad'];
        
        // Verificar que el repuesto existe
        $repuesto = $this->repuestoRepository->findById($data['repuesto_id']);
        if (!$repuesto) {
            throw new \Exception('El repuesto no existe');
        }
        
        // Verificar que el proveedor existe (si se proporciona)
        if (!empty($data['proveedor_id'])) {
            $proveedor = $this->proveedorRepository->findById($data['proveedor_id']);
            if (!$proveedor) {
                throw new \Exception('El proveedor no existe');
            }
        }
        
        // Crear movimiento de entrada
        $movimiento = new MovimientoInventario();
        $movimiento->setRepuestoId($data['repuesto_id'])
                   ->setTipo(MOVIMIENTO_ENTRADA)
                   ->setCantidad($data['cantidad'])
                   ->setMotivo($data['motivo'])
                   ->setProveedorId($data['proveedor_id'] ?? null)
                   ->setUsuarioId($data['usuario_id'])
                   ->setObservaciones($data['observaciones'] ?? '');
        
        // Validar el movimiento
        $errors = $movimiento->validate();
        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }
        
        // Transacción: lock + crear movimiento + update atómico
        $db = \App\Core\Database::getInstance();
        try {
            $db->beginTransaction();
            // Bloqueo pesimista de la fila
            $locked = $this->repuestoRepository->lockById($data['repuesto_id']);
            if (!$locked) { throw new \App\Core\ConcurrencyException('Repuesto no encontrado al bloquear'); }
            $movimiento = $this->movimientoRepository->create($movimiento);
            // Actualización atómica por delta positivo
            $this->repuestoRepository->updateStockAtomic($data['repuesto_id'], $movimiento->getCantidad(), false);
            $db->commit();
            return $movimiento;
        } catch (\Exception $e) {
            if (method_exists($db, 'rollback')) { $db->rollback(); }
            throw $e;
        }
    }
    
    /**
     * Registrar salida de stock (RF9)
     */
    public function registrarSalida($data) {
        // Validar datos requeridos
        if (empty($data['repuesto_id']) || empty($data['cantidad']) || empty($data['motivo'])) {
            throw new \Exception('Repuesto, cantidad y motivo son requeridos');
        }

        if (!is_numeric($data['cantidad']) || $data['cantidad'] <= 0) {
            throw new \Exception('La cantidad debe ser un número positivo');
        }
        $data['cantidad'] = (int)$data['cantidad'];
        
        // Verificar que el repuesto existe
        $repuesto = $this->repuestoRepository->findById($data['repuesto_id']);
        if (!$repuesto) {
            throw new \Exception('El repuesto no existe');
        }
        
        // Verificar que hay stock suficiente
        if (!$repuesto->hasStock($data['cantidad'])) {
            throw new \Exception("Stock insuficiente. Disponible: {$repuesto->getStockActual()}, Solicitado: {$data['cantidad']}");
        }
        
        // Crear movimiento de salida
        $movimiento = new MovimientoInventario();
        $movimiento->setRepuestoId($data['repuesto_id'])
                   ->setTipo(MOVIMIENTO_SALIDA)
                   ->setCantidad($data['cantidad'])
                   ->setMotivo($data['motivo'])
                   ->setProveedorId(null) // Las salidas no tienen proveedor
                   ->setUsuarioId($data['usuario_id'])
                   ->setObservaciones($data['observaciones'] ?? '');
        
        // Validar el movimiento
        $errors = $movimiento->validate();
        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }
        
        $db = \App\Core\Database::getInstance();
        try {
            $db->beginTransaction();
            $locked = $this->repuestoRepository->lockById($data['repuesto_id']);
            if (!$locked) { throw new \App\Core\ConcurrencyException('Repuesto no encontrado al bloquear'); }
            if ((int)$locked['stock_actual'] < $movimiento->getCantidad()) {
                throw new \App\Core\ConcurrencyException('Stock insuficiente (condición concurrente)');
            }
            $movimiento = $this->movimientoRepository->create($movimiento);
            $this->repuestoRepository->updateStockAtomic($data['repuesto_id'], -$movimiento->getCantidad(), true);
            $db->commit();
            return $movimiento;
        } catch (\Exception $e) {
            if (method_exists($db, 'rollback')) { $db->rollback(); }
            throw $e;
        }
    }
    
    /**
     * Registrar ajuste de stock
     */
    public function registrarAjuste($data) {
        // Validar datos requeridos
        if (empty($data['repuesto_id']) || empty($data['cantidad']) || empty($data['motivo'])) {
            throw new \Exception('Repuesto, cantidad y motivo son requeridos');
        }

        if (!is_numeric($data['cantidad']) || $data['cantidad'] < 0) { // Se permite cero solo para ajuste a 0
            throw new \Exception('La cantidad (stock objetivo) debe ser un número igual o mayor a 0');
        }
        $data['cantidad'] = (int)$data['cantidad'];
        
        // Verificar que el repuesto existe
        $repuesto = $this->repuestoRepository->findById($data['repuesto_id']);
        if (!$repuesto) {
            throw new \Exception('El repuesto no existe');
        }
        
        // Crear movimiento de ajuste
        $movimiento = new MovimientoInventario();
        $movimiento->setRepuestoId($data['repuesto_id'])
                   ->setTipo(MOVIMIENTO_AJUSTE)
                   ->setCantidad($data['cantidad'])
                   ->setMotivo($data['motivo'])
                   ->setProveedorId(null) // Los ajustes no tienen proveedor
                   ->setUsuarioId($data['usuario_id'])
                   ->setObservaciones($data['observaciones'] ?? '');
        
        // Validar el movimiento
        $errors = $movimiento->validate();
        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }
        
        $db = \App\Core\Database::getInstance();
        try {
            $db->beginTransaction();
            $locked = $this->repuestoRepository->lockById($data['repuesto_id']);
            if (!$locked) { throw new \App\Core\ConcurrencyException('Repuesto no encontrado al bloquear'); }
            $movimiento = $this->movimientoRepository->create($movimiento);
            // Ajuste absoluto: calcular delta para usar atomic (opcional) o set directo
            $delta = $data['cantidad'] - (int)$locked['stock_actual'];
            if ($delta !== 0) {
                $this->repuestoRepository->updateStockAtomic($data['repuesto_id'], $delta, false);
            } else {
                // No cambia stock, pero ya registramos movimiento (inventario físico sin variación)
            }
            $db->commit();
            return $movimiento;
        } catch (\Exception $e) {
            if (method_exists($db, 'rollback')) { $db->rollback(); }
            throw $e;
        }
    }
    
    /**
     * Obtener estadísticas de inventario
     */
    public function getInventarioStats() {
        return $this->movimientoRepository->getStats();
    }
    
    /**
     * Obtener movimientos recientes
     */
    public function getMovimientosRecientes($limit = 10) {
        return $this->movimientoRepository->getRecientes($limit);
    }
    
    /**
     * Obtener movimientos por usuario
     */
    public function getMovimientosByUsuario($usuarioId, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $movimientos = $this->movimientoRepository->findByUsuario($usuarioId, $page, $limit);
        $total = $this->movimientoRepository->countByUsuario($usuarioId);
        
        return [
            'movimientos' => $movimientos,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page,
            'usuario_id' => $usuarioId
        ];
    }
    
    /**
     * Obtener movimientos por proveedor
     */
    public function getMovimientosByProveedor($proveedorId, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $movimientos = $this->movimientoRepository->findByProveedor($proveedorId, $page, $limit);
        $total = $this->movimientoRepository->countByProveedor($proveedorId);
        
        $proveedor = $this->proveedorRepository->findById($proveedorId);
        
        return [
            'movimientos' => $movimientos,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page,
            'proveedor' => $proveedor
        ];
    }
    
    /**
     * Obtener todos los repuestos para selección
     */
    public function getAllRepuestos() {
        return $this->repuestoRepository->findAll(1, 1000); // Obtener todos
    }
    
    /**
     * Obtener todos los proveedores para selección
     */
    public function getAllProveedores() {
        return $this->proveedorRepository->findAll(1, 1000); // Obtener todos
    }
    
    /**
     * Obtener tipos de movimiento disponibles
     */
    public function getTiposMovimiento() {
        return [
            MOVIMIENTO_ENTRADA => 'Entrada de Stock',
            MOVIMIENTO_SALIDA => 'Salida de Stock',
            MOVIMIENTO_AJUSTE => 'Ajuste de Stock'
        ];
    }
    
    /**
     * Obtener motivos predefinidos
     */
    public function getMotivosPredefinidos() {
        return [
            'Compra a proveedor' => 'Compra a proveedor',
            'Venta al cliente' => 'Venta al cliente',
            'Ajuste de inventario' => 'Ajuste de inventario',
            'Devolución de cliente' => 'Devolución de cliente',
            'Devolución a proveedor' => 'Devolución a proveedor',
            'Pérdida por daño' => 'Pérdida por daño',
            'Transferencia entre sucursales' => 'Transferencia entre sucursales',
            'Inventario físico' => 'Inventario físico'
        ];
    }
}
