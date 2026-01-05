<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-key me-2"></i>Cambiar Contraseña</h2>
    <a href="<?= BASE_URL ?>usuarios/<?= $user->getId() ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver al Usuario
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    <?= htmlspecialchars($user->getNombre()) ?>
                </h5>
                <small class="text-muted"><?= htmlspecialchars($user->getEmail()) ?></small>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>usuarios/<?= $user->getId() ?>/cambiar-password">
                    <?= \App\Core\Csrf::field(); ?>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Contraseña Actual *
                        </label>
                        <input type="password" class="form-control" id="current_password" 
                               name="current_password" required>
                        <div class="form-text">Ingrese la contraseña actual para confirmar</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">
                            <i class="fas fa-key me-2"></i>Nueva Contraseña *
                        </label>
                        <input type="password" class="form-control" id="new_password" 
                               name="new_password" required minlength="6">
                        <div class="form-text">Mínimo 6 caracteres</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">
                            <i class="fas fa-check-circle me-2"></i>Confirmar Nueva Contraseña *
                        </label>
                        <input type="password" class="form-control" id="confirm_password" 
                               name="confirm_password" required minlength="6">
                        <div class="form-text">Repita la nueva contraseña</div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>usuarios/<?= $user->getId() ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Cambiar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validar que las contraseñas coincidan
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (newPassword !== confirmPassword) {
        this.setCustomValidity('Las contraseñas no coinciden');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('new_password').addEventListener('input', function() {
    const confirmPassword = document.getElementById('confirm_password');
    if (confirmPassword.value) {
        confirmPassword.dispatchEvent(new Event('input'));
    }
});
</script>

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
