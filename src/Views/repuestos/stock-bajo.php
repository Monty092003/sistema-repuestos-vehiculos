<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-bell-exclamation me-2 text-danger"></i>Alertas de Stock</h2>
    <a href="<?= BASE_URL ?>repuestos" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver a Repuestos
    </a>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">
        <form class="row g-3" method="GET" action="<?= BASE_URL ?>repuestos/stock-bajo">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Categoría</label>
                <select name="categoria_id" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <?php if (!empty($categorias)): foreach($categorias as $cat): ?>
                        <option value="<?= $cat->getId() ?>" <?= (isset($filters['categoria_id']) && (int)$filters['categoria_id'] === (int)$cat->getId()) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat->getNombre()) ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="include_near" id="include_near" <?= !empty($filters['include_near']) ? 'checked' : '' ?> <?= !empty($filters['solo_criticos']) ? 'disabled' : '' ?>>
                    <label class="form-check-label small" for="include_near">Incluir CASI</label>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="solo_criticos" id="solo_criticos" <?= !empty($filters['solo_criticos']) ? 'checked' : '' ?>>
                    <label class="form-check-label small" for="solo_criticos">Solo CRÍTICOS</label>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-sm btn-primary me-2" type="submit"><i class="fas fa-filter me-1"></i>Filtrar</button>
                <a href="<?= BASE_URL ?>repuestos/stock-bajo" class="btn btn-sm btn-outline-secondary"><i class="fas fa-undo me-1"></i>Reset</a>
            </div>
        </form>
    </div>
    <div class="card-body py-2">
        <small class="text-muted">Severidades: <span class="badge bg-danger">CRITICO</span> <span class="badge bg-warning text-dark">BAJO</span> <span class="badge bg-info text-dark">CASI</span></small>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-danger h-100">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-danger fw-bold"><i class="fas fa-skull-crossbones me-1"></i>Críticos</span>
                    <span class="fs-5 text-danger"><?= $metrics['conteo']['critico'] ?? 0 ?></span>
                </div>
                <div class="progress mt-2" style="height:5px;">
                    <div class="progress-bar bg-danger" style="width: <?= $metrics['porcentajes']['critico'] ?? 0 ?>%"></div>
                </div>
                <small class="text-muted"><?= $metrics['porcentajes']['critico'] ?? 0 ?>%</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning h-100">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-warning fw-bold"><i class="fas fa-exclamation-triangle me-1"></i>Bajos</span>
                    <span class="fs-5 text-warning"><?= $metrics['conteo']['bajo'] ?? 0 ?></span>
                </div>
                <div class="progress mt-2" style="height:5px;">
                    <div class="progress-bar bg-warning" style="width: <?= $metrics['porcentajes']['bajo'] ?? 0 ?>%"></div>
                </div>
                <small class="text-muted"><?= $metrics['porcentajes']['bajo'] ?? 0 ?>%</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info h-100">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-info fw-bold"><i class="fas fa-hourglass-half me-1"></i>Casi</span>
                    <span class="fs-5 text-info"><?= $metrics['conteo']['casi'] ?? 0 ?></span>
                </div>
                <div class="progress mt-2" style="height:5px;">
                    <div class="progress-bar bg-info" style="width: <?= $metrics['porcentajes']['casi'] ?? 0 ?>%"></div>
                </div>
                <small class="text-muted"><?= $metrics['porcentajes']['casi'] ?? 0 ?>%</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-secondary h-100">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-secondary fw-bold"><i class="fas fa-database me-1"></i>Valor Afectado</span>
                    <span class="fs-6">S/ <?= number_format($metrics['valor_afectado'] ?? 0, 2) ?></span>
                </div>
                <small class="text-muted">Costo potencial de reposición (parcial)</small>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Listado de Alertas</h5>
        <span class="badge bg-primary">Total filtrado: <?= $total ?? 0 ?></span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($repuestos)): ?>
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h5 class="text-success">Sin alertas</h5>
                <p class="text-muted mb-0">No hay repuestos en condiciones de alerta para los filtros aplicados.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cod</th>
                        <th>Nombre</th>
                        <th>Cat</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Min</th>
                        <th class="text-center">% Min</th>
                        <th class="text-center">Severidad</th>
                        <th class="text-center">Shortage</th>
                        <th class="text-center">Reponer</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($repuestos as $r): ?>
                    <?php 
                        $sev = $r->severidad ?? 'OK';
                        $rowClass = '';
                        switch ($sev) {
                            case 'CRITICO': $rowClass='table-danger'; break;
                            case 'BAJO': $rowClass='table-warning'; break;
                            case 'CASI': $rowClass='table-info'; break;
                        }
                        $porc = $r->porcentaje_min !== null ? round($r->porcentaje_min,1) : '-';
                        $shortage = $r->shortage ?? 0; // negativo
                        $reponer = $r->recomendado_reponer ?? 0;
                    ?>
                    <tr class="<?= $rowClass ?>">
                        <td><code><?= htmlspecialchars($r->getCodigo()) ?></code></td>
                        <td>
                            <strong><?= htmlspecialchars($r->getNombre()) ?></strong>
                            <?php if ($r->getDescripcion()): ?><br><small class="text-muted"><?= htmlspecialchars(substr($r->getDescripcion(),0,45)) ?>...</small><?php endif; ?>
                        </td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($r->getCategoria()) ?></span></td>
                        <td class="text-center fw-bold"><?= $r->getStockActual() ?></td>
                        <td class="text-center text-muted"><?= $r->getStockMinimo() ?></td>
                        <td class="text-center">
                            <?php if ($porc !== '-'): ?>
                                <span class="badge bg-<?= $sev==='CRITICO'?'danger':($sev==='BAJO'?'warning text-dark':($sev==='CASI'?'info text-dark':'secondary')) ?>"><?= $porc ?>%</span>
                            <?php else: ?>-
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-<?= $sev==='CRITICO'?'danger':($sev==='BAJO'?'warning text-dark':($sev==='CASI'?'info text-dark':'secondary')) ?>"><?= $sev ?></span>
                        </td>
                        <td class="text-center text-danger"><?= $shortage ?></td>
                        <td class="text-center text-primary fw-bold"><?= $reponer ?></td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn-outline-info" href="<?= BASE_URL ?>repuestos/<?= $r->getId() ?>" title="Ver"><i class="fas fa-eye"></i></a>
                                <a class="btn btn-outline-warning" href="<?= BASE_URL ?>repuestos/<?= $r->getId() ?>/editar" title="Editar"><i class="fas fa-edit"></i></a>
                                <a class="btn btn-success" href="<?= BASE_URL ?>inventario/entradas?repuesto_id=<?= $r->getId() ?>" title="Agregar Stock"><i class="fas fa-plus"></i></a>
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

<?php if (!empty($metrics['top_criticos'])): ?>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-fire me-1 text-danger"></i>Top 5 Críticos Prioritarios</h6>
        <small class="text-muted">Ordenados por necesidad de reposición</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cod</th><th>Nombre</th><th>Cat</th><th class="text-center">Stock</th><th class="text-center">Min</th><th class="text-center">% Min</th><th class="text-center">Reponer</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($metrics['top_criticos'] as $tc): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($tc->getCodigo()) ?></code></td>
                        <td><?= htmlspecialchars($tc->getNombre()) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($tc->getCategoria()) ?></span></td>
                        <td class="text-center fw-bold text-danger"><?= $tc->getStockActual() ?></td>
                        <td class="text-center text-muted"><?= $tc->getStockMinimo() ?></td>
                        <td class="text-center"><?= $tc->porcentaje_min !== null ? round($tc->porcentaje_min,1).'%' : '-' ?></td>
                        <td class="text-center fw-bold text-primary"><?= $tc->recomendado_reponer ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Paginación -->
<?php if (isset($pages) && $pages > 1): ?>
<nav aria-label="Paginación de alertas" class="mt-4">
    <ul class="pagination justify-content-center mb-0">
        <?php for ($i=1; $i <= $pages; $i++): ?>
            <?php 
                $q = http_build_query([
                    'page' => $i,
                    'categoria_id' => $filters['categoria_id'] ?? '',
                    'include_near' => !empty($filters['include_near']) ? 'on' : null,
                    'solo_criticos' => !empty($filters['solo_criticos']) ? 'on' : null,
                ]);
            ?>
            <li class="page-item <?= $i==$current_page ? 'active' : '' ?>"><a class="page-link" href="<?= BASE_URL ?>repuestos/stock-bajo?<?= $q ?>"><?= $i ?></a></li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
