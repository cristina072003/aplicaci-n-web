<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';

// Verificar autenticación y rol de admin
verificarAutenticacion();
verificarRol('admin');

header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => 'Acción no válida'];

    switch ($action) {
        case 'crear':
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $duracion_min = intval($_POST['duracion_min'] ?? 0);

            // Validaciones
            if (empty($nombre) || empty($descripcion) || $duracion_min < 5) {
                throw new Exception("Todos los campos son obligatorios y la duración mínima es 5 minutos");
            }

            // Verificar si el test ya existe
            $stmt = $conexion->prepare("SELECT id_test FROM tests WHERE nombre = ?");
            $stmt->bind_param("s", $nombre);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                throw new Exception("Ya existe un test con ese nombre");
            }

            // Crear nuevo test
            $stmt = $conexion->prepare("INSERT INTO tests (nombre, descripcion, duracion_min) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $nombre, $descripcion, $duracion_min);
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Test creado correctamente'];
            } else {
                throw new Exception("Error al crear el test");
            }
            break;

        case 'actualizar':
            $id = intval($_POST['id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $duracion_min = intval($_POST['duracion_min'] ?? 0);

            // Validaciones
            if ($id < 1 || empty($nombre) || empty($descripcion) || $duracion_min < 5) {
                throw new Exception("Todos los campos son obligatorios y la duración mínima es 5 minutos");
            }

            // Verificar si el nombre ya existe (excluyendo el test actual)
            $stmt = $conexion->prepare("SELECT id_test FROM tests WHERE nombre = ? AND id_test != ?");
            $stmt->bind_param("si", $nombre, $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                throw new Exception("Ya existe otro test con ese nombre");
            }

            // Actualizar test
            $stmt = $conexion->prepare("UPDATE tests SET nombre = ?, descripcion = ?, duracion_min = ? WHERE id_test = ?");
            $stmt->bind_param("ssii", $nombre, $descripcion, $duracion_min, $id);
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Test actualizado correctamente'];
            } else {
                throw new Exception("Error al actualizar el test");
            }
            break;

        case 'eliminar':
            $id = intval($_POST['id'] ?? 0);

            if ($id < 1) {
                throw new Exception("ID de test no válido");
            }

            // Verificar si tiene preguntas asociadas
            $stmt = $conexion->prepare("SELECT COUNT(*) FROM preguntas WHERE id_test = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $num_preguntas = 0;
            if ($result) {
                $row = $result->fetch_row();
                $num_preguntas = $row[0];
            }

            if ($num_preguntas > 0) {
                throw new Exception("No se puede eliminar un test que tiene preguntas asociadas");
            }

            // Eliminar test
            $stmt = $conexion->prepare("DELETE FROM tests WHERE id_test = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Test eliminado correctamente'];
            } else {
                throw new Exception("Error al eliminar el test");
            }
            break;

        case 'listar':
            // Listar tests con conteo de preguntas y asignaciones
            $sql = "SELECT t.id_test, t.nombre, t.descripcion, t.duracion_min, 
                    COUNT(p.id_pregunta) as num_preguntas,
                    COUNT(DISTINCT ta.id_asignacion) as veces_asignado
                    FROM tests t
                    LEFT JOIN preguntas p ON t.id_test = p.id_test
                    LEFT JOIN tests_asignados ta ON t.id_test = ta.id_test
                    GROUP BY t.id_test
                    ORDER BY t.nombre";
            $stmt = $conexion->query($sql);
            $tests = [];
            if ($stmt) {
                $tests = $stmt->fetch_all(MYSQLI_ASSOC);
            }
            $response = ['success' => true, 'tests' => $tests];
            break;

        default:
            $response = ['success' => false, 'message' => 'Acción no permitida'];
            break;
    }
} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
} finally {
    echo json_encode($response);
}
