<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';

// Verificar autenticación
verificarAutenticacion();

$titulo = "Realizar Test";

// Validar test_id recibido
if (!isset($_GET['test_id']) || !is_numeric($_GET['test_id'])) {
    header('Location: perfil.php');
    exit;
}
$test_id = intval($_GET['test_id']);

// Verificar conexión
if (!isset($conexion) || !$conexion) {
    die('<div class="alert alert-danger">Error de conexión a la base de datos.</div>');
}

// Obtener información del test
$sqlTest = "SELECT nombre, descripcion, duracion_min FROM tests WHERE id_test = ?";
$stmtTest = $conexion->prepare($sqlTest);
if (!$stmtTest) {
    die('<div class="alert alert-danger">Error al preparar la consulta del test: ' . $conexion->error . '</div>');
}
$stmtTest->bind_param('i', $test_id);
$stmtTest->execute();
$resultTest = $stmtTest->get_result();
$test = $resultTest->fetch_assoc();
if (!$test) {
    echo '<div class="alert alert-danger">Test no encontrado.</div>';
    exit;
}

// Obtener preguntas y opciones
$sqlPreguntas = "SELECT p.id_pregunta, p.texto FROM preguntas p WHERE p.id_test = ? ORDER BY p.id_pregunta";
$stmtPreg = $conexion->prepare($sqlPreguntas);
if (!$stmtPreg) {
    die('<div class="alert alert-danger">Error al preparar la consulta de preguntas: ' . $conexion->error . '</div>');
}
$stmtPreg->bind_param('i', $test_id);
$stmtPreg->execute();
$resultPreg = $stmtPreg->get_result();
$preguntas = [];
while ($row = $resultPreg->fetch_assoc()) {
    $preguntas[$row['id_pregunta']] = [
        'pregunta' => $row['texto'],
        'opciones' => []
    ];
}

if ($preguntas) {
    $ids = implode(',', array_keys($preguntas));
    $sqlOpciones = "SELECT id_opcion, id_pregunta, texto FROM opciones WHERE id_pregunta IN ($ids) ORDER BY id_opcion";
    $resultOpc = $conexion->query($sqlOpciones);
    while ($row = $resultOpc->fetch_assoc()) {
        $preguntas[$row['id_pregunta']]['opciones'][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #e0e7ff 0%, #f8fafc 100%);
            min-height: 100vh;
        }

        .test-main-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(106, 115, 255, 0.10);
            padding: 2rem 1.2rem;
            max-width: 700px;
            margin: 0 auto;
        }

        .test-timer {
            background: linear-gradient(90deg, #6B73FF 0%, #000DFF 100%);
            color: #fff;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            padding: 0.7rem 1.2rem;
            margin-bottom: 1.5rem;
            display: inline-block;
        }

        .test-question {
            font-weight: 600;
            color: #2d3a4a;
        }

        .test-options .form-check {
            background: #f3f6fd;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            padding: 0.5rem 1rem;
            transition: background 0.2s;
        }

        .test-options .form-check-input:checked~.form-check-label {
            color: #000DFF;
            font-weight: 700;
        }

        .test-btn {
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(107, 115, 255, 0.08);
            transition: all 0.2s;
        }

        .test-btn-success {
            background: linear-gradient(90deg, #6B73FF 0%, #000DFF 100%);
            color: #fff;
            border: none;
        }

        .test-btn-success:hover {
            background: linear-gradient(90deg, #000DFF 0%, #6B73FF 100%);
            color: #fff;
        }

        .test-btn-cancel {
            background: #fff;
            color: #6B73FF;
            border: 2px solid #6B73FF;
        }

        .test-btn-cancel:hover {
            background: #6B73FF;
            color: #fff;
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../partials_cliente/header.php'; ?>
    <div class="container my-5 px-2 px-sm-3 px-md-5">
        <div class="test-main-card">
            <h2 class="mb-3 text-center fs-3">Test: <?= htmlspecialchars($test['nombre']) ?></h2>
            <p class="mb-4 text-center fs-6 text-secondary"><?= htmlspecialchars($test['descripcion']) ?></p>
            <?php if (!empty($test['duracion_min'])): ?>
                <div class="test-timer mx-auto text-center">
                    Tiempo restante: <span id="timer" class="fw-bold"></span>
                </div>
                <script>
                    // Contador regresivo en minutos
                    let tiempo = <?= (int)$test['duracion_min'] ?> * 60; // segundos
                    const timerElem = document.getElementById('timer');

                    function updateTimer() {
                        const min = Math.floor(tiempo / 60);
                        const seg = tiempo % 60;
                        timerElem.textContent = `${min.toString().padStart(2, '0')}:${seg.toString().padStart(2, '0')}`;
                        if (tiempo <= 0) {
                            clearInterval(timerInterval);
                            document.getElementById('formTest').submit();
                        }
                        tiempo--;
                    }
                    updateTimer();
                    const timerInterval = setInterval(updateTimer, 1000);
                </script>
            <?php endif; ?>
            <?php if (empty($preguntas)): ?>
                <div class="alert alert-warning text-center">Este test no tiene preguntas asignadas.</div>
            <?php else: ?>
                <form id="formTest" action="../../controllers/cliente/guardar_respuestas.php" method="post" autocomplete="off">
                    <input type="hidden" name="test_id" value="<?= $test_id ?>">
                    <?php $num = 1;
                    foreach ($preguntas as $id_pregunta => $preg): ?>
                        <div class="mb-4">
                            <div class="test-question mb-2"><?= $num++ . '. ' . htmlspecialchars($preg['pregunta']) ?></div>
                            <div class="row g-2 test-options">
                                <?php foreach ($preg['opciones'] as $opcion): ?>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="respuestas[<?= $id_pregunta ?>]" id="opcion<?= $opcion['id_opcion'] ?>" value="<?= $opcion['id_opcion'] ?>" required>
                                            <label class="form-check-label" for="opcion<?= $opcion['id_opcion'] ?>">
                                                <?= htmlspecialchars($opcion['texto']) ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="d-flex flex-column flex-md-row gap-2 justify-content-center mt-4">
                        <button type="submit" class="btn test-btn test-btn-success w-100 w-md-auto">Enviar Respuestas</button>
                        <a href="perfil.php" class="btn test-btn test-btn-cancel w-100 w-md-auto">Cancelar</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php require_once __DIR__ . '/../partials_cliente/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Advertencia al intentar salir o navegar mientras el test no se ha enviado
        let testEnviado = false;
        const formTest = document.getElementById('formTest');
        if (formTest) {
            formTest.addEventListener('submit', function() {
                testEnviado = true;
            });
            window.addEventListener('beforeunload', function(e) {
                if (!testEnviado) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
            // Interceptar clicks en enlaces del header y otros links
            document.querySelectorAll('a').forEach(function(link) {
                if (link.closest('form') !== formTest) {
                    link.addEventListener('click', function(e) {
                        if (!testEnviado && link.getAttribute('href') && !link.getAttribute('href').startsWith('#')) {
                            const salir = confirm('¿Estás seguro de salir? Se perderán tus respuestas no enviadas.');
                            if (!salir) {
                                e.preventDefault();
                            }
                        }
                    });
                }
            });
        }
    </script>
</body>

</html>