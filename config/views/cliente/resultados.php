<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';
verificarAutenticacion();
$titulo = "Resultados de Tests";
$id_usuario = $_SESSION['id_usuario'];
$resultados = [];
$sql = "SELECT r.id_resultado, t.nombre, r.puntaje_total, r.recomendacion, r.fecha_resultado, t.id_test FROM resultados r INNER JOIN tests t ON r.id_test = t.id_test WHERE r.id_usuario = $id_usuario ORDER BY r.fecha_resultado DESC";
$result = $conexion->query($sql);
if ($result) {
    $resultados = $result->fetch_all(MYSQLI_ASSOC);
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
        <h2 class="mb-4 text-center text-primary fw-bold">Resultados de Tests</h2>
        <div class="table-responsive">
            <table class="table table-bordered align-middle shadow-sm">
                <thead class="table-primary">
                    <tr>
                        <th>Test</th>
                        <th>Puntaje Total</th>
                        <th>Recomendación</th>
                        <th>Fecha</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $res): ?>
                        <tr>
                            <td class="fw-semibold text-primary"><?= htmlspecialchars($res['nombre']) ?></td>
                            <td><?= $res['puntaje_total'] ?></td>
                            <td class="text-secondary small"><?= htmlspecialchars($res['recomendacion']) ?></td>
                            <td><?= $res['fecha_resultado'] ?></td>
                            <td><a href="ver_resultado.php?id=<?= $res['id_resultado'] ?>" class="btn btn-info btn-sm rounded-pill">Ver Detalle</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php require_once __DIR__ . '/../partials_cliente/footer.php'; ?>