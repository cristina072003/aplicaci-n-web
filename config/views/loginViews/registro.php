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

    // Validaciones de solo letras para nombre y apellido
    if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/u', $nombre)) {
        $errores[] = "El nombre solo puede contener letras y espacios";
    }
    if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/u', $apellido)) {
        $errores[] = "El apellido solo puede contener letras y espacios";
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
    <title>Registro de Usuario</title>
    <!-- DaisyUI y Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.13/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="../../public/assets/css/login_registro.css">
    <style>
        body {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            transition: background-image 0.5s;
        }
    </style>
    <script>
        function setLoginBg() {
            const horizontal = '../../public/assets/images/fondo_horizontal.png';
            const vertical = '../../public/assets/images/fondo_vertical.png';
            if (window.matchMedia('(orientation: landscape)').matches) {
                document.body.style.backgroundImage = `url('${horizontal}')`;
            } else {
                document.body.style.backgroundImage = `url('${vertical}')`;
            }
        }
        window.addEventListener('DOMContentLoaded', setLoginBg);
        window.addEventListener('orientationchange', setLoginBg);
        window.addEventListener('resize', setLoginBg);
    </script>
</head>

<body>
    <div class="login-container mx-2 sm:mx-4">
        <!-- Avatar con el diseño de anillo -->
        <div class="avatar-container">
            <div class="avatar-ring">
                <img src="../../public/assets/images/logo.png"
                    alt="Avatar de usuario"
                    class="avatar-img">
            </div>
        </div>

        <!-- Título de bienvenida -->
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Registro de Usuario</h2>
        <p class="text-center text-gray-600 mb-6">Completa el formulario para crear tu cuenta</p>

        <!-- Mensajes de error -->
        <?php if (!empty($errores)): ?>
            <div class="error-message alert mt-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul class="error-list mb-0">
                        <?php foreach ($errores as $error): ?>
                            <li class="error-item"> <?= $error ?> </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- Mensaje de éxito -->
        <?php if ($exito): ?>
            <div class="alert alert-success mt-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span class="success-message">¡Registro exitoso! Serás redirigido al inicio de sesión en unos segundos.</span>
                </div>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = "loginUsuario.php";
                }, 3000);
            </script>
        <?php else: ?>
            <!-- Formulario de registro -->
            <form method="post" class="space-y-4">
                <div class="form-control">
                    <label class="input input-bordered flex items-center gap-2 input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" class="grow" id="nombre" name="nombre" maxlength="50"
                            value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>" required
                            pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+" placeholder="Nombre" title="Solo letras y espacios" />
                    </label>
                </div>
                <div class="form-control">
                    <label class="input input-bordered flex items-center gap-2 input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" class="grow" id="apellido" name="apellido" maxlength="50"
                            value="<?= isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : '' ?>" required
                            pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+" placeholder="Apellido" title="Solo letras y espacios" />
                    </label>
                </div>
                <div class="form-control">
                    <label class="input input-bordered flex items-center gap-2 input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" class="grow" id="email" name="email" maxlength="100"
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required
                            placeholder="Correo electrónico" />
                    </label>
                </div>
                <div class="form-control">
                    <label class="input input-bordered flex items-center gap-2 input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="grow" id="password" name="password" maxlength="100" required
                            placeholder="Contraseña" />
                    </label>
                    <small class="text-muted">Mínimo 8 y máximo 100 caracteres</small>
                </div>
                <div class="form-control">
                    <label class="input input-bordered flex items-center gap-2 input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="grow" id="confirm_password" name="confirm_password" maxlength="100" required
                            placeholder="Confirmar contraseña" />
                    </label>
                    <div id="aviso-match-container" class="w-full mt-1"></div>
                </div>
                <button type="submit" class="btn login-btn w-full mt-4">
                    <i class="fas fa-user-plus mr-2"></i> Registrarse
                </button>
                <div class="text-center mt-4">
                    ¿Ya tienes cuenta?
                    <a href="loginUsuario.php" class="text-blue-700 font-semibold hover:underline">Inicia sesión aquí</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
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
            const avisoMatchContainer = document.getElementById('aviso-match-container');
            if (pass && confirm && avisoMatchContainer) {
                function checkMatch() {
                    let aviso = avisoMatchContainer.querySelector('.aviso-match');
                    if (!aviso) {
                        aviso = document.createElement('div');
                        aviso.className = 'aviso-match text-red-600 text-xs sm:text-sm w-full break-words mt-1';
                        avisoMatchContainer.appendChild(aviso);
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

        // Validación en tiempo real para nombre y apellido (solo letras y espacios)
        function soloLetrasEspacios(e) {
            let valor = e.target.value;
            e.target.value = valor.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ ]/g, '');
        }
        document.getElementById('nombre').addEventListener('input', soloLetrasEspacios);
        document.getElementById('apellido').addEventListener('input', soloLetrasEspacios);
    </script>
</body>

</html>