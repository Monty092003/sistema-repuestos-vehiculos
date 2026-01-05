<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user me-2"></i>Detalles del Usuario</h2>
    <div>
        <a href="<?= BASE_URL ?>usuarios/<?= $user->getId() ?>/editar" class="btn btn-warning me-2">
            <i class="fas fa-edit me-2"></i>Editar
        </a>
        <a href="<?= BASE_URL ?>usuarios" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                        <?= strtoupper(substr($user->getNombre(), 0, 1)) ?>
                    </div>
                    <div>
                        <h5 class="mb-0"><?= htmlspecialchars($user->getNombre()) ?></h5>
                        <p class="mb-0 text-muted"><?= htmlspecialchars($user->getEmail()) ?></p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-id-card me-2"></i>ID de Usuario
                            </label>
                            <p class="form-control-plaintext"><?= $user->getId() ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($user->getEmail()) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user-tag me-2"></i>Rol
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge <?= $user->isAdmin() ? 'bg-danger' : 'bg-info' ?> fs-6">
                                    <?= $user->getRolNombre() ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-toggle-on me-2"></i>Estado
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge <?= $user->isActivo() ? 'bg-success' : 'bg-secondary' ?> fs-6">
                                    <?= $user->isActivo() ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-plus me-2"></i>Fecha de Creación
                            </label>
                            <p class="form-control-plaintext">
                                <?= date('d/m/Y H:i:s', strtotime($user->getCreatedAt())) ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-edit me-2"></i>Última Actualización
                            </label>
                            <p class="form-control-plaintext">
                                <?= date('d/m/Y H:i:s', strtotime($user->getUpdatedAt())) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="<?= BASE_URL ?>usuarios/<?= $user->getId() ?>/cambiar-password" 
                           class="btn btn-outline-warning">
                            <i class="fas fa-key me-2"></i>Cambiar Contraseña
                        </a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-danger" 
                                onclick="confirmDelete(<?= $user->getId() ?>, '<?= htmlspecialchars($user->getNombre()) ?>')">
                            <i class="fas fa-trash me-2"></i>Eliminar Usuario
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar al usuario <strong id="userName"></strong>?</p>
                <p class="text-muted">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="" id="deleteForm" class="d-inline">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(userId, userName) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('deleteForm').action = '<?= BASE_URL ?>usuarios/' + userId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php 
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
