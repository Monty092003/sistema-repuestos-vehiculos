<?php
/**
 * Model Categoria - Sistema de Repuestos de Vehículos
 * Capa de Negocio - Entidad Categoría
 */

namespace App\Models;

class Categoria {
    private $id;
    private $nombre;
    private $descripcion;
    private $activa;
    private $createdAt;
    
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
        $this->nombre = $data['nombre'] ?? '';
        $this->descripcion = $data['descripcion'] ?? '';
        $this->activa = $data['activa'] ?? true;
        $this->createdAt = $data['created_at'] ?? null;
    }
    
    /**
     * Convertir a array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'activa' => $this->activa,
            'created_at' => $this->createdAt
        ];
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }
    public function isActiva() { return $this->activa; }
    public function getCreatedAt() { return $this->createdAt; }
    
    // Setters
    public function setNombre($nombre) { 
        $this->nombre = trim($nombre);
        return $this;
    }
    
    public function setDescripcion($descripcion) { 
        $this->descripcion = trim($descripcion);
        return $this;
    }
    
    public function setActiva($activa) { 
        $this->activa = (bool)$activa;
        return $this;
    }
    
    /**
     * Validar datos de la categoría
     */
    public function validate() {
        $errors = [];
        
        if (empty($this->nombre)) {
            $errors[] = 'El nombre de la categoría es requerido';
        } elseif (strlen($this->nombre) < 2) {
            $errors[] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($this->nombre) > 50) {
            $errors[] = 'El nombre no puede exceder 50 caracteres';
        }
        
        if (!empty($this->descripcion) && strlen($this->descripcion) > 255) {
            $errors[] = 'La descripción no puede exceder 255 caracteres';
        }
        
        return $errors;
    }
    
    /**
     * Obtener nombre formateado
     */
    public function getNombreFormateado() {
        return ucfirst(strtolower($this->nombre));
    }
}
