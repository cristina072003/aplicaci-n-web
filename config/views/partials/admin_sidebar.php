<div class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-dark text-white" style="width: var(--sidebar-width); min-height: 100vh; position: fixed; left: 0; top: 0; z-index: 1020; transition: left 0.3s, width 0.3s;">
    <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="bi bi-speedometer2 me-2 fs-4"></i>
        <span class="fs-4 sidebar-label">Administración</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : 'text-white' ?>">
                <i class="bi bi-speedometer2 me-2"></i>
                <span class="sidebar-label">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="usuarios.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'usuarios.php' ? 'active' : 'text-white' ?>">
                <i class="bi bi-people me-2"></i>
                <span class="sidebar-label">Usuarios</span>
            </a>
        </li>
        <li>
            <a href="tests.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'tests.php' ? 'active' : 'text-white' ?>">
                <i class="bi bi-question-circle me-2"></i>
                <span class="sidebar-label">Tests Vocacionales</span>
            </a>
        </li>
        <li>
            <a href="reportes.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'reportes.php' ? 'active' : 'text-white' ?>">
                <i class="bi bi-bar-chart me-2"></i>
                <span class="sidebar-label">Reportes</span>
            </a>
        </li>
        <li>
            <a href="asignar_test.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'asignar_test.php' ? 'active' : 'text-white' ?>">
                <i class="bi bi-clipboard-check me-2"></i>
                <span class="sidebar-label">Asignar Test</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="corregir_resultados.php">
                <i class="bi bi-pencil-square me-1"></i> <span class="sidebar-label">Corregir Resultados</span>
            </a>
        </li>
        <li>
            <a href="configuracion.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'configuracion.php' ? 'active' : 'text-white' ?>">
                <i class="bi bi-gear me-2"></i>
                <span class="sidebar-label">Configuración</span>
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown mt-auto">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown">
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                <i class="bi bi-person-fill"></i>
            </div>
            <strong class="sidebar-label"><?= $_SESSION['nombre'] ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li><a class="dropdown-item" href="../admin/perfil.php"><i class="bi bi-person me-2"></i>Perfil</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item text-danger" href="../../controllers/loginControllers/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
        </ul>
    </div>
</div>

<style>
    @media (max-width: 991.98px) {
        .sidebar {
            left: -280px !important;
        }

        .sidebar.show {
            left: 0 !important;
        }

        .sidebar-label {
            display: none !important;
        }
    }

    @media (max-width: 575.98px) {
        .sidebar {
            width: 85vw;
            min-width: 180px;
            max-width: 320px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        const header = document.querySelector('.admin-header');

        function hideSidebar() {
            if (sidebar) sidebar.classList.remove('show');
            if (header) header.style.left = '0';
        }

        function showSidebar() {
            if (sidebar) sidebar.classList.add('show');
            if (header) header.style.left = window.innerWidth >= 992 ? 'var(--sidebar-width)' : '0';
        }

        if (sidebarToggle && sidebar && header) {
            sidebarToggle.addEventListener('click', function() {
                if (sidebar.classList.contains('show')) {
                    hideSidebar();
                } else {
                    showSidebar();
                }
            });

            // Ocultar sidebar en móvil al hacer clic fuera
            document.addEventListener('click', function(e) {
                if (window.innerWidth < 992 && sidebar.classList.contains('show')) {
                    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        hideSidebar();
                    }
                }
            });

            // Ajustar en carga según tamaño de pantalla
            if (window.innerWidth < 992) {
                hideSidebar();
            } else {
                showSidebar();
            }

            // Ajustar en redimensionamiento
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    showSidebar();
                } else {
                    hideSidebar();
                }
            });
        }
    });
</script>