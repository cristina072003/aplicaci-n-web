<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';

// Verificar autenticación y rol de admin
verificarAutenticacion();
verificarRol('admin');

$titulo = "Gestión de Usuarios";

// Obtener lista de usuarios
$database = new Database();
$conexion = $database->getConnection();

$sql = "SELECT id_usuario, nombre, apellido, email, rol, fecha_registro FROM usuarios ORDER BY fecha_registro DESC";
$usuarios = [];
if ($resultado = $conexion->query($sql)) {
    while ($row = $resultado->fetch_assoc()) {
        $usuarios[] = $row;
    }
    $resultado->free();
} else {
    $error = "Error al cargar los usuarios: " . $conexion->error;
}
$conexion->close();
?>

<?php include '../partials/admin_header.php'; ?>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../partials/admin_sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4" style="margin-top: 80px;">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Gestión de Usuarios</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-primary shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                            <i class="bi bi-plus-circle me-1"></i> Nuevo Usuario
                        </button>
                    </div>
                </div>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="table-responsive rounded-3">
                            <table id="usuariosTable" class="table table-hover align-middle mb-0" style="width:100%">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Fecha Registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td class="text-secondary-emphasis small">#<?= htmlspecialchars($usuario['id_usuario']) ?></td>
                                            <td class="fw-semibold text-dark"><?= htmlspecialchars($usuario['nombre']) ?></td>
                                            <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                                            <td><span class="text-primary-emphasis"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($usuario['email']) ?></span></td>
                                            <td>
                                                <span class="badge rounded-pill px-3 py-2 <?= $usuario['rol'] === 'admin' ? 'bg-gradient text-white' : 'bg-success-subtle text-success' ?>">
                                                    <i class="bi <?= $usuario['rol'] === 'admin' ? 'bi-shield-lock' : 'bi-person' ?> me-1"></i><?= ucfirst($usuario['rol']) ?>
                                                </span>
                                            </td>
                                            <td class="small text-secondary-emphasis"><?= date('d/m/Y H:i', strtotime($usuario['fecha_registro'])) ?></td>
                                            <td class="action-buttons">
                                                <button class="btn btn-sm btn-outline-primary btn-editar me-1"
                                                    data-id="<?= $usuario['id_usuario'] ?>"
                                                    data-nombre="<?= htmlspecialchars($usuario['nombre']) ?>"
                                                    data-apellido="<?= htmlspecialchars($usuario['apellido']) ?>"
                                                    data-email="<?= htmlspecialchars($usuario['email']) ?>"
                                                    data-rol="<?= $usuario['rol'] ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editarUsuarioModal">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger btn-eliminar"
                                                    data-id="<?= $usuario['id_usuario'] ?>">
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
            </main>
        </div>
    </div>

    <!-- Modal Nuevo Usuario -->
    <div class="modal fade" id="nuevoUsuarioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-4">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title">Crear Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formNuevoUsuario" action="../../controllers/admin/usuariosController.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="crear">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="cliente">Cliente</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="8">
                            <div class="form-text">Mínimo 8 caracteres</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-4">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarUsuario" action="../../controllers/admin/usuariosController.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="actualizar">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="edit_apellido" name="apellido" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_rol" class="form-label">Rol</label>
                            <select class="form-select" id="edit_rol" name="rol" required>
                                <option value="cliente">Cliente</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Nueva Contraseña (opcional)</label>
                            <input type="password" class="form-control" id="edit_password" name="password" minlength="8">
                            <div class="form-text">Dejar en blanco para no cambiar</div>
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
    <style>
        .table-responsive {
            overflow-x: auto;
        }

        .badge-admin {
            background: linear-gradient(90deg, #6a3093 0%, #a044ff 100%);
            color: #fff;
        }

        .badge-cliente,
        .bg-success-subtle {
            background: #e6fff3;
            color: #20c997;
        }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.95rem;
            border-radius: 0.5rem;
        }

        .card {
            border-radius: 1.2rem;
        }

        .modal-content {
            border-radius: 1.2rem;
        }

        @media (max-width: 767.98px) {

            .card-body,
            .modal-content {
                padding: 0.7rem;
            }

            .table-responsive {
                font-size: 0.95rem;
            }

            .btn-toolbar {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#usuariosTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                order: [
                    [0, 'desc']
                ]
            });

            // Manejar edición de usuario
            $('.btn-editar').click(function() {
                const id = $(this).data('id');
                const nombre = $(this).data('nombre');
                const apellido = $(this).data('apellido');
                const email = $(this).data('email');
                const rol = $(this).data('rol');

                $('#edit_id').val(id);
                $('#edit_nombre').val(nombre);
                $('#edit_apellido').val(apellido);
                $('#edit_email').val(email);
                $('#edit_rol').val(rol);
            });

            // Manejar eliminación de usuario
            $('.btn-eliminar').click(function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Crear formulario dinámico para enviar la solicitud POST
                        const form = $('<form>').attr({
                            method: 'POST',
                            action: '../../controllers/admin/usuariosController.php'
                        }).append(
                            $('<input>').attr({
                                type: 'hidden',
                                name: 'action',
                                value: 'eliminar'
                            }),
                            $('<input>').attr({
                                type: 'hidden',
                                name: 'id',
                                value: id
                            })
                        ).appendTo('body');

                        form.submit();
                    }
                });
            });

            // Manejar envío de formularios con AJAX
            $('#formNuevoUsuario, #formEditarUsuario').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const formData = form.serialize();

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Éxito',
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
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Ocurrió un error al procesar la solicitud',
                            icon: 'error'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>