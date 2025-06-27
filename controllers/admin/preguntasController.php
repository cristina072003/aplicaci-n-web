<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../helpers/auth.php';
verificarAutenticacion();
verificarRol('admin');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'crear') {
        $test_id = intval($_POST['test_id']);
        $texto = trim($_POST['texto']);
        $tipo = $_POST['tipo'];
        if ($tipo === 'opcion_multiple' && isset($_POST['opciones']) && isset($_POST['opcion_correcta'])) {
            // 1. Insertar pregunta
            $sql = "INSERT INTO preguntas (id_test, texto, tipo) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('iss', $test_id, $texto, $tipo);
            if ($stmt->execute()) {
                $id_pregunta = $conexion->insert_id;
                $stmt->close();
                // 2. Insertar opciones
                $opciones = $_POST['opciones'];
                $correcta = intval($_POST['opcion_correcta']);
                $ok = true;
                foreach ($opciones as $idx => $op) {
                    $op_texto = trim($op['texto']);
                    $valor = ($idx == $correcta) ? 1 : 0;
                    $sql_op = "INSERT INTO opciones (id_pregunta, texto, valor) VALUES (?, ?, ?)";
                    $stmt_op = $conexion->prepare($sql_op);
                    $stmt_op->bind_param('isi', $id_pregunta, $op_texto, $valor);
                    if (!$stmt_op->execute()) {
                        $ok = false;
                        break;
                    }
                    $stmt_op->close();
                }
                if ($ok) {
                    echo json_encode(['success' => true, 'message' => 'Pregunta y opciones guardadas correctamente.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al guardar las opciones.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar la pregunta.']);
            }
            exit;
        }
        // Si es pregunta abierta
        if ($tipo === 'abierta') {
            $sql = "INSERT INTO preguntas (id_test, texto, tipo) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('iss', $test_id, $texto, $tipo);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Pregunta guardada correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar la pregunta.']);
            }
            $stmt->close();
            exit;
        }
    }
    if ($action === 'eliminar' && isset($_POST['id_pregunta'])) {
        $id_pregunta = intval($_POST['id_pregunta']);
        // Eliminar opciones primero (por FK ON DELETE CASCADE, pero por si acaso)
        $conexion->query("DELETE FROM opciones WHERE id_pregunta = $id_pregunta");
        // Eliminar la pregunta
        $sql = "DELETE FROM preguntas WHERE id_pregunta = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('i', $id_pregunta);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Pregunta eliminada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la pregunta.']);
        }
        $stmt->close();
        exit;
    }
}
// Backend: obtener opciones de una pregunta para edición
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_opciones' && isset($_GET['id_pregunta'])) {
    $id_pregunta = intval($_GET['id_pregunta']);
    $sql = "SELECT texto, valor FROM opciones WHERE id_pregunta = ? ORDER BY id_opcion";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $id_pregunta);
    $stmt->execute();
    $result = $stmt->get_result();
    $opciones = [];
    while ($row = $result->fetch_assoc()) {
        $opciones[] = $row;
    }
    echo json_encode($opciones);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Solicitud inválida.']);
