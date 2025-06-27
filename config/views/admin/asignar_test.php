<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';
verificarAutenticacion();
verificarRol('admin');

$titulo = 'Asignar Test a Usuarios';

// Obtener usuarios (solo clientes)
$sql_usuarios = "SELECT id_usuario, nombre, apellido, email FROM usuarios WHERE rol = 'cliente' ORDER BY nombre, apellido";
$result_usuarios = $conexion->query($sql_usuarios);

// Obtener tests
$sql_tests = "SELECT id_test, nombre FROM tests ORDER BY nombre";
$result_tests = $conexion->query($sql_tests);

// Procesar asignación
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'], $_POST['id_test'])) {
    $id_usuario = intval($_POST['id_usuario']);
    $id_test = intval($_POST['id_test']);
    // Verificar si ya está asignado
    $sql_check = "SELECT id_asignacion FROM tests_asignados WHERE id_usuario = ? AND id_test = ?";
    $stmt = $conexion->prepare($sql_check);
    $stmt->bind_param('ii', $id_usuario, $id_test);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $mensaje = '<div class="alert alert-warning">El test ya está asignado a este usuario.</div>';
    } else {
        $sql_insert = "INSERT INTO tests_asignados (id_usuario, id_test) VALUES (?, ?)";
        $stmt_insert = $conexion->prepare($sql_insert);
        $stmt_insert->bind_param('ii', $id_usuario, $id_test);
        if ($stmt_insert->execute()) {
            $mensaje = '<div class="alert alert-success">Test asignado correctamente.</div>';
        } else {
            $mensaje = '<div class="alert alert-danger">Error al asignar el test.</div>';
        }
        $stmt_insert->close();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include '../partials/admin_header.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <?php include '../partials/admin_sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4" style="margin-top: 80px;">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-8">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body p-4">
                                <h2 class="mb-4 text-primary-emphasis fw-bold text-center">Asignar Test a Usuario</h2>
                                <?= $mensaje ?>
                                <form method="post" class="row g-3">
                                    <div class="col-md-6">
                                        <label for="id_usuario" class="form-label">Usuario</label>
                                        <select name="id_usuario" id="id_usuario" class="form-select" required>
                                            <option value="">Seleccione un usuario</option>
                                            <?php while ($usuario = $result_usuarios->fetch_assoc()): ?>
                                                <option value="<?= $usuario['id_usuario'] ?>">
                                                    <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido'] . ' (' . $usuario['email'] . ')') ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="id_test" class="form-label">Test</label>
                                        <select name="id_test" id="id_test" class="form-select" required>
                                            <option value="">Seleccione un test</option>
                                            <?php while ($test = $result_tests->fetch_assoc()): ?>
                                                <option value="<?= $test['id_test'] ?>">
                                                    <?= htmlspecialchars($test['nombre']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">Asignar Test</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <style>
        .card {
            border-radius: 1.2rem;
        }

        .card-body {
            padding: 2rem;
        }

        .btn {
            border-radius: 0.5rem;
        }

        @media (max-width: 767.98px) {
            .card-body {
                padding: 1rem;
            }

            h2 {
                font-size: 1.3rem;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>