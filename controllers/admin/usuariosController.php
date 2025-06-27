<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';

// Verificar autenticación y rol de admin
verificarAutenticacion();
verificarRol('admin');

header('Content-Type: application/json');

// Crear instancia de la base de datos
$database = new Database();
$conexion = $database->getConnection();

try {
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => 'Acción no válida'];

    switch ($action) {
        case 'crear':
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $rol = $_POST['rol'] ?? 'cliente';
            $password = $_POST['password'] ?? '';

            // Validaciones
            if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
                throw new Exception("Todos los campos son obligatorios");
            }

            if (strlen($password) < 8) {
                throw new Exception("La contraseña debe tener al menos 8 caracteres");
            }

            // Verificar si el email ya existe
            $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                throw new Exception("El email ya está registrado");
            }

            // Hash de la contraseña
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insertar nuevo usuario
            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, email, password, rol) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nombre, $apellido, $email, $passwordHash, $rol);

            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Usuario creado correctamente'];
            } else {
                throw new Exception("Error al crear el usuario: " . $conexion->error);
            }
            break;

        case 'actualizar':
            $id = $_POST['id'] ?? 0;
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $rol = $_POST['rol'] ?? 'cliente';
            $password = $_POST['password'] ?? '';

            // Validaciones
            if (empty($id) || empty($nombre) || empty($apellido) || empty($email)) {
                throw new Exception("Todos los campos son obligatorios");
            }

            // Verificar si el email ya existe (excluyendo el usuario actual)
            $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE email = ? AND id_usuario != ?");
            $stmt->bind_param("si", $email, $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                throw new Exception("El email ya está registrado por otro usuario");
            }

            // Preparar consulta de actualización
            if (!empty($password)) {
                if (strlen($password) < 8) {
                    throw new Exception("La contraseña debe tener al menos 8 caracteres");
                }
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, rol = ?, password = ? WHERE id_usuario = ?");
                $stmt->bind_param("sssssi", $nombre, $apellido, $email, $rol, $passwordHash, $id);
            } else {
                $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, rol = ? WHERE id_usuario = ?");
                $stmt->bind_param("ssssi", $nombre, $apellido, $email, $rol, $id);
            }

            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Usuario actualizado correctamente'];
            } else {
                throw new Exception("Error al actualizar el usuario: " . $conexion->error);
            }
            break;

        case 'eliminar':
            $id = $_POST['id'] ?? 0;

            if (empty($id)) {
                throw new Exception("ID de usuario no válido");
            }

            // Verificar que no sea el último admin
            $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin'");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['total'] <= 1) {
                $stmt = $conexion->prepare("SELECT rol FROM usuarios WHERE id_usuario = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                if ($user['rol'] === 'admin') {
                    throw new Exception("No se puede eliminar el último administrador");
                }
            }

            // Eliminar usuario
            $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Usuario eliminado correctamente'];
            } else {
                throw new Exception("Error al eliminar el usuario: " . $conexion->error);
            }
            break;

        case 'listar':
            // Listar usuarios
            $query = "SELECT id_usuario, nombre, apellido, email, rol, fecha_registro FROM usuarios ORDER BY fecha_registro DESC";
            $result = $conexion->query($query);
            $usuarios = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $usuarios[] = $row;
                }
                $result->free();
            }
            $response = ['success' => true, 'usuarios' => $usuarios];
            break;

        default:
            $response = ['success' => false, 'message' => 'Acción no permitida'];
            break;
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
} finally {
    echo json_encode($response);
    if (isset($conexion)) {
        $conexion->close();
    }
}