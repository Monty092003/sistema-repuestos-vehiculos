<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-arrow-down me-2 text-danger"></i>Registrar Salida de Stock</h2>
    <a href="<?= BASE_URL ?>inventario" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver a Inventario
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="fas fa-minus me-2"></i>Nueva Salida de Stock
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>inventario/salidas">
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
                                <div class="form-text">Seleccione el repuesto del cual se retira stock</div>
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
                                <div class="form-text">Cantidad de unidades que salen</div>
                            </div>
                        </div>
                    </div>
                    
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
                        <div class="form-text">Motivo de la salida de stock</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">
                            <i class="fas fa-comment me-2"></i>Observaciones
                        </label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                  placeholder="Observaciones adicionales sobre la salida de stock..."><?= htmlspecialchars($_POST['observaciones'] ?? '') ?></textarea>
                        <div class="form-text">Información adicional sobre el movimiento</div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Advertencia:</strong> Al registrar una salida de stock, la cantidad se restará automáticamente del stock actual del repuesto seleccionado. Asegúrese de que hay suficiente stock disponible.
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>inventario" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-save me-2"></i>Registrar Salida
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Mostrar información del repuesto seleccionado y validar stock
document.getElementById('repuesto_id').addEventListener('change', function() {
    const repuestoId = this.value;
    const cantidadInput = document.getElementById('cantidad');
    
    if (repuestoId) {
        // Validar que la cantidad no exceda el stock disponible
        cantidadInput.addEventListener('input', function() {
            const cantidad = parseInt(this.value);
            if (cantidad > 0) {
                // Aquí podrías validar contra el stock real
                this.setCustomValidity('');
            } else {
                this.setCustomValidity('La cantidad debe ser mayor a 0');
            }
        });
    }
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
