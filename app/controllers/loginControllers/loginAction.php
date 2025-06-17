<?php
require_once __DIR__ . '../../../config/Database.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Seguridad: Intentos de login ---
if (!isset($_SESSION['login_intentos'])) {
    $_SESSION['login_intentos'] = 0;
    $_SESSION['login_bloqueado_hasta'] = 0;
}

if ($_SESSION['login_bloqueado_hasta'] > time()) {
    header("Location: ../../views/loginViews/loginUsuario.php?error=bloqueado");
    exit();
}

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/loginViews/loginUsuario.php?error=metodo_no_valido");
    exit();
}

// Sanitizar y validar entradas
$email = sanitizeInput($_POST['email'] ?? '', $conexion);
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header("Location: ../../views/loginViews/loginUsuario.php?error=campos_vacios");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../../views/loginViews/loginUsuario.php?error=email_invalido");
    exit();
}

// Buscar usuario en la base de datos (usando consulta preparada)
$sql = "SELECT id_usuario, nombre, apellido, email, password, rol FROM usuarios WHERE email = ? LIMIT 1";
$resultado = $database->executeQuery($sql, [$email], 's');

if (!$resultado || $resultado->num_rows === 0) {
    $_SESSION['login_intentos']++;
    if ($_SESSION['login_intentos'] >= 5) {
        $_SESSION['login_bloqueado_hasta'] = time() + 300; // 5 minutos
        $_SESSION['login_intentos'] = 0;
        header("Location: ../../views/loginViews/loginUsuario.php?error=bloqueado");
        exit();
    }
    header("Location: ../../views/loginViews/loginUsuario.php?error=credenciales_incorrectas");
    exit();
}

$usuario = $resultado->fetch_assoc();

// Verificar contraseña
if (!password_verify($password, $usuario['password'])) {
    $_SESSION['login_intentos']++;
    if ($_SESSION['login_intentos'] >= 5) {
        $_SESSION['login_bloqueado_hasta'] = time() + 300; // 5 minutos
        $_SESSION['login_intentos'] = 0;
        header("Location: ../../views/loginViews/loginUsuario.php?error=bloqueado");
        exit();
    }
    header("Location: ../../views/loginViews/loginUsuario.php?error=credenciales_incorrectas");
    exit();
}

// Verificar rol válido según tu ENUM
$roles_permitidos = ['admin', 'cliente']; // Ajustado a los roles de tu DB
if (!in_array($usuario['rol'], $roles_permitidos)) {
    header("Location: ../../views/loginViews/loginUsuario.php?error=rol_no_valido");
    exit();
}

// Iniciar sesión
$_SESSION['login_intentos'] = 0;
$_SESSION['login_bloqueado_hasta'] = 0;
$_SESSION = [
    'id_usuario' => $usuario['id_usuario'],
    'nombre' => $usuario['nombre'],
    'apellido' => $usuario['apellido'],
    'email' => $usuario['email'],
    'rol' => $usuario['rol'],
    'logged_in' => true
];

// Regenerar ID de sesión para prevenir fixation
session_regenerate_id(true);

// Redireccionar según rol
switch ($usuario['rol']) {
    case 'admin':
        header("Location: ../../views/admin/dashboard.php");
        break;
    case 'cliente':
        header("Location: ../../views/cliente/perfil.php");
        break;
    default:
        header("Location: ../../views/loginViews/loginUsuario.php");
}
exit();
