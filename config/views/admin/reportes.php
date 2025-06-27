<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';

verificarAutenticacion();
verificarRol('admin');

$titulo = "Reportes y Estadísticas";

// Obtener datos iniciales
try {
    $sql = "SELECT 
                COUNT(DISTINCT u.id_usuario) as total_usuarios,
                COUNT(DISTINCT t.id_test) as total_tests,
                COUNT(DISTINCT r.id_resultado) as tests_completados,
                AVG(r.puntaje_total) as promedio_puntaje
            FROM usuarios u
            CROSS JOIN tests t
            LEFT JOIN resultados r ON u.id_usuario = r.id_usuario AND t.id_test = r.id_test";
    $resultado = $conexion->query($sql);
    if ($resultado) {
        $estadisticas = $resultado->fetch_assoc();
    } else {
        $estadisticas = [
            'total_usuarios' => 0,
            'total_tests' => 0,
            'tests_completados' => 0,
            'promedio_puntaje' => 0
        ];
        $error = "Error al cargar los reportes: " . $conexion->error;
    }
} catch (PDOException $e) {
    $error = "Error al cargar los reportes: " . $e->getMessage();
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card-stat {
            border-left: 4px solid;
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 1.2rem;
            box-shadow: 0 2px 8px rgba(111, 66, 193, 0.07);
        }

        .card-stat:hover {
            transform: translateY(-5px) scale(1.03);
            box-shadow: 0 8px 24px rgba(111, 66, 193, 0.10);
        }

        .stat-1 {
            border-color: #6f42c1;
        }

        .stat-2 {
            border-color: #20c997;
        }

        .stat-3 {
            border-color: #fd7e14;
        }

        .stat-4 {
            border-color: #d63384;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 2rem;
        }

        .table-responsive {
            max-height: 400px;
            border-radius: 1.2rem;
        }

        .card,
        .modal-content {
            border-radius: 1.2rem;
        }

        .btn,
        .btn-group .btn {
            border-radius: 0.5rem;
        }

        .progress {
            height: 1.2rem;
            border-radius: 0.7rem;
        }

        .progress-bar {
            font-size: 0.9rem;
        }

        @media (max-width: 991.98px) {
            .card-stat {
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

            .chart-container {
                height: 220px;
            }
        }

        @media (max-width: 767.98px) {

            .card-header,
            .card-body {
                padding-left: 0.7rem;
                padding-right: 0.7rem;
            }

            .table-responsive {
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

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4" style="margin-top: 80px;">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Reportes y Estadísticas</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="../../controllers/admin/exportar_pdf.php" class="btn btn-sm btn-outline-secondary" id="exportarPDF" target="_blank">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
                            </a>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                                <i class="bi bi-calendar me-1"></i> Filtro de tiempo
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item filter-range" href="#" data-range="7">Últimos 7 días</a></li>
                                <li><a class="dropdown-item filter-range" href="#" data-range="30">Últimos 30 días</a></li>
                                <li><a class="dropdown-item filter-range" href="#" data-range="90">Últimos 3 meses</a></li>
                                <li><a class="dropdown-item filter-range" href="#" data-range="180">Últimos 6 meses</a></li>
                                <li><a class="dropdown-item filter-range" href="#" data-range="365">Último año</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item filter-range" href="#" data-range="all">Todo el tiempo</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <!-- Estadísticas Rápidas -->
                <div class="row mb-4" id="estadisticas-container">
                    <div class="col-md-3 mb-3">
                        <div class="card card-stat stat-1 h-100">
                            <div class="card-body">
                                <h5 class="card-title">Usuarios Registrados</h5>
                                <h2 class="card-text" id="total-usuarios"><?= $estadisticas['total_usuarios'] ?></h2>
                                <p class="text-muted mb-0">Total en el sistema</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card card-stat stat-2 h-100">
                            <div class="card-body">
                                <h5 class="card-title">Tests Disponibles</h5>
                                <h2 class="card-text" id="total-tests"><?= $estadisticas['total_tests'] ?></h2>
                                <p class="text-muted mb-0">Total de tests</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card card-stat stat-3 h-100">
                            <div class="card-body">
                                <h5 class="card-title">Tests Completados</h5>
                                <h2 class="card-text" id="tests-completados"><?= $estadisticas['tests_completados'] ?></h2>
                                <p class="text-muted mb-0">Total realizados</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card card-stat stat-4 h-100">
                            <div class="card-body">
                                <h5 class="card-title">Puntaje Promedio</h5>
                                <h2 class="card-text" id="promedio-puntaje"><?= round($estadisticas['promedio_puntaje'], 1) ?></h2>
                                <p class="text-muted mb-0">En todos los tests</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Tests Completados por Mes</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartTestsPorMes"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Tests Más Populares</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartTestsPopulares"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de últimos resultados -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Últimos Resultados Registrados</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="resultadosTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th>Test</th>
                                        <th>Puntaje</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="ultimos-resultados-body">
                                    <!-- Datos cargados por AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Reporte de tests populares -->
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tests Más Realizados</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Test</th>
                                        <th>Veces Completado</th>
                                        <th>Porcentaje</th>
                                        <th>Puntaje Promedio</th>
                                    </tr>
                                </thead>
                                <tbody id="tests-populares-body">
                                    <!-- Datos cargados por AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal para ver detalle de resultado -->
    <div class="modal fade" id="detalleResultadoModal" tabindex="-1" aria-labelledby="detalleResultadoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detalleResultadoLabel">Detalle del Resultado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Fecha:</strong> <span id="detalle-fecha"></span></li>
                        <li class="list-group-item"><strong>Usuario:</strong> <span id="detalle-usuario"></span></li>
                        <li class="list-group-item"><strong>Test:</strong> <span id="detalle-test"></span></li>
                        <li class="list-group-item"><strong>Puntaje:</strong> <span id="detalle-puntaje"></span></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
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
            // Variables globales para los gráficos
            let chartTestsMes, chartTestsPopulares;
            let currentRange = '30'; // Rango por defecto

            // Inicializar DataTable
            const resultadosTable = $('#resultadosTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'fecha'
                    },
                    {
                        data: 'usuario'
                    },
                    {
                        data: 'test'
                    },
                    {
                        data: 'puntaje'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `<button class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Ver Detalle
                                    </button>`;
                        }
                    }
                ]
            });

            // Función para cargar todos los datos
            function cargarDatos(range) {
                currentRange = range;
                cargarEstadisticas();
                cargarDatosMensuales();
                cargarTestsPopulares();
                cargarUltimosResultados();
            }

            // Cargar estadísticas generales
            function cargarEstadisticas() {
                $.ajax({
                    url: '../../controllers/admin/ReportesController.php?action=estadisticas-generales&range=' + currentRange,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#total-usuarios').text(response.data.total_usuarios);
                            $('#total-tests').text(response.data.total_tests);
                            $('#tests-completados').text(response.data.tests_completados);
                            $('#promedio-puntaje').text(response.data.promedio_puntaje ? response.data.promedio_puntaje.toFixed(1) : '0');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al cargar estadísticas', 'error');
                    }
                });
            }

            // Cargar datos mensuales para gráfico
            function cargarDatosMensuales() {
                $.ajax({
                    url: '../../controllers/admin/ReportesController.php?action=datos-mensuales&range=' + currentRange,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            actualizarGraficoTestsMes(response.data);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al cargar datos mensuales', 'error');
                    }
                });
            }

            // Cargar tests populares
            function cargarTestsPopulares() {
                $.ajax({
                    url: '../../controllers/admin/ReportesController.php?action=tests-populares&range=' + currentRange,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            actualizarGraficoTestsPopulares(response.data);
                            actualizarTablaTestsPopulares(response.data);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al cargar tests populares', 'error');
                    }
                });
            }

            // Cargar últimos resultados
            function cargarUltimosResultados() {
                $.ajax({
                    url: '../../controllers/admin/ReportesController.php?action=ultimos-resultados&range=' + currentRange,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            actualizarTablaUltimosResultados(response.data);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al cargar últimos resultados', 'error');
                    }
                });
            }

            // Actualizar gráfico de tests por mes
            function actualizarGraficoTestsMes(datos) {
                const ctx = document.getElementById('chartTestsPorMes').getContext('2d');

                if (chartTestsMes) {
                    chartTestsMes.destroy();
                }

                chartTestsMes = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: datos.map(item => item.mes),
                        datasets: [{
                            label: 'Tests Completados',
                            data: datos.map(item => item.cantidad),
                            borderColor: '#6f42c1',
                            backgroundColor: 'rgba(111, 66, 193, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }

            // Actualizar gráfico de tests populares
            function actualizarGraficoTestsPopulares(datos) {
                const ctx = document.getElementById('chartTestsPopulares').getContext('2d');
                const total = datos.reduce((sum, item) => sum + item.completados, 0);

                if (chartTestsPopulares) {
                    chartTestsPopulares.destroy();
                }

                chartTestsPopulares = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: datos.map(item => item.nombre),
                        datasets: [{
                            data: datos.map(item => item.completados),
                            backgroundColor: [
                                '#6f42c1',
                                '#20c997',
                                '#fd7e14',
                                '#d63384',
                                '#6c757d'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw;
                                        const percentage = Math.round((value / total) * 100);
                                        return `${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Actualizar tabla de tests populares
            function actualizarTablaTestsPopulares(datos) {
                const totalCompletados = datos.reduce((sum, item) => sum + parseInt(item.completados), 0);
                let html = '';

                if (datos.length === 0) {
                    html = `<tr><td colspan="4" class="text-center text-muted">Sin datos para mostrar</td></tr>`;
                } else {
                    datos.forEach(item => {
                        const porcentaje = totalCompletados > 0 ? (item.completados / totalCompletados) * 100 : 0;
                        html += `
                            <tr>
                                <td>${item.nombre}</td>
                                <td>${item.completados}</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar" 
                                             role="progressbar" 
                                             style="width: ${porcentaje}%" 
                                             aria-valuenow="${item.completados}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="${totalCompletados}">
                                        </div>
                                    </div>
                                    ${porcentaje.toFixed(1)}%
                                </td>
                                <td>${item.promedio_puntaje && !isNaN(item.promedio_puntaje) ? Number(item.promedio_puntaje).toFixed(1) : 'N/A'}</td>
                            </tr>
                        `;
                    });
                }

                $('#tests-populares-body').html(html);
            }

            // Actualizar tabla de últimos resultados
            function actualizarTablaUltimosResultados(datos) {
                const rows = datos.map(item => ({
                    fecha: new Date(item.fecha_resultado).toLocaleDateString('es-ES', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }),
                    usuario: `${item.nombre} ${item.apellido}`,
                    test: item.test,
                    puntaje: item.puntaje_total
                }));

                resultadosTable.clear().rows.add(rows).draw();
            }

            // Evento para mostrar el modal de detalle
            $('#resultadosTable tbody').on('click', 'button', function() {
                var data = resultadosTable.row($(this).parents('tr')).data();
                $('#detalle-fecha').text(data.fecha);
                $('#detalle-usuario').text(data.usuario);
                $('#detalle-test').text(data.test);
                $('#detalle-puntaje').text(data.puntaje);
                var modal = new bootstrap.Modal(document.getElementById('detalleResultadoModal'));
                modal.show();
            });

            // Filtro de tiempo
            $('.filter-range').click(function(e) {
                e.preventDefault();
                const range = $(this).data('range');

                Swal.fire({
                    title: 'Cargando datos',
                    text: `Filtrando resultados de los últimos ${range === 'all' ? 'todos los tiempos' : range + ' días'}`,
                    didOpen: () => {
                        Swal.showLoading();
                        cargarDatos(range);
                    },
                    timer: 1500,
                    showConfirmButton: false
                });
            });

            // Cargar datos iniciales
            cargarDatos(currentRange);
        });
    </script>
</body>

</html>