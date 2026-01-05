<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-edit me-2"></i>Editar Usuario</h2>
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
                <form method="POST" action="<?= BASE_URL ?>usuarios/<?= $user->getId() ?>">
                    <?= \App\Core\Csrf::field(); ?>
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-user me-2"></i>Nombre Completo *
                                </label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?= htmlspecialchars($user->getNombre()) ?>" required>
                                <div class="form-text">Ingrese el nombre completo del usuario</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email *
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($user->getEmail()) ?>" required>
                                <div class="form-text">El email será usado para iniciar sesión</div>
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
                                            <?= ($user->getRol() == $value) ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Los administradores tienen acceso completo al sistema</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar me-2"></i>Fecha de Creación
                                </label>
                                <input type="text" class="form-control" 
                                       value="<?= date('d/m/Y H:i', strtotime($user->getCreatedAt())) ?>" 
                                       readonly>
                                <div class="form-text">Fecha de registro en el sistema</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" 
                                   <?= $user->isActivo() ? 'checked' : '' ?>>
                            <label class="form-check-label" for="activo">
                                <i class="fas fa-check-circle me-2"></i>Usuario activo
                            </label>
                            <div class="form-text">Los usuarios inactivos no pueden iniciar sesión</div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="<?= BASE_URL ?>usuarios/<?= $user->getId() ?>/cambiar-password" 
                               class="btn btn-outline-warning">
                                <i class="fas fa-key me-2"></i>Cambiar Contraseña
                            </a>
                        </div>
                        <div>
                            <a href="<?= BASE_URL ?>usuarios" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
