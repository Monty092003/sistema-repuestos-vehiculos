<?php
namespace App\Models;

class VentaDetalle {
    private $id;
    private $ventaId;
    private $repuestoId;
    private $repuestoCodigo;
    private $repuestoNombre;
    private $cantidad;
    private $precioUnitario;
    private $subtotal;

    public function __construct($data = []) {
        if (!empty($data)) $this->fill($data);
    }

    public function fill(array $data) {
        $this->id = $data['id'] ?? $this->id;
        $this->ventaId = $data['venta_id'] ?? $this->ventaId;
        $this->repuestoId = $data['repuesto_id'] ?? $this->repuestoId;
        $this->repuestoCodigo = $data['codigo'] ?? $data['repuesto_codigo'] ?? $this->repuestoCodigo;
        $this->repuestoNombre = $data['nombre'] ?? $data['repuesto_nombre'] ?? $this->repuestoNombre;
        $this->cantidad = (int)($data['cantidad'] ?? $this->cantidad);
        $this->precioUnitario = (float)($data['precio_unitario'] ?? $this->precioUnitario);
        $this->subtotal = (float)($data['subtotal'] ?? ($this->cantidad * $this->precioUnitario));
    }

    public function validate() {
        $errors = [];
        if (empty($this->repuestoId)) $errors[] = 'Repuesto requerido';
        if ($this->cantidad <= 0) $errors[] = 'Cantidad debe ser mayor a 0';
        if ($this->precioUnitario < 0) $errors[] = 'Precio unitario inválido';
        return $errors;
    }

    // Getters
    public function getId(){return $this->id;}
    public function getVentaId(){return $this->ventaId;}
    public function getRepuestoId(){return $this->repuestoId;}
    public function getRepuestoCodigo(){return $this->repuestoCodigo;}
    public function getRepuestoNombre(){return $this->repuestoNombre;}
    public function getCantidad(){return $this->cantidad;}
    public function getPrecioUnitario(){return $this->precioUnitario;}
    public function getSubtotal(){return $this->subtotal;}

    // Setters fluidos
    public function setRepuestoId($v){$this->repuestoId = $v; return $this;}
    public function setRepuestoCodigo($v){$this->repuestoCodigo = $v; return $this;}
    public function setRepuestoNombre($v){$this->repuestoNombre = $v; return $this;}
    public function setCantidad($v){$this->cantidad = (int)$v; return $this;}
    public function setPrecioUnitario($v){$this->precioUnitario = (float)$v; return $this;}
    public function setSubtotal($v){$this->subtotal = (float)$v; return $this;}
}
