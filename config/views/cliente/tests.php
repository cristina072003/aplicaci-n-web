<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';
verificarAutenticacion();
$titulo = "Mis Tests";
$id_usuario = $_SESSION['id_usuario'];
$tests = [];
$sql = "SELECT ta.id_asignacion, t.nombre, t.descripcion, ta.completado, t.id_test FROM tests_asignados ta INNER JOIN tests t ON ta.id_test = t.id_test WHERE ta.id_usuario = $id_usuario ORDER BY ta.fecha_asignacion DESC";
$result = $conexion->query($sql);
if ($result) {
    $tests = $result->fetch_all(MYSQLI_ASSOC);
}
require_once __DIR__ . '/../partials_cliente/header.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">
    <div class="container my-5 flex-grow-1">
        <h2 class="mb-4 text-center text-primary fw-bold">Tests Asignados</h2>
        <div class="row g-4">
            <?php if (empty($tests)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">No tienes tests asignados actualmente.</div>
                </div>
            <?php else: ?>
                <?php foreach ($tests as $test): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card shadow-sm h-100 border-0">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title text-primary fw-bold mb-2"><?= htmlspecialchars($test['nombre']) ?></h5>
                                    <p class="card-text text-secondary small mb-3"><?= htmlspecialchars($test['descripcion']) ?></p>
                                </div>
                                <div class="mb-3">
                                    <?= $test['completado'] ? '<span class="badge bg-success px-3 py-2">Completado</span>' : '<span class="badge bg-warning text-dark px-3 py-2">Pendiente</span>' ?>
                                </div>
                                <div class="d-flex gap-2">
                                    <?php if (!$test['completado']): ?>
                                        <a href="realizar_test.php?test_id=<?= $test['id_test'] ?>" class="btn btn-primary w-100 rounded-pill">Realizar</a>
                                    <?php else: ?>
                                        <a href="resultados.php?test_id=<?= $test['id_test'] ?>" class="btn btn-success w-100 rounded-pill">Ver Resultado</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php require_once __DIR__ . '/../partials_cliente/footer.php'; ?>