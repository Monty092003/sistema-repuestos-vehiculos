<?php 
$content = ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="fas fa-receipt me-2"></i>Ventas</h2>
  <a href="<?= BASE_URL ?>ventas/crear" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nueva Venta</a>
</div>
<?php if (!empty($success)): ?><div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check me-2"></i><?= htmlspecialchars($success) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="card mb-3">
  <div class="card-body">
    <form method="GET" class="row g-3">
      <div class="col-md-2"><input type="text" name="numero" value="<?= htmlspecialchars($filters['numero'] ?? '') ?>" placeholder="N° Venta" class="form-control" /></div>
      <div class="col-md-3"><input type="text" name="cliente" value="<?= htmlspecialchars($filters['cliente'] ?? '') ?>" placeholder="Cliente" class="form-control" /></div>
      <div class="col-md-2">
        <select name="estado" class="form-select">
          <option value="">Estado</option>
          <?php foreach (['PENDIENTE','COMPLETADA','ANULADA'] as $est): ?>
          <option value="<?= $est ?>" <?= (strtoupper($filters['estado'] ?? '') === $est)?'selected':'' ?>><?= ucfirst(strtolower($est)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2"><input type="date" name="fecha_inicio" value="<?= htmlspecialchars($filters['fecha_inicio'] ?? '') ?>" class="form-control" /></div>
      <div class="col-md-2"><input type="date" name="fecha_fin" value="<?= htmlspecialchars($filters['fecha_fin'] ?? '') ?>" class="form-control" /></div>
      <div class="col-md-1 d-grid"><button class="btn btn-outline-primary"><i class="fas fa-search"></i></button></div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Listado de Ventas</h5>
    <span class="badge bg-primary"><?= $total ?> ventas</span>
  </div>
  <div class="card-body p-0">
    <?php if (empty($ventas)): ?>
      <div class="text-center py-5">
        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No hay ventas registradas</h5>
        <a href="<?= BASE_URL ?>ventas/crear" class="btn btn-primary mt-2"><i class="fas fa-plus me-2"></i>Registrar primera venta</a>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Fecha</th><th>N° Venta</th><th>Cliente</th><th>Estado</th><th>Subtotal</th><th>Total</th><th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ventas as $v): ?>
              <tr>
                <td><small><?= date('d/m/Y H:i', strtotime($v['created_at'])) ?></small></td>
                <td><strong><?= htmlspecialchars($v['numero_venta']) ?></strong></td>
                <td><?= htmlspecialchars($v['cliente_nombre'] ?: '-') ?></td>
                <?php $estado = strtoupper($v['estado']); $badge = 'secondary'; if($estado==='COMPLETADA') $badge='success'; elseif($estado==='PENDIENTE') $badge='warning'; elseif($estado==='ANULADA') $badge='dark'; ?>
                <td><span class="badge bg-<?= $badge ?>"><?= ucfirst(strtolower($estado)) ?></span></td>
                <td>S/ <?= number_format($v['subtotal'],2) ?></td>
                <td class="fw-bold">S/ <?= number_format($v['total'],2) ?></td>
                <td><a href="<?= BASE_URL ?>ventas/<?= $v['id'] ?>" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php if (($pages ?? 0) > 1): ?>
<nav class="mt-4" aria-label="Paginación ventas">
  <ul class="pagination justify-content-center">
    <?php for ($i=1;$i<=$pages;$i++): ?>
      <li class="page-item <?= $i==$current_page?'active':'' ?>"><a class="page-link" href="<?= BASE_URL ?>ventas?page=<?= $i ?>"><?= $i ?></a></li>
    <?php endfor; ?>
  </ul>
</nav>
<?php endif; ?>

<?php
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>