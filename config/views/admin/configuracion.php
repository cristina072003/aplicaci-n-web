<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';

// Verificar autenticación y rol de admin
verificarAutenticacion();
verificarRol('admin');

$titulo = "Configuración del Sistema";

// Obtener estadísticas básicas del sistema
try {
    // Contar usuarios registrados
    $sqlUsuarios = "SELECT COUNT(*) as total FROM usuarios";
    $resultUsuarios = $conexion->query($sqlUsuarios);
    $totalUsuarios = $resultUsuarios ? $resultUsuarios->fetch_assoc()['total'] : 0;

    // Contar tests disponibles
    $sqlTests = "SELECT COUNT(*) as total FROM tests";
    $resultTests = $conexion->query($sqlTests);
    $totalTests = $resultTests ? $resultTests->fetch_assoc()['total'] : 0;

    // Contar tests completados
    $sqlTestsCompletados = "SELECT COUNT(*) as total FROM resultados";
    $resultCompletados = $conexion->query($sqlTestsCompletados);
    $totalCompletados = $resultCompletados ? $resultCompletados->fetch_assoc()['total'] : 0;

    // Obtener últimos tests asignados
    $sqlUltimosAsignados = "SELECT ta.*, u.nombre, u.apellido, t.nombre as test_nombre 
                           FROM tests_asignados ta
                           JOIN usuarios u ON ta.id_usuario = u.id_usuario
                           JOIN tests t ON ta.id_test = t.id_test
                           ORDER BY ta.fecha_asignacion DESC
                           LIMIT 5";
    $resultUltimosAsignados = $conexion->query($sqlUltimosAsignados);
    $ultimosAsignados = [];
    if ($resultUltimosAsignados) {
        while ($row = $resultUltimosAsignados->fetch_assoc()) {
            $ultimosAsignados[] = $row;
        }
    }
} catch (PDOException $e) {
    $error = "Error al cargar las estadísticas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        main {
            margin-top: 90px;
        }

        .stat-card {
            border-left: 4px solid #6a3093;
            transition: all 0.3s;
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(106, 48, 147, 0.07);
            background: #fff;
        }

        .stat-card:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 5px 18px rgba(106, 48, 147, 0.13);
        }

        .recent-item {
            border-left: 3px solid #6a3093;
            transition: all 0.2s;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            background: #f9fafd;
        }

        .recent-item:hover {
            background-color: #f3eaff;
        }

        .card-header {
            background: linear-gradient(90deg, #6a3093 0%, #a044ff 100%);
            color: #fff;
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
        }

        .card,
        .modal-content {
            border-radius: 1rem;
            overflow: hidden;
        }

        .form-control:focus {
            border-color: #6a3093;
            box-shadow: 0 0 0 0.2rem rgba(106, 48, 147, .15);
        }

        .btn-success,
        .btn-primary,
        .btn-outline-primary,
        .btn-outline-secondary {
            border-radius: 2rem;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .btn-outline-primary {
            border-color: #6a3093;
            color: #6a3093;
        }

        .btn-outline-primary:hover {
            background: #6a3093;
            color: #fff;
        }

        .btn-outline-secondary {
            border-color: #a044ff;
            color: #a044ff;
        }

        .btn-outline-secondary:hover {
            background: #a044ff;
            color: #fff;
        }

        .form-label {
            font-weight: 500;
        }

        @media (max-width: 991.98px) {
            main {
                padding-top: 1.5rem !important;
            }

            .card-header {
                font-size: 1.1rem;
                padding: 0.75rem 1rem;
            }

            .card-body {
                padding: 1rem;
            }
        }

        @media (max-width: 767.98px) {
            .stat-card {
                margin-bottom: 1rem;
            }

            .card-header {
                font-size: 1rem;
            }

            .btn {
                font-size: 0.97rem;
            }
        }

        @media (max-width: 575.98px) {
            .card-header {
                font-size: 0.98rem;
            }

            .btn {
                font-size: 0.95rem;
            }
        }
    </style>
</head>

<body>
    <?php include '../partials/admin_header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include '../partials/admin_sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Configuración del Sistema</h1>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <!-- Estadísticas del sistema -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Usuarios Registrados</h5>
                                <h2 class="card-text"><?= $totalUsuarios ?></h2>
                                <p class="text-muted mb-0">Total en el sistema</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Tests Disponibles</h5>
                                <h2 class="card-text"><?= $totalTests ?></h2>
                                <p class="text-muted mb-0">Total de tests</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Tests Completados</h5>
                                <h2 class="card-text"><?= $totalCompletados ?></h2>
                                <p class="text-muted mb-0">Total realizados</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración básica -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Ajustes Generales</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Nombre del Sistema</label>
                                <input type="text" class="form-control" value="Sistema Vocacional" readonly>
                                <small class="text-muted">Para cambiar el nombre, edite manualmente el código</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email de Contacto</label>
                                <input type="email" class="form-control" value="contacto@sistema.edu" readonly>
                                <small class="text-muted">Para cambiar el email, edite manualmente el código</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Roles Disponibles</label>
                                <input type="text" class="form-control" value="admin, cliente" readonly>
                                <small class="text-muted">Definidos en la base de datos</small>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Últimas asignaciones -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Últimos Tests Asignados</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php foreach ($ultimosAsignados as $asignacion): ?>
                                <div class="list-group-item recent-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= htmlspecialchars($asignacion['test_nombre']) ?></h6>
                                        <small><?= date('d/m/Y H:i', strtotime($asignacion['fecha_asignacion'])) ?></small>
                                    </div>
                                    <p class="mb-1">Asignado a: <?= htmlspecialchars($asignacion['nombre'] . ' ' . $asignacion['apellido']) ?></p>
                                    <small>Estado: <?= $asignacion['completado'] ? 'Completado' : 'Pendiente' ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Acciones del sistema -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Acciones del Sistema</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">Exportar Datos</h6>
                                        <p class="card-text">Genera un archivo con todos los datos del sistema.</p>
                                        <button class="btn btn-outline-primary" id="exportarDatos">
                                            <i class="bi bi-download me-1"></i> Exportar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">Limpiar Cache</h6>
                                        <p class="card-text">Borra archivos temporales y caché del sistema.</p>
                                        <button class="btn btn-outline-secondary" id="limpiarCache">
                                            <i class="bi bi-trash me-1"></i> Limpiar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Exportar datos
            $('#exportarDatos').click(function() {
                Swal.fire({
                    title: 'Exportar datos del sistema',
                    text: 'Esta acción generará un archivo con todos los datos actuales.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#6a3093',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Exportar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Generando archivo',
                            text: 'Por favor espere mientras se prepara la exportación...',
                            didOpen: () => {
                                Swal.showLoading();
                                // Simular exportación
                                setTimeout(() => {
                                    Swal.fire(
                                        '¡Exportación completada!',
                                        'El archivo está listo para descargar.',
                                        'success'
                                    );
                                }, 2000);
                            }
                        });
                    }
                });
            });

            // Limpiar cache
            $('#limpiarCache').click(function() {
                Swal.fire({
                    title: 'Limpiar caché del sistema',
                    text: 'Esta acción borrará todos los archivos temporales.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Limpiar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Limpiando caché',
                            text: 'Por favor espere mientras se limpian los archivos temporales...',
                            didOpen: () => {
                                Swal.showLoading();
                                // Simular limpieza
                                setTimeout(() => {
                                    Swal.fire(
                                        '¡Limpieza completada!',
                                        'La caché del sistema ha sido limpiada.',
                                        'success'
                                    );
                                }, 1500);
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>