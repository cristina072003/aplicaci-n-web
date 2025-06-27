<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';

// Verificar autenticación y rol de admin
verificarAutenticacion();
verificarRol('admin');

$titulo = "Panel de Administración";

// Consultas para estadísticas
$usuariosCount = $conexion->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
$testsCount = $conexion->query("SELECT COUNT(*) AS total FROM tests")->fetch_assoc()['total'];
$reportesCount = $conexion->query("SELECT COUNT(*) AS total FROM resultados")->fetch_assoc()['total'];

// Actividad reciente: últimos tests realizados
$actividad = $conexion->query(
    "SELECT u.nombre, u.apellido, u.email, t.nombre AS test, r.fecha_resultado
     FROM resultados r
     JOIN usuarios u ON r.id_usuario = u.id_usuario
     JOIN tests t ON r.id_test = t.id_test
     ORDER BY r.fecha_resultado DESC
     LIMIT 5"
);
?>

<?php include '../partials/admin_header.php'; ?>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../partials/admin_sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4" style="margin-top: 80px; min-height: 80vh;">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2 mb-2 mb-md-0">Panel de Administración</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2 align-items-center">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nombre'] . ' ' . $_SESSION['apellido']) ?>&background=0d6efd&color=fff&size=64" class="admin-avatar" alt="Avatar">
                            <span class="btn btn-sm btn-outline-primary fw-bold">
                                <i class="bi bi-person"></i> <?= $_SESSION['nombre'] . ' ' . $_SESSION['apellido'] ?>
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Estadísticas -->
                <div class="row mb-4 g-3">
                    <div class="col-12 col-md-4">
                        <div class="card stat-card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-people-fill me-2"></i>Usuarios Registrados</h5>
                                <h2 class="card-text"><?= $usuariosCount ?></h2>
                                <p class="text-muted mb-0">Total en el sistema</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card stat-card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-journal-text me-2"></i>Tests Disponibles</h5>
                                <h2 class="card-text"><?= $testsCount ?></h2>
                                <p class="text-muted mb-0">Total de tests</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card stat-card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-bar-chart-fill me-2"></i>Reportes Generados</h5>
                                <h2 class="card-text"><?= $reportesCount ?></h2>
                                <p class="text-muted mb-0">Total de reportes</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Actividad reciente -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white rounded-top">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Actividad Reciente</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php if ($actividad && $actividad->num_rows > 0): ?>
                                <?php while ($row = $actividad->fetch_assoc()): ?>
                                    <div class="list-group-item recent-item">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <h6 class="mb-1 fw-bold text-primary"><i class="bi bi-journal-check me-1"></i><?= htmlspecialchars($row['test']) ?></h6>
                                            <small class="text-secondary"><i class="bi bi-calendar-event me-1"></i><?= date('d/m/Y H:i', strtotime($row['fecha_resultado'])) ?></small>
                                        </div>
                                        <p class="mb-1">Usuario: <span class="fw-semibold text-dark"><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) ?></span> <span class="text-muted">(<?= htmlspecialchars($row['email']) ?>)</span></p>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="list-group-item text-center">Sin actividad reciente</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>