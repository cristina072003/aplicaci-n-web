<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';

verificarAutenticacion();
verificarRol('admin');

header('Content-Type: application/json');

try {
    $action = $_GET['action'] ?? '';
    $range = $_GET['range'] ?? '30'; // Por defecto últimos 30 días

    // Validar rango de tiempo
    $validRanges = ['7', '30', '90', '180', '365', 'all'];
    if (!in_array($range, $validRanges)) {
        $range = '30';
    }

    // Construir condición WHERE según el rango
    $whereCondition = '';
    if ($range !== 'all') {
        $whereCondition = "WHERE r.fecha_resultado >= DATE_SUB(NOW(), INTERVAL $range DAY)";
    }

    switch ($action) {
        case 'estadisticas-generales':
            $sql = "SELECT 
                    COUNT(DISTINCT u.id_usuario) as total_usuarios,
                    COUNT(DISTINCT t.id_test) as total_tests,
                    COUNT(DISTINCT r.id_resultado) as tests_completados,
                    AVG(r.puntaje_total) as promedio_puntaje
                FROM usuarios u
                CROSS JOIN tests t
                LEFT JOIN resultados r ON u.id_usuario = r.id_usuario AND t.id_test = r.id_test
                $whereCondition";
            $resultado = $conexion->query($sql);
            if ($resultado) {
                $data = $resultado->fetch_assoc();
            } else {
                $data = [];
            }
            break;

        case 'datos-mensuales':
            $sql = "SELECT 
                    DATE_FORMAT(r.fecha_resultado, '%Y-%m') as mes,
                    COUNT(r.id_resultado) as cantidad,
                    AVG(r.puntaje_total) as promedio
                FROM resultados r
                $whereCondition
                GROUP BY mes
                ORDER BY mes";
            $resultado = $conexion->query($sql);
            $data = [];
            if ($resultado) {
                while ($row = $resultado->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            break;

        case 'tests-populares':
            $sql = "SELECT 
                    t.nombre, 
                    COUNT(r.id_resultado) as completados,
                    AVG(r.puntaje_total) as promedio_puntaje
                FROM tests t
                LEFT JOIN resultados r ON t.id_test = r.id_test
                $whereCondition
                GROUP BY t.id_test
                ORDER BY completados DESC
                LIMIT 5";
            $resultado = $conexion->query($sql);
            $data = [];
            if ($resultado) {
                while ($row = $resultado->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            break;

        case 'ultimos-resultados':
            $sql = "SELECT 
                    r.fecha_resultado, 
                    u.nombre, 
                    u.apellido, 
                    t.nombre as test, 
                    r.puntaje_total
                FROM resultados r
                JOIN usuarios u ON r.id_usuario = u.id_usuario
                JOIN tests t ON r.id_test = t.id_test
                $whereCondition
                ORDER BY r.fecha_resultado DESC
                LIMIT 10";
            $resultado = $conexion->query($sql);
            $data = [];
            if ($resultado) {
                while ($row = $resultado->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            break;

        default:
            throw new Exception("Acción no válida");
    }

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
