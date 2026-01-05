<?php 
/** @var \App\Models\Venta $venta */
$content = ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h2 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Venta <?= htmlspecialchars($venta->getNumero()) ?></h2>
    <small class="text-muted">Registrada el <?= htmlspecialchars($venta->getFecha()) ?></small>
  </div>
  <div class="d-flex gap-2">
    <a href="<?= BASE_URL ?>ventas" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a>
    <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print me-1"></i>Imprimir</button>
    <?php if (in_array($venta->getEstado(), ['COMPLETADA','PENDIENTE'])): ?>
      <form method="POST" action="<?= BASE_URL ?>ventas/<?= $venta->getId() ?>/anular" onsubmit="return confirm('¿Confirmar anulación?');" class="d-inline">
        <?= \App\Core\Csrf::field(); ?>
        <button class="btn btn-outline-danger"><i class="fas fa-ban me-1"></i>Anular</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php if (!empty($success)): ?><div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check me-2"></i><?= htmlspecialchars($success) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header"><h6 class="mb-0">Datos Cliente</h6></div>
      <div class="card-body">
        <p class="mb-1"><strong>Nombre:</strong><br><?= htmlspecialchars($venta->getClienteNombre() ?: '—') ?></p>
        <p class="mb-1"><strong>Documento:</strong><br><?= htmlspecialchars($venta->getClienteDocumento() ?: '—') ?></p>
        <p class="mb-0"><strong>Teléfono:</strong><br><?= htmlspecialchars($venta->getClienteTelefono() ?: '—') ?></p>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header"><h6 class="mb-0">Resumen</h6></div>
      <div class="card-body">
        <p class="mb-1"><strong>Subtotal:</strong> S/ <?= number_format($venta->getSubtotal(),2) ?></p>
        <p class="mb-1"><strong>Descuento:</strong> S/ <?= number_format($venta->getDescuento(),2) ?></p>
        <hr />
        <p class="fs-5 mb-0"><strong>Total:</strong> S/ <?= number_format($venta->getTotal(),2) ?></p>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header"><h6 class="mb-0">Estado</h6></div>
      <div class="card-body">
  <?php $estado = strtoupper($venta->getEstado()); $badge = 'secondary'; if($estado==='COMPLETADA') $badge='success'; elseif($estado==='PENDIENTE') $badge='warning'; elseif($estado==='ANULADA') $badge='dark'; ?>
  <p class="mb-3"><span class="badge bg-<?= $badge ?>"><?= ucfirst(strtolower($estado)) ?></span></p>
        <p class="small text-muted mb-0">Este estado puede gestionarse en funcionalidades futuras (cobranza / anulación).</p>
      </div>
    </div>
  </div>
</div>

<div class="card mt-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Detalle de Ítems</h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light"><tr><th>#</th><th>Código</th><th>Descripción</th><th class="text-end">Cant.</th><th class="text-end">P.Unit</th><th class="text-end">Subtotal</th></tr></thead>
        <tbody>
        <?php foreach($venta->getDetalles() as $i => $det): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($det->getRepuestoCodigo() ?: '') ?></td>
            <td><?= htmlspecialchars($det->getRepuestoNombre() ?: '') ?></td>
            <td class="text-end"><?= number_format($det->getCantidad(),2) ?></td>
            <td class="text-end">S/ <?= number_format($det->getPrecioUnitario(),2) ?></td>
            <td class="text-end">S/ <?= number_format($det->getSubtotal(),2) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
@media print {
  body { background: #fff; }
  nav, header, footer, .btn, .alert, .breadcrumb { display:none !important; }
  .card { box-shadow:none; border: none; }
  .card-header { border:none; }
}
</style>
<?php
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>