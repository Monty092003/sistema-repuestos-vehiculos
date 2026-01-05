<?php 
$content = ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
    <div class="text-muted">
        <i class="fas fa-calendar me-1"></i>
        <span id="current-datetime"><?= date('d/m/Y H:i:s') ?></span>
    </div>
</div>

<!-- Métricas Principales -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Repuestos</h6>
                        <h4><?= number_format($stats['total_repuestos'] ?? 0) ?></h4>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-cogs fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Ventas del Mes</h6>
                        <h4><?= number_format($stats['ventas_mes'] ?? 0) ?></h4>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Stock Bajo</h6>
                        <h4><?= number_format($stats['stock_bajo'] ?? 0) ?></h4>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Usuarios Activos</h6>
                        <h4><?= number_format($stats['usuarios_activos'] ?? 0) ?></h4>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Repuestos con Stock Bajo -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Repuestos con Stock Bajo</h5>
                <a href="<?= BASE_URL ?>repuestos/stock-bajo" class="btn btn-sm btn-outline-warning">Ver Todos</a>
            </div>
            <div class="card-body">
                <?php if (!empty($repuestos_stock_bajo)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Stock</th>
                                    <th>Mínimo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach(array_slice($repuestos_stock_bajo, 0, 5) as $repuesto): ?>
                                <tr>
                                    <td><?= htmlspecialchars($repuesto['codigo']) ?></td>
                                    <td><?= htmlspecialchars($repuesto['nombre']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $repuesto['stock_actual'] <= STOCK_CRITICO_LIMIT ? 'danger' : 'warning' ?>">
                                            <?= $repuesto['stock_actual'] ?>
                                        </span>
                                    </td>
                                    <td><?= $repuesto['stock_minimo'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p>Todos los repuestos tienen stock adecuado</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Movimientos Recientes -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Movimientos Recientes</h5>
                <a href="<?= BASE_URL ?>inventario" class="btn btn-sm btn-outline-primary">Ver Todos</a>
            </div>
            <div class="card-body">
                <?php if (!empty($movimientos_recientes)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Repuesto</th>
                                    <th>Cantidad</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($movimientos_recientes as $mov): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-<?= $mov['tipo'] === 'entrada' ? 'success' : ($mov['tipo'] === 'salida' ? 'danger' : 'info') ?>">
                                            <?= ucfirst($mov['tipo']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($mov['repuesto_codigo'] ?? $mov['repuesto_nombre'] ?? 'N/A') ?></td>
                                    <td><?= $mov['cantidad'] ?></td>
                                    <td><?= date('d/m H:i', strtotime($mov['fecha_movimiento'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>No hay movimientos recientes</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Ventas Recientes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Ventas Recientes</h5>
                <a href="<?= BASE_URL ?>ventas" class="btn btn-sm btn-outline-success">Ver Todas</a>
            </div>
            <div class="card-body">
                <?php if (!empty($ventas_recientes)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($ventas_recientes as $venta): ?>
                                <tr>
                                    <td>
                                        <a href="<?= BASE_URL ?>ventas/<?= $venta['id'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($venta['numero_venta']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($venta['cliente_nombre'] ?: 'Cliente General') ?></td>
                                    <td>S/ <?= number_format($venta['total'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= strtolower($venta['estado']) === 'completada' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($venta['estado']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($venta['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>No hay ventas registradas</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function updateDateTime() {
    const now = new Date();
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const year = now.getFullYear();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    const formattedDateTime = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
    
    const datetimeElement = document.getElementById('current-datetime');
    if (datetimeElement) {
        datetimeElement.textContent = formattedDateTime;
    }
}

// Actualizar inmediatamente
updateDateTime();

// Actualizar cada segundo
setInterval(updateDateTime, 1000);
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>