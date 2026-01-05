<?php 
$content = ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="fas fa-chart-line me-2"></i>Reporte de Ventas</h2>
  <a href="<?= BASE_URL ?>ventas" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Ventas</a>
</div>
<?php if (!empty($success)): ?><div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check me-2"></i><?= htmlspecialchars($success) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="card mb-4">
  <div class="card-body">
    <form class="row g-3" method="GET">
      <div class="col-md-3">
        <label class="form-label">Desde</label>
        <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>" class="form-control" />
      </div>
      <div class="col-md-3">
        <label class="form-label">Hasta</label>
        <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>" class="form-control" />
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>Filtrar</button>
      </div>
    </form>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Resumen Diario</h5>
        <span class="badge bg-primary">Últimos registros</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>Fecha</th><th class="text-end">Ventas</th><th class="text-end">Subtotal</th><th class="text-end">Desc.</th><th class="text-end">Total</th></tr></thead>
            <tbody>
              <?php if (empty($resumenDiario)): ?>
                <tr><td colspan="5" class="text-center py-4 text-muted">Sin datos</td></tr>
              <?php else: ?>
                <?php foreach ($resumenDiario as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['fecha']) ?></td>
                    <td class="text-end"><?= (int)$r['cantidad'] ?></td>
                    <td class="text-end">S/ <?= number_format($r['subtotal'] ?? 0,2) ?></td>
                    <td class="text-end text-danger">S/ <?= number_format($r['descuentos'] ?? 0,2) ?></td>
                    <td class="text-end fw-bold">S/ <?= number_format($r['monto'] ?? 0,2) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Resumen Semanal</h5>
        <span class="badge bg-secondary">Últimas semanas</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>Semana</th><th class="text-end">Ventas</th><th class="text-end">Total</th></tr></thead>
            <tbody>
              <?php if (empty($resumenSemanal)): ?>
                <tr><td colspan="3" class="text-center py-4 text-muted">Sin datos</td></tr>
              <?php else: ?>
                <?php foreach ($resumenSemanal as $r): ?>
                  <tr>
                    <td><small><?= htmlspecialchars($r['desde']) ?> → <?= htmlspecialchars($r['hasta']) ?></small></td>
                    <td class="text-end"><?= (int)$r['cantidad'] ?></td>
                    <td class="text-end fw-bold">S/ <?= number_format($r['monto'] ?? 0,2) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>