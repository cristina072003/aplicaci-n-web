<?php
function verificarAutenticacion()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start([
            'cookie_httponly' => true,
            'cookie_secure' => true,
            'use_strict_mode' => true
        ]);
    }

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: ../../views/loginViews/loginUsuario.php?error=no_autenticado");
        exit();
    }
}

function verificarRol($rolRequerido)
{
    if (!isset($_SESSION['rol'])) {
        header("Location: ../../views/loginViews/loginUsuario.php?error=rol_no_definido");
        exit();
    }

    if ($_SESSION['rol'] !== $rolRequerido) {
        header("Location: ../../views/loginViews/loginUsuario.php?error=permisos_insuficientes");
        exit();
    }
}

function obtenerUsuarioActual()
{
    verificarAutenticacion();
    return [
        'id' => $_SESSION['id_usuario'],
        'nombre' => $_SESSION['nombre'],
        'apellido' => $_SESSION['apellido'],
        'email' => $_SESSION['email'],
        'rol' => $_SESSION['rol']
    ];
}
