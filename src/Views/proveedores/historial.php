<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-history me-2"></i>Historial de Compras</h2>
    <a href="<?= BASE_URL ?>proveedores/<?= $proveedor->getId() ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver al Proveedor
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

<!-- Información del proveedor -->
<div class="card mb-4">
    <div class="card-body">
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
</div>

<!-- Tabla de movimientos -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Movimientos de Compra</h5>
        <span class="badge bg-primary"><?= $total ?? 0 ?> movimientos</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($movimientos)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No hay compras registradas</h5>
            <p class="text-muted">Este proveedor no tiene movimientos de compra registrados</p>
            <a href="<?= BASE_URL ?>inventario/entradas" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Registrar Primera Compra
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Repuesto</th>
                        <th>Cantidad</th>
                        <th>Motivo</th>
                        <th>Usuario</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $movimiento): ?>
                    <tr>
                        <td>
                            <div>
                                <strong><?= date('d/m/Y', strtotime($movimiento['fecha_movimiento'])) ?></strong>
                                <br><small class="text-muted"><?= date('H:i:s', strtotime($movimiento['fecha_movimiento'])) ?></small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong><?= htmlspecialchars($movimiento['repuesto_nombre']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($movimiento['repuesto_codigo']) ?></small>
                            </div>
                        </td>
                        <td>
                            <span class="text-success fw-bold fs-5">+<?= $movimiento['cantidad'] ?></span>
                        </td>
                        <td>
                            <div>
                                <strong><?= htmlspecialchars($movimiento['motivo']) ?></strong>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong><?= htmlspecialchars($movimiento['usuario_nombre']) ?></strong>
                            </div>
                        </td>
                        <td>
                            <?php if ($movimiento['observaciones']): ?>
                                <small class="text-muted"><?= htmlspecialchars(substr($movimiento['observaciones'], 0, 50)) ?>...</small>
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
<nav aria-label="Paginación de historial" class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
            <a class="page-link" href="<?= BASE_URL ?>proveedores/<?= $proveedor->getId() ?>/historial?page=<?= $i ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<!-- Resumen de compras -->
<?php if (!empty($movimientos)): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="alert alert-info">
            <h6><i class="fas fa-chart-bar me-2"></i>Resumen de Compras:</h6>
            <div class="row">
                <div class="col-md-3">
                    <strong>Total de movimientos:</strong> <?= $total ?? 0 ?>
                </div>
                <div class="col-md-3">
                    <strong>Total de unidades:</strong> 
                    <?php 
                    $totalUnidades = array_sum(array_column($movimientos, 'cantidad'));
                    echo $totalUnidades;
                    ?>
                </div>
                <div class="col-md-3">
                    <strong>Última compra:</strong> 
                    <?= date('d/m/Y', strtotime($movimientos[0]['fecha_movimiento'])) ?>
                </div>
                <div class="col-md-3">
                    <strong>Primera compra:</strong> 
                    <?= date('d/m/Y', strtotime(end($movimientos)['fecha_movimiento'])) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
