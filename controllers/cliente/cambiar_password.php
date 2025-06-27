<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['id_usuario'] ?? null;
    $actual = $_POST['actual'] ?? '';
    $nueva = $_POST['nueva'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if (!$id_usuario) {
        header('Location: ../../views/cliente/perfil.php?error=auth');
        exit;
    }
    if (empty($actual) || empty($nueva) || empty($confirmar)) {
        header('Location: ../../views/cliente/perfil.php?error=campos_vacios_pw');
        exit;
    }
    if (strlen($nueva) < 8) {
        header('Location: ../../views/cliente/perfil.php?error=pass_corta');
        exit;
    }
    if ($nueva !== $confirmar) {
        header('Location: ../../views/cliente/perfil.php?error=pass_no_coincide');
        exit;
    }
    $sql = "SELECT password FROM usuarios WHERE id_usuario='$id_usuario'";
    $res = $conexion->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        if (!password_verify($actual, $row['password'])) {
            header('Location: ../../views/cliente/perfil.php?error=pass_actual');
            exit;
        }
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $sql2 = "UPDATE usuarios SET password='$hash' WHERE id_usuario='$id_usuario'";
        if ($conexion->query($sql2)) {
            header('Location: ../../views/cliente/perfil.php?exito=pass');
        } else {
            header('Location: ../../views/cliente/perfil.php?error=bd_pw');
        }
        exit;
    }
    header('Location: ../../views/cliente/perfil.php?error=bd_pw');
    exit;
}
header('Location: ../../views/cliente/perfil.php');
exit;
