<?php
// Configuración de la base de datos para Docker
define('DB_HOST', 'db');       // Usar el nombre del servicio como host
define('DB_USER', 'userUpds'); // Usuario definido en docker-compose
define('DB_PASS', 'updsPassword'); // Contraseña definida en docker-compose
define('DB_NAME', 'Jhunior_Critina'); // Nombre de la base de datos definido en docker-compose

class Database {
    private $conn;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        // Crear conexión
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Verificar conexión
        if ($this->conn->connect_error) {
            error_log("Error de conexión: " . $this->conn->connect_error);
            die("Error al conectar con la base de datos. Por favor, intente más tarde.");
        }
        
        // Establecer charset
        if (!$this->conn->set_charset("utf8mb4")) {
            error_log("Error al establecer charset: " . $this->conn->error);
            die("Error de configuración de la base de datos.");
        }
    }
    
    public function getConnection() {
        // Verificar si la conexión sigue activa
        if ($this->conn && $this->conn->ping()) {
            return $this->conn;
        } else {
            // Reconectar si se perdió la conexión
            $this->connect();
            return $this->conn;
        }
    }
    
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
            $this->conn = null;
        }
    }
    
    // Método para ejecutar consultas preparadas de forma segura
    public function executeQuery($sql, $params = [], $types = '') {
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Error al preparar consulta: " . $conn->error);
            return false;
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            error_log("Error al ejecutar consulta: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result;
    }
}

// Crear instancia única de la base de datos
$database = new Database();
$conexion = $database->getConnection();

// Función para sanitizar entradas (adicional a las consultas preparadas)
function sanitizeInput($data, $conexion) {
    if (is_array($data)) {
        return array_map(function($item) use ($conexion) {
            return htmlspecialchars($conexion->real_escape_string(trim($item)));
        }, $data);
    }
    return htmlspecialchars($conexion->real_escape_string(trim($data)));
}

// Registrar función de cierre al terminar el script
register_shutdown_function(function() use ($database) {
    $database->closeConnection();
});