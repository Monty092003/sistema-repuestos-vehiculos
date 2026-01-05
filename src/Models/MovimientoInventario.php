<?php
/**
 * Model MovimientoInventario - Sistema de Repuestos de Vehículos
 * Capa de Negocio - Entidad Movimiento de Inventario
 */

namespace App\Models;

class MovimientoInventario {
    private $id;
    private $repuestoId;
    private $repuesto;
    private $tipo;
    private $cantidad;
    private $motivo;
    private $proveedorId;
    private $proveedor;
    private $usuarioId;
    private $usuario;
    private $fechaMovimiento;
    private $observaciones;
    
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
        $this->repuestoId = $data['repuesto_id'] ?? null;
        $this->repuesto = $data['repuesto'] ?? null;
        $this->tipo = $data['tipo'] ?? '';
        $this->cantidad = $data['cantidad'] ?? 0;
        $this->motivo = $data['motivo'] ?? '';
        $this->proveedorId = $data['proveedor_id'] ?? null;
        $this->proveedor = $data['proveedor'] ?? null;
        $this->usuarioId = $data['usuario_id'] ?? null;
        $this->usuario = $data['usuario'] ?? null;
        $this->fechaMovimiento = $data['fecha_movimiento'] ?? null;
        $this->observaciones = $data['observaciones'] ?? '';
    }
    
    /**
     * Convertir a array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'repuesto_id' => $this->repuestoId,
            'repuesto' => $this->repuesto,
            'tipo' => $this->tipo,
            'cantidad' => $this->cantidad,
            'motivo' => $this->motivo,
            'proveedor_id' => $this->proveedorId,
            'proveedor' => $this->proveedor,
            'usuario_id' => $this->usuarioId,
            'usuario' => $this->usuario,
            'fecha_movimiento' => $this->fechaMovimiento,
            'observaciones' => $this->observaciones
        ];
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getRepuestoId() { return $this->repuestoId; }
    public function getRepuesto() { return $this->repuesto; }
    public function getTipo() { return $this->tipo; }
    public function getCantidad() { return $this->cantidad; }
    public function getMotivo() { return $this->motivo; }
    public function getProveedorId() { return $this->proveedorId; }
    public function getProveedor() { return $this->proveedor; }
    public function getUsuarioId() { return $this->usuarioId; }
    public function getUsuario() { return $this->usuario; }
    public function getFechaMovimiento() { return $this->fechaMovimiento; }
    public function getObservaciones() { return $this->observaciones; }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setRepuestoId($repuestoId) { 
        $this->repuestoId = $repuestoId;
        return $this;
    }
    
    public function setRepuesto($repuesto) { 
        $this->repuesto = $repuesto;
        return $this;
    }
    
    public function setTipo($tipo) { 
        $this->tipo = $tipo;
        return $this;
    }
    
    public function setCantidad($cantidad) { 
        $this->cantidad = (int)$cantidad;
        return $this;
    }
    
    public function setMotivo($motivo) { 
        $this->motivo = trim($motivo);
        return $this;
    }
    
    public function setProveedorId($proveedorId) { 
        $this->proveedorId = $proveedorId;
        return $this;
    }
    
    public function setProveedor($proveedor) { 
        $this->proveedor = $proveedor;
        return $this;
    }
    
    public function setUsuarioId($usuarioId) { 
        $this->usuarioId = $usuarioId;
        return $this;
    }
    
    public function setUsuario($usuario) { 
        $this->usuario = $usuario;
        return $this;
    }
    
    public function setFechaMovimiento($fecha) { 
        $this->fechaMovimiento = $fecha;
        return $this;
    }
    
    public function setObservaciones($observaciones) { 
        $this->observaciones = trim($observaciones);
        return $this;
    }
    
    /**
     * Validar datos del movimiento
     */
    public function validate() {
        $errors = [];
        
        if (empty($this->repuestoId)) {
            $errors[] = 'El repuesto es requerido';
        }
        
        if (empty($this->tipo)) {
            $errors[] = 'El tipo de movimiento es requerido';
        } elseif (!in_array($this->tipo, [MOVIMIENTO_ENTRADA, MOVIMIENTO_SALIDA, MOVIMIENTO_AJUSTE])) {
            $errors[] = 'El tipo de movimiento no es válido';
        }
        
        if ($this->cantidad <= 0) {
            $errors[] = 'La cantidad debe ser mayor a 0';
        }
        
        if (empty($this->motivo)) {
            $errors[] = 'El motivo del movimiento es requerido';
        } elseif (strlen($this->motivo) > 200) {
            $errors[] = 'El motivo no puede exceder 200 caracteres';
        }
        
        if (empty($this->usuarioId)) {
            $errors[] = 'El usuario es requerido';
        }
        
        if (!empty($this->observaciones) && strlen($this->observaciones) > 500) {
            $errors[] = 'Las observaciones no pueden exceder 500 caracteres';
        }
        
        return $errors;
    }
    
    /**
     * Verificar si es una entrada
     */
    public function isEntrada() {
        return $this->tipo === MOVIMIENTO_ENTRADA;
    }
    
    /**
     * Verificar si es una salida
     */
    public function isSalida() {
        return $this->tipo === MOVIMIENTO_SALIDA;
    }
    
    /**
     * Verificar si es un ajuste
     */
    public function isAjuste() {
        return $this->tipo === MOVIMIENTO_AJUSTE;
    }
    
    /**
     * Obtener nombre del tipo de movimiento
     */
    public function getTipoNombre() {
        $tipos = [
            MOVIMIENTO_ENTRADA => 'Entrada',
            MOVIMIENTO_SALIDA => 'Salida',
            MOVIMIENTO_AJUSTE => 'Ajuste'
        ];
        return $tipos[$this->tipo] ?? 'Desconocido';
    }
    
    /**
     * Obtener clase CSS para el tipo de movimiento
     */
    public function getTipoClase() {
        $clases = [
            MOVIMIENTO_ENTRADA => 'bg-success',
            MOVIMIENTO_SALIDA => 'bg-danger',
            MOVIMIENTO_AJUSTE => 'bg-warning'
        ];
        return $clases[$this->tipo] ?? 'bg-secondary';
    }
    
    /**
     * Obtener icono para el tipo de movimiento
     */
    public function getTipoIcono() {
        $iconos = [
            MOVIMIENTO_ENTRADA => 'fas fa-arrow-up',
            MOVIMIENTO_SALIDA => 'fas fa-arrow-down',
            MOVIMIENTO_AJUSTE => 'fas fa-edit'
        ];
        return $iconos[$this->tipo] ?? 'fas fa-question';
    }
    
    /**
     * Obtener cantidad con signo según el tipo
     */
    public function getCantidadConSigno() {
        if ($this->isEntrada()) {
            return '+' . $this->cantidad;
        } elseif ($this->isSalida()) {
            return '-' . $this->cantidad;
        } else {
            return $this->cantidad;
        }
    }
    
    /**
     * Obtener clase CSS para la cantidad
     */
    public function getCantidadClase() {
        if ($this->isEntrada()) {
            return 'text-success';
        } elseif ($this->isSalida()) {
            return 'text-danger';
        } else {
            return 'text-warning';
        }
    }
}
