<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-cogs me-2"></i>Gestión de Repuestos</h2>
    <div>
        <a href="<?= BASE_URL ?>repuestos/stock-bajo" class="btn btn-warning me-2">
            <i class="fas fa-exclamation-triangle me-2"></i>Stock Bajo
        </a>
        <a href="<?= BASE_URL ?>repuestos/crear" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Repuesto
        </a>
    </div>
</div>

<!-- Filtros de búsqueda -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>repuestos" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="search" 
                           value="<?= htmlspecialchars($search_term ?? '') ?>" 
                           placeholder="Buscar por nombre, código o descripción...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="categoria_id">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= $categoria->getId() ?>" 
                            <?= (($categoria_id ?? '') == $categoria->getId()) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categoria->getNombre()) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="fas fa-search me-1"></i>Buscar
                </button>
                <a href="<?= BASE_URL ?>repuestos" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de repuestos -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Repuestos</h5>
        <span class="badge bg-primary"><?= $total ?? 0 ?> repuestos</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($repuestos)): ?>
        <div class="text-center py-5">
            <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No hay repuestos registrados</h5>
            <p class="text-muted">Comience creando el primer repuesto del sistema</p>
            <a href="<?= BASE_URL ?>repuestos/crear" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Crear Repuesto
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio Compra</th>
                        <th>Precio Venta</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($repuestos as $repuesto): ?>
                    <tr>
                        <td>
                            <code class="text-primary"><?= htmlspecialchars($repuesto->getCodigo()) ?></code>
                        </td>
                        <td>
                            <div>
                                <strong><?= htmlspecialchars($repuesto->getNombre()) ?></strong>
                                <?php if ($repuesto->getDescripcion()): ?>
                                <br><small class="text-muted"><?= htmlspecialchars(substr($repuesto->getDescripcion(), 0, 50)) ?>...</small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info"><?= htmlspecialchars($repuesto->getCategoria()) ?></span>
                        </td>
                        <td>
                            <span class="text-success">$<?= number_format($repuesto->getPrecioCompra(), 2) ?></span>
                        </td>
                        <td>
                            <span class="text-primary">$<?= number_format($repuesto->getPrecioVenta(), 2) ?></span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2"><?= $repuesto->getStockActual() ?></span>
                                <span class="badge <?= $repuesto->getEstadoStockClase() ?>">
                                    <?= $repuesto->getEstadoStockNombre() ?>
                                </span>
                            </div>
                            <small class="text-muted">
                                Min: <?= $repuesto->getStockMinimo() ?> | 
                                Max: <?= $repuesto->getStockMaximo() ?>
                            </small>
                        </td>
                        <td>
                            <span class="badge <?= $repuesto->isActivo() ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $repuesto->isActivo() ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="<?= BASE_URL ?>repuestos/<?= $repuesto->getId() ?>" 
                                   class="btn btn-sm btn-outline-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>repuestos/<?= $repuesto->getId() ?>/editar" 
                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="confirmDelete(<?= $repuesto->getId() ?>, '<?= htmlspecialchars($repuesto->getNombre()) ?>')" 
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

<!-- Paginación -->
<?php if (isset($pages) && $pages > 1): ?>
<nav aria-label="Paginación de repuestos" class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
            <a class="page-link" href="<?= BASE_URL ?>repuestos?page=<?= $i ?><?= !empty($search_term) ? '&search=' . urlencode($search_term) : '' ?><?= !empty($categoria_id) ? '&categoria_id=' . $categoria_id : '' ?>">
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
                <p>¿Está seguro de que desea eliminar el repuesto <strong id="repuestoName"></strong>?</p>
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
function confirmDelete(repuestoId, repuestoName) {
    document.getElementById('repuestoName').textContent = repuestoName;
    document.getElementById('deleteForm').action = '<?= BASE_URL ?>repuestos/' + repuestoId + '/eliminar';
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
