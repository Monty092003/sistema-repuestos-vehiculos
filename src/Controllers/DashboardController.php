<?php
/**
 * DashboardController - Sistema de Repuestos de Vehículos
 * Controlador para el dashboard principal con métricas y estadísticas
 */

namespace App\Controllers;

use App\Services\DashboardService;
use App\Controllers\AuthController;

class DashboardController {
    private $dashboardService;
    private $authController;
    
    public function __construct() {
        $this->dashboardService = new DashboardService();
        $this->authController = new AuthController();
    }
    
    public function index() {
        // Verificar autenticación
        $this->authController->requireAuth();
        
        try {
            // Obtener datos para el dashboard
            $stats = $this->dashboardService->getEstadisticasGenerales();
            $repuestos_stock_bajo = $this->dashboardService->getRepuestosStockBajoDashboard();
            $movimientos_recientes = $this->dashboardService->getMovimientosRecientes();
            $ventas_recientes = $this->dashboardService->getVentasRecientes();
            
            // Renderizar vista del dashboard
            $this->render('dashboard/index', [
                'title' => 'Dashboard - Sistema de Repuestos',
                'stats' => $stats,
                'repuestos_stock_bajo' => $repuestos_stock_bajo,
                'movimientos_recientes' => $movimientos_recientes,
                'ventas_recientes' => $ventas_recientes
            ]);
            
        } catch (\Exception $e) {
            // En caso de error, mostrar dashboard básico
            $this->render('dashboard/index', [
                'title' => 'Dashboard - Sistema de Repuestos',
                'stats' => [],
                'repuestos_stock_bajo' => [],
                'movimientos_recientes' => [],
                'ventas_recientes' => [],
                'error' => 'Error al cargar datos del dashboard: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Renderizar vista
     */
    private function render($view, $data = []) {
        extract($data);
        include __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';
    }
}
