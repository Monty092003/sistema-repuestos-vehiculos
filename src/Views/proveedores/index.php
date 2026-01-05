<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-truck me-2"></i>Gestión de Proveedores</h2>
    <a href="<?= BASE_URL ?>proveedores/crear" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nuevo Proveedor
    </a>
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

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['total'] ?? 0 ?></h4>
                        <p class="mb-0">Total Proveedores</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-truck fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['activos'] ?? 0 ?></h4>
                        <p class="mb-0">Activos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['con_email'] ?? 0 ?></h4>
                        <p class="mb-0">Con Email</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-envelope fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['con_telefono'] ?? 0 ?></h4>
                        <p class="mb-0">Con Teléfono</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-phone fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y búsqueda -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>proveedores" class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="search" 
                           value="<?= htmlspecialchars($search_term ?? '') ?>" 
                           placeholder="Buscar por nombre, contacto o email...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="view">
                    <option value="list" <?= (($view ?? '') == 'list') ? 'selected' : '' ?>>Vista Lista</option>
                    <option value="stats" <?= (($view ?? '') == 'stats') ? 'selected' : '' ?>>Vista Estadísticas</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="fas fa-search me-1"></i>Buscar
                </button>
                <a href="<?= BASE_URL ?>proveedores" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <!-- Lista de Proveedores -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Proveedores</h5>
                <span class="badge bg-primary"><?= $total ?? 0 ?> proveedores</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($proveedores)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay proveedores registrados</h5>
                    <p class="text-muted">Comience creando el primer proveedor del sistema</p>
                    <a href="<?= BASE_URL ?>proveedores/crear" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Crear Proveedor
                    </a>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Proveedor</th>
                                <th>Contacto</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <?php if ($view === 'stats'): ?>
                                <th>Compras</th>
                                <th>Última Compra</th>
                                <?php endif; ?>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($proveedores as $proveedor): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <?= $proveedor->getIniciales() ?>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($proveedor->getNombre()) ?></strong>
                                            <?php if ($proveedor->getDireccion()): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars(substr($proveedor->getDireccion(), 0, 30)) ?>...</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?= $proveedor->getContacto() ? htmlspecialchars($proveedor->getContacto()) : '-' ?>
                                </td>
                                <td>
                                    <?php if ($proveedor->getTelefono()): ?>
                                        <a href="tel:<?= htmlspecialchars($proveedor->getTelefono()) ?>" class="text-decoration-none">
                                            <i class="fas fa-phone me-1"></i><?= htmlspecialchars($proveedor->getTelefono()) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($proveedor->getEmail()): ?>
                                        <a href="mailto:<?= htmlspecialchars($proveedor->getEmail()) ?>" class="text-decoration-none">
                                            <i class="fas fa-envelope me-1"></i><?= htmlspecialchars($proveedor->getEmail()) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($view === 'stats'): ?>
                                <td>
                                    <span class="badge bg-info"><?= $proveedor->totalMovimientos ?? 0 ?> movimientos</span>
                                    <br><small class="text-muted"><?= $proveedor->totalEntradas ?? 0 ?> unidades</small>
                                </td>
                                <td>
                                    <?php if ($proveedor->ultimaCompra): ?>
                                        <small><?= date('d/m/Y', strtotime($proveedor->ultimaCompra)) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Nunca</span>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                                <td>
                                    <span class="badge <?= $proveedor->isActivo() ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $proveedor->isActivo() ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= BASE_URL ?>proveedores/<?= $proveedor->getId() ?>" 
                                           class="btn btn-sm btn-outline-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>proveedores/<?= $proveedor->getId() ?>/editar" 
                                           class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>proveedores/<?= $proveedor->getId() ?>/historial" 
                                           class="btn btn-sm btn-outline-primary" title="Historial">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?= $proveedor->getId() ?>, '<?= htmlspecialchars($proveedor->getNombre()) ?>')" 
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Panel lateral -->
    <div class="col-md-4">
        <!-- Proveedores más utilizados -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-star me-2"></i>Proveedores Más Utilizados</h6>
            </div>
            <div class="card-body">
                <?php if (empty($mas_utilizados)): ?>
                <p class="text-muted mb-0">No hay datos disponibles</p>
                <?php else: ?>
                <?php foreach ($mas_utilizados as $proveedor): ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong><?= htmlspecialchars($proveedor->getNombre()) ?></strong>
                    </div>
                    <span class="badge bg-primary"><?= $proveedor->totalMovimientos ?? 0 ?></span>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Acciones rápidas -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>proveedores/crear" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nuevo Proveedor
                    </a>
                    <a href="<?= BASE_URL ?>inventario/entradas" class="btn btn-outline-success">
                        <i class="fas fa-arrow-up me-2"></i>Registrar Compra
                    </a>
                    <a href="<?= BASE_URL ?>proveedores?view=stats" class="btn btn-outline-info">
                        <i class="fas fa-chart-bar me-2"></i>Ver Estadísticas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Paginación -->
<?php if (isset($pages) && $pages > 1): ?>
<nav aria-label="Paginación de proveedores" class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
            <a class="page-link" href="<?= BASE_URL ?>proveedores?page=<?= $i ?><?= !empty($search_term) ? '&search=' . urlencode($search_term) : '' ?><?= !empty($view) ? '&view=' . $view : '' ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
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
                    <?= \App\Core\Csrf::field(); ?>
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

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
