<?php
/**
 * RepuestoController - Sistema de Repuestos de Vehículos
 * Capa de Presentación - CRUD de repuestos
 */

namespace App\Controllers;

use App\Services\RepuestoService;
use App\Controllers\AuthController;
use App\Core\Flash;

class RepuestoController {
    private $repuestoService;
    private $authController;
    
    public function __construct() {
        $this->repuestoService = new RepuestoService();
        $this->authController = new AuthController();
    }
    
    /**
     * Listar repuestos (RF4)
     */
    public function index() {
        $this->authController->requirePermission('view_repuestos');
        
        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $categoriaId = $_GET['categoria_id'] ?? null;
        
        try {
            if (!empty($search) || !empty($categoriaId)) {
                $result = $this->repuestoService->searchRepuestos($search, $categoriaId, $page);
            } else {
                $result = $this->repuestoService->getAllRepuestos($page);
            }
            
            $categorias = $this->repuestoService->getAllCategorias();
            
            $this->render('repuestos/index', [
                'title' => 'Gestión de Repuestos',
                'repuestos' => $result['repuestos'],
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current_page' => $result['current_page'],
                'search_term' => $search,
                'categoria_id' => $categoriaId,
                'categorias' => $categorias,
                'success' => Flash::get('success'),
                'error' => Flash::get('error')
            ]);
            
        } catch (\Exception $e) {
            $this->render('repuestos/index', [
                'title' => 'Gestión de Repuestos',
                'repuestos' => [],
                'total' => 0,
                'pages' => 0,
                'current_page' => 1,
                'search_term' => $search,
                'categoria_id' => $categoriaId,
                'categorias' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mostrar formulario de creación (RF4)
     */
    public function create() {
        $this->authController->requirePermission('create_repuestos');
        
        $categorias = $this->repuestoService->getAllCategorias();
        
        $this->render('repuestos/create', [
            'title' => 'Crear Repuesto',
            'categorias' => $categorias,
            'error' => Flash::get('error')
        ]);
    }
    
    /**
     * Procesar creación de repuesto (RF4)
     */
    public function store() {
        $this->authController->requirePermission('create_repuestos');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/repuestos');
            return;
        }
        if (!\App\Core\Csrf::validateFromRequest()) {
            Flash::error('Token CSRF inválido');
            $this->redirect('/repuestos/crear');
            return;
        }
        
        try {
            $data = [
                'codigo' => $_POST['codigo'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'categoria_id' => $_POST['categoria_id'] ?? '',
                'precio_compra' => $_POST['precio_compra'] ?? 0,
                'precio_venta' => $_POST['precio_venta'] ?? 0,
                'stock_actual' => $_POST['stock_actual'] ?? 0,
                'stock_minimo' => $_POST['stock_minimo'] ?? 5,
                'stock_maximo' => $_POST['stock_maximo'] ?? 100,
                'activo' => isset($_POST['activo'])
            ];
            
            $repuesto = $this->repuestoService->createRepuesto($data);
            
            Flash::success('Repuesto creado correctamente');
            $this->redirect('/repuestos');
            
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/repuestos/crear');
        }
    }
    
    /**
     * Mostrar repuesto específico (RF4)
     */
    public function show($id) {
        $this->authController->requirePermission('view_repuestos');
        
        try {
            $repuesto = $this->repuestoService->getRepuestoById($id);
            
            $this->render('repuestos/show', [
                'title' => 'Detalles del Repuesto',
                'repuesto' => $repuesto,
                'error' => Flash::get('error')
            ]);
            
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/repuestos');
        }
    }
    
    /**
     * Mostrar formulario de edición (RF4)
     */
    public function edit($id) {
        $this->authController->requirePermission('edit_repuestos');
        
        try {
            $repuesto = $this->repuestoService->getRepuestoById($id);
            $categorias = $this->repuestoService->getAllCategorias();
            
            $this->render('repuestos/edit', [
                'title' => 'Editar Repuesto',
                'repuesto' => $repuesto,
                'categorias' => $categorias,
                'error' => Flash::get('error')
            ]);
            
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/repuestos');
        }
    }
    
    /**
     * Procesar actualización de repuesto (RF4)
     */
    public function update($id) {
        $this->authController->requirePermission('edit_repuestos');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/repuestos');
            return;
        }
        if (!\App\Core\Csrf::validateFromRequest()) {
            Flash::error('Token CSRF inválido');
            $this->redirect("/repuestos/{$id}/editar");
            return;
        }
        
        try {
            $data = [
                'codigo' => $_POST['codigo'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'categoria_id' => $_POST['categoria_id'] ?? '',
                'precio_compra' => $_POST['precio_compra'] ?? 0,
                'precio_venta' => $_POST['precio_venta'] ?? 0,
                'stock_actual' => $_POST['stock_actual'] ?? 0,
                'stock_minimo' => $_POST['stock_minimo'] ?? 5,
                'stock_maximo' => $_POST['stock_maximo'] ?? 100,
                'activo' => isset($_POST['activo'])
            ];
            
            $this->repuestoService->updateRepuesto($id, $data);
            
            Flash::success('Repuesto actualizado correctamente');
            $this->redirect('/repuestos');
            
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect("/repuestos/{$id}/editar");
        }
    }
    
    /**
     * Eliminar repuesto (RF4)
     */
    public function destroy($id) {
        $this->authController->requirePermission('delete_repuestos');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/repuestos');
            return;
        }
        if (!\App\Core\Csrf::validateFromRequest()) {
            Flash::error('Token CSRF inválido');
            $this->redirect('/repuestos');
            return;
        }
        
        try {
            $this->repuestoService->deleteRepuesto($id);
            
            Flash::success('Repuesto eliminado correctamente');
            $this->redirect('/repuestos');
            
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/repuestos');
        }
    }
    
    /**
     * Buscar repuestos (RF7)
     */
    public function search() {
        $this->authController->requirePermission('view_repuestos');
        
        $term = $_GET['q'] ?? '';
        $categoriaId = $_GET['categoria_id'] ?? null;
        $page = (int)($_GET['page'] ?? 1);
        
        try {
            $result = $this->repuestoService->searchRepuestos($term, $categoriaId, $page);
            $categorias = $this->repuestoService->getAllCategorias();
            
            $this->render('repuestos/search', [
                'title' => 'Buscar Repuestos',
                'repuestos' => $result['repuestos'],
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current_page' => $result['current_page'],
                'search_term' => $term,
                'categoria_id' => $categoriaId,
                'categorias' => $categorias
            ]);
            
        } catch (\Exception $e) {
            $this->render('repuestos/search', [
                'title' => 'Buscar Repuestos',
                'repuestos' => [],
                'total' => 0,
                'pages' => 0,
                'current_page' => 1,
                'search_term' => $term,
                'categoria_id' => $categoriaId,
                'categorias' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mostrar repuestos por categoría (RF6)
     */
    public function byCategoria($categoriaId) {
        $this->authController->requirePermission('view_repuestos');
        
        $page = (int)($_GET['page'] ?? 1);
        
        try {
            $result = $this->repuestoService->getRepuestosByCategoria($categoriaId, $page);
            
            $this->render('repuestos/by-categoria', [
                'title' => 'Repuestos por Categoría',
                'repuestos' => $result['repuestos'],
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current_page' => $result['current_page'],
                'categoria' => $result['categoria']
            ]);
            
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/repuestos');
        }
    }
    
    /**
     * Mostrar repuestos con stock bajo (RF5)
     */
    public function stockBajo() {
        $this->authController->requirePermission('view_repuestos');
        $page = (int)($_GET['page'] ?? 1);
        $categoriaId = $_GET['categoria_id'] ?? null;
        $includeNear = isset($_GET['include_near']);
        $soloCriticos = isset($_GET['solo_criticos']);

        try {
            $result = $this->repuestoService->getStockAlerts([
                'categoria_id' => $categoriaId,
                'include_near' => $includeNear,
                'solo_criticos' => $soloCriticos,
                'page' => $page
            ]);

            $categorias = $this->repuestoService->getAllCategorias();

            $this->render('repuestos/stock-bajo', [
                'title' => 'Alertas de Stock de Repuestos',
                'repuestos' => $result['repuestos'],
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current_page' => $result['current_page'],
                'filters' => $result['filters'],
                'metrics' => $result['metrics'],
                'categorias' => $categorias
            ]);
        } catch (\Exception $e) {
            $this->render('repuestos/stock-bajo', [
                'title' => 'Alertas de Stock de Repuestos',
                'repuestos' => [],
                'total' => 0,
                'pages' => 0,
                'current_page' => 1,
                'filters' => [
                    'categoria_id' => $categoriaId,
                    'include_near' => $includeNear,
                    'solo_criticos' => $soloCriticos
                ],
                'metrics' => [
                    'conteo' => ['critico'=>0,'bajo'=>0,'casi'=>0,'total'=>0,'total_pagina'=>0],
                    'porcentajes' => ['critico'=>0,'bajo'=>0,'casi'=>0],
                    'valor_afectado' => 0,
                    'top_criticos' => []
                ],
                'categorias' => $this->repuestoService->getAllCategorias(),
                'error' => $e->getMessage()
            ]);
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
