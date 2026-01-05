<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-arrow-up me-2 text-success"></i>Registrar Entrada de Stock</h2>
    <a href="<?= BASE_URL ?>inventario" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver a Inventario
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Nueva Entrada de Stock
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>inventario/entradas">
                    <?= \App\Core\Csrf::field(); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="repuesto_id" class="form-label">
                                    <i class="fas fa-cog me-2"></i>Repuesto *
                                </label>
                                <select class="form-select" id="repuesto_id" name="repuesto_id" required>
                                    <option value="">Seleccionar repuesto...</option>
                                    <?php foreach ($repuestos as $repuesto): ?>
                                    <option value="<?= $repuesto->getId() ?>" 
                                            <?= (($_POST['repuesto_id'] ?? '') == $repuesto->getId()) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($repuesto->getCodigo()) ?> - 
                                        <?= htmlspecialchars($repuesto->getNombre()) ?>
                                        (Stock: <?= $repuesto->getStockActual() ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Seleccione el repuesto que recibió stock</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cantidad" class="form-label">
                                    <i class="fas fa-hashtag me-2"></i>Cantidad *
                                </label>
                                <input type="number" class="form-control" id="cantidad" name="cantidad" 
                                       value="<?= $_POST['cantidad'] ?? '' ?>" 
                                       min="1" step="1" required>
                                <div class="form-text">Cantidad de unidades que ingresan</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="proveedor_id" class="form-label">
                                    <i class="fas fa-truck me-2"></i>Proveedor
                                </label>
                                <select class="form-select" id="proveedor_id" name="proveedor_id">
                                    <option value="">Sin proveedor</option>
                                    <?php foreach ($proveedores as $proveedor): ?>
                                    <option value="<?= $proveedor->getId() ?>" 
                                            <?= (($_POST['proveedor_id'] ?? '') == $proveedor->getId()) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($proveedor->getNombre()) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Proveedor del cual se recibió el stock</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="motivo" class="form-label">
                                    <i class="fas fa-tag me-2"></i>Motivo *
                                </label>
                                <select class="form-select" id="motivo" name="motivo" required>
                                    <option value="">Seleccionar motivo...</option>
                                    <?php foreach ($motivos as $value => $label): ?>
                                    <option value="<?= $value ?>" 
                                            <?= (($_POST['motivo'] ?? '') == $value) ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Motivo de la entrada de stock</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">
                            <i class="fas fa-comment me-2"></i>Observaciones
                        </label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                  placeholder="Observaciones adicionales sobre la entrada de stock..."><?= htmlspecialchars($_POST['observaciones'] ?? '') ?></textarea>
                        <div class="form-text">Información adicional sobre el movimiento</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Al registrar una entrada de stock, la cantidad se sumará automáticamente al stock actual del repuesto seleccionado.
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>inventario" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Registrar Entrada
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Mostrar información del repuesto seleccionado
document.getElementById('repuesto_id').addEventListener('change', function() {
    const repuestoId = this.value;
});

// Validar cantidad mínima
document.getElementById('cantidad').addEventListener('input', function() {
    const cantidad = parseInt(this.value);
    if (cantidad < 1) {
        this.setCustomValidity('La cantidad debe ser mayor a 0');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
