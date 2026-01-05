<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus-circle me-2"></i>Crear Nuevo Proveedor</h2>
    <a href="<?= BASE_URL ?>proveedores" class="btn btn-outline-secondary">
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

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información del Proveedor</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>proveedores">
                    <?= \App\Core\Csrf::field(); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-building me-2"></i>Nombre del Proveedor *
                                </label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" 
                                       placeholder="Ej: Repuestos del Norte S.A." required>
                                <div class="form-text">Nombre comercial de la empresa</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contacto" class="form-label">
                                    <i class="fas fa-user me-2"></i>Persona de Contacto
                                </label>
                                <input type="text" class="form-control" id="contacto" name="contacto" 
                                       value="<?= htmlspecialchars($_POST['contacto'] ?? '') ?>" 
                                       placeholder="Ej: Juan Pérez">
                                <div class="form-text">Nombre de la persona de contacto</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono" class="form-label">
                                    <i class="fas fa-phone me-2"></i>Teléfono
                                </label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" 
                                       value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>" 
                                       placeholder="Ej: (01) 234-5678">
                                <div class="form-text">Número de teléfono de contacto</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                                       placeholder="Ej: contacto@repuestosdelnorte.com">
                                <div class="form-text">Correo electrónico de contacto</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="direccion" class="form-label">
                            <i class="fas fa-map-marker-alt me-2"></i>Dirección
                        </label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="3" 
                                  placeholder="Dirección completa del proveedor..."><?= htmlspecialchars($_POST['direccion'] ?? '') ?></textarea>
                        <div class="form-text">Dirección física del proveedor</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" 
                                   <?= (($_POST['activo'] ?? '') == 'on') ? 'checked' : 'checked' ?>>
                            <label class="form-check-label" for="activo">
                                <i class="fas fa-check-circle me-2"></i>Proveedor activo
                            </label>
                            <div class="form-text">Los proveedores inactivos no aparecen en las listas de selección</div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Los campos marcados con * son obligatorios. 
                        Los proveedores se pueden asociar a las compras de repuestos para llevar un mejor control del inventario.
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>proveedores" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Crear Proveedor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validar email si se proporciona
document.getElementById('email').addEventListener('blur', function() {
    const email = this.value;
    if (email && !email.includes('@')) {
        this.setCustomValidity('Por favor ingrese un email válido');
    } else {
        this.setCustomValidity('');
    }
});

// Formatear teléfono automáticamente
document.getElementById('telefono').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 0) {
        if (value.length <= 3) {
            value = `(${value}`;
        } else if (value.length <= 6) {
            value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
        } else {
            value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`;
        }
    }
    this.value = value;
});
</script>

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
