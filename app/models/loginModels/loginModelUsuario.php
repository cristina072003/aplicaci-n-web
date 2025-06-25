<?php
require_once __DIR__ . '../../../config/Database.php';

class LoginModel
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Autentica un usuario usando email y contraseña
     * @param string $email Email del usuario
     * @param string $password Contraseña sin encriptar
     * @return array Resultado de la autenticación
     */
    public function authenticate($email, $password)
    {
        try {
            // Consulta preparada para prevenir inyección SQL
            $sql = "SELECT id_usuario, nombre, apellido, email, password, rol 
                    FROM usuarios 
                    WHERE email = ? 
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->conn->error);
            }

            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            // Verificar si el usuario existe
            if ($result->num_rows === 0) {
                return [
                    'success' => false,
                    'error' => 'Usuario no encontrado',
                    'error_code' => 'user_not_found'
                ];
            }

            $user = $result->fetch_assoc();

            // Verificar la contraseña (usando password_verify)
            if (!password_verify($password, $user['password'])) {
                return [
                    'success' => false,
                    'error' => 'Contraseña incorrecta',
                    'error_code' => 'wrong_password'
                ];
            }

            // Verificar que el rol sea válido
            if (!in_array($user['rol'], ['admin', 'cliente'])) {
                return [
                    'success' => false,
                    'error' => 'Rol de usuario no válido',
                    'error_code' => 'invalid_role'
                ];
            }

            // Retornar datos del usuario sin la contraseña
            unset($user['password']);

            return [
                'success' => true,
                'user' => $user
            ];
        } catch (Exception $e) {
            error_log("Error en LoginModel: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error del sistema',
                'error_code' => 'system_error'
            ];
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }

    /**
     * Obtiene información de usuario por ID (para sesiones)
     * @param int $id_usuario ID del usuario
     * @return array Datos del usuario
     */
    public function getUserById($id_usuario)
    {
        try {
            $sql = "SELECT id_usuario, nombre, apellido, email, rol 
                    FROM usuarios 
                    WHERE id_usuario = ? 
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return [];
            }

            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error al obtener usuario por ID: " . $e->getMessage());
            return [];
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }
}
