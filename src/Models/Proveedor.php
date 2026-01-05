<?php
/**
 * Model Proveedor - Sistema de Repuestos de Vehículos
 * Capa de Negocio - Entidad Proveedor
 */

namespace App\Models;

class Proveedor {
    private $id;
    private $nombre;
    private $contacto;
    private $telefono;
    private $email;
    private $direccion;
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
        $this->nombre = $data['nombre'] ?? '';
        $this->contacto = $data['contacto'] ?? '';
        $this->telefono = $data['telefono'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->direccion = $data['direccion'] ?? '';
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
            'nombre' => $this->nombre,
            'contacto' => $this->contacto,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion,
            'activo' => $this->activo,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getContacto() { return $this->contacto; }
    public function getTelefono() { return $this->telefono; }
    public function getEmail() { return $this->email; }
    public function getDireccion() { return $this->direccion; }
    public function isActivo() { return $this->activo; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setNombre($nombre) { 
        $this->nombre = trim($nombre);
        return $this;
    }
    
    public function setContacto($contacto) { 
        $this->contacto = trim($contacto);
        return $this;
    }
    
    public function setTelefono($telefono) { 
        $this->telefono = trim($telefono);
        return $this;
    }
    
    public function setEmail($email) { 
        $this->email = trim(strtolower($email));
        return $this;
    }
    
    public function setDireccion($direccion) { 
        $this->direccion = trim($direccion);
        return $this;
    }
    
    public function setActivo($activo) { 
        $this->activo = (bool)$activo;
        return $this;
    }
    
    /**
     * Validar datos del proveedor
     */
    public function validate() {
        $errors = [];
        
        if (empty($this->nombre)) {
            $errors[] = 'El nombre del proveedor es requerido';
        } elseif (strlen($this->nombre) < 2) {
            $errors[] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($this->nombre) > 100) {
            $errors[] = 'El nombre no puede exceder 100 caracteres';
        }
        
        if (!empty($this->contacto) && strlen($this->contacto) > 100) {
            $errors[] = 'El contacto no puede exceder 100 caracteres';
        }
        
        if (!empty($this->telefono) && strlen($this->telefono) > 20) {
            $errors[] = 'El teléfono no puede exceder 20 caracteres';
        }
        
        if (!empty($this->email)) {
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El email no es válido';
            } elseif (strlen($this->email) > 100) {
                $errors[] = 'El email no puede exceder 100 caracteres';
            }
        }
        
        if (!empty($this->direccion) && strlen($this->direccion) > 255) {
            $errors[] = 'La dirección no puede exceder 255 caracteres';
        }
        
        return $errors;
    }
    
    /**
     * Obtener nombre formateado
     */
    public function getNombreFormateado() {
        return ucwords(strtolower($this->nombre));
    }
    
    /**
     * Obtener información de contacto completa
     */
    public function getInfoContacto() {
        $info = [];
        
        if ($this->contacto) {
            $info[] = "Contacto: {$this->contacto}";
        }
        
        if ($this->telefono) {
            $info[] = "Tel: {$this->telefono}";
        }
        
        if ($this->email) {
            $info[] = "Email: {$this->email}";
        }
        
        return implode(' | ', $info);
    }
    
    /**
     * Verificar si tiene información de contacto completa
     */
    public function hasContactoCompleto() {
        return !empty($this->contacto) && !empty($this->telefono) && !empty($this->email);
    }
    
    /**
     * Obtener iniciales del proveedor
     */
    public function getIniciales() {
        $palabras = explode(' ', $this->nombre);
        $iniciales = '';
        
        foreach ($palabras as $palabra) {
            if (!empty($palabra)) {
                $iniciales .= strtoupper(substr($palabra, 0, 1));
            }
        }
        
        return substr($iniciales, 0, 3); // Máximo 3 iniciales
    }
}
