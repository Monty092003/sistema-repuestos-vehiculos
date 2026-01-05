<?php
/**
 * RepuestoService - Sistema de Repuestos de Vehículos
 * Capa de Negocio - Lógica de negocio para repuestos
 */

namespace App\Services;

use App\Models\Repuesto;
use App\Models\Categoria;
use App\Repositories\RepuestoRepository;
use App\Repositories\CategoriaRepository;

class RepuestoService {
    private $repuestoRepository;
    private $categoriaRepository;
    
    public function __construct() {
        $this->repuestoRepository = new RepuestoRepository();
        $this->categoriaRepository = new CategoriaRepository();
    }
    
    /**
     * Obtener todos los repuestos con paginación (RF4)
     */
    public function getAllRepuestos($page = 1, $limit = ITEMS_POR_PAGINA) {
        $repuestos = $this->repuestoRepository->findAll($page, $limit);
        $total = $this->repuestoRepository->count();
        
        return [
            'repuestos' => $repuestos,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ];
    }
    
    /**
     * Buscar repuestos (RF7)
     */
    public function searchRepuestos($term, $categoriaId = null, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $repuestos = $this->repuestoRepository->search($term, $categoriaId, $page, $limit);
        $total = $this->repuestoRepository->countFiltered($term, $categoriaId);
        
        return [
            'repuestos' => $repuestos,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page,
            'search_term' => $term,
            'categoria_id' => $categoriaId
        ];
    }
    
    /**
     * Obtener repuestos por categoría (RF6)
     */
    public function getRepuestosByCategoria($categoriaId, $page = 1, $limit = ITEMS_POR_PAGINA) {
        $repuestos = $this->repuestoRepository->findByCategoria($categoriaId, $page, $limit);
        $total = $this->repuestoRepository->countByCategoria($categoriaId);
        
        $categoria = $this->categoriaRepository->findById($categoriaId);
        
        return [
            'repuestos' => $repuestos,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page,
            'categoria' => $categoria
        ];
    }
    
    /**
     * Obtener repuestos con stock bajo (RF5)
     */
    public function getRepuestosStockBajo($page = 1, $limit = ITEMS_POR_PAGINA) {
        $repuestos = $this->repuestoRepository->findStockBajo($page, $limit);
        $total = $this->repuestoRepository->countStockBajo();
        
        return [
            'repuestos' => $repuestos,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ];
    }
    
    /**
     * Obtener repuesto por ID
     */
    public function getRepuestoById($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new \Exception('ID de repuesto inválido');
        }
        
        $repuesto = $this->repuestoRepository->findById($id);
        if (!$repuesto) {
            throw new \Exception('Repuesto no encontrado');
        }
        
        return $repuesto;
    }
    
    /**
     * Crear nuevo repuesto (RF4)
     */
    public function createRepuesto($data) {
        // Validar datos requeridos
        if (empty($data['codigo']) || empty($data['nombre']) || empty($data['categoria_id'])) {
            throw new \Exception('Código, nombre y categoría son requeridos');
        }
        
        // Verificar si el código ya existe
        if ($this->repuestoRepository->codigoExists($data['codigo'])) {
            throw new \Exception('El código ya está registrado');
        }
        
        // Verificar que la categoría existe
        $categoria = $this->categoriaRepository->findById($data['categoria_id']);
        if (!$categoria) {
            throw new \Exception('La categoría seleccionada no existe');
        }
        
        // Crear instancia del repuesto
        $repuesto = new Repuesto();
        $repuesto->setCodigo($data['codigo'])
                 ->setNombre($data['nombre'])
                 ->setDescripcion($data['descripcion'] ?? '')
                 ->setCategoriaId($data['categoria_id'])
                 ->setPrecioCompra($data['precio_compra'] ?? 0)
                 ->setPrecioVenta($data['precio_venta'] ?? 0)
                 ->setStockActual($data['stock_actual'] ?? 0)
                 ->setStockMinimo($data['stock_minimo'] ?? 5)
                 ->setStockMaximo($data['stock_maximo'] ?? 100)
                 ->setActivo($data['activo'] ?? true);
        
        // Validar el repuesto
        $errors = $repuesto->validate();
        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }
        
        // Guardar en la base de datos
        return $this->repuestoRepository->create($repuesto);
    }
    
    /**
     * Actualizar repuesto (RF4)
     */
    public function updateRepuesto($id, $data) {
        // Obtener repuesto existente
        $repuesto = $this->getRepuestoById($id);
        
        // Actualizar datos
        if (isset($data['codigo'])) {
            // Verificar si el nuevo código ya existe (excluyendo el repuesto actual)
            if ($data['codigo'] !== $repuesto->getCodigo() && 
                $this->repuestoRepository->codigoExists($data['codigo'], $id)) {
                throw new \Exception('El código ya está registrado');
            }
            $repuesto->setCodigo($data['codigo']);
        }
        
        if (isset($data['nombre'])) {
            $repuesto->setNombre($data['nombre']);
        }
        
        if (isset($data['descripcion'])) {
            $repuesto->setDescripcion($data['descripcion']);
        }
        
        if (isset($data['categoria_id'])) {
            // Verificar que la categoría existe
            $categoria = $this->categoriaRepository->findById($data['categoria_id']);
            if (!$categoria) {
                throw new \Exception('La categoría seleccionada no existe');
            }
            $repuesto->setCategoriaId($data['categoria_id']);
        }
        
        if (isset($data['precio_compra'])) {
            $repuesto->setPrecioCompra($data['precio_compra']);
        }
        
        if (isset($data['precio_venta'])) {
            $repuesto->setPrecioVenta($data['precio_venta']);
        }
        
        if (isset($data['stock_actual'])) {
            $repuesto->setStockActual($data['stock_actual']);
        }
        
        if (isset($data['stock_minimo'])) {
            $repuesto->setStockMinimo($data['stock_minimo']);
        }
        
        if (isset($data['stock_maximo'])) {
            $repuesto->setStockMaximo($data['stock_maximo']);
        }
        
        if (isset($data['activo'])) {
            $repuesto->setActivo($data['activo']);
        }
        
        // Validar el repuesto actualizado
        $errors = $repuesto->validate();
        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }
        
        // Guardar cambios
        return $this->repuestoRepository->update($repuesto);
    }
    
    /**
     * Eliminar repuesto (RF4)
     */
    public function deleteRepuesto($id) {
        $repuesto = $this->getRepuestoById($id);
        
        // Verificar si tiene stock
        if ($repuesto->getStockActual() > 0) {
            throw new \Exception('No se puede eliminar un repuesto que tiene stock disponible');
        }
        
        return $this->repuestoRepository->delete($id);
    }
    
    /**
     * Actualizar stock de repuesto
     */
    public function updateStock($id, $nuevoStock) {
        $repuesto = $this->getRepuestoById($id);
        
        if ($nuevoStock < 0) {
            throw new \Exception('El stock no puede ser negativo');
        }
        
        $this->repuestoRepository->updateStock($id, $nuevoStock);
        
        return true;
    }
    
    /**
     * Obtener estadísticas de repuestos
     */
    public function getRepuestoStats() {
        return $this->repuestoRepository->getStats();
    }
    
    /**
     * Obtener repuestos más vendidos
     */
    public function getRepuestosMasVendidos($limit = 10) {
        return $this->repuestoRepository->getMasVendidos($limit);
    }
    
    /**
     * Obtener todas las categorías
     */
    public function getAllCategorias() {
        return $this->categoriaRepository->findAll();
    }
    
    /**
     * Obtener categorías con conteo de repuestos
     */
    public function getCategoriasWithCount() {
        return $this->categoriaRepository->findAllWithRepuestoCount();
    }
    
    /**
     * Verificar disponibilidad de stock
     */
    public function verificarStock($repuestoId, $cantidad) {
        $repuesto = $this->getRepuestoById($repuestoId);
        
        if (!$repuesto->hasStock($cantidad)) {
            throw new \Exception("Stock insuficiente. Disponible: {$repuesto->getStockActual()}, Solicitado: {$cantidad}");
        }
        
        return true;
    }
    
    /**
     * Obtener alertas de stock
     */
    public function getAlertasStock() {
        $repuestos = $this->repuestoRepository->findStockBajo(1, 1000); // Obtener todos los de stock bajo
        
        $alertas = [
            'critico' => [],
            'bajo' => []
        ];
        
        foreach ($repuestos as $repuesto) {
            if ($repuesto->isStockCritico()) {
                $alertas['critico'][] = $repuesto;
            } elseif ($repuesto->isStockBajo()) {
                $alertas['bajo'][] = $repuesto;
            }
        }
        
        return $alertas;
    }

    /**
     * Obtener alertas de stock avanzadas (RF16)
     * Filtros:
     * - categoria_id (int|null)
     * - include_near (bool) incluir CASI
     * - solo_criticos (bool) filtra solo severidad CRITICO (ignora include_near)
     * - page (int)
     * - limit (int)
     * Devuelve estructura con items y métricas agregadas.
     */
    public function getStockAlerts(array $filters = []) {
        $categoriaId   = isset($filters['categoria_id']) && $filters['categoria_id'] !== '' ? (int)$filters['categoria_id'] : null;
        $includeNear   = !empty($filters['include_near']);
        $soloCriticos  = !empty($filters['solo_criticos']);
        $page          = isset($filters['page']) && (int)$filters['page'] > 0 ? (int)$filters['page'] : 1;
        $limit         = defined('ITEMS_POR_PAGINA') ? ITEMS_POR_PAGINA : 15; // fallback

        // Obtener items y total para paginación
        $items = $this->repuestoRepository->findStockAlerts($categoriaId, $includeNear, $soloCriticos, $page, $limit);
        $total = $this->repuestoRepository->countStockAlerts($categoriaId, $includeNear, $soloCriticos);

        // Métricas agregadas sobre los items de la página (y algunas relativas al total)
        $conteo = [ 'critico' => 0, 'bajo' => 0, 'casi' => 0 ];
        $valorAfectado = 0.0; // suma stock_actual * precio_compra de los items listados
        $topCriticosPool = [];

        foreach ($items as $r) {
            $sev = $r->severidad ?? 'OK';
            switch ($sev) {
                case 'CRITICO': $conteo['critico']++; $topCriticosPool[] = $r; break;
                case 'BAJO': $conteo['bajo']++; break;
                case 'CASI': $conteo['casi']++; break;
            }
            if (property_exists($r, 'precio_compra')) {
                $valorAfectado += ($r->getStockActual() * (float)$r->getPrecioCompra());
            }
        }

        $conteo['total_pagina'] = array_sum($conteo);
        $conteo['total'] = $total; // total global de alertas con filtros (todas las páginas)

        // Porcentajes relativos al total global filtrado
        $porcentajes = [];
        if ($total > 0) {
            $porcentajes = [
                'critico' => round(($conteo['critico'] / $total) * 100, 2),
                'bajo'    => round(($conteo['bajo'] / $total) * 100, 2),
                'casi'    => round(($conteo['casi'] / $total) * 100, 2),
            ];
        } else {
            $porcentajes = ['critico'=>0,'bajo'=>0,'casi'=>0];
        }

        // Top 5 críticos ordenados por recomendado_reponer desc, luego porcentaje_min asc
        usort($topCriticosPool, function($a, $b){
            $ra = $a->recomendado_reponer ?? 0; $rb = $b->recomendado_reponer ?? 0;
            if ($ra === $rb) {
                $pa = $a->porcentaje_min ?? 9999; $pb = $b->porcentaje_min ?? 9999;
                return $pa <=> $pb; // menor porcentaje primero
            }
            return $rb <=> $ra; // mayor recomendado primero
        });
        $topCriticos = array_slice($topCriticosPool, 0, 5);

        // Páginas
        $pages = $limit > 0 ? (int)ceil($total / $limit) : 1;

        return [
            'repuestos' => $items,
            'total' => $total,
            'pages' => $pages,
            'current_page' => $page,
            'filters' => [
                'categoria_id' => $categoriaId,
                'include_near' => $includeNear,
                'solo_criticos' => $soloCriticos
            ],
            'metrics' => [
                'conteo' => $conteo,
                'porcentajes' => $porcentajes,
                'valor_afectado' => $valorAfectado,
                'top_criticos' => $topCriticos
            ]
        ];
    }
}
