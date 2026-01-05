<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-boxes me-2"></i>Gestión de Inventario</h2>
    <div>
        <a href="<?= BASE_URL ?>inventario/entradas" class="btn btn-success me-2">
            <i class="fas fa-plus me-2"></i>Entrada de Stock
        </a>
        <a href="<?= BASE_URL ?>inventario/salidas" class="btn btn-danger me-2">
            <i class="fas fa-minus me-2"></i>Salida de Stock
        </a>
        <a href="<?= BASE_URL ?>inventario/ajustes" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Ajuste de Stock
        </a>
    </div>
</div>

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['total_movimientos'] ?? 0 ?></h4>
                        <p class="mb-0">Total Movimientos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x"></i>
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
                        <h4><?= $stats['total_entradas'] ?? 0 ?></h4>
                        <p class="mb-0">Entradas</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-arrow-up fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['total_salidas'] ?? 0 ?></h4>
                        <p class="mb-0">Salidas</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-arrow-down fa-2x"></i>
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
                        <h4><?= $stats['total_ajustes'] ?? 0 ?></h4>
                        <p class="mb-0">Ajustes</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-edit fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>inventario" class="row g-3">
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
                <a href="<?= BASE_URL ?>inventario" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <!-- Movimientos Recientes -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Movimientos Recientes</h5>
                <a href="<?= BASE_URL ?>inventario/movimientos" class="btn btn-sm btn-outline-primary">
                    Ver Todos
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($movimientos)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay movimientos registrados</h5>
                    <p class="text-muted">Comience registrando entradas o salidas de stock</p>
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
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movimientos as $movimiento): ?>
                            <tr>
                                <td>
                                    <small><?= date('d/m/Y H:i', strtotime($movimiento->getFechaMovimiento())) ?></small>
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
                                    <span class="<?= $movimiento->getCantidadClase() ?> fw-bold">
                                        <?= $movimiento->getCantidadConSigno() ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($movimiento->getMotivo()) ?></small>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($movimiento->getUsuario()) ?></small>
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
    
    <!-- Acciones Rápidas -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>inventario/entradas" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Registrar Entrada
                    </a>
                    <a href="<?= BASE_URL ?>inventario/salidas" class="btn btn-danger">
                        <i class="fas fa-minus me-2"></i>Registrar Salida
                    </a>
                    <a href="<?= BASE_URL ?>inventario/ajustes" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Ajustar Stock
                    </a>
                    <a href="<?= BASE_URL ?>inventario/movimientos" class="btn btn-outline-primary">
                        <i class="fas fa-history me-2"></i>Ver Historial
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Resumen de Cantidades -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Resumen de Cantidades</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h5 class="text-success"><?= $stats['total_cantidad_entrada'] ?? 0 ?></h5>
                        <small class="text-muted">Entradas</small>
                    </div>
                    <div class="col-6">
                        <h5 class="text-danger"><?= $stats['total_cantidad_salida'] ?? 0 ?></h5>
                        <small class="text-muted">Salidas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Paginación -->
<?php if (isset($pages) && $pages > 1): ?>
<nav aria-label="Paginación de movimientos" class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
            <a class="page-link" href="<?= BASE_URL ?>inventario?page=<?= $i ?><?= !empty($filtro_tipo) ? '&tipo=' . urlencode($filtro_tipo) : '' ?><?= !empty($filtro_fecha_inicio) ? '&fecha_inicio=' . $filtro_fecha_inicio : '' ?><?= !empty($filtro_fecha_fin) ? '&fecha_fin=' . $filtro_fecha_fin : '' ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
