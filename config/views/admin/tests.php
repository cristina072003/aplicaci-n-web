<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';

// Verificar autenticación y rol de admin
verificarAutenticacion();
verificarRol('admin');

$titulo = "Gestión de Tests Vocacionales";

// Obtener lista de tests
try {
    $sql = "SELECT t.id_test, t.nombre, t.descripcion, t.duracion_min, 
                   COUNT(p.id_pregunta) as num_preguntas,
                   COUNT(DISTINCT ta.id_asignacion) as veces_asignado
            FROM tests t
            LEFT JOIN preguntas p ON t.id_test = p.id_test
            LEFT JOIN tests_asignados ta ON t.id_test = ta.id_test
            GROUP BY t.id_test
            ORDER BY t.nombre";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $tests = [];
    $result = $stmt->get_result();
    if ($result) {
        $tests = $result->fetch_all(MYSQLI_ASSOC);
    }
} catch (PDOException $e) {
    $error = "Error al cargar los tests: " . $e->getMessage();
}
?>

<?php include '../partials/admin_header.php'; ?>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../partials/admin_sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4" style="margin-top: 80px;">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Tests Vocacionales</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-primary shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#nuevoTestModal">
                            <i class="bi bi-plus-circle me-1"></i> Nuevo Test
                        </button>
                    </div>
                </div>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <!-- Vista de tarjetas -->
                <div class="row mb-4 g-3">
                    <?php foreach ($tests as $test): ?>
                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <div class="card card-test h-100 border-0 shadow-sm rounded-4">
                                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 rounded-top-4">
                                    <h5 class="mb-0 text-primary-emphasis fw-bold"><?= htmlspecialchars($test['nombre']) ?></h5>
                                    <span class="badge rounded-pill <?= getTestBadgeClass($test['nombre']) ?> px-3 py-2">
                                        <?= strtoupper($test['nombre']) ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <p class="card-text text-secondary-emphasis small mb-3"><?= htmlspecialchars($test['descripcion']) ?></p>
                                    <ul class="list-group list-group-flush mb-3">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Duración
                                            <span class="badge bg-primary rounded-pill"><?= $test['duracion_min'] ?> min</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Preguntas
                                            <span class="badge bg-primary rounded-pill"><?= $test['num_preguntas'] ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Asignaciones
                                            <span class="badge bg-primary rounded-pill"><?= $test['veces_asignado'] ?></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-footer bg-transparent d-flex flex-wrap gap-2 justify-content-between border-0">
                                    <button class="btn btn-sm btn-outline-primary btn-editar-test"
                                        data-id="<?= $test['id_test'] ?>"
                                        data-nombre="<?= htmlspecialchars($test['nombre']) ?>"
                                        data-descripcion="<?= htmlspecialchars($test['descripcion']) ?>"
                                        data-duracion="<?= $test['duracion_min'] ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarTestModal">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>
                                    <button class="btn btn-sm btn-outline-success"
                                        onclick="window.location.href='preguntas.php?test_id=<?= $test['id_test'] ?>'">
                                        <i class="bi bi-question-circle"></i> Preguntas
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-eliminar-test"
                                        data-id="<?= $test['id_test'] ?>"
                                        data-nombre="<?= htmlspecialchars($test['nombre']) ?>">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- Vista de tabla (alternativa) -->
                <div class="card shadow-sm d-none d-lg-block border-0 rounded-4">
                    <div class="card-body">
                        <div class="table-responsive rounded-3">
                            <table id="testsTable" class="table table-striped table-hover align-middle mb-0" style="width:100%">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Duración (min)</th>
                                        <th>Preguntas</th>
                                        <th>Asignaciones</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tests as $test): ?>
                                        <tr>
                                            <td><?= $test['id_test'] ?></td>
                                            <td>
                                                <span class="badge rounded-pill <?= getTestBadgeClass($test['nombre']) ?> me-1">
                                                    <?= strtoupper($test['nombre']) ?>
                                                </span>
                                                <?= htmlspecialchars($test['nombre']) ?>
                                            </td>
                                            <td><?= htmlspecialchars($test['descripcion']) ?></td>
                                            <td><?= $test['duracion_min'] ?></td>
                                            <td><?= $test['num_preguntas'] ?></td>
                                            <td><?= $test['veces_asignado'] ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary btn-editar-test"
                                                        data-id="<?= $test['id_test'] ?>"
                                                        data-nombre="<?= htmlspecialchars($test['nombre']) ?>"
                                                        data-descripcion="<?= htmlspecialchars($test['descripcion']) ?>"
                                                        data-duracion="<?= $test['duracion_min'] ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editarTestModal">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="preguntas.php?test_id=<?= $test['id_test'] ?>" class="btn btn-outline-success">
                                                        <i class="bi bi-question-circle"></i>
                                                    </a>
                                                    <button class="btn btn-outline-danger btn-eliminar-test"
                                                        data-id="<?= $test['id_test'] ?>"
                                                        data-nombre="<?= htmlspecialchars($test['nombre']) ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
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

    <!-- Modal Nuevo Test -->
    <div class="modal fade" id="nuevoTestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Nuevo Test</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formNuevoTest" action="../../controllers/admin/testsController.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="crear">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Test *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                            <small class="text-muted">Ej: IPPR, CHASIDE, DAT</small>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción *</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="duracion_min" class="form-label">Duración estimada (minutos) *</label>
                            <input type="number" class="form-control" id="duracion_min" name="duracion_min" min="5" max="180" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Test</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Test -->
    <div class="modal fade" id="editarTestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Test</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarTest" action="../../controllers/admin/testsController.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="actualizar">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label">Nombre del Test *</label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_descripcion" class="form-label">Descripción *</label>
                            <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_duracion_min" class="form-label">Duración estimada (minutos) *</label>
                            <input type="number" class="form-control" id="edit_duracion_min" name="duracion_min" min="5" max="180" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#testsTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                order: [
                    [1, 'asc']
                ]
            });

            // Manejar edición de test
            $('.btn-editar-test').click(function() {
                const id = $(this).data('id');
                const nombre = $(this).data('nombre');
                const descripcion = $(this).data('descripcion');
                const duracion = $(this).data('duracion');

                $('#edit_id').val(id);
                $('#edit_nombre').val(nombre);
                $('#edit_descripcion').val(descripcion);
                $('#edit_duracion_min').val(duracion);
            });

            // Manejar eliminación de test
            $('.btn-eliminar-test').click(function() {
                const id = $(this).data('id');
                const nombre = $(this).data('nombre');

                Swal.fire({
                    title: '¿Eliminar Test?',
                    html: `¿Estás seguro de eliminar el test <b>${nombre}</b>?<br><br>
                          <small>Todas las preguntas y resultados asociados también se eliminarán.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '../../controllers/admin/testsController.php',
                            method: 'POST',
                            data: {
                                action: 'eliminar',
                                id: id
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: '¡Eliminado!',
                                        text: response.message,
                                        icon: 'success'
                                    }).then(() => {
                                        location.reload();
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
                                    text: 'Hubo un problema al eliminar el test',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });

            // Manejar envío de formularios con AJAX
            $('#formNuevoTest, #formEditarTest').submit(function(e) {
                e.preventDefault();
                const form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: '¡Éxito!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un problema al procesar la solicitud',
                            icon: 'error'
                        });
                    }
                });
            });
        });
    </script>

    <style>
        .card-test {
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            border-radius: 1.2rem;
        }

        .card-test:hover {
            transform: translateY(-5px) scale(1.03);
            box-shadow: 0 10px 24px rgba(111, 66, 193, 0.10);
        }

        .badge-ippr {
            background: linear-gradient(90deg, #6a3093 0%, #a044ff 100%);
            color: #fff;
        }

        .badge-chaside {
            background: #20c997;
            color: #fff;
        }

        .badge-dat {
            background: #fd7e14;
            color: #fff;
        }

        .badge-custom {
            background: #6c757d;
            color: #fff;
        }

        .card {
            border-radius: 1.2rem;
        }

        .modal-content {
            border-radius: 1.2rem;
        }

        .btn {
            border-radius: 0.5rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        @media (max-width: 991.98px) {
            .card-test {
                margin-bottom: 1.2rem;
            }

            .card-body,
            .modal-content {
                padding: 0.7rem;
            }

            .btn-toolbar {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</body>

</html>

<?php
// Función auxiliar para determinar la clase del badge según el nombre del test
function getTestBadgeClass($nombreTest)
{
    $nombreTest = strtolower($nombreTest);
    if (strpos($nombreTest, 'ippr') !== false) return 'badge-ippr';
    if (strpos($nombreTest, 'chaside') !== false) return 'badge-chaside';
    if (strpos($nombreTest, 'dat') !== false) return 'badge-dat';
    return 'badge-custom';
}
?>