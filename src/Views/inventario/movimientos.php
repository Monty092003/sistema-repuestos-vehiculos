<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-history me-2"></i>Historial de Movimientos de Inventario</h2>
    <a href="<?= BASE_URL ?>inventario" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver a Inventario
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>inventario/movimientos" class="row g-3">
            <div class="col-md-3">
                <select class="form-select" name="tipo">
                    <option value="">Todos los tipos</option>
                    <?php foreach ($tipos as $value => $label): ?>
                    <option value="<?= $value ?>" <?= (($filtro_tipo ?? '') == $value) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" name="fecha_inicio" 
                       value="<?= $filtro_fecha_inicio ?? '' ?>" placeholder="Fecha inicio">
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" name="fecha_fin" 
                       value="<?= $filtro_fecha_fin ?? '' ?>" placeholder="Fecha fin">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="fas fa-search me-1"></i>Filtrar
                </button>
                <a href="<?= BASE_URL ?>inventario/movimientos" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de movimientos -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Movimientos</h5>
        <span class="badge bg-primary"><?= $total ?? 0 ?> movimientos</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($movimientos)): ?>
        <div class="text-center py-5">
            <i class="fas fa-history fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No hay movimientos registrados</h5>
            <p class="text-muted">Los movimientos de inventario aparecerán aquí</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Repuesto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Motivo</th>
                        <th>Proveedor</th>
                        <th>Usuario</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $movimiento): ?>
                    <tr>
                        <td>
                            <div>
                                <strong><?= date('d/m/Y', strtotime($movimiento->getFechaMovimiento())) ?></strong>
                                <br><small class="text-muted"><?= date('H:i:s', strtotime($movimiento->getFechaMovimiento())) ?></small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong><?= htmlspecialchars($movimiento->getRepuesto()) ?></strong>
                            </div>
                        </td>
                        <td>
                            <span class="badge <?= $movimiento->getTipoClase() ?>">
                                <i class="<?= $movimiento->getTipoIcono() ?> me-1"></i>
                                <?= $movimiento->getTipoNombre() ?>
                            </span>
                        </td>
                        <td>
                            <span class="<?= $movimiento->getCantidadClase() ?> fw-bold fs-5">
                                <?= $movimiento->getCantidadConSigno() ?>
                            </span>
                        </td>
                        <td>
                            <div>
                                <strong><?= htmlspecialchars($movimiento->getMotivo()) ?></strong>
                            </div>
                        </td>
                        <td>
                            <?php if ($movimiento->getProveedor()): ?>
                                <span class="badge bg-info"><?= htmlspecialchars($movimiento->getProveedor()) ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div>
                                <strong><?= htmlspecialchars($movimiento->getUsuario()) ?></strong>
                            </div>
                        </td>
                        <td>
                            <?php if ($movimiento->getObservaciones()): ?>
                                <small class="text-muted"><?= htmlspecialchars(substr($movimiento->getObservaciones(), 0, 50)) ?>...</small>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
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
<nav aria-label="Paginación de movimientos" class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
            <a class="page-link" href="<?= BASE_URL ?>inventario/movimientos?page=<?= $i ?><?= !empty($filtro_tipo) ? '&tipo=' . urlencode($filtro_tipo) : '' ?><?= !empty($filtro_fecha_inicio) ? '&fecha_inicio=' . $filtro_fecha_inicio : '' ?><?= !empty($filtro_fecha_fin) ? '&fecha_fin=' . $filtro_fecha_fin : '' ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<!-- Resumen de Filtros Aplicados -->
<?php if (!empty($filtro_tipo) || !empty($filtro_fecha_inicio) || !empty($filtro_fecha_fin)): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="alert alert-info">
            <h6><i class="fas fa-filter me-2"></i>Filtros Aplicados:</h6>
            <ul class="mb-0">
                <?php if (!empty($filtro_tipo)): ?>
                <li><strong>Tipo:</strong> <?= $tipos[$filtro_tipo] ?? $filtro_tipo ?></li>
                <?php endif; ?>
                <?php if (!empty($filtro_fecha_inicio)): ?>
                <li><strong>Fecha inicio:</strong> <?= date('d/m/Y', strtotime($filtro_fecha_inicio)) ?></li>
                <?php endif; ?>
                <?php if (!empty($filtro_fecha_fin)): ?>
                <li><strong>Fecha fin:</strong> <?= date('d/m/Y', strtotime($filtro_fecha_fin)) ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<?php endif; ?>

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
