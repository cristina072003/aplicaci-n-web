<!-- Header Cliente -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="perfil.php">
            <img src="../../public/assets/images/logo.png" alt="Logo" width="38" height="38" class="d-inline-block align-text-top rounded-circle" style="object-fit:cover;">
            <span class="fw-bold">Sistema Vocacional</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="perfil.php">
                        <i class="fas fa-user me-1"></i> Perfil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tests.php">
                        <i class="fas fa-question-circle me-1"></i> Mis Tests
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="resultados.php">
                        <i class="fas fa-chart-pie me-1"></i> Resultados
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="../../controllers/loginControllers/logout.php">
                        <i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>