<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-cog me-2"></i>Detalles del Repuesto</h2>
    <div>
        <a href="<?= BASE_URL ?>repuestos/<?= $repuesto->getId() ?>/editar" class="btn btn-warning me-2">
            <i class="fas fa-edit me-2"></i>Editar
        </a>
        <a href="<?= BASE_URL ?>repuestos" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                        <i class="fas fa-cog fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="mb-0"><?= htmlspecialchars($repuesto->getNombre()) ?></h5>
                        <p class="mb-0 text-muted"><?= htmlspecialchars($repuesto->getCodigo()) ?></p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-barcode me-2"></i>Código
                            </label>
                            <p class="form-control-plaintext">
                                <code class="text-primary fs-5"><?= htmlspecialchars($repuesto->getCodigo()) ?></code>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tags me-2"></i>Categoría
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info fs-6"><?= htmlspecialchars($repuesto->getCategoria()) ?></span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <?php if ($repuesto->getDescripcion()): ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-align-left me-2"></i>Descripción
                    </label>
                    <p class="form-control-plaintext"><?= htmlspecialchars($repuesto->getDescripcion()) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-dollar-sign me-2"></i>Precio de Compra
                            </label>
                            <p class="form-control-plaintext fs-5 text-success">
                                $<?= number_format($repuesto->getPrecioCompra(), 2) ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tag me-2"></i>Precio de Venta
                            </label>
                            <p class="form-control-plaintext fs-5 text-primary">
                                $<?= number_format($repuesto->getPrecioVenta(), 2) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-boxes me-2"></i>Stock Actual
                            </label>
                            <p class="form-control-plaintext fs-4">
                                <span class="badge <?= $repuesto->getEstadoStockClase() ?> fs-6">
                                    <?= $repuesto->getStockActual() ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-exclamation-triangle me-2"></i>Stock Mínimo
                            </label>
                            <p class="form-control-plaintext fs-5"><?= $repuesto->getStockMinimo() ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-arrow-up me-2"></i>Stock Máximo
                            </label>
                            <p class="form-control-plaintext fs-5"><?= $repuesto->getStockMaximo() ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-toggle-on me-2"></i>Estado
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge <?= $repuesto->isActivo() ? 'bg-success' : 'bg-secondary' ?> fs-6">
                                    <?= $repuesto->isActivo() ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-chart-line me-2"></i>Margen de Ganancia
                            </label>
                            <p class="form-control-plaintext fs-5">
                                <span class="text-success"><?= number_format($repuesto->getMargenGanancia(), 2) ?>%</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-plus me-2"></i>Fecha de Creación
                            </label>
                            <p class="form-control-plaintext">
                                <?= date('d/m/Y H:i:s', strtotime($repuesto->getCreatedAt())) ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-edit me-2"></i>Última Actualización
                            </label>
                            <p class="form-control-plaintext">
                                <?= date('d/m/Y H:i:s', strtotime($repuesto->getUpdatedAt())) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Estado del Stock -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Estado del Stock</h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <span class="badge <?= $repuesto->getEstadoStockClase() ?> fs-6">
                        <?= $repuesto->getEstadoStockNombre() ?>
                    </span>
                </div>
                
                <div class="progress mb-3" style="height: 20px;">
                    <?php 
                    $porcentaje = $repuesto->getStockMaximo() > 0 ? 
                        ($repuesto->getStockActual() / $repuesto->getStockMaximo()) * 100 : 0;
                    $claseProgreso = $repuesto->isStockCritico() ? 'bg-danger' : 
                                   ($repuesto->isStockBajo() ? 'bg-warning' : 'bg-success');
                    ?>
                    <div class="progress-bar <?= $claseProgreso ?>" 
                         style="width: <?= min($porcentaje, 100) ?>%">
                        <?= $repuesto->getStockActual() ?>
                    </div>
                </div>
                
                <small class="text-muted">
                    <?= $repuesto->getStockActual() ?> de <?= $repuesto->getStockMaximo() ?> unidades
                </small>
            </div>
        </div>
        
        <!-- Alertas -->
        <?php if ($repuesto->isStockCritico()): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Stock Crítico</strong><br>
            El stock está por debajo del límite crítico.
        </div>
        <?php elseif ($repuesto->isStockBajo()): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Stock Bajo</strong><br>
            El stock está por debajo del nivel mínimo.
        </div>
        <?php endif; ?>
        
        <!-- Acciones -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Acciones</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>repuestos/<?= $repuesto->getId() ?>/editar" 
                       class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Editar Repuesto
                    </a>
                    
                    <button type="button" class="btn btn-outline-danger" 
                            onclick="confirmDelete(<?= $repuesto->getId() ?>, '<?= htmlspecialchars($repuesto->getNombre()) ?>')">
                        <i class="fas fa-trash me-2"></i>Eliminar Repuesto
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar el repuesto <strong id="repuestoName"></strong>?</p>
                <p class="text-muted">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="" id="deleteForm" class="d-inline">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(repuestoId, repuestoName) {
    document.getElementById('repuestoName').textContent = repuestoName;
    document.getElementById('deleteForm').action = '<?= BASE_URL ?>repuestos/' + repuestoId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
