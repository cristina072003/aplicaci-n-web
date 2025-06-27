<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';
session_start();

// Verifica autenticación básica
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../views/cliente/loginUsuario.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_id'], $_POST['respuestas'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $test_id = intval($_POST['test_id']);
    $respuestas = $_POST['respuestas']; // [id_pregunta => id_opcion]

    // Verificar que el usuario existe
    $sql_check_user = "SELECT id_usuario FROM usuarios WHERE id_usuario = ? LIMIT 1";
    $stmt = $conexion->prepare($sql_check_user);
    $stmt->bind_param('i', $id_usuario);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $stmt->close();
        session_destroy();
        header('Location: ../../views/cliente/loginUsuario.php?error=usuario_no_existe');
        exit;
    }
    $stmt->close();

    // 1. Insertar resultado general
    $sql_resultado = "INSERT INTO resultados (id_usuario, id_test, puntaje_total, recomendacion) VALUES (?, ?, 0, '')";
    $stmt = $conexion->prepare($sql_resultado);
    $stmt->bind_param('ii', $id_usuario, $test_id);
    if (!$stmt->execute()) {
        die('Error al guardar el resultado general: ' . $stmt->error);
    }
    $id_resultado = $conexion->insert_id;
    $stmt->close();

    $puntaje_total = 0;

    // 2. Guardar cada respuesta en detalle_resultados
    foreach ($respuestas as $id_pregunta => $id_opcion) {
        // Obtener valor de la opción seleccionada
        $sql_valor = "SELECT valor FROM opciones WHERE id_opcion = ?";
        $stmt = $conexion->prepare($sql_valor);
        $stmt->bind_param('i', $id_opcion);
        $stmt->execute();
        $stmt->bind_result($valor);
        $stmt->fetch();
        $stmt->close();
        if ($valor === null) $valor = 0;
        $puntaje_total += $valor;

        // Insertar detalle
        $sql_detalle = "INSERT INTO detalle_resultados (id_resultado, categoria, puntaje, observacion) VALUES (?, '', ?, '')";
        $stmt = $conexion->prepare($sql_detalle);
        $stmt->bind_param('ii', $id_resultado, $valor);
        $stmt->execute();
        $stmt->close();
    }

    // 3. Actualizar puntaje total en resultados
    $sql_update = "UPDATE resultados SET puntaje_total = ? WHERE id_resultado = ?";
    $stmt = $conexion->prepare($sql_update);
    $stmt->bind_param('ii', $puntaje_total, $id_resultado);
    $stmt->execute();
    $stmt->close();

    // 4. (Opcional) Marcar test como completado
    $sql_completado = "UPDATE tests_asignados SET completado = 1 WHERE id_usuario = ? AND id_test = ?";
    $stmt = $conexion->prepare($sql_completado);
    $stmt->bind_param('ii', $id_usuario, $test_id);
    $stmt->execute();
    $stmt->close();

    header('Location: ../../views/cliente/resultados.php?mensaje=Test+enviado+correctamente');
    exit;
} else {
    // Si no es POST o faltan datos, redirige
    header('Location: ../../views/cliente/perfil.php');
    exit;
}
