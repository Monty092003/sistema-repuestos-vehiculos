<?php
namespace App\Models;

class Venta {
    private $id;
    private $numeroVenta;
    private $clienteNombre;
    private $clienteDocumento;
    private $clienteTelefono;
    private $subtotal = 0.0;
    private $descuento = 0.0;
    private $total = 0.0;
    private $estado = 'COMPLETADA';
    private $usuarioId;
    private $createdAt;
    private $updatedAt;

    /** @var VentaDetalle[] */
    private $detalles = [];

    public function __construct($data = []) {
        if (!empty($data)) $this->fill($data);
    }

    public function fill(array $data) {
        $this->id = $data['id'] ?? $this->id;
        $this->numeroVenta = $data['numero_venta'] ?? $this->numeroVenta;
        $this->clienteNombre = $data['cliente_nombre'] ?? $this->clienteNombre;
        $this->clienteDocumento = $data['cliente_documento'] ?? $this->clienteDocumento;
        $this->clienteTelefono = $data['cliente_telefono'] ?? $this->clienteTelefono;
        $this->subtotal = (float)($data['subtotal'] ?? $this->subtotal);
        $this->descuento = (float)($data['descuento'] ?? $this->descuento);
        $this->total = (float)($data['total'] ?? $this->total);
    $this->estado = !empty($data['estado']) ? strtoupper($data['estado']) : $this->estado;
        $this->usuarioId = $data['usuario_id'] ?? $this->usuarioId;
        $this->createdAt = $data['created_at'] ?? $this->createdAt;
        $this->updatedAt = $data['updated_at'] ?? $this->updatedAt;
    }

    public function addDetalle(VentaDetalle $detalle) {
        $this->detalles[] = $detalle;
        return $this;
    }

    public function getDetalles() { return $this->detalles; }

    public function recalcTotals() {
        $this->subtotal = 0.0;
        foreach ($this->detalles as $d) {
            $this->subtotal += $d->getSubtotal();
        }
        if ($this->descuento < 0) $this->descuento = 0;
        if ($this->descuento > $this->subtotal) $this->descuento = $this->subtotal;
        $this->total = $this->subtotal - $this->descuento;
    }

    public function validate() {
        $errors = [];
        if (empty($this->numeroVenta)) $errors[] = 'Número de venta requerido';
        if (empty($this->usuarioId)) $errors[] = 'Usuario requerido';
        if (empty($this->detalles)) $errors[] = 'La venta debe tener al menos un ítem';
        foreach ($this->detalles as $detalle) {
            $dErr = $detalle->validate();
            if (!empty($dErr)) $errors = array_merge($errors, $dErr);
        }
        if ($this->subtotal < 0) $errors[] = 'Subtotal inválido';
        if ($this->total < 0) $errors[] = 'Total inválido';
        return $errors;
    }

    // Getters
    public function getId(){return $this->id;}    
    public function getNumeroVenta(){return $this->numeroVenta;}
    public function getClienteNombre(){return $this->clienteNombre;}
    public function getClienteDocumento(){return $this->clienteDocumento;}
    public function getClienteTelefono(){return $this->clienteTelefono;}
    public function getSubtotal(){return $this->subtotal;}
    public function getDescuento(){return $this->descuento;}
    public function getTotal(){return $this->total;}
    public function getEstado(){return $this->estado;}
    // Alias para vistas
    public function getNumero(){return $this->numeroVenta;}
    public function getFecha(){return $this->createdAt;}
    public function getUsuarioId(){return $this->usuarioId;}
    public function getCreatedAt(){return $this->createdAt;}
    public function getUpdatedAt(){return $this->updatedAt;}

    // Setters fluidos
    public function setNumeroVenta($v){$this->numeroVenta = $v; return $this;}
    public function setClienteNombre($v){$this->clienteNombre = trim($v); return $this;}
    public function setClienteDocumento($v){$this->clienteDocumento = trim($v); return $this;}
    public function setClienteTelefono($v){$this->clienteTelefono = trim($v); return $this;}
    public function setDescuento($v){$this->descuento = (float)$v; return $this;}
    public function setEstado($v){$this->estado = strtoupper($v); return $this;}
    public function setUsuarioId($v){$this->usuarioId = $v; return $this;}
}
