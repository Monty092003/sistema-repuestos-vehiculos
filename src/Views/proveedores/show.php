<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-truck me-2"></i>Detalles del Proveedor</h2>
    <div>
        <a href="<?= BASE_URL ?>proveedores/<?= $proveedor->getId() ?>/editar" class="btn btn-warning me-2">
            <i class="fas fa-edit me-2"></i>Editar
        </a>
        <a href="<?= BASE_URL ?>proveedores" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

<?php if (!empty($success)): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                        <?= $proveedor->getIniciales() ?>
                    </div>
                    <div>
                        <h5 class="mb-0"><?= htmlspecialchars($proveedor->getNombre()) ?></h5>
                        <p class="mb-0 text-muted"><?= htmlspecialchars($proveedor->getInfoContacto()) ?></p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-building me-2"></i>Nombre del Proveedor
                            </label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($proveedor->getNombre()) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user me-2"></i>Persona de Contacto
                            </label>
                            <p class="form-control-plaintext">
                                <?= $proveedor->getContacto() ? htmlspecialchars($proveedor->getContacto()) : '-' ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-phone me-2"></i>Teléfono
                            </label>
                            <p class="form-control-plaintext">
                                <?php if ($proveedor->getTelefono()): ?>
                                    <a href="tel:<?= htmlspecialchars($proveedor->getTelefono()) ?>" class="text-decoration-none">
                                        <i class="fas fa-phone me-1"></i><?= htmlspecialchars($proveedor->getTelefono()) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No especificado</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <p class="form-control-plaintext">
                                <?php if ($proveedor->getEmail()): ?>
                                    <a href="mailto:<?= htmlspecialchars($proveedor->getEmail()) ?>" class="text-decoration-none">
                                        <i class="fas fa-envelope me-1"></i><?= htmlspecialchars($proveedor->getEmail()) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No especificado</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-map-marker-alt me-2"></i>Dirección
                    </label>
                    <p class="form-control-plaintext">
                        <?= $proveedor->getDireccion() ? htmlspecialchars($proveedor->getDireccion()) : 'No especificada' ?>
                    </p>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-toggle-on me-2"></i>Estado
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge <?= $proveedor->isActivo() ? 'bg-success' : 'bg-secondary' ?> fs-6">
                                    <?= $proveedor->isActivo() ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-plus me-2"></i>Fecha de Registro
                            </label>
                            <p class="form-control-plaintext">
                                <?= date('d/m/Y H:i', strtotime($proveedor->getCreatedAt())) ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-edit me-2"></i>Última Actualización
                            </label>
                            <p class="form-control-plaintext">
                                <?= date('d/m/Y H:i', strtotime($proveedor->getUpdatedAt())) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Información de contacto -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-address-book me-2"></i>Información de Contacto</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-phone text-primary me-2"></i>
                    <span><?= $proveedor->getTelefono() ?: 'No especificado' ?></span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-envelope text-primary me-2"></i>
                    <span><?= $proveedor->getEmail() ?: 'No especificado' ?></span>
                </div>
                <div class="d-flex align-items-start">
                    <i class="fas fa-map-marker-alt text-primary me-2 mt-1"></i>
                    <span><?= $proveedor->getDireccion() ?: 'No especificada' ?></span>
                </div>
            </div>
        </div>
        
        <!-- Estado de información -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Estado de Información</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Información completa:</span>
                    <span class="badge <?= $proveedor->hasContactoCompleto() ? 'bg-success' : 'bg-warning' ?>">
                        <?= $proveedor->hasContactoCompleto() ? 'Completa' : 'Incompleta' ?>
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Estado:</span>
                    <span class="badge <?= $proveedor->isActivo() ? 'bg-success' : 'bg-secondary' ?>">
                        <?= $proveedor->isActivo() ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Acciones -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Acciones</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>proveedores/<?= $proveedor->getId() ?>/editar" 
                       class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Editar Proveedor
                    </a>
                    
                    <a href="<?= BASE_URL ?>proveedores/<?= $proveedor->getId() ?>/historial" 
                       class="btn btn-outline-info">
                        <i class="fas fa-history me-2"></i>Ver Historial de Compras
                    </a>
                    
                    <a href="<?= BASE_URL ?>inventario/entradas?proveedor_id=<?= $proveedor->getId() ?>" 
                       class="btn btn-outline-success">
                        <i class="fas fa-plus me-2"></i>Registrar Compra
                    </a>
                    
            <button type="button" class="btn btn-outline-danger" 
                onclick="confirmDelete(<?= $proveedor->getId() ?>, '<?= htmlspecialchars($proveedor->getNombre()) ?>')">
                        <i class="fas fa-trash me-2"></i>Eliminar Proveedor
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Historial de compras reciente -->
<?php if (!empty($historial['movimientos'])): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial de Compras Reciente</h5>
                <a href="<?= BASE_URL ?>proveedores/<?= $proveedor->getId() ?>/historial" class="btn btn-sm btn-outline-primary">
                    Ver Todo
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Repuesto</th>
                                <th>Cantidad</th>
                                <th>Motivo</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial['movimientos'] as $movimiento): ?>
                            <tr>
                                <td>
                                    <small><?= date('d/m/Y H:i', strtotime($movimiento['fecha_movimiento'])) ?></small>
                                </td>
                                <td><?= htmlspecialchars($movimiento['repuesto_nombre']) ?></td>
                                <td>
                                    <span class="text-success fw-bold">+<?= $movimiento['cantidad'] ?></span>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($movimiento['motivo']) ?></small>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($movimiento['usuario_nombre']) ?></small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar al proveedor <strong id="proveedorName"></strong>?</p>
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
function confirmDelete(proveedorId, proveedorName) {
    document.getElementById('proveedorName').textContent = proveedorName;
    document.getElementById('deleteForm').action = '<?= BASE_URL ?>proveedores/' + proveedorId + '/eliminar';
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<script>
function confirmDelete(proveedorId, proveedorName) {
    document.getElementById('proveedorName').textContent = proveedorName;
    document.getElementById('deleteForm').action = '<?= BASE_URL ?>proveedores/' + proveedorId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
