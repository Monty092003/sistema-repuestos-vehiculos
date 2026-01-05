<?php
namespace App\Services;

use App\Repositories\VentaRepository;
use App\Repositories\RepuestoRepository;
use App\Repositories\MovimientoInventarioRepository;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\MovimientoInventario;

class VentaService {
    private $ventaRepository;
    private $repuestoRepository;
    private $movimientoRepository;

    public function __construct(){
        $this->ventaRepository = new VentaRepository();
        $this->repuestoRepository = new RepuestoRepository();
        $this->movimientoRepository = new MovimientoInventarioRepository();
    }

    public function listarVentas($page = 1, $limit = ITEMS_POR_PAGINA, $filters = []) {
        $data = $this->ventaRepository->findAll($page, $limit, $filters);
        $total = $this->ventaRepository->count($filters);
        return [
            'ventas' => $data,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ];
    }

    public function obtenerVenta($id) {
        $venta = $this->ventaRepository->findById($id);
        if (!$venta) throw new \Exception('Venta no encontrada');
        return $venta;
    }

    public function crearVenta(array $payload) {
        // Validaciones iniciales
        $items = $payload['items'] ?? [];
        if (empty($items)) throw new \Exception('Debe agregar al menos un repuesto a la venta');
        $usuarioId = $payload['usuario_id'] ?? null;
        if (!$usuarioId) throw new \Exception('Usuario no autenticado');

        // Construir modelo Venta
        $venta = new Venta();
        $venta->setNumeroVenta($this->ventaRepository->generateNumeroVenta())
              ->setClienteNombre($payload['cliente_nombre'] ?? '')
              ->setClienteDocumento($payload['cliente_documento'] ?? '')
              ->setClienteTelefono($payload['cliente_telefono'] ?? '')
              ->setUsuarioId($usuarioId)
              ->setDescuento($payload['descuento'] ?? 0);

        $repuestosInvolved = [];

        // Armar detalles
        foreach ($items as $item) {
            if (empty($item['repuesto_id']) || empty($item['cantidad'])) {
                throw new \Exception('Item inválido: repuesto y cantidad requeridos');
            }
            $rep = $this->repuestoRepository->findById($item['repuesto_id']);
            if (!$rep) throw new \Exception('Repuesto no encontrado ID ' . $item['repuesto_id']);
            $cantidad = (int)$item['cantidad'];
            if ($cantidad <= 0) throw new \Exception('Cantidad inválida en un item');
            if (!$rep->hasStock($cantidad)) {
                throw new \Exception('Stock insuficiente para repuesto ' . $rep->getNombre());
            }
            $precioUnit = isset($item['precio_unitario']) ? (float)$item['precio_unitario'] : (float)$rep->getPrecioVenta();
            if ($precioUnit < 0) throw new \Exception('Precio unitario inválido');
            $detalle = new VentaDetalle([
                'repuesto_id' => $rep->getId(),
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnit,
                'subtotal' => $precioUnit * $cantidad
            ]);
            $venta->addDetalle($detalle);
            $repuestosInvolved[] = [$rep, $cantidad];
        }

        $venta->recalcTotals();
        // Validación explícita de descuento
        if ($venta->getDescuento() < 0) {
            throw new \Exception('Descuento no puede ser negativo');
        }
        if ($venta->getDescuento() > $venta->getSubtotal()) {
            throw new \Exception('Descuento no puede exceder el subtotal');
        }
        $errors = $venta->validate();
        if (!empty($errors)) throw new \Exception(implode(', ', $errors));

        // Transacción con locking pesimista
        $db = \App\Core\Database::getInstance();
        try {
            $db->beginTransaction();
            // Lock de todos los repuestos involucrados (ordenado para evitar deadlocks)
            $ids = array_map(function($tuple){ return $tuple[0]->getId(); }, $repuestosInvolved);
            $locked = $this->repuestoRepository->lockMultiple($ids);
            // Validar existencia tras lock y stock suficiente (revalidación defensiva)
            foreach ($repuestosInvolved as [$rep, $cantidad]) {
                $rid = $rep->getId();
                if (!isset($locked[$rid])) {
                    throw new \App\Core\ConcurrencyException('Repuesto desaparecido durante bloqueo (ID '.$rid.')');
                }
                if ((int)$locked[$rid]['stock_actual'] < $cantidad) {
                    throw new \App\Core\ConcurrencyException('Stock insuficiente concurrente para repuesto ID '.$rid);
                }
            }
            // Insert venta y detalles
            $ventaId = $this->ventaRepository->create($venta);
            $this->ventaRepository->bulkInsertDetalles($ventaId, $venta->getDetalles());
            // Movimientos + decrementos atómicos
            foreach ($repuestosInvolved as [$rep, $cantidad]) {
                $mov = new MovimientoInventario();
                $mov->setRepuestoId($rep->getId())
                    ->setTipo(MOVIMIENTO_SALIDA)
                    ->setCantidad($cantidad)
                    ->setMotivo('Venta ' . $venta->getNumeroVenta())
                    ->setProveedorId(null)
                    ->setUsuarioId($usuarioId)
                    ->setObservaciones('Generado automáticamente por venta');
                $mErrors = $mov->validate();
                if (!empty($mErrors)) throw new \Exception('Error movimiento: ' . implode(', ', $mErrors));
                $this->movimientoRepository->create($mov);
                $this->repuestoRepository->updateStockAtomic($rep->getId(), -$cantidad, true);
            }
            $db->commit();
            return $ventaId;
        } catch (\Exception $e) {
            if (method_exists($db, 'rollback')) { $db->rollback(); }
            throw $e;
        }
    }

    public function anularVenta($ventaId, $usuarioId) {
        $venta = $this->ventaRepository->findById($ventaId);
        if (!$venta) throw new \Exception('Venta no encontrada');
    if ($venta->getEstado() === VENTA_ANULADA) throw new \Exception('La venta ya está anulada');
    if (!in_array($venta->getEstado(), [VENTA_COMPLETADA, VENTA_PENDIENTE])) throw new \Exception('Estado de venta no permite anulación');
        // Recuperar detalles para revertir stock
        $detalles = $this->ventaRepository->getDetallesVenta($ventaId);
        if (empty($detalles)) throw new \Exception('No hay detalles para revertir');
        $db = \App\Core\Database::getInstance();
        try {
            $db->beginTransaction();
            // Lock de repuestos afectados
            $ids = array_unique(array_map(function($d){ return (int)$d['repuesto_id']; }, $detalles));
            $this->repuestoRepository->lockMultiple($ids); // no necesitamos datos completos ahora
            foreach ($detalles as $d) {
                $rep = $this->repuestoRepository->findById($d['repuesto_id']);
                if ($rep) {
                    $mov = new MovimientoInventario();
                    $mov->setRepuestoId($rep->getId())
                        ->setTipo(MOVIMIENTO_AJUSTE)
                        ->setCantidad((int)$d['cantidad'])
                        ->setMotivo('Anulación Venta ' . $venta->getNumero())
                        ->setProveedorId(null)
                        ->setUsuarioId($usuarioId)
                        ->setObservaciones('Reverso de stock por anulación de venta');
                    $mErr = $mov->validate();
                    if (!empty($mErr)) throw new \Exception('Error mov inv: ' . implode(', ', $mErr));
                    $this->movimientoRepository->create($mov);
                    $this->repuestoRepository->updateStockAtomic($rep->getId(), (int)$d['cantidad'], false);
                }
            }
            $this->ventaRepository->updateEstado($ventaId, VENTA_ANULADA);
            $db->commit();
        } catch (\Exception $e) {
            if (method_exists($db, 'rollback')) { $db->rollback(); }
            throw $e;
        }
    }

    public function resumenDiario($fechaInicio, $fechaFin) {
        if (empty($fechaInicio) || empty($fechaFin)) {
            // Por defecto últimos 7 días
            $fechaFin = date('Y-m-d');
            $fechaInicio = date('Y-m-d', strtotime('-6 days'));
        }
        return $this->ventaRepository->resumenDiario($fechaInicio, $fechaFin);
    }

    public function resumenSemanal($semanas = 6) {
        return $this->ventaRepository->resumenSemanal($semanas);
    }
}
