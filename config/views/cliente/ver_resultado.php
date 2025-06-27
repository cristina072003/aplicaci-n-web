<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';
verificarAutenticacion();
$titulo = "Detalle de Resultado";
$id_resultado = isset($_GET['id']) ? intval($_GET['id']) : 0;
$detalle = [];
$resumen = null;
if ($id_resultado > 0) {
    $sql = "SELECT t.nombre as test_nombre, r.puntaje_total, r.recomendacion, r.fecha_resultado FROM resultados r INNER JOIN tests t ON r.id_test = t.id_test WHERE r.id_resultado = $id_resultado";
    $result = $conexion->query($sql);
    if ($result) {
        $resumen = $result->fetch_assoc();
    }
    $sql2 = "SELECT categoria, puntaje, observacion FROM detalle_resultados WHERE id_resultado = $id_resultado";
    $result2 = $conexion->query($sql2);
    if ($result2) {
        $detalle = $result2->fetch_all(MYSQLI_ASSOC);
    }
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
        <h2 class="mb-4 text-center text-primary fw-bold">Detalle de Resultado</h2>
        <?php if ($resumen): ?>
            <div class="mb-4 p-4 rounded shadow-sm bg-white border border-2 border-primary-subtle">
                <h4 class="text-primary fw-bold mb-3"> <?= htmlspecialchars($resumen['test_nombre']) ?> </h4>
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <div class="bg-primary-subtle rounded p-3 h-100">
                            <span class="fw-semibold">Puntaje Total:</span><br>
                            <span class="fs-4 text-primary fw-bold"> <?= $resumen['puntaje_total'] ?> </span>
                        </div>
                    </div>
                    <div class="col-12 col-md-5">
                        <div class="bg-info-subtle rounded p-3 h-100">
                            <span class="fw-semibold">Recomendación:</span><br>
                            <span class="text-secondary"> <?= htmlspecialchars($resumen['recomendacion']) ?> </span>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="bg-light rounded p-3 h-100">
                            <span class="fw-semibold">Fecha:</span><br>
                            <span class="text-muted"> <?= $resumen['fecha_resultado'] ?> </span>
                        </div>
                    </div>
                </div>
            </div>
            <h5 class="mb-3 text-primary">Detalle por categoría:</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle shadow-sm">
                    <thead class="table-primary">
                        <tr>
                            <th>Categoría</th>
                            <th>Puntaje</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalle as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['categoria']) ?></td>
                                <td><?= $d['puntaje'] ?></td>
                                <td><?= htmlspecialchars($d['observacion']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-danger text-center">No se encontró el resultado solicitado.</div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php require_once __DIR__ . '/../partials_cliente/footer.php'; ?>