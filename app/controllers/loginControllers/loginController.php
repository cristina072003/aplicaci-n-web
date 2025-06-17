<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../models/loginModels/loginModelUsuario.php';

class LoginController
{
    private $model;
    private $conexion;

    public function __construct()
    {
        $this->conexion = new Database(); // Usando la clase Database que te proporcioné
        $this->model = new LoginModel();
    }

    public function handleLoginRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Método no permitido', 'redirect' => '../../views/loginViews/loginUsuario.php?error=metodo_no_permitido'];
        }

        $email = $this->sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validaciones básicas
        if (empty($email) || empty($password)) {
            return ['success' => false, 'error' => 'Campos vacíos', 'redirect' => '../../views/loginViews/loginUsuario.php?error=campos_vacios'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Email inválido', 'redirect' => '../../views/loginViews/loginUsuario.php?error=email_invalido'];
        }

        // Procesar login
        $result = $this->login($email, $password);

        if (!$result['success']) {
            return $result; // Retorna el error específico del modelo
        }

        // Iniciar sesión segura
        $this->startSecureSession($result['usuario']);

        return [
            'success' => true,
            'redirect' => $this->getRedirectPath($result['usuario']['rol'])
        ];
    }

    private function login($email, $password)
    {
        return $this->model->authenticate($email, $password);
    }

    private function startSecureSession($usuario)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure' => true, // En producción debería ser true
                'use_strict_mode' => true
            ]);
        }

        $_SESSION = [
            'id_usuario' => $usuario['id_usuario'],
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol'],
            'logged_in' => true,
            'last_activity' => time()
        ];

        session_regenerate_id(true);
    }

    private function getRedirectPath($rol)
    {
        $routes = [
            'admin' => '../../views/admin/dashboard.php',
            'cliente' => '../../views/cliente/perfil.php'
        ];

        return $routes[$rol] ?? '../../views/loginViews/loginUsuario.php';
    }

    private function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map(function ($item) {
                return htmlspecialchars(strip_tags(trim($item)));
            }, $data);
        }
        return htmlspecialchars(strip_tags(trim($data)));
    }

    public function logout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Destruir todas las variables de sesión
        $_SESSION = array();

        // Borrar la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destruir la sesión
        session_destroy();

        return ['redirect' => '../../views/loginViews/loginUsuario.php?logout=success'];
    }
}

// Uso del controlador
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $controller = new LoginController();
    $result = $controller->logout();
    header("Location: " . $result['redirect']);
    exit();
}
