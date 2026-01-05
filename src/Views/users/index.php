<?php 
$content = ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users me-2"></i>Gestión de Usuarios</h2>
    <a href="<?= BASE_URL ?>usuarios/crear" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nuevo Usuario
    </a>
</div>

<!-- Filtros de búsqueda -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>usuarios" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="search" 
                           value="<?= htmlspecialchars($search_term ?? '') ?>" 
                           placeholder="Buscar por nombre o email...">
                </div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="fas fa-search me-1"></i>Buscar
                </button>
                <a href="<?= BASE_URL ?>usuarios" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de usuarios -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Usuarios</h5>
        <span class="badge bg-primary"><?= $total ?? 0 ?> usuarios</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($users)): ?>
        <div class="text-center py-5">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No hay usuarios registrados</h5>
            <p class="text-muted">Comience creando el primer usuario del sistema</p>
            <a href="<?= BASE_URL ?>usuarios/crear" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Crear Usuario
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user->getId() ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <?= strtoupper(substr($user->getNombre(), 0, 1)) ?>
                                </div>
                                <?= htmlspecialchars($user->getNombre()) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($user->getEmail()) ?></td>
                        <td>
                            <span class="badge <?= $user->isAdmin() ? 'bg-danger' : 'bg-info' ?>">
                                <?= $user->getRolNombre() ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= $user->isActivo() ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $user->isActivo() ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($user->getCreatedAt())) ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="<?= BASE_URL ?>usuarios/<?= $user->getId() ?>" 
                                   class="btn btn-sm btn-outline-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>usuarios/<?= $user->getId() ?>/editar" 
                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= BASE_URL ?>usuarios/<?= $user->getId() ?>/cambiar-password" 
                                   class="btn btn-sm btn-outline-secondary" title="Cambiar Contraseña">
                                    <i class="fas fa-key"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="confirmDelete(<?= $user->getId() ?>, '<?= htmlspecialchars($user->getNombre()) ?>')" 
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
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

<!-- Paginación -->
<?php if (isset($pages) && $pages > 1): ?>
<nav aria-label="Paginación de usuarios" class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
            <a class="page-link" href="<?= BASE_URL ?>usuarios?page=<?= $i ?><?= !empty($search_term) ? '&search=' . urlencode($search_term) : '' ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

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
                    <?= \App\Core\Csrf::field(); ?>
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
