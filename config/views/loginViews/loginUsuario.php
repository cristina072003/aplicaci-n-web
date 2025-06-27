<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema</title>
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
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Bienvenido de vuelta</h2>
        <p class="text-center text-gray-600 mb-6">Ingresa a tu cuenta para continuar</p>

        <!-- Formulario de login -->
        <form action="../../controllers/loginControllers/loginAction.php" method="post" class="space-y-4">
            <!-- Campo de email -->
            <div class="form-control">
                <label class="input input-bordered flex items-center gap-2 input-field">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70">
                        <path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" />
                        <path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" />
                    </svg>
                    <input type="email" id="email" name="email"
                        class="grow"
                        placeholder="Correo electrónico"
                        required />
                </label>
            </div>

            <!-- Campo de contraseña -->
            <div class="form-control">
                <label class="input input-bordered flex items-center gap-2 input-field">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70">
                        <path fill-rule="evenodd" d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z" clip-rule="evenodd" />
                    </svg>
                    <input type="password" id="password" name="password"
                        class="grow"
                        placeholder="Contraseña"
                        required />
                </label>
            </div>

            <!-- Botón de login -->
            <button type="submit" class="btn login-btn w-full mt-4">
                <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
            </button>

            <!-- Enlace de registro -->
            <div class="text-center mt-4">
                ¿No tienes cuenta?
                <a href="registro.php" class="text-blue-700 font-semibold hover:underline">Regístrate</a>
            </div>

            <!-- Mensaje de error -->
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message alert mt-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>
                            <?php
                            switch ($_GET['error']) {
                                case 'credenciales':
                                case 'credenciales_incorrectas':
                                    echo 'Correo o contraseña incorrectos';
                                    break;
                                case 'inactivo':
                                    echo 'Tu cuenta está desactivada';
                                    break;
                                case 'db_error':
                                    echo 'Error en el sistema. Por favor, intente más tarde';
                                    break;
                                case 'campos_vacios':
                                    echo 'Por favor, completa todos los campos';
                                    break;
                                case 'email_invalido':
                                    echo 'El correo electrónico no es válido';
                                    break;
                                case 'rol_no_valido':
                                    echo 'Tu rol no tiene acceso a esta sección';
                                    break;
                                case 'rol_no_definido':
                                    echo 'No se ha definido un rol para tu usuario';
                                    break;
                                case 'metodo_no_valido':
                                case 'metodo_no_permitido':
                                    echo 'Método de acceso no permitido';
                                    break;
                                case 'bloqueado':
                                    echo 'Demasiados intentos fallidos. Intenta nuevamente en 5 minutos.';
                                    break;
                                default:
                                    echo 'Error desconocido. Intenta nuevamente.';
                                    break;
                            }
                            ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>
</body>

</html>