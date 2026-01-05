<?php
namespace App\Controllers;

use App\Services\VentaService;
use App\Services\RepuestoService;
use App\Core\Flash;
use App\Core\Csrf;

class VentaController {
    private $ventaService;
    private $repuestoService;
    private $authController;

    public function __construct(){
        $this->ventaService = new VentaService();
        $this->repuestoService = new RepuestoService();
        $this->authController = new AuthController();
    }

    public function index() {
        $this->authController->requirePermission('view_ventas');
        $page = (int)($_GET['page'] ?? 1);
        $filters = [
            'numero' => $_GET['numero'] ?? null,
            'cliente' => $_GET['cliente'] ?? null,
            'estado' => $_GET['estado'] ?? null,
            'fecha_inicio' => $_GET['fecha_inicio'] ?? null,
            'fecha_fin' => $_GET['fecha_fin'] ?? null,
        ];
        $result = $this->ventaService->listarVentas($page, ITEMS_POR_PAGINA, $filters);
        $this->render('ventas/index', [
            'title' => 'Ventas',
            'ventas' => $result['ventas'],
            'total' => $result['total'],
            'pages' => $result['pages'],
            'current_page' => $result['current_page'],
            'filters' => $filters,
            'success' => Flash::get('success'),
            'error' => Flash::get('error')
        ]);
    }

    public function create() {
        $this->authController->requirePermission('create_ventas');
        $repuestos = $this->repuestoService->searchRepuestos('', null, 1, 1000)['repuestos'] ?? [];
        $this->render('ventas/create', [
            'title' => 'Nueva Venta',
            'repuestos' => $repuestos,
            'success' => Flash::get('success'),
            'error' => Flash::get('error')
        ]);
    }

    public function store() {
        $this->authController->requirePermission('create_ventas');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('/ventas'); return; }
        if (!Csrf::validateFromRequest()) {
            Flash::error('Token CSRF inválido o expirado');
            $this->redirect('/ventas/crear');
            return;
        }
        try {
            $items = [];
            if (isset($_POST['items']) && is_array($_POST['items'])) {
                $items = array_values(array_filter($_POST['items'], function($it){
                    return !empty($it['repuesto_id']) && !empty($it['cantidad']);
                }));
            }
            $payload = [
                'cliente_nombre' => $_POST['cliente_nombre'] ?? '',
                'cliente_documento' => $_POST['cliente_documento'] ?? '',
                'cliente_telefono' => $_POST['cliente_telefono'] ?? '',
                'descuento' => $_POST['descuento'] ?? 0,
                'items' => $items,
                'usuario_id' => $_SESSION['user_id'] ?? null
            ];
            $ventaId = $this->ventaService->crearVenta($payload);
            Flash::success('Venta registrada correctamente');
            $this->redirect('/ventas/' . $ventaId);
        } catch (\App\Core\ConcurrencyException $ce) {
            Flash::error('Concurrencia: ' . $ce->getMessage());
            $this->redirect('/ventas/crear');
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/ventas/crear');
        }
    }

    public function show($id) {
        $this->authController->requirePermission('view_ventas');
        try {
            $venta = $this->ventaService->obtenerVenta($id);
            $this->render('ventas/show', [
                'title' => 'Detalle Venta',
                'venta' => $venta,
                'success' => Flash::get('success'),
                'error' => Flash::get('error')
            ]);
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/ventas');
        }
    }

    // Comprobante imprimible reutiliza la misma vista show (se podría separar si difiere diseño)
    public function comprobante($id) {
        $this->authController->requirePermission('view_ventas');
        try {
            $venta = $this->ventaService->obtenerVenta($id);
            $this->render('ventas/show', [
                'title' => 'Comprobante Venta',
                'venta' => $venta,
                'success' => Flash::get('success'),
                'error' => Flash::get('error')
            ]);
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/ventas');
        }
    }

    public function anular($id) {
        $this->authController->requirePermission('anular_ventas');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('/ventas/' . $id); return; }
        if (!Csrf::validateFromRequest()) {
            Flash::error('Token CSRF inválido o expirado');
            $this->redirect('/ventas/' . $id);
            return;
        }
        try {
            $this->ventaService->anularVenta($id, $_SESSION['user_id'] ?? null);
            Flash::success('Venta anulada y stock revertido');
        } catch (\App\Core\ConcurrencyException $ce) {
            Flash::error('Concurrencia: ' . $ce->getMessage());
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
        }
        $this->redirect('/ventas/' . $id);
    }

    private function redirect($url) {
        header('Location: ' . BASE_URL . ltrim($url, '/'));
        exit;
    }

    private function render($view, $data = []) {
        extract($data);
        $viewPath = SRC_PATH . '/Views/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo 'Vista no encontrada: ' . $view;
        }
    }
}
