<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../helpers/auth.php';

// Verificar autenticación y rol de admin
verificarAutenticacion();
verificarRol('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['id_usuario'];
    $actual = isset($_POST['actual']) ? trim($_POST['actual']) : '';
    $nueva = isset($_POST['nueva']) ? trim($_POST['nueva']) : '';
    $confirmar = isset($_POST['confirmar']) ? trim($_POST['confirmar']) : '';

    // Validaciones
    if (empty($actual) || empty($nueva) || empty($confirmar)) {
        header('Location: ../../views/admin/perfil.php?error=campos_vacios_pw');
        exit;
    }
    if (strlen($nueva) < 8) {
        header('Location: ../../views/admin/perfil.php?error=pass_corta');
        exit;
    }
    if ($nueva !== $confirmar) {
        header('Location: ../../views/admin/perfil.php?error=pass_no_coincide');
        exit;
    }

    // Obtener contraseña actual
    $sql = "SELECT password FROM usuarios WHERE id_usuario = $id_usuario LIMIT 1";
    $result = $conexion->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        if (!password_verify($actual, $row['password'])) {
            header('Location: ../../views/admin/perfil.php?error=pass_actual');
            exit;
        }
        // Actualizar contraseña
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $sqlUpdate = "UPDATE usuarios SET password = '$hash' WHERE id_usuario = $id_usuario";
        if ($conexion->query($sqlUpdate)) {
            header('Location: ../../views/admin/perfil.php?exito=pass');
            exit;
        } else {
            header('Location: ../../views/admin/perfil.php?error=bd_pw');
            exit;
        }
    } else {
        header('Location: ../../views/admin/perfil.php?error=bd_pw');
        exit;
    }
} else {
    header('Location: ../../views/admin/perfil.php');
    exit;
}
