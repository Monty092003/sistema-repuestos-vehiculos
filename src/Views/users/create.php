<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-plus me-2"></i>Crear Nuevo Usuario</h2>
    <a href="<?= BASE_URL ?>usuarios" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver a Usuarios
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información del Usuario</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>usuarios">
                    <?= \App\Core\Csrf::field(); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-user me-2"></i>Nombre Completo *
                                </label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
                                <div class="form-text">Ingrese el nombre completo del usuario</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email *
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                <div class="form-text">El email será usado para iniciar sesión</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Contraseña *
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Mínimo 6 caracteres</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Confirmar Contraseña *
                                </label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                <div class="form-text">Repita la contraseña exactamente</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rol" class="form-label">
                                    <i class="fas fa-user-tag me-2"></i>Rol *
                                </label>
                                <select class="form-select" id="rol" name="rol" required>
                                    <option value="">Seleccionar rol...</option>
                                    <?php foreach ($roles as $value => $label): ?>
                                    <option value="<?= $value ?>" 
                                            <?= (($_POST['rol'] ?? '') == $value) ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Los administradores tienen acceso completo al sistema</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-shield-alt me-2"></i>Política de Contraseña</label>
                                <div class="small text-muted">
                                    - Mínimo 6 caracteres<br>
                                    - Se recomienda incluir números y letras
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" 
                                   <?= (($_POST['activo'] ?? '') == 'on') ? 'checked' : 'checked' ?>>
                            <label class="form-check-label" for="activo">
                                <i class="fas fa-check-circle me-2"></i>Usuario activo
                            </label>
                            <div class="form-text">Los usuarios inactivos no pueden iniciar sesión</div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>usuarios" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Crear Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const pwd = document.getElementById('password');
const pwd2 = document.getElementById('password_confirmation');
function validatePasswords(){
    if(pwd.value !== pwd2.value){
        pwd2.setCustomValidity('Las contraseñas no coinciden');
    } else {
        pwd2.setCustomValidity('');
    }
}
pwd.addEventListener('input', validatePasswords);
pwd2.addEventListener('input', validatePasswords);
</script>

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
