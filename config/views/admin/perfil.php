<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';

// Verificar autenticación y rol de admin
verificarAutenticacion();
verificarRol('admin');

$titulo = "Perfil de Administrador";
$id_usuario = $_SESSION['id_usuario'];

// Actualizar datos si se envió el formulario
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_perfil'])) {
    $nombre = $conexion->real_escape_string(trim($_POST['nombre']));
    $apellido = $conexion->real_escape_string(trim($_POST['apellido']));
    $email = $conexion->real_escape_string(trim($_POST['email']));
    // Validación básica
    if ($nombre && $apellido && $email) {
        $sql = "UPDATE usuarios SET nombre='$nombre', apellido='$apellido', email='$email' WHERE id_usuario=$id_usuario";
        if ($conexion->query($sql)) {
            $_SESSION['nombre'] = $nombre;
            $_SESSION['apellido'] = $apellido;
            $_SESSION['email'] = $email;
            $mensaje = '<div class="alert alert-success">Datos actualizados correctamente.</div>';
        } else {
            $mensaje = '<div class="alert alert-danger">Error al actualizar: ' . $conexion->error . '</div>';
        }
    } else {
        $mensaje = '<div class="alert alert-warning">Todos los campos son obligatorios.</div>';
    }
}

// Consultar estadísticas personales
$testsRealizados = $conexion->query("SELECT COUNT(*) as total FROM resultados WHERE id_usuario = $id_usuario")->fetch_assoc()['total'];
$testsDisponibles = $conexion->query("SELECT COUNT(*) as total FROM tests")->fetch_assoc()['total'];

// Últimos tests gestionados (como admin, puede ser últimos tests creados o gestionados)
$ultimosTests = $conexion->query(
    "SELECT t.nombre, t.fecha_creacion
     FROM tests t
     ORDER BY t.fecha_creacion DESC
     LIMIT 5"
);
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
        .stat-card {
            border-left: 4px solid #0d6efd;
            transition: all 0.3s;
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.07);
            background: #fff;
        }

        .stat-card:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 5px 18px rgba(13, 110, 253, 0.13);
        }

        .recent-item {
            border-left: 3px solid #0d6efd;
            transition: all 0.2s;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            background: #f9fafd;
        }

        .recent-item:hover {
            background-color: #e9f2ff;
        }

        .card-header {
            background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);
            color: #fff;
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
        }

        .card {
            border-radius: 1rem;
            overflow: hidden;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, .15);
        }

        .btn-success,
        .btn-primary {
            border-radius: 2rem;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .modal-content {
            border-radius: 1rem;
        }

        @media (max-width: 767.98px) {
            main {
                padding-top: 1.5rem !important;
            }

            .stat-card {
                margin-bottom: 1rem;
            }

            .card-header {
                font-size: 1.1rem;
                padding: 0.75rem 1rem;
            }

            .card-body {
                padding: 1rem;
            }
        }

        @media (max-width: 575.98px) {
            .stat-card {
                font-size: 0.95rem;
            }

            .card-header {
                font-size: 1rem;
            }
        }

        /* Espacio para evitar que el header tape el contenido */
        main {
            margin-top: 70px;
        }
    </style>
</head>

<body>
    <?php include '../partials/admin_header.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <?php include '../partials/admin_sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4" style="min-height: 100vh; background: #f4f6fa;">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2 mb-0">Perfil de Administrador</h1>
                </div>
                <?= $mensaje ?>
                <!-- Mensajes de éxito/error para cambio de contraseña -->
                <?php if (isset($_GET['exito']) && $_GET['exito'] === 'pass'): ?>
                    <div class="alert alert-success">Contraseña cambiada correctamente.</div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php
                        switch ($_GET['error']) {
                            case 'campos_vacios_pw':
                                echo 'Completa todos los campos de contraseña.';
                                break;
                            case 'pass_corta':
                                echo 'La nueva contraseña debe tener al menos 8 caracteres.';
                                break;
                            case 'pass_no_coincide':
                                echo 'Las nuevas contraseñas no coinciden.';
                                break;
                            case 'pass_actual':
                                echo 'La contraseña actual es incorrecta.';
                                break;
                            case 'bd_pw':
                                echo 'Error al cambiar la contraseña.';
                                break;
                            default:
                                echo 'Error desconocido.';
                                break;
                        }
                        ?>
                    </div>
                <?php endif; ?>
                <div class="row mb-4 g-3">
                    <div class="col-12 col-md-4">
                        <div class="card stat-card h-100 text-center">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-person-circle mb-2" style="font-size:3rem;"></i>
                                <h5 class="card-title mt-2">Bienvenido</h5>
                                <h4 class="card-text mb-1"><?= $_SESSION['nombre'] . ' ' . $_SESSION['apellido'] ?></h4>
                                <p class="text-muted mb-0 small"><?= $_SESSION['email'] ?></p>
                                <span class="badge bg-primary mt-2">Administrador</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="card stat-card h-100 text-center">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-clipboard-data text-primary mb-2" style="font-size:2rem;"></i>
                                <h5 class="card-title">Tests Gestionados</h5>
                                <h2 class="card-text fw-bold text-primary mb-1"><?= $testsDisponibles ?></h2>
                                <p class="text-muted mb-0">Total en el sistema</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="card stat-card h-100 text-center">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-check2-circle text-success mb-2" style="font-size:2rem;"></i>
                                <h5 class="card-title">Tests Realizados</h5>
                                <h2 class="card-text fw-bold text-success mb-1"><?= $testsRealizados ?></h2>
                                <p class="text-muted mb-0">Completados por ti</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Información personal editable -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Información Personal</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" autocomplete="off">
                            <div class="row mb-3 g-2">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" name="nombre" id="nombre" maxlength="50" value="<?= htmlspecialchars($_SESSION['nombre']) ?>" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+" title="Solo letras y espacios">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" class="form-control" name="apellido" id="apellido" maxlength="50" value="<?= htmlspecialchars($_SESSION['apellido']) ?>" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+" title="Solo letras y espacios">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" name="email" id="email" maxlength="100" value="<?= htmlspecialchars($_SESSION['email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo de Cuenta</label>
                                <input type="text" class="form-control" value="Administrador" readonly>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" name="actualizar_perfil" class="btn btn-success flex-grow-1"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
                                <button type="button" class="btn btn-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#cambiarPasswordModal">
                                    <i class="bi bi-key me-1"></i>Cambiar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Últimos tests gestionados -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Últimos Tests Creados</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php if ($ultimosTests && $ultimosTests->num_rows > 0): ?>
                                <?php while ($row = $ultimosTests->fetch_assoc()): ?>
                                    <div class="list-group-item recent-item d-flex align-items-center">
                                        <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                        <div class="flex-grow-1">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1 fw-semibold"><?= htmlspecialchars($row['nombre']) ?></h6>
                                                <small class="text-muted"><i class="bi bi-calendar-event me-1"></i><?= date('d/m/Y H:i', strtotime($row['fecha_creacion'])) ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="list-group-item text-center">No hay tests recientes</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <!-- Modal Cambiar Contraseña -->
    <div class="modal fade" id="cambiarPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-key me-2"></i>Cambiar Contraseña</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="cambiarPasswordForm" method="post" action="../../controllers/admin/cambiar_password.php" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Contraseña Actual</label>
                            <input type="password" class="form-control" name="actual" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" name="nueva" id="nueva_password" required minlength="8" maxlength="100">
                            <small class="text-muted">Mínimo 8 y máximo 100 caracteres</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" name="confirmar" id="confirmar_password" required minlength="8" maxlength="100">
                            <div id="aviso-match-container" class="w-100 mt-1"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar advertencia al llegar al límite de caracteres
        function mostrarAdvertencia(input, max, mensaje) {
            let aviso = input.parentNode.querySelector('.aviso-limite');
            if (!aviso) {
                aviso = document.createElement('div');
                aviso.className = 'aviso-limite text-danger small';
                input.parentNode.appendChild(aviso);
            }
            if (input.value.length >= max) {
                aviso.textContent = mensaje;
            } else {
                aviso.textContent = '';
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const campos = [{
                    id: 'nombre',
                    max: 50,
                    msg: 'Máximo 50 caracteres'
                },
                {
                    id: 'apellido',
                    max: 50,
                    msg: 'Máximo 50 caracteres'
                },
                {
                    id: 'email',
                    max: 100,
                    msg: 'Máximo 100 caracteres'
                }
            ];
            campos.forEach(campo => {
                const input = document.getElementById(campo.id);
                if (input) {
                    input.addEventListener('input', function() {
                        mostrarAdvertencia(input, campo.max, campo.msg);
                    });
                }
            });
            // Validación en tiempo real para nombre y apellido (solo letras y espacios)
            function soloLetrasEspacios(e) {
                let valor = e.target.value;
                e.target.value = valor.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ ]/g, '');
            }
            const nombre = document.getElementById('nombre');
            const apellido = document.getElementById('apellido');
            if (nombre) nombre.addEventListener('input', soloLetrasEspacios);
            if (apellido) apellido.addEventListener('input', soloLetrasEspacios);

            // Cambiar contraseña (ELIMINADO: fetch innecesario, solo validación visual)
            // Validación visual de contraseñas en el modal
            const nueva = document.getElementById('nueva_password');
            const confirmar = document.getElementById('confirmar_password');
            const avisoMatchContainer = document.getElementById('aviso-match-container');
            if (nueva && confirmar && avisoMatchContainer) {
                function checkMatch() {
                    let aviso = avisoMatchContainer.querySelector('.aviso-match');
                    if (!aviso) {
                        aviso = document.createElement('div');
                        aviso.className = 'aviso-match text-danger small w-100 break-words mt-1';
                        avisoMatchContainer.appendChild(aviso);
                    }
                    if (confirmar.value && nueva.value !== confirmar.value) {
                        aviso.textContent = 'Las contraseñas no coinciden';
                    } else {
                        aviso.textContent = '';
                    }
                }
                nueva.addEventListener('input', checkMatch);
                confirmar.addEventListener('input', checkMatch);
            }
        });
    </script>
</body>

</html>