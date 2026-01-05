<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistema de Repuestos' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar { 
            min-height: 100vh; 
            background-color: #343a40;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        .sidebar .nav-link { 
            color: #adb5bd; 
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 8px;
        }
        .sidebar .nav-link:hover { 
            color: #fff; 
            background-color: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }
        .sidebar .nav-link.active { 
            color: #fff; 
            background-color: #495057;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .main-content { margin-left: 0; }
        @media (min-width: 768px) { .main-content { margin-left: 200px; } }
        .logo-img {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .logo-img:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.4) !important;
        }
        .sidebar .nav-link i {
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover i {
            transform: scale(1.1);
        }
        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
        }
        .sidebar-footer {
            flex-shrink: 0;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Sidebar -->
    <nav class="sidebar position-fixed d-none d-md-block" style="width: 200px;">
        <!-- Header del Sidebar -->
        <div class="p-3 border-bottom border-secondary text-center">
            <div class="mb-2">
                <img src="<?= BASE_URL ?>logo.jpg" alt="Logo" class="logo-img" style="width: 140px; height: 140px; object-fit: contain; border-radius: 20px; background: rgba(255,255,255,0.1); padding: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.6);">
            </div>
            <div>
                <h6 class="text-white mb-1 fw-bold" style="font-size: 0.9rem;"><?= APP_NAME ?></h6>
            </div>
        </div>
        
        <!-- Menu del Sidebar -->
        <div class="sidebar-menu">
            <ul class="nav nav-pills flex-column px-2 py-2">
                <li class="nav-item mb-1">
                    <a class="nav-link text-center py-2 rounded" href="<?= BASE_URL ?>dashboard">
                        <i class="fas fa-tachometer-alt d-block mb-1" style="font-size: 1.1em;"></i>
                        <span class="small">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link text-center py-2 rounded" href="<?= BASE_URL ?>usuarios">
                        <i class="fas fa-users d-block mb-1" style="font-size: 1.1em;"></i>
                        <span class="small">Usuarios</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link text-center py-2 rounded" href="<?= BASE_URL ?>repuestos">
                        <i class="fas fa-cogs d-block mb-1" style="font-size: 1.1em;"></i>
                        <span class="small">Repuestos</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link text-center py-2 rounded" href="<?= BASE_URL ?>inventario">
                        <i class="fas fa-boxes d-block mb-1" style="font-size: 1.1em;"></i>
                        <span class="small">Inventario</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link text-center py-2 rounded" href="<?= BASE_URL ?>proveedores">
                        <i class="fas fa-truck d-block mb-1" style="font-size: 1.1em;"></i>
                        <span class="small">Proveedores</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link text-center py-2 rounded" href="<?= BASE_URL ?>ventas">
                        <i class="fas fa-shopping-cart d-block mb-1" style="font-size: 1.1em;"></i>
                        <span class="small">Ventas</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link text-center py-2 rounded" href="<?= BASE_URL ?>reportes">
                        <i class="fas fa-chart-bar d-block mb-1" style="font-size: 1.1em;"></i>
                        <span class="small">Reportes</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Footer del Sidebar -->
        <div class="sidebar-footer border-top border-secondary p-2">
            <div class="dropdown text-center">
                <a class="nav-link dropdown-toggle text-white py-2" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle me-1" style="font-size: 1.2em;"></i>
                    <span class="small"><?= $_SESSION['user_name'] ?? 'Usuario' ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>usuarios/<?= $_SESSION['user_id'] ?? '' ?>"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>usuarios/<?= $_SESSION['user_id'] ?? '' ?>/cambiar-password"><i class="fas fa-key me-2"></i>Cambiar Contraseña</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>logout"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <?php if (isset($_SESSION['user_id'])): ?>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary d-md-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="d-flex align-items-center">
                    <img src="<?= BASE_URL ?>logo.jpg" alt="Logo" class="me-2 d-md-none" style="width: 35px; height: 35px; object-fit: contain; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
                    <span class="navbar-brand mb-0 h1"><?= $title ?? 'Sistema de Repuestos' ?></span>
                </div>
            </div>
        </nav>
        <?php endif; ?>

        <!-- Content -->
        <div class="container-fluid p-4">
            <?php if (isset($success) && $success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </div>

    <!-- Mobile Sidebar -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title"><?= APP_NAME ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>usuarios">
                        <i class="fas fa-users me-2"></i>Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>repuestos">
                        <i class="fas fa-cogs me-2"></i>Repuestos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>inventario">
                        <i class="fas fa-boxes me-2"></i>Inventario
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>proveedores">
                        <i class="fas fa-truck me-2"></i>Proveedores
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>ventas">
                        <i class="fas fa-shopping-cart me-2"></i>Ventas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>reportes">
                        <i class="fas fa-chart-bar me-2"></i>Reportes
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
