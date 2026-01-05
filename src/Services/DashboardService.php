<?php
/**
 * DashboardService - Sistema de Repuestos de Vehículos
 * Servicio para obtener estadísticas y datos del dashboard
 */

namespace App\Services;

use App\Repositories\RepuestoRepository;
use App\Repositories\VentaRepository;
use App\Repositories\UserRepository;
use App\Repositories\MovimientoInventarioRepository;

class DashboardService {
    private $repuestoRepository;
    private $ventaRepository;
    private $userRepository;
    private $movimientoRepository;
    
    public function __construct() {
        $this->repuestoRepository = new RepuestoRepository();
        $this->ventaRepository = new VentaRepository();
        $this->userRepository = new UserRepository();
        $this->movimientoRepository = new MovimientoInventarioRepository();
    }
    
    /**
     * Obtener estadísticas generales para el dashboard
     */
    public function getEstadisticasGenerales() {
        return [
            'total_repuestos' => $this->getTotalRepuestos(),
            'ventas_mes' => $this->getVentasDelMes(),
            'stock_bajo' => $this->getRepuestosStockBajo(),
            'usuarios_activos' => $this->getUsuariosActivos()
        ];
    }
    
    /**
     * Obtener repuestos con stock bajo para dashboard
     */
    public function getRepuestosStockBajoDashboard($limit = 5) {
        return $this->repuestoRepository->getRepuestosStockBajo($limit);
    }
    
    /**
     * Obtener movimientos recientes para dashboard
     */
    public function getMovimientosRecientes($limit = 5) {
        return $this->movimientoRepository->getMovimientosRecientes($limit);
    }
    
    /**
     * Obtener ventas recientes para dashboard
     */
    public function getVentasRecientes($limit = 5) {
        return $this->ventaRepository->getVentasRecientes($limit);
    }
    
    /**
     * Obtener total de repuestos activos
     */
    private function getTotalRepuestos() {
        try {
            return $this->repuestoRepository->countActivos();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Obtener número de ventas del mes actual
     */
    private function getVentasDelMes() {
        try {
            $fechaInicio = date('Y-m-01');
            $fechaFin = date('Y-m-t');
            return $this->ventaRepository->countByDateRange($fechaInicio, $fechaFin);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Obtener cantidad de repuestos con stock bajo
     */
    private function getRepuestosStockBajo() {
        try {
            return $this->repuestoRepository->countStockBajo();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Obtener número de usuarios activos
     */
    private function getUsuariosActivos() {
        try {
            return $this->userRepository->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}