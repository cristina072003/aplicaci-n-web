<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';

// Verificar autenticación y rol de admin
verificarAutenticacion();
verificarRol('admin');

$test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : 0;
if ($test_id <= 0) {
    die('ID de test no válido.');
}

// Obtener datos del test
$stmt = $conexion->prepare('SELECT nombre, descripcion FROM tests WHERE id_test = ?');
$stmt->bind_param('i', $test_id);
$stmt->execute();
$result = $stmt->get_result();
$test = $result->fetch_assoc();
if (!$test) {
    die('Test no encontrado.');
}

// Obtener preguntas del test
$stmt = $conexion->prepare('SELECT id_pregunta, texto, tipo FROM preguntas WHERE id_test = ? ORDER BY id_pregunta');
$stmt->bind_param('i', $test_id);
$stmt->execute();
$preguntas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$titulo = 'Preguntas del Test: ' . htmlspecialchars($test['nombre']);
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
    <style>
        main {
            margin-top: 70px;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.07);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);
            color: #fff;
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
        }

        .table {
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .table thead {
            background: #0d6efd;
            color: #fff;
        }

        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #f8fafd;
        }

        .btn-primary,
        .btn-success,
        .btn-danger,
        .btn-secondary,
        .btn-outline-primary,
        .btn-outline-danger {
            border-radius: 2rem;
            padding-left: 1.2rem;
            padding-right: 1.2rem;
        }

        .input-group .form-control {
            border-radius: 2rem 0 0 2rem;
        }

        .input-group-text {
            border-radius: 0 2rem 2rem 0;
        }

        .opcion-item {
            background: #f4f8ff;
            border-radius: 1rem;
            box-shadow: 0 1px 4px rgba(13, 110, 253, 0.06);
        }

        .modal-content {
            border-radius: 1rem;
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
            .table-responsive {
                font-size: 0.97rem;
            }

            .btn {
                font-size: 0.97rem;
            }

            .card-header {
                font-size: 1rem;
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
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4" style="margin-top: 80px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2><?= $titulo ?></h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaPreguntaModal">
                        <i class="bi bi-plus-circle me-1"></i> Nueva Pregunta
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="preguntasTable" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Texto</th>
                                        <th>Tipo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($preguntas as $pregunta): ?>
                                        <tr>
                                            <td><?= $pregunta['id_pregunta'] ?></td>
                                            <td><?= htmlspecialchars($pregunta['texto']) ?></td>
                                            <td><?= htmlspecialchars($pregunta['tipo']) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary btn-editar-pregunta"
                                                    data-id="<?= $pregunta['id_pregunta'] ?>"
                                                    data-texto="<?= htmlspecialchars($pregunta['texto']) ?>"
                                                    data-tipo="<?= htmlspecialchars($pregunta['tipo']) ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editarPreguntaModal">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger btn-eliminar-pregunta"
                                                    data-id="<?= $pregunta['id_pregunta'] ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Agregar opciones a preguntas -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Agregar Opción a Pregunta</h5>
                    </div>
                    <div class="card-body">
                        <form id="formAgregarOpcion">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Pregunta</label>
                                    <select class="form-select" name="id_pregunta" required>
                                        <option value="">Seleccione una pregunta</option>
                                        <?php foreach ($preguntas as $pregunta): ?>
                                            <option value="<?= $pregunta['id_pregunta'] ?>">#<?= $pregunta['id_pregunta'] ?> - <?= htmlspecialchars($pregunta['texto']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Texto de la Opción</label>
                                    <input type="text" class="form-control" name="texto" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">¿Respuesta Correcta?</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="es_correcta" id="es_correcta">
                                        <label class="form-check-label" for="es_correcta">Sí</label>
                                    </div>
                                </div>
                                <div class="col-md-2 d-none">
                                    <label class="form-label">Valor</label>
                                    <input type="number" class="form-control" name="valor" id="valor_opcion" value="0">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success">Agregar Opción</button>
                                </div>
                            </div>
                            <input type="hidden" name="action" value="agregar_opcion">
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Nueva Pregunta -->
    <div class="modal fade" id="nuevaPreguntaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Pregunta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formNuevaPregunta" action="../../controllers/admin/preguntasController.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="crear">
                        <input type="hidden" name="test_id" value="<?= $test_id ?>">
                        <div class="mb-3">
                            <label for="texto" class="form-label">Texto de la Pregunta *</label>
                            <textarea class="form-control" id="texto" name="texto" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo *</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="opcion_multiple">Opción Múltiple</option>
                                <option value="abierta">Abierta</option>
                            </select>
                        </div>
                        <div id="opciones-multiples" class="mb-3">
                            <label class="form-label">Opciones</label>
                            <div id="opciones-lista">
                                <div class="input-group mb-2 opcion-item">
                                    <input type="text" class="form-control" name="opciones[0][texto]" placeholder="Texto de la opción" required>
                                    <div class="input-group-text">
                                        <input type="radio" name="opcion_correcta" value="0" required title="Marcar como correcta">
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm btn-quitar-opcion"><i class="bi bi-x"></i></button>
                                </div>
                                <div class="input-group mb-2 opcion-item">
                                    <input type="text" class="form-control" name="opciones[1][texto]" placeholder="Texto de la opción" required>
                                    <div class="input-group-text">
                                        <input type="radio" name="opcion_correcta" value="1" required title="Marcar como correcta">
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm btn-quitar-opcion"><i class="bi bi-x"></i></button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnAgregarOpcion"><i class="bi bi-plus"></i> Agregar Opción</button>
                            <div class="form-text">Marca el círculo para indicar la respuesta correcta.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Pregunta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Pregunta -->
    <div class="modal fade" id="editarPreguntaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Pregunta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarPregunta" action="../../controllers/admin/preguntasController.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="actualizar">
                        <input type="hidden" id="edit_id_pregunta" name="id_pregunta">
                        <input type="hidden" name="test_id" value="<?= $test_id ?>">
                        <div class="mb-3">
                            <label for="edit_texto" class="form-label">Texto de la Pregunta *</label>
                            <textarea class="form-control" id="edit_texto" name="texto" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_tipo" class="form-label">Tipo *</label>
                            <select class="form-select" id="edit_tipo" name="tipo" required>
                                <option value="opcion_multiple">Opción Múltiple</option>
                                <option value="abierta">Abierta</option>
                            </select>
                        </div>
                        <div id="editar-opciones-multiples" class="mb-3">
                            <label class="form-label">Opciones</label>
                            <div id="editar-opciones-lista"></div>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnEditarAgregarOpcion"><i class="bi bi-plus"></i> Agregar Opción</button>
                            <div class="form-text">Marca el círculo para indicar la respuesta correcta.</div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#preguntasTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                order: [
                    [0, 'asc']
                ]
            });

            // Editar pregunta
            $('.btn-editar-pregunta').click(function() {
                const id = $(this).data('id');
                const texto = $(this).data('texto');
                const tipo = $(this).data('tipo');
                $('#edit_id_pregunta').val(id);
                $('#edit_texto').val(texto);
                $('#edit_tipo').val(tipo);
                if (tipo === 'opcion_multiple') {
                    $('#editar-opciones-multiples').show();
                    // Cargar opciones vía AJAX
                    $.getJSON('../../controllers/admin/preguntasController.php', {
                        action: 'get_opciones',
                        id_pregunta: id
                    }, function(data) {
                        let html = '';
                        if (data && data.length) {
                            data.forEach(function(op, idx) {
                                html += `<div class=\"input-group mb-2 opcion-item\">\n` +
                                    `<input type=\"text\" class=\"form-control\" name=\"editar_opciones[${idx}][texto]\" value=\"${op.texto.replace(/"/g, '&quot;')}\" required>\n` +
                                    `<div class=\"input-group-text\">\n` +
                                    `<input type=\"radio\" name=\"editar_opcion_correcta\" value=\"${idx}\" ${op.valor == 1 ? 'checked' : ''} required title=\"Marcar como correcta\">\n` +
                                    `</div>\n` +
                                    `<button type=\"button\" class=\"btn btn-danger btn-sm btn-quitar-opcion\"><i class=\"bi bi-x\"></i></button>\n` +
                                    `</div>`;
                            });
                        }
                        $('#editar-opciones-lista').html(html);
                    });
                } else {
                    $('#editar-opciones-multiples').hide();
                }
            });

            // Eliminar pregunta
            $('.btn-eliminar-pregunta').click(function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: '¿Eliminar Pregunta?',
                    text: '¿Estás seguro de eliminar esta pregunta?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '../../controllers/admin/preguntasController.php',
                            method: 'POST',
                            data: {
                                action: 'eliminar',
                                id_pregunta: id,
                                test_id: <?= $test_id ?>
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
                                    text: 'Hubo un problema al eliminar la pregunta',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });

            // Envío de formularios con AJAX
            $('#formNuevaPregunta, #formEditarPregunta').submit(function(e) {
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
                    error: function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un problema al procesar la solicitud',
                            icon: 'error'
                        });
                    }
                });
            });

            // Agregar opción a pregunta
            $('#formAgregarOpcion').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                // Si el checkbox está marcado, valor=1, si no, valor=0
                if ($('#es_correcta').is(':checked')) {
                    $('#valor_opcion').val(1);
                } else {
                    $('#valor_opcion').val(0);
                }
                $.ajax({
                    url: '../../controllers/admin/respuestasController.php',
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
                    error: function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un problema al agregar la opción',
                            icon: 'error'
                        });
                    }
                });
            });

            // Mostrar/ocultar opciones según tipo
            $('#tipo').on('change', function() {
                if ($(this).val() === 'opcion_multiple') {
                    $('#opciones-multiples').show();
                    $('#opciones-multiples input').prop('required', true);
                } else {
                    $('#opciones-multiples').hide();
                    $('#opciones-multiples input').prop('required', false);
                }
            }).trigger('change');

            // Agregar nueva opción
            let opcionIndex = 2;
            $('#btnAgregarOpcion').click(function() {
                const html = `<div class="input-group mb-2 opcion-item">
                    <input type="text" class="form-control" name="opciones[${opcionIndex}][texto]" placeholder="Texto de la opción" required>
                    <div class="input-group-text">
                        <input type="radio" name="opcion_correcta" value="${opcionIndex}" required title="Marcar como correcta">
                    </div>
                    <button type="button" class="btn btn-danger btn-sm btn-quitar-opcion"><i class="bi bi-x"></i></button>
                </div>`;
                $('#opciones-lista').append(html);
                opcionIndex++;
            });
            // Quitar opción
            $(document).on('click', '.btn-quitar-opcion', function() {
                $(this).closest('.opcion-item').remove();
                // Si quedan menos de 2, agrega una vacía
                if ($('#opciones-lista .opcion-item').length < 2) {
                    $('#btnAgregarOpcion').trigger('click');
                }
            });
            // Al abrir modal, resetear opciones
            $('#nuevaPreguntaModal').on('show.bs.modal', function() {
                $('#opciones-lista').html('');
                for (let i = 0; i < 2; i++) {
                    $('#btnAgregarOpcion').trigger('click');
                }
            });
            // Agregar nueva opción en edición
            let editarOpcionIndex = 2;
            $('#btnEditarAgregarOpcion').click(function() {
                const idx = $('#editar-opciones-lista .opcion-item').length;
                const html = `<div class=\"input-group mb-2 opcion-item\">\n` +
                    `<input type=\"text\" class=\"form-control\" name=\"editar_opciones[${idx}][texto]\" placeholder=\"Texto de la opción\" required>\n` +
                    `<div class=\"input-group-text\">\n` +
                    `<input type=\"radio\" name=\"editar_opcion_correcta\" value=\"${idx}\" required title=\"Marcar como correcta\">\n` +
                    `</div>\n` +
                    `<button type=\"button\" class=\"btn btn-danger btn-sm btn-quitar-opcion\"><i class=\"bi bi-x\"></i></button>\n` +
                    `</div>`;
                $('#editar-opciones-lista').append(html);
            });
            // Quitar opción en edición
            $(document).on('click', '#editar-opciones-lista .btn-quitar-opcion', function() {
                $(this).closest('.opcion-item').remove();
            });
            // Mostrar/ocultar opciones en edición según tipo
            $('#edit_tipo').on('change', function() {
                if ($(this).val() === 'opcion_multiple') {
                    $('#editar-opciones-multiples').show();
                } else {
                    $('#editar-opciones-multiples').hide();
                }
            });
        });
    </script>
</body>

</html>