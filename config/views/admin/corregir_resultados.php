<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';
verificarAutenticacion();
verificarRol('admin');

// Obtener resultados de tests para corrección
$sql = "SELECT r.id_resultado, r.puntaje_total, r.recomendacion, r.fecha_resultado, u.nombre, u.apellido, t.nombre as test_nombre
        FROM resultados r
        INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
        INNER JOIN tests t ON r.id_test = t.id_test
        ORDER BY r.fecha_resultado DESC";
$result = $conexion->query($sql);
$resultados = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$titulo = "Corregir Resultados de Tests";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include '../partials/admin_header.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <?php include '../partials/admin_sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4" style="margin-top: 80px;">
                <h2 class="mb-4">Corregir Resultados de Tests</h2>
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Test</th>
                                        <th>Puntaje</th>
                                        <th>Recomendación</th>
                                        <th>Fecha</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resultados as $res): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($res['nombre'] . ' ' . $res['apellido']) ?></td>
                                            <td><?= htmlspecialchars($res['test_nombre']) ?></td>
                                            <td><input type="number" class="form-control form-control-sm puntaje-input" value="<?= $res['puntaje_total'] ?>" data-id="<?= $res['id_resultado'] ?>"></td>
                                            <td><input type="text" class="form-control form-control-sm recomendacion-input" value="<?= htmlspecialchars($res['recomendacion']) ?>" data-id="<?= $res['id_resultado'] ?>"></td>
                                            <td><?= date('d/m/Y H:i', strtotime($res['fecha_resultado'])) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-success btn-guardar-correccion" data-id="<?= $res['id_resultado'] ?>">Guardar</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.btn-guardar-correccion').click(function() {
                const id = $(this).data('id');
                const puntaje = $(".puntaje-input[data-id='" + id + "']").val();
                const recomendacion = $(".recomendacion-input[data-id='" + id + "']").val();
                $.ajax({
                    url: '../../controllers/admin/respuestasController.php',
                    method: 'POST',
                    data: {
                        action: 'corregir',
                        id_resultado: id,
                        puntaje_total: puntaje,
                        recomendacion: recomendacion
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: '¡Éxito!',
                                text: response.message,
                                icon: 'success'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un problema al guardar la corrección',
                            icon: 'error'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>