<?php 
$content = ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><?= htmlspecialchars($title ?? 'Reporte') ?></h2>
  <a href="<?= BASE_URL ?>reportes/ventas" class="btn btn-outline-secondary"><i class="fas fa-chart-line me-1"></i>Ventas</a>
</div>
<div class="text-center py-5">
  <i class="fas fa-tools fa-3x text-muted mb-3"></i>
  <h4 class="text-muted mb-2">En construcción</h4>
  <p class="text-muted"><?= htmlspecialchars($mensaje ?? 'Sección pendiente de desarrollo') ?></p>
</div>
<?php
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>