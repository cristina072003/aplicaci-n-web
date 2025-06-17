<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';

// Verificar autenticación
verificarAutenticacion();

$titulo = "Mi Perfil";
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
            border-radius: 15px;
        }
        .test-card {
            transition: transform 0.3s;
            cursor: pointer;
        }
        .test-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Sistema Vocacional</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="perfil.php">
                            <i class="fas fa-user me-1"></i> Perfil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tests.php">
                            <i class="fas fa-question-circle me-1"></i> Mis Tests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="resultados.php">
                            <i class="fas fa-chart-pie me-1"></i> Resultados
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../../controllers/loginControllers/logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <!-- Perfil del usuario -->
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="card profile-header text-center py-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-5x"></i>
                        </div>
                        <h3><?= $_SESSION['nombre'] . ' ' . $_SESSION['apellido'] ?></h3>
                        <p class="mb-0"><?= $_SESSION['email'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Información Personal</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" value="<?= $_SESSION['nombre'] ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" class="form-control" value="<?= $_SESSION['apellido'] ?>" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" value="<?= $_SESSION['email'] ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo de Cuenta</label>
                                <input type="text" class="form-control" value="<?= ucfirst($_SESSION['rol']) ?>" readonly>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cambiarPasswordModal">
                                Cambiar Contraseña
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tests disponibles -->
        <h4 class="mb-4">Tests Disponibles</h4>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card test-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">IPPR</h5>
                        <p class="card-text">Inventario de Preferencias Profesionales</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">30 preguntas</span>
                            <a href="realizar_test.php?test=ippr" class="btn btn-sm btn-primary">Comenzar</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card test-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">CHASIDE</h5>
                        <p class="card-text">Cuestionario de Hábitos de Estudio</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">40 preguntas</span>
                            <a href="realizar_test.php?test=chaside" class="btn btn-sm btn-primary">Comenzar</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card test-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">DAT</h5>
                        <p class="card-text">Pruebas de Aptitudes Diferenciales</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">60 preguntas</span>
                            <a href="realizar_test.php?test=dat" class="btn btn-sm btn-primary">Comenzar</a>
                        </div>
                    </div>
                </div>
            </div>
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
                    <form id="cambiarPasswordForm">
                        <div class="mb-3">
                            <label class="form-label">Contraseña Actual</label>
                            <input type="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>