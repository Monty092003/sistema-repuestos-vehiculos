<?php
/**
 * Model User - Sistema de Repuestos de Vehículos
 * Capa de Negocio - Entidad Usuario
 */

namespace App\Models;

class User {
    private $id;
    private $nombre;
    private $email;
    private $password;
    private $rol;
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
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->rol = $data['rol'] ?? ROLE_EMPLOYEE;
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
            'email' => $this->email,
            'rol' => $this->rol,
            'activo' => $this->activo,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getEmail() { return $this->email; }
    public function getRol() { return $this->rol; }
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
    
    public function setEmail($email) { 
        $this->email = trim(strtolower($email));
        return $this;
    }
    
    public function setRol($rol) { 
        $this->rol = $rol;
        return $this;
    }
    
    public function setActivo($activo) { 
        $this->activo = (bool)$activo;
        return $this;
    }
    
    /**
     * Establecer password (se hashea automáticamente)
     */
    public function setPassword($password) {
        if (!empty($password)) {
            $this->password = password_hash($password, PASSWORD_DEFAULT);
        }
        return $this;
    }

    /**
     * Obtener hash interno (uso restringido a repositorio)
     */
    public function getPasswordHash() {
        return $this->password;
    }
    
    /**
     * Verificar password
     */
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }
    
    /**
     * Validar datos del usuario
     */
    public function validate() {
        $errors = [];
        
        if (empty($this->nombre)) {
            $errors[] = 'El nombre es requerido';
        } elseif (strlen($this->nombre) < 2) {
            $errors[] = 'El nombre debe tener al menos 2 caracteres';
        }
        
        if (empty($this->email)) {
            $errors[] = 'El email es requerido';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email no es válido';
        }
        
        if (!in_array($this->rol, [ROLE_ADMIN, ROLE_EMPLOYEE])) {
            $errors[] = 'El rol no es válido';
        }
        
        return $errors;
    }
    
    /**
     * Verificar si es administrador
     */
    public function isAdmin() {
        return $this->rol === ROLE_ADMIN;
    }
    
    /**
     * Verificar si es empleado
     */
    public function isEmployee() {
        return $this->rol === ROLE_EMPLOYEE;
    }
    
    /**
     * Obtener nombre del rol en español
     */
    public function getRolNombre() {
        return $this->rol === ROLE_ADMIN ? 'Administrador' : 'Empleado';
    }
}
