<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $id_usuario = $_SESSION['id_usuario'] ?? null;
    $errores = [];

    if (!$id_usuario) {
        header('Location: ../../views/cliente/perfil.php?error=auth');
        exit;
    }

    if (empty($nombre) || empty($apellido)) {
        header('Location: ../../views/cliente/perfil.php?error=campos_vacios');
        exit;
    }

    if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/u', $nombre)) {
        header('Location: ../../views/cliente/perfil.php?error=nombre_invalido');
        exit;
    }
    if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/u', $apellido)) {
        header('Location: ../../views/cliente/perfil.php?error=apellido_invalido');
        exit;
    }

    $nombre = $conexion->real_escape_string($nombre);
    $apellido = $conexion->real_escape_string($apellido);

    $sql = "UPDATE usuarios SET nombre='$nombre', apellido='$apellido' WHERE id_usuario='$id_usuario'";
    if ($conexion->query($sql)) {
        $_SESSION['nombre'] = $nombre;
        $_SESSION['apellido'] = $apellido;
        header('Location: ../../views/cliente/perfil.php?exito=perfil');
    } else {
        header('Location: ../../views/cliente/perfil.php?error=bd');
    }
    exit;
}
header('Location: ../../views/cliente/perfil.php');
exit;
