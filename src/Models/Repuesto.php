<?php
/**
 * Model Repuesto - Sistema de Repuestos de Vehículos
 * Capa de Negocio - Entidad Repuesto
 */

namespace App\Models;

class Repuesto {
    private $id;
    private $codigo;
    private $nombre;
    private $descripcion;
    private $categoriaId;
    private $categoria;
    private $precioCompra;
    private $precioVenta;
    private $stockActual;
    private $stockMinimo;
    private $stockMaximo;
    private $activo;
    private $createdAt;
    private $updatedAt;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->fill($data);
        }
    }
    
    /**
     * Llenar propiedades desde array
     */
    public function fill($data) {
        $this->id = $data['id'] ?? null;
        $this->codigo = $data['codigo'] ?? '';
        $this->nombre = $data['nombre'] ?? '';
        $this->descripcion = $data['descripcion'] ?? '';
        $this->categoriaId = $data['categoria_id'] ?? null;
        $this->categoria = $data['categoria'] ?? null;
        $this->precioCompra = $data['precio_compra'] ?? 0.00;
        $this->precioVenta = $data['precio_venta'] ?? 0.00;
        $this->stockActual = $data['stock_actual'] ?? 0;
        $this->stockMinimo = $data['stock_minimo'] ?? 5;
        $this->stockMaximo = $data['stock_maximo'] ?? 100;
        $this->activo = $data['activo'] ?? true;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }
    
    /**
     * Convertir a array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria_id' => $this->categoriaId,
            'categoria' => $this->categoria,
            'precio_compra' => $this->precioCompra,
            'precio_venta' => $this->precioVenta,
            'stock_actual' => $this->stockActual,
            'stock_minimo' => $this->stockMinimo,
            'stock_maximo' => $this->stockMaximo,
            'activo' => $this->activo,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getCodigo() { return $this->codigo; }
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }
    public function getCategoriaId() { return $this->categoriaId; }
    public function getCategoria() { return $this->categoria; }
    public function getPrecioCompra() { return $this->precioCompra; }
    public function getPrecioVenta() { return $this->precioVenta; }
    public function getStockActual() { return $this->stockActual; }
    public function getStockMinimo() { return $this->stockMinimo; }
    public function getStockMaximo() { return $this->stockMaximo; }
    public function isActivo() { return $this->activo; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setCodigo($codigo) { 
        $this->codigo = strtoupper(trim($codigo));
        return $this;
    }
    
    public function setNombre($nombre) { 
        $this->nombre = trim($nombre);
        return $this;
    }
    
    public function setDescripcion($descripcion) { 
        $this->descripcion = trim($descripcion);
        return $this;
    }
    
    public function setCategoriaId($categoriaId) { 
        $this->categoriaId = $categoriaId;
        return $this;
    }
    
    public function setCategoria($categoria) { 
        $this->categoria = $categoria;
        return $this;
    }
    
    public function setPrecioCompra($precio) { 
        $this->precioCompra = (float)$precio;
        return $this;
    }
    
    public function setPrecioVenta($precio) { 
        $this->precioVenta = (float)$precio;
        return $this;
    }
    
    public function setStockActual($stock) { 
        $this->stockActual = (int)$stock;
        return $this;
    }
    
    public function setStockMinimo($stock) { 
        $this->stockMinimo = (int)$stock;
        return $this;
    }
    
    public function setStockMaximo($stock) { 
        $this->stockMaximo = (int)$stock;
        return $this;
    }
    
    public function setActivo($activo) { 
        $this->activo = (bool)$activo;
        return $this;
    }
    
    /**
     * Validar datos del repuesto
     */
    public function validate() {
        $errors = [];
        
        if (empty($this->codigo)) {
            $errors[] = 'El código del repuesto es requerido';
        } elseif (strlen($this->codigo) < 3) {
            $errors[] = 'El código debe tener al menos 3 caracteres';
        } elseif (strlen($this->codigo) > 50) {
            $errors[] = 'El código no puede exceder 50 caracteres';
        }
        
        if (empty($this->nombre)) {
            $errors[] = 'El nombre del repuesto es requerido';
        } elseif (strlen($this->nombre) < 2) {
            $errors[] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($this->nombre) > 200) {
            $errors[] = 'El nombre no puede exceder 200 caracteres';
        }
        
        if (empty($this->categoriaId)) {
            $errors[] = 'La categoría es requerida';
        }
        
        if ($this->precioCompra < 0) {
            $errors[] = 'El precio de compra no puede ser negativo';
        }
        
        if ($this->precioVenta < 0) {
            $errors[] = 'El precio de venta no puede ser negativo';
        }
        
        if ($this->precioVenta < $this->precioCompra) {
            $errors[] = 'El precio de venta debe ser mayor o igual al precio de compra';
        }
        
        if ($this->stockActual < 0) {
            $errors[] = 'El stock actual no puede ser negativo';
        }
        
        if ($this->stockMinimo < 0) {
            $errors[] = 'El stock mínimo no puede ser negativo';
        }
        
        if ($this->stockMaximo < $this->stockMinimo) {
            $errors[] = 'El stock máximo debe ser mayor o igual al stock mínimo';
        }
        
        return $errors;
    }
    
    /**
     * Verificar si hay stock bajo
     */
    public function isStockBajo() {
        return $this->stockActual <= $this->stockMinimo;
    }
    
    /**
     * Verificar si hay stock crítico
     */
    public function isStockCritico() {
        return $this->stockActual <= STOCK_CRITICO_LIMIT;
    }
    
    /**
     * Verificar si hay stock disponible
     */
    public function hasStock($cantidad = 1) {
        return $this->stockActual >= $cantidad;
    }
    
    /**
     * Obtener margen de ganancia
     */
    public function getMargenGanancia() {
        if ($this->precioCompra == 0) return 0;
        return (($this->precioVenta - $this->precioCompra) / $this->precioCompra) * 100;
    }
    
    /**
     * Obtener estado del stock
     */
    public function getEstadoStock() {
        if ($this->isStockCritico()) {
            return 'critico';
        } elseif ($this->isStockBajo()) {
            return 'bajo';
        } elseif ($this->stockActual >= $this->stockMaximo) {
            return 'exceso';
        } else {
            return 'normal';
        }
    }
    
    /**
     * Obtener nombre del estado del stock
     */
    public function getEstadoStockNombre() {
        $estados = [
            'critico' => 'Crítico',
            'bajo' => 'Bajo',
            'exceso' => 'Exceso',
            'normal' => 'Normal'
        ];
        return $estados[$this->getEstadoStock()] ?? 'Desconocido';
    }
    
    /**
     * Obtener clase CSS para el estado del stock
     */
    public function getEstadoStockClase() {
        $clases = [
            'critico' => 'bg-danger',
            'bajo' => 'bg-warning',
            'exceso' => 'bg-info',
            'normal' => 'bg-success'
        ];
        return $clases[$this->getEstadoStock()] ?? 'bg-secondary';
    }
}
