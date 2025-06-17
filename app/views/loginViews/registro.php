<?php
require_once __DIR__ . '../../../config/Database.php';
$errores = [];
$exito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar datos
    $nombre = $conexion->real_escape_string(trim($_POST['nombre']));
    $apellido = $conexion->real_escape_string(trim($_POST['apellido']));
    $email = $conexion->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validaciones de longitud
    if (mb_strlen($nombre) > 50) {
        $errores[] = "El nombre no puede tener más de 50 caracteres";
    }
    if (mb_strlen($apellido) > 50) {
        $errores[] = "El apellido no puede tener más de 50 caracteres";
    }
    if (mb_strlen($email) > 100) {
        $errores[] = "El correo electrónico no puede tener más de 100 caracteres";
    }
    if (mb_strlen($password) > 100) {
        $errores[] = "La contraseña no puede tener más de 100 caracteres";
    }

    // Validaciones básicas
    if (empty($nombre)) {
        $errores[] = "El nombre es requerido";
    }

    if (empty($apellido)) {
        $errores[] = "El apellido es requerido";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido";
    } else {
        // Verificar si el correo ya existe
        $existe = $conexion->query("SELECT id_usuario FROM usuarios WHERE email = '$email'");
        if ($existe->num_rows > 0) {
            $errores[] = "Este correo electrónico ya está registrado";
        }
    }

    if (strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres";
    }

    if ($password !== $confirm_password) {
        $errores[] = "Las contraseñas no coinciden";
    }

    // Si no hay errores, proceder con el registro
    if (empty($errores)) {
        $conexion->begin_transaction();

        try {
            // Encriptar la contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insertar en tabla usuarios
            $conexion->query("
                INSERT INTO usuarios (
                    nombre, 
                    apellido,
                    email, 
                    password, 
                    rol
                ) VALUES (
                    '$nombre',
                    '$apellido',
                    '$email',
                    '$password_hash',
                    'cliente'
                )
            ");

            $conexion->commit();
            $exito = true;
        } catch (Exception $e) {
            $conexion->rollback();
            $errores[] = "Error al registrar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema Vocacional</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="../../public/assets/css/registro.css">
</head>

<body>
    <div class="container">
        <div class="register-container">
            <div class="register-header">
                <h2><i class="fas fa-user-plus me-2"></i>Registro de Usuario</h2>
                <p>Completa el formulario para registrarte en el sistema</p>
            </div>

            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger">
                    <ul class="error-list">
                        <?php foreach ($errores as $error): ?>
                            <li class="error-item"><i class="fas fa-exclamation-circle me-2"></i><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($exito): ?>
                <div class="alert alert-success">
                    <p class="success-message">
                        <i class="fas fa-check-circle me-2"></i>
                        ¡Registro exitoso! Serás redirigido al inicio de sesión en unos segundos.
                    </p>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = "loginUsuario.php";
                    }, 3000);
                </script>
            <?php else: ?>
                <form method="post">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="50"
                                value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellido" class="form-label">Apellido *</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" maxlength="50"
                                value="<?= isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : '' ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico *</label>
                        <input type="email" class="form-control" id="email" name="email" maxlength="100"
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Contraseña *</label>
                            <input type="password" class="form-control" id="password" name="password" maxlength="100" required>
                            <small class="text-muted">Mínimo 8 y máximo 100 caracteres</small>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Confirmar Contraseña *</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" maxlength="100" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-register">
                        <i class="fas fa-user-plus me-2"></i> Registrarse
                    </button>

                    <div class="login-link">
                        <p>¿Ya tienes una cuenta? <a href="loginUsuario.php">Inicia sesión aquí</a></p>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/assets/js/registro.js"></script>
    <script>
        // Mostrar advertencia al llegar al límite de caracteres
        function mostrarAdvertencia(input, max, mensaje) {
            let aviso = input.parentNode.querySelector('.aviso-limite');
            if (!aviso) {
                aviso = document.createElement('div');
                aviso.className = 'aviso-limite text-danger small';
                input.parentNode.appendChild(aviso);
            }
            if (input.value.length >= max) {
                aviso.textContent = mensaje;
            } else {
                aviso.textContent = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const campos = [{
                    id: 'nombre',
                    max: 50,
                    msg: 'Máximo 50 caracteres'
                },
                {
                    id: 'apellido',
                    max: 50,
                    msg: 'Máximo 50 caracteres'
                },
                {
                    id: 'email',
                    max: 100,
                    msg: 'Máximo 100 caracteres'
                },
                {
                    id: 'password',
                    max: 100,
                    msg: 'Máximo 100 caracteres'
                },
                {
                    id: 'confirm_password',
                    max: 100,
                    msg: 'Máximo 100 caracteres'
                }
            ];
            campos.forEach(campo => {
                const input = document.getElementById(campo.id);
                if (input) {
                    input.addEventListener('input', function() {
                        mostrarAdvertencia(input, campo.max, campo.msg);
                    });
                }
            });

            // Validación visual de contraseñas
            const pass = document.getElementById('password');
            const confirm = document.getElementById('confirm_password');
            if (pass && confirm) {
                function checkMatch() {
                    let aviso = confirm.parentNode.querySelector('.aviso-match');
                    if (!aviso) {
                        aviso = document.createElement('div');
                        aviso.className = 'aviso-match text-danger small';
                        confirm.parentNode.appendChild(aviso);
                    }
                    if (confirm.value && pass.value !== confirm.value) {
                        aviso.textContent = 'Las contraseñas no coinciden';
                    } else {
                        aviso.textContent = '';
                    }
                }
                pass.addEventListener('input', checkMatch);
                confirm.addEventListener('input', checkMatch);
            }
        });
    </script>
</body>

</html>