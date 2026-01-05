<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-chart-bar me-2"></i>Estadísticas de Proveedores</h2>
    <a href="<?= BASE_URL ?>proveedores" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver a Proveedores
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

<!-- Estadísticas Generales -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?= number_format($stats['general']['total_proveedores'] ?? 0) ?></h3>
                        <p class="mb-0">Total Proveedores</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-truck fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?= number_format($stats['general']['activos'] ?? 0) ?></h3>
                        <p class="mb-0">Proveedores Activos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?= number_format($stats['general']['inactivos'] ?? 0) ?></h3>
                        <p class="mb-0">Proveedores Inactivos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top 5 Proveedores -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-trophy me-2"></i>Top 5 Proveedores por Compras
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($stats['top_proveedores'])): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Posición</th>
                                    <th>Proveedor</th>
                                    <th>Total Compras</th>
                                    <th>Movimientos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['top_proveedores'] as $index => $proveedor): ?>
                                <tr>
                                    <td>
                                        <?php if ($index === 0): ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-trophy"></i> #<?= $index + 1 ?>
                                            </span>
                                        <?php elseif ($index === 1): ?>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-medal"></i> #<?= $index + 1 ?>
                                            </span>
                                        <?php elseif ($index === 2): ?>
                                            <span class="badge bg-dark">
                                                <i class="fas fa-medal"></i> #<?= $index + 1 ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark">#<?= $index + 1 ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($proveedor['nombre']) ?></td>
                                    <td>
                                        <span class="badge bg-success">
                                            <?= number_format($proveedor['total_compras'] ?? 0) ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($proveedor['total_movimientos'] ?? 0) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-bar fa-3x mb-3"></i>
                        <p>No hay datos de compras disponibles</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Actividad por Mes -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Actividad por Mes (Últimos 6 meses)
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($stats['por_mes'])): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th>Proveedores Activos</th>
                                    <th>Movimientos</th>
                                    <th>Cantidad Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['por_mes'] as $mes): ?>
                                <tr>
                                    <td>
                                        <?php 
                                        $fecha = DateTime::createFromFormat('Y-m', $mes['mes']);
                                        echo $fecha ? $fecha->format('M Y') : $mes['mes'];
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= number_format($mes['proveedores_activos']) ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($mes['total_movimientos']) ?></td>
                                    <td><?= number_format($mes['total_cantidad']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        <p>No hay actividad registrada en los últimos 6 meses</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Información Adicional -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Información de las Estadísticas
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Métricas Incluidas:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Total de proveedores registrados</li>
                            <li><i class="fas fa-check text-success me-2"></i>Proveedores activos e inactivos</li>
                            <li><i class="fas fa-check text-success me-2"></i>Ranking de proveedores por volumen de compras</li>
                            <li><i class="fas fa-check text-success me-2"></i>Actividad mensual de los últimos 6 meses</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Notas:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-info-circle text-primary me-2"></i>Los datos se actualizan en tiempo real</li>
                            <li><i class="fas fa-info-circle text-primary me-2"></i>Solo se incluyen movimientos de tipo "entrada" (compras)</li>
                            <li><i class="fas fa-info-circle text-primary me-2"></i>El ranking se basa en la cantidad total de productos comprados</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>