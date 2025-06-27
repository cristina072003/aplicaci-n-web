<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';

// Verificar autenticación
verificarAutenticacion();

$titulo = "Mi Perfil";

// Obtener tests disponibles desde la base de datos
$tests = [];
$sql = "SELECT t.id_test, t.nombre, t.descripcion, COUNT(p.id_pregunta) as num_preguntas FROM tests t LEFT JOIN preguntas p ON t.id_test = p.id_test GROUP BY t.id_test ORDER BY t.nombre";
$result = $conexion->query($sql);
if ($result) {
    $tests = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
            color: white;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(106, 115, 255, 0.15);
            position: relative;
            overflow: hidden;
        }

        .profile-avatar {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            margin-bottom: 1rem;
            background: #fff;
        }

        .profile-header .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40px;
            background: url('data:image/svg+xml;utf8,<svg width="100%25" height="100%25" viewBox="0 0 1200 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,0V46.29c47.29,22.09,104.09,29,158,17.39C230.87,51.6,284.09,17.39,339,6.13c54.91-11.26,104.09,6.13,158,17.39,53.91,11.26,107.13,17.39,158,6.13,50.87-11.26,104.09-35.48,158-40.61,53.91-5.13,107.13,11.26,158,17.39,50.87,6.13,104.09,0,158-6.13,53.91-6.13,107.13-12.26,158-6.13V0Z" opacity=".25" fill="%23fff"/></svg>') repeat-x;
            background-size: cover;
            z-index: 1;
        }

        .profile-header h3,
        .profile-header p {
            position: relative;
            z-index: 2;
        }

        .profile-card {
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            border: none;
        }

        .profile-form input:focus {
            border-color: #6B73FF;
            box-shadow: 0 0 0 0.2rem rgba(107, 115, 255, 0.15);
        }

        .profile-btn {
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(107, 115, 255, 0.08);
            transition: all 0.2s;
        }

        .profile-btn-success {
            background: linear-gradient(90deg, #6B73FF 0%, #000DFF 100%);
            color: #fff;
            border: none;
        }

        .profile-btn-success:hover {
            background: linear-gradient(90deg, #000DFF 0%, #6B73FF 100%);
            color: #fff;
        }

        .profile-btn-primary {
            background: #fff;
            color: #6B73FF;
            border: 2px solid #6B73FF;
        }

        .profile-btn-primary:hover {
            background: #6B73FF;
            color: #fff;
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../partials_cliente/header.php'; ?>

    <div class="container my-5">
        <!-- Mensajes de éxito/error -->
        <?php if (isset($_GET['exito']) && $_GET['exito'] === 'perfil'): ?>
            <div class="alert alert-success">Datos personales actualizados correctamente.</div>
        <?php elseif (isset($_GET['exito']) && $_GET['exito'] === 'pass'): ?>
            <div class="alert alert-success">Contraseña cambiada correctamente.</div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php
                switch ($_GET['error']) {
                    case 'campos_vacios':
                        echo 'Completa todos los campos.';
                        break;
                    case 'nombre_invalido':
                        echo 'Nombre inválido.';
                        break;
                    case 'apellido_invalido':
                        echo 'Apellido inválido.';
                        break;
                    case 'bd':
                        echo 'Error de base de datos.';
                        break;
                    case 'auth':
                        echo 'Sesión no válida.';
                        break;
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
        <!-- Perfil del usuario -->
        <div class="row mb-5 g-3 flex-column-reverse flex-md-row">
            <div class="col-12 col-md-4">
                <div class="card profile-header text-center py-4 h-100 mb-3 mb-md-0">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <img src="../../public/assets/images/logo.png" alt="Avatar" class="profile-avatar">
                        <h3 class="fs-4 text-break mb-1"><?= $_SESSION['nombre'] . ' ' . $_SESSION['apellido'] ?></h3>
                        <p class="mb-0 text-break small"> <?= $_SESSION['email'] ?> </p>
                    </div>
                    <div class="wave"></div>
                </div>
            </div>
            <div class="col-12 col-md-8">
                <div class="card profile-card h-100">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="mb-0 fw-bold text-primary">Información Personal</h5>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" method="post" action="../../controllers/cliente/actualizar_perfil.php" id="perfilForm" class="profile-form">
                            <div class="row mb-3 g-2">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" name="nombre" id="nombre" value="<?= $_SESSION['nombre'] ?>" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+" maxlength="50" title="Solo letras y espacios, máximo 50 caracteres">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" class="form-control" name="apellido" id="apellido" value="<?= $_SESSION['apellido'] ?>" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+" maxlength="50" title="Solo letras y espacios, máximo 50 caracteres">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" value="<?= $_SESSION['email'] ?>" readonly maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo de Cuenta</label>
                                <input type="text" class="form-control" value="<?= ucfirst($_SESSION['rol']) ?>" readonly disabled tabindex="-1">
                            </div>
                            <div class="d-flex flex-column flex-md-row gap-2 mt-3">
                                <button type="submit" class="btn profile-btn profile-btn-success w-100 w-md-auto">Guardar Cambios</button>
                                <button type="button" class="btn profile-btn profile-btn-primary w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#cambiarPasswordModal">
                                    Cambiar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tests disponibles -->
        <h4 class="mb-4">Tests Disponibles</h4>
        <div class="row g-3">
            <?php foreach ($tests as $test): ?>
                <div class="col-12 col-sm-6 col-md-4 mb-0">
                    <div class="card test-card h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title text-break"><?= htmlspecialchars($test['nombre']) ?></h5>
                                <p class="card-text text-break small"><?= htmlspecialchars($test['descripcion']) ?></p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="badge bg-primary"><?= $test['num_preguntas'] ?> preguntas</span>
                                <a href="realizar_test.php?test_id=<?= $test['id_test'] ?>" class="btn btn-sm btn-primary">Comenzar</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal Cambiar Contraseña -->
    <div class="modal fade" id="cambiarPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="cambiarPasswordForm" method="post" action="../../controllers/cliente/cambiar_password.php" autocomplete="off">
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

    <?php require_once __DIR__ . '/../partials_cliente/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación en tiempo real para nombre y apellido (solo letras y espacios y máximo 50 caracteres)
        function soloLetrasEspacios(e) {
            let valor = e.target.value;
            e.target.value = valor.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ ]/g, '');
        }
        document.getElementById('nombre').addEventListener('input', soloLetrasEspacios);
        document.getElementById('apellido').addEventListener('input', soloLetrasEspacios);

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
    </script>
</body>

</html>