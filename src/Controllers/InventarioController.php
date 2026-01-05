<?php
/**
 * InventarioController - Sistema de Repuestos de Vehículos
 * Capa de Presentación - Gestión de inventario
 */

namespace App\Controllers;

use App\Services\InventarioService;
use App\Controllers\AuthController;
use App\Core\Csrf;

class InventarioController {
    private $inventarioService;
    private $authController;
    
    public function __construct() {
        $this->inventarioService = new InventarioService();
        $this->authController = new AuthController();
    }
    
    /**
     * Mostrar dashboard de inventario
     */
    public function index() {
        $this->authController->requirePermission('view_inventario');
        
        $page = (int)($_GET['page'] ?? 1);
        $tipo = $_GET['tipo'] ?? '';
        $fechaInicio = $_GET['fecha_inicio'] ?? '';
        $fechaFin = $_GET['fecha_fin'] ?? '';
        
        try {
            if (!empty($tipo)) {
                $result = $this->inventarioService->getMovimientosByTipo($tipo, $page);
            } elseif (!empty($fechaInicio) && !empty($fechaFin)) {
                $result = $this->inventarioService->getMovimientosByFechaRange($fechaInicio, $fechaFin, $page);
            } else {
                $result = $this->inventarioService->getAllMovimientos($page);
            }
            
            $stats = $this->inventarioService->getInventarioStats();
            $recientes = $this->inventarioService->getMovimientosRecientes(5);
            $tipos = $this->inventarioService->getTiposMovimiento();
            
            $this->render('inventario/index', [
                'title' => 'Gestión de Inventario',
                'movimientos' => $result['movimientos'],
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current_page' => $result['current_page'],
                'stats' => $stats,
                'recientes' => $recientes,
                'tipos' => $tipos,
                'filtro_tipo' => $tipo,
                'filtro_fecha_inicio' => $fechaInicio,
                'filtro_fecha_fin' => $fechaFin,
                'success' => $_SESSION['success'] ?? null,
                'error' => $_SESSION['error'] ?? null
            ]);
            
            // Limpiar mensajes de sesión
            unset($_SESSION['success'], $_SESSION['error']);
            
        } catch (\Exception $e) {
            $this->render('inventario/index', [
                'title' => 'Gestión de Inventario',
                'movimientos' => [],
                'total' => 0,
                'pages' => 0,
                'current_page' => 1,
                'stats' => [],
                'recientes' => [],
                'tipos' => [],
                'filtro_tipo' => $tipo,
                'filtro_fecha_inicio' => $fechaInicio,
                'filtro_fecha_fin' => $fechaFin,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mostrar formulario de entrada de stock (RF8)
     */
    public function entradas() {
        $this->authController->requirePermission('create_inventario');
        
        $repuestos = $this->inventarioService->getAllRepuestos();
        $proveedores = $this->inventarioService->getAllProveedores();
        $motivos = $this->inventarioService->getMotivosPredefinidos();
        
        $this->render('inventario/entradas', [
            'title' => 'Registrar Entrada de Stock',
            'repuestos' => $repuestos,
            'proveedores' => $proveedores,
            'motivos' => $motivos,
            'error' => $_SESSION['error'] ?? null
        ]);
        
        unset($_SESSION['error']);
    }
    
    /**
     * Procesar entrada de stock (RF8)
     */
    public function storeEntrada() {
        $this->authController->requirePermission('create_inventario');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/inventario/entradas');
            return;
        }
        if (!Csrf::validateFromRequest()) {
            $_SESSION['error'] = 'Token CSRF inválido o expirado';
            $this->redirect('/inventario/entradas');
            return;
        }
        
        try {
            $data = [
                'repuesto_id' => $_POST['repuesto_id'] ?? '',
                'cantidad' => $_POST['cantidad'] ?? 0,
                'motivo' => $_POST['motivo'] ?? '',
                'proveedor_id' => $_POST['proveedor_id'] ?? null,
                'usuario_id' => $_SESSION['user_id'],
                'observaciones' => $_POST['observaciones'] ?? ''
            ];
            
            $movimiento = $this->inventarioService->registrarEntrada($data);
            
            $_SESSION['success'] = 'Entrada de stock registrada correctamente';
            $this->redirect('/inventario');
            
        } catch (\App\Core\ConcurrencyException $ce) {
            $_SESSION['error'] = 'Concurrencia: ' . $ce->getMessage();
            $this->redirect('/inventario/entradas');
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/inventario/entradas');
        }
    }
    
    /**
     * Mostrar formulario de salida de stock (RF9)
     */
    public function salidas() {
        $this->authController->requirePermission('create_inventario');
        
        $repuestos = $this->inventarioService->getAllRepuestos();
        $motivos = $this->inventarioService->getMotivosPredefinidos();
        
        $this->render('inventario/salidas', [
            'title' => 'Registrar Salida de Stock',
            'repuestos' => $repuestos,
            'motivos' => $motivos,
            'error' => $_SESSION['error'] ?? null
        ]);
        
        unset($_SESSION['error']);
    }
    
    /**
     * Procesar salida de stock (RF9)
     */
    public function storeSalida() {
        $this->authController->requirePermission('create_inventario');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/inventario/salidas');
            return;
        }
        if (!Csrf::validateFromRequest()) {
            $_SESSION['error'] = 'Token CSRF inválido o expirado';
            $this->redirect('/inventario/salidas');
            return;
        }
        
        try {
            $data = [
                'repuesto_id' => $_POST['repuesto_id'] ?? '',
                'cantidad' => $_POST['cantidad'] ?? 0,
                'motivo' => $_POST['motivo'] ?? '',
                'usuario_id' => $_SESSION['user_id'],
                'observaciones' => $_POST['observaciones'] ?? ''
            ];
            
            $movimiento = $this->inventarioService->registrarSalida($data);
            
            $_SESSION['success'] = 'Salida de stock registrada correctamente';
            $this->redirect('/inventario');
            
        } catch (\App\Core\ConcurrencyException $ce) {
            $_SESSION['error'] = 'Concurrencia: ' . $ce->getMessage();
            $this->redirect('/inventario/salidas');
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/inventario/salidas');
        }
    }
    
    /**
     * Mostrar historial de movimientos (RF10)
     */
    public function movimientos() {
        $this->authController->requirePermission('view_inventario');
        
        $page = (int)($_GET['page'] ?? 1);
        $tipo = $_GET['tipo'] ?? '';
        $fechaInicio = $_GET['fecha_inicio'] ?? '';
        $fechaFin = $_GET['fecha_fin'] ?? '';
        
        try {
            if (!empty($tipo)) {
                $result = $this->inventarioService->getMovimientosByTipo($tipo, $page);
            } elseif (!empty($fechaInicio) && !empty($fechaFin)) {
                $result = $this->inventarioService->getMovimientosByFechaRange($fechaInicio, $fechaFin, $page);
            } else {
                $result = $this->inventarioService->getAllMovimientos($page);
            }
            
            $tipos = $this->inventarioService->getTiposMovimiento();
            
            $this->render('inventario/movimientos', [
                'title' => 'Historial de Movimientos',
                'movimientos' => $result['movimientos'],
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current_page' => $result['current_page'],
                'tipos' => $tipos,
                'filtro_tipo' => $tipo,
                'filtro_fecha_inicio' => $fechaInicio,
                'filtro_fecha_fin' => $fechaFin
            ]);
            
        } catch (\Exception $e) {
            $this->render('inventario/movimientos', [
                'title' => 'Historial de Movimientos',
                'movimientos' => [],
                'total' => 0,
                'pages' => 0,
                'current_page' => 1,
                'tipos' => [],
                'filtro_tipo' => $tipo,
                'filtro_fecha_inicio' => $fechaInicio,
                'filtro_fecha_fin' => $fechaFin,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mostrar formulario de ajuste de stock
     */
    public function ajustes() {
        $this->authController->requirePermission('create_inventario');
        
        $repuestos = $this->inventarioService->getAllRepuestos();
        $motivos = $this->inventarioService->getMotivosPredefinidos();
        
        $this->render('inventario/ajustes', [
            'title' => 'Ajuste de Stock',
            'repuestos' => $repuestos,
            'motivos' => $motivos,
            'error' => $_SESSION['error'] ?? null
        ]);
        
        unset($_SESSION['error']);
    }
    
    /**
     * Procesar ajuste de stock
     */
    public function storeAjuste() {
        $this->authController->requirePermission('create_inventario');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/inventario/ajustes');
            return;
        }
        if (!Csrf::validateFromRequest()) {
            $_SESSION['error'] = 'Token CSRF inválido o expirado';
            $this->redirect('/inventario/ajustes');
            return;
        }
        
        try {
            $data = [
                'repuesto_id' => $_POST['repuesto_id'] ?? '',
                'cantidad' => $_POST['cantidad'] ?? 0,
                'motivo' => $_POST['motivo'] ?? '',
                'usuario_id' => $_SESSION['user_id'],
                'observaciones' => $_POST['observaciones'] ?? ''
            ];
            
            $movimiento = $this->inventarioService->registrarAjuste($data);
            
            $_SESSION['success'] = 'Ajuste de stock registrado correctamente';
            $this->redirect('/inventario');
            
        } catch (\App\Core\ConcurrencyException $ce) {
            $_SESSION['error'] = 'Concurrencia: ' . $ce->getMessage();
            $this->redirect('/inventario/ajustes');
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/inventario/ajustes');
        }
    }
    
    /**
     * Redirigir a una URL
     */
    private function redirect($url) {
        header("Location: " . BASE_URL . ltrim($url, '/'));
        exit;
    }
    
    /**
     * Renderizar vista
     */
    private function render($view, $data = []) {
        extract($data);
        $viewPath = SRC_PATH . '/Views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Vista no encontrada: {$view}";
        }
    }
}
