<?php
namespace App\Controllers;

use App\Services\VentaService;
use App\Core\Flash;

class ReporteController {
    private $ventaService;
    private $authController;

    public function __construct() {
        $this->ventaService = new VentaService();
        $this->authController = new AuthController();
    }

    public function ventas() {
        $this->authController->requirePermission('view_reportes');
        $fechaInicio = $_GET['fecha_inicio'] ?? '';
        $fechaFin = $_GET['fecha_fin'] ?? '';
        $resumenDiario = $this->ventaService->resumenDiario($fechaInicio, $fechaFin);
        $resumenSemanal = $this->ventaService->resumenSemanal();
        $this->render('reportes/ventas', [
            'title' => 'Reporte de Ventas',
            'resumenDiario' => $resumenDiario,
            'resumenSemanal' => $resumenSemanal,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'success' => Flash::get('success'),
            'error' => Flash::get('error')
        ]);
    }

    public function index() {
        $this->authController->requirePermission('view_reportes');
        // Redireccionar a reporte de ventas como landing provisional
        header('Location: ' . BASE_URL . 'reportes/ventas');
        exit;
    }

    public function stockBajo() {
        $this->authController->requirePermission('view_reportes');
        // Placeholder simple
        $this->render('reportes/placeholder', [
            'title' => 'Reporte Stock Bajo',
            'mensaje' => 'Vista de stock bajo pendiente de implementación'
        ]);
    }

    public function movimientos() {
        $this->authController->requirePermission('view_reportes');
        $this->render('reportes/placeholder', [
            'title' => 'Reporte de Movimientos',
            'mensaje' => 'Vista de movimientos pendiente de implementación'
        ]);
    }

    private function render($view, $data = []) {
        extract($data);
        $viewPath = SRC_PATH . '/Views/' . $view . '.php';
        if (file_exists($viewPath)) include $viewPath; else echo 'Vista no encontrada: ' . $view;
    }
}
