<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../helpers/auth.php';
verificarAutenticacion();
verificarRol('admin');

header('Content-Type: application/json');
$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => 'Acción no válida'];

switch ($action) {
    case 'corregir':
        $id_resultado = intval($_POST['id_resultado'] ?? 0);
        $nuevo_puntaje = intval($_POST['puntaje_total'] ?? 0);
        $nueva_recomendacion = trim($_POST['recomendacion'] ?? '');
        if ($id_resultado > 0) {
            $stmt = $conexion->prepare('UPDATE resultados SET puntaje_total = ?, recomendacion = ? WHERE id_resultado = ?');
            $stmt->bind_param('isi', $nuevo_puntaje, $nueva_recomendacion, $id_resultado);
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Resultado corregido correctamente'];
            } else {
                $response = ['success' => false, 'message' => 'Error al corregir el resultado'];
            }
        } else {
            $response = ['success' => false, 'message' => 'ID de resultado no válido'];
        }
        break;
    case 'agregar_opcion':
        $id_pregunta = intval($_POST['id_pregunta'] ?? 0);
        $texto = trim($_POST['texto'] ?? '');
        $valor = intval($_POST['valor'] ?? 0);
        if ($id_pregunta > 0 && $texto !== '') {
            $stmt = $conexion->prepare('INSERT INTO opciones (id_pregunta, texto, valor) VALUES (?, ?, ?)');
            $stmt->bind_param('isi', $id_pregunta, $texto, $valor);
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Opción agregada correctamente'];
            } else {
                $response = ['success' => false, 'message' => 'Error al agregar la opción'];
            }
        } else {
            $response = ['success' => false, 'message' => 'Datos incompletos'];
        }
        break;
}
echo json_encode($response);
