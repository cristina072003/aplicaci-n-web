document.addEventListener('DOMContentLoaded', function() {
    // Validación de contraseña en tiempo real
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            if (password.length > 0 && password.length < 8) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }

    // Validar que las contraseñas coincidan
    const confirmPasswordInput = document.getElementById('confirm_password');
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmacion = this.value;

            if (confirmacion !== password && confirmacion.length > 0) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }

    // Validación del formulario antes de enviar
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let valid = true;
            
            // Validar contraseña
            if (passwordInput.value.length < 8) {
                passwordInput.classList.add('is-invalid');
                valid = false;
            }
            
            // Validar coincidencia de contraseñas
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.classList.add('is-invalid');
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
                
                // Mostrar mensaje de error general
                if (!document.querySelector('.error-general')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger error-general mt-3';
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Por favor corrige los errores en el formulario';
                    form.insertBefore(errorDiv, form.firstChild);
                }
            }
        });
    }
});