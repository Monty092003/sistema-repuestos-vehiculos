<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus-circle me-2"></i>Crear Nuevo Repuesto</h2>
    <a href="<?= BASE_URL ?>repuestos" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver a Repuestos
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información del Repuesto</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>repuestos">
                    <?= \App\Core\Csrf::field(); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="codigo" class="form-label">
                                    <i class="fas fa-barcode me-2"></i>Código del Repuesto *
                                </label>
                                <input type="text" class="form-control" id="codigo" name="codigo" 
                                       value="<?= htmlspecialchars($_POST['codigo'] ?? '') ?>" 
                                       placeholder="Ej: FREN001" required>
                                <div class="form-text">Código único para identificar el repuesto</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoria_id" class="form-label">
                                    <i class="fas fa-tags me-2"></i>Categoría *
                                </label>
                                <select class="form-select" id="categoria_id" name="categoria_id" required>
                                    <option value="">Seleccionar categoría...</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria->getId() ?>" 
                                            <?= (($_POST['categoria_id'] ?? '') == $categoria->getId()) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($categoria->getNombre()) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Clasificación del repuesto</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            <i class="fas fa-cog me-2"></i>Nombre del Repuesto *
                        </label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" 
                               placeholder="Ej: Pastillas de Freno Delanteras" required>
                        <div class="form-text">Nombre descriptivo del repuesto</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left me-2"></i>Descripción
                        </label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                  placeholder="Descripción detallada del repuesto..."><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
                        <div class="form-text">Información adicional sobre el repuesto</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="precio_compra" class="form-label">
                                    <i class="fas fa-dollar-sign me-2"></i>Precio de Compra *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precio_compra" name="precio_compra" 
                                           value="<?= $_POST['precio_compra'] ?? '' ?>" 
                                           step="0.01" min="0" required>
                                </div>
                                <div class="form-text">Costo de adquisición</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="precio_venta" class="form-label">
                                    <i class="fas fa-tag me-2"></i>Precio de Venta *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precio_venta" name="precio_venta" 
                                           value="<?= $_POST['precio_venta'] ?? '' ?>" 
                                           step="0.01" min="0" required>
                                </div>
                                <div class="form-text">Precio de venta al público</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_actual" class="form-label">
                                    <i class="fas fa-boxes me-2"></i>Stock Inicial
                                </label>
                                <input type="number" class="form-control" id="stock_actual" name="stock_actual" 
                                       value="<?= $_POST['stock_actual'] ?? '0' ?>" 
                                       min="0" step="1">
                                <div class="form-text">Cantidad inicial en inventario</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_minimo" class="form-label">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Stock Mínimo
                                </label>
                                <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" 
                                       value="<?= $_POST['stock_minimo'] ?? '5' ?>" 
                                       min="0" step="1">
                                <div class="form-text">Alerta cuando baje de este nivel</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_maximo" class="form-label">
                                    <i class="fas fa-arrow-up me-2"></i>Stock Máximo
                                </label>
                                <input type="number" class="form-control" id="stock_maximo" name="stock_maximo" 
                                       value="<?= $_POST['stock_maximo'] ?? '100' ?>" 
                                       min="0" step="1">
                                <div class="form-text">Nivel máximo recomendado</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-toggle-on me-2"></i>Estado
                                </label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" 
                                           <?= (($_POST['activo'] ?? '') == 'on') ? 'checked' : 'checked' ?>>
                                    <label class="form-check-label" for="activo">
                                        Repuesto activo
                                    </label>
                                    <div class="form-text">Los repuestos inactivos no aparecen en ventas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>repuestos" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Crear Repuesto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Calcular margen de ganancia automáticamente
document.getElementById('precio_compra').addEventListener('input', calculateMargin);
document.getElementById('precio_venta').addEventListener('input', calculateMargin);

function calculateMargin() {
    const precioCompra = parseFloat(document.getElementById('precio_compra').value) || 0;
    const precioVenta = parseFloat(document.getElementById('precio_venta').value) || 0;
    
    if (precioCompra > 0 && precioVenta > 0) {
        const margen = ((precioVenta - precioCompra) / precioCompra) * 100;
    }
}

// Validar que el precio de venta sea mayor o igual al de compra
document.getElementById('precio_venta').addEventListener('input', function() {
    const precioCompra = parseFloat(document.getElementById('precio_compra').value) || 0;
    const precioVenta = parseFloat(this.value) || 0;
    
    if (precioVenta < precioCompra) {
        this.setCustomValidity('El precio de venta debe ser mayor o igual al precio de compra');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
