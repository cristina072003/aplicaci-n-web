<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';

verificarAutenticacion();
verificarRol('admin');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Panel de Administración' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 56px;
            --primary-color: #6a3093;
            --secondary-color: #8e44ad;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .admin-header {
            height: var(--header-height);
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            z-index: 1030;
            padding: 0 1.5rem;
            transition: left 0.3s;
        }

        @media (max-width: 991.98px) {
            .admin-header {
                left: 0;
                width: 100vw;
                padding: 0 1rem;
            }

            .admin-header h1 {
                font-size: 1.1rem;
            }

            .sidebar-toggle {
                display: inline-block !important;
            }
        }

        @media (min-width: 992px) {
            .sidebar-toggle {
                display: none !important;
            }
        }

        @media (max-width: 575.98px) {
            .admin-header {
                flex-direction: column;
                align-items: flex-start !important;
                height: auto;
                padding: 0.5rem 0.5rem;
            }

            .admin-header h1 {
                margin-bottom: 0.5rem;
            }

            .dropdown {
                width: 100%;
            }

            .dropdown-toggle {
                width: 100%;
                justify-content: flex-start;
            }

            .offcanvas {
                width: 85vw !important;
                max-width: 320px;
            }
        }

        .offcanvas.offcanvas-start {
            top: var(--header-height);
            height: calc(100vh - var(--header-height));
        }

        .offcanvas-body {
            padding: 0;
        }

        .offcanvas .nav-link.active {
            background: var(--primary-color);
            color: #fff !important;
        }
    </style>
</head>

<body>
    <header class="admin-header d-flex justify-content-between align-items-center flex-wrap">
        <div class="d-flex align-items-center flex-wrap">
            <button class="sidebar-toggle btn btn-link text-dark me-3 d-lg-none" type="button" aria-label="Menú lateral" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="h4 mb-0"><?= $titulo ?? 'Panel de Administración' ?></h1>
        </div>
        <div class="dropdown mt-2 mt-sm-0">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
                <span class="me-2 d-none d-sm-inline"><?= $_SESSION['nombre'] . ' ' . $_SESSION['apellido'] ?></span>
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-person-fill"></i>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li>
                    <h6 class="dropdown-header"><?= $_SESSION['email'] ?></h6>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="../admin/perfil.php"><i class="bi bi-person me-2"></i>Mi perfil</a></li>
                <li><a class="dropdown-item" href="../admin/configuracion.php"><i class="bi bi-gear me-2"></i>Configuración</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="../../controllers/loginControllers/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
            </ul>
        </div>
    </header>
    <!-- Offcanvas para menú lateral en móvil -->
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasMenuLabel">Menú</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body p-0">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="../admin/dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : 'text-dark' ?>">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="../admin/usuarios.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'usuarios.php' ? 'active' : 'text-dark' ?>">
                        <i class="bi bi-people me-2"></i> Usuarios
                    </a>
                </li>
                <li>
                    <a href="../admin/tests.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'tests.php' ? 'active' : 'text-dark' ?>">
                        <i class="bi bi-question-circle me-2"></i> Tests Vocacionales
                    </a>
                </li>
                <li>
                    <a href="../admin/reportes.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'reportes.php' ? 'active' : 'text-dark' ?>">
                        <i class="bi bi-bar-chart me-2"></i> Reportes
                    </a>
                </li>
                <li>
                    <a href="../admin/asignar_test.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'asignar_test.php' ? 'active' : 'text-dark' ?>">
                        <i class="bi bi-clipboard-check me-2"></i> Asignar Test
                    </a>
                </li>
                <li>
                    <a href="../admin/corregir_resultados.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'corregir_resultados.php' ? 'active' : 'text-dark' ?>">
                        <i class="bi bi-pencil-square me-1"></i> Corregir Resultados
                    </a>
                </li>
                <li>
                    <a href="../admin/configuracion.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'configuracion.php' ? 'active' : 'text-dark' ?>">
                        <i class="bi bi-gear me-2"></i> Configuración
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <script>
        // Opcional: ejemplo de cómo mostrar/ocultar el sidebar en móvil
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    const sidebar = document.querySelector('.sidebar');
                    if (sidebar) {
                        sidebar.classList.toggle('d-none');
                    }
                });
            }
        });
    </script>