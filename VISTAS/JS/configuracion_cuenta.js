$(document).ready(function() {
    const $formPerfil = $('#formPerfil');
    const $formCambiarPassword = $('#formCambiarPassword');
    const $mensajeGeneral = $('#mensajeGeneral');

    // --- Función de Validación de Cédula Ecuatoriana ---
    function validarCedulaEC(cedula) {
        if (typeof cedula !== 'string' || (cedula.length !== 10 && cedula.length !== 13)) {
            return false;
        }

        // Validación para RUC de persona natural o jurídica (primeros 10 dígitos como cédula)
        // o cédula directamente.
        const numeroProvincia = parseInt(cedula.substring(0, 2), 10);
        if (numeroProvincia < 1 || numeroProvincia > 24) { // Hay 24 provincias
            // O > 30 si se incluyen códigos especiales para empresas públicas, etc.
            // Para simplificar, validamos las provincias estándar.
            return false;
        }
        
        // Para RUC de persona natural, el tercer dígito es < 6
        // Para cédula, el tercer dígito es < 6
        // Para RUC de sociedad privada o pública, el tercer dígito es 9 o 6 respectivamente.
        // Esta función se enfoca en cédula o RUC persona natural.
        const tercerDigito = parseInt(cedula.charAt(2), 10);
        if (cedula.length === 13 && !(tercerDigito === 9 || tercerDigito === 6 || tercerDigito < 6 )) {
             // Si es RUC y el tercer digito no es 9 (privada) o 6 (publica) o <6 (natural) no es valido.
             // Para simplificar, si es RUC y no es persona natural, no lo validamos con este algoritmo.
             // Se podría tener una validación de RUC más completa.
        } else if (cedula.length === 10 && tercerDigito >= 6) {
            return false; // Cédula normal no puede tener 3er dígito >= 6
        }


        const coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        let suma = 0;
        const digitos = cedula.substring(0, 9).split('').map(Number);

        for (let i = 0; i < digitos.length; i++) {
            let valor = digitos[i] * coeficientes[i];
            if (valor >= 10) {
                valor -= 9;
            }
            suma += valor;
        }

        const digitoVerificadorCalculado = (suma % 10 === 0) ? 0 : 10 - (suma % 10);
        const digitoVerificadorObtenido = parseInt(cedula.charAt(9), 10);

        if (digitoVerificadorCalculado !== digitoVerificadorObtenido) {
            return false;
        }

        // Si es RUC (13 dígitos), verificar que termine en "001", "002", etc.
        if (cedula.length === 13) {
            if (cedula.substring(10, 13) === '000') return false; // No puede ser 000
            // Aquí se podría añadir una validación más estricta del RUC si se desea.
        }
        return true;
    }

    // --- Helper para mostrar/ocultar mensajes de validación ---
    function setValidationMessage($input, message, isValid) {
        const $feedback = $input.next('.invalid-feedback');
        if (isValid) {
            $input.removeClass('is-invalid').addClass('is-valid');
            if ($feedback.length) $feedback.text(''); // Limpiar mensaje si es válido
        } else {
            $input.removeClass('is-valid').addClass('is-invalid');
            if ($feedback.length) $feedback.text(message);
        }
    }
    
    function clearValidation($form) {
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.is-valid').removeClass('is-valid');
        $form.find('.invalid-feedback').text('');
        $mensajeGeneral.addClass('d-none').removeClass('alert-success alert-danger alert-warning');
    }

    // --- Validación y Envío del Formulario de Perfil ---
    $formPerfil.on('submit', function(e) {
        e.preventDefault();
        clearValidation($formPerfil);
        let isValidForm = true;

        const $nombre = $('#usu_nombre');
        const $apellido = $('#usu_apellido');
        const $cedula = $('#usu_cedula');
        const $fnacimiento = $('#usu_fnacimiento');
        // Teléfono y dirección son opcionales según la BD, pero puedes añadir validaciones de formato si se ingresan.

        if ($nombre.val().trim() === '') {
            setValidationMessage($nombre, 'El nombre es requerido.', false);
            isValidForm = false;
        } else {
            setValidationMessage($nombre, '', true);
        }

        if ($apellido.val().trim() === '') {
            setValidationMessage($apellido, 'El apellido es requerido.', false);
            isValidForm = false;
        } else {
            setValidationMessage($apellido, '', true);
        }

        const cedulaVal = $cedula.val().trim();
        if (cedulaVal === '') {
            setValidationMessage($cedula, 'La cédula es requerida.', false);
            isValidForm = false;
        } else if (!/^\d+$/.test(cedulaVal) || (cedulaVal.length !== 10 && cedulaVal.length !== 13)) {
            setValidationMessage($cedula, 'La cédula debe tener 10 o 13 dígitos numéricos.', false);
            isValidForm = false;
        } else if (cedulaVal.length === 10 && !validarCedulaEC(cedulaVal)) {
            setValidationMessage($cedula, 'La cédula ingresada no es válida.', false);
            isValidForm = false;
        } else if (cedulaVal.length === 13 && !validarCedulaEC(cedulaVal.substring(0,10))) { 
            // Si es RUC, validamos los primeros 10 dígitos como cédula (simplificación)
            // Una validación de RUC completa es más compleja.
            setValidationMessage($cedula, 'El RUC persona natural no parece válido (verifique los primeros 10 dígitos).', false);
            isValidForm = false;
        }
         else {
            setValidationMessage($cedula, '', true);
        }
        
        if ($fnacimiento.val() !== '') {
            const fechaNac = new Date($fnacimiento.val());
            const hoy = new Date();
            hoy.setHours(0,0,0,0); // Para comparar solo fechas
            if (isNaN(fechaNac.getTime()) || fechaNac > hoy) {
                setValidationMessage($fnacimiento, 'Ingresa una fecha de nacimiento válida y no futura.', false);
                isValidForm = false;
            } else {
                 setValidationMessage($fnacimiento, '', true);
            }
        } else {
            // Si es opcional y está vacío, es válido. Si es requerido, se manejaría como los otros.
            // En este caso, el SP lo maneja como NULL si está vacío.
            $fnacimiento.removeClass('is-invalid is-valid');
        }


        if (!isValidForm) return;

        const $btnGuardarPerfil = $('#btnGuardarPerfil');
        const originalButtonText = $btnGuardarPerfil.html();
        $btnGuardarPerfil.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...');

        $.ajax({
            url: '../AJAX/cuenta_ajax.php',
            type: 'POST',
            data: $formPerfil.serialize() + '&accion=actualizar_perfil',
            dataType: 'json',
            success: function(response) {
                $mensajeGeneral.removeClass('d-none alert-danger alert-warning alert-success');
                if (response.status === 'success') {
                    $mensajeGeneral.addClass('alert-success').html('<i class="bi bi-check-circle-fill me-2"></i>' + response.message);
                     // Opcional: actualizar los valores en los campos si el SP devuelve los datos actualizados
                } else if (response.status === 'duplicate_cedula') {
                    $mensajeGeneral.addClass('alert-warning').html('<i class="bi bi-exclamation-triangle-fill me-2"></i>' + response.message);
                    setValidationMessage($cedula, response.message, false);
                } 
                else {
                    $mensajeGeneral.addClass('alert-danger').html('<i class="bi bi-x-circle-fill me-2"></i>' + (response.message || 'Error desconocido al actualizar.'));
                }
                $('html, body').animate({ scrollTop: $mensajeGeneral.offset().top - 70 }, 500); // Scroll hacia el mensaje
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $mensajeGeneral.removeClass('d-none alert-success alert-warning').addClass('alert-danger')
                    .html('<i class="bi bi-exclamation-octagon-fill me-2"></i>Error de conexión o del servidor. Intenta de nuevo.');
                console.error("Error AJAX (perfil): ", textStatus, errorThrown, jqXHR.responseText);
                $('html, body').animate({ scrollTop: $mensajeGeneral.offset().top - 70 }, 500);
            },
            complete: function() {
                $btnGuardarPerfil.prop('disabled', false).html(originalButtonText);
            }
        });
    });

    // --- Validación y Envío del Formulario de Cambio de Contraseña ---
    $formCambiarPassword.on('submit', function(e) {
        e.preventDefault();
        clearValidation($formCambiarPassword);
        let isValidForm = true;

        const $passActual = $('#pass_actual');
        const $passNueva = $('#pass_nueva');
        const $passConfirmar = $('#pass_confirmar');

        if ($passActual.val() === '') {
            setValidationMessage($passActual, 'Ingresa tu contraseña actual.', false);
            isValidForm = false;
        } else {
            setValidationMessage($passActual, '', true); // Solo validamos que no esté vacío aquí
        }

        const nuevaPassVal = $passNueva.val();
        if (nuevaPassVal === '') {
            setValidationMessage($passNueva, 'Ingresa una nueva contraseña.', false);
            isValidForm = false;
        } else if (nuevaPassVal.length < 8) {
            setValidationMessage($passNueva, 'La nueva contraseña debe tener al menos 8 caracteres.', false);
            isValidForm = false;
        } else {
            setValidationMessage($passNueva, '', true);
        }

        if ($passConfirmar.val() === '') {
            setValidationMessage($passConfirmar, 'Confirma tu nueva contraseña.', false);
            isValidForm = false;
        } else if ($passConfirmar.val() !== nuevaPassVal) {
            setValidationMessage($passConfirmar, 'Las nuevas contraseñas no coinciden.', false);
            isValidForm = false;
        } else {
            setValidationMessage($passConfirmar, '', true);
        }

        if (!isValidForm) return;

        const $btnCambiarPassword = $('#btnCambiarPassword');
        const originalButtonText = $btnCambiarPassword.html();
        $btnCambiarPassword.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Actualizando...');

        $.ajax({
            url: '../AJAX/cuenta_ajax.php',
            type: 'POST',
            data: $formCambiarPassword.serialize() + '&accion=cambiar_password',
            dataType: 'json',
            success: function(response) {
                $mensajeGeneral.removeClass('d-none alert-danger alert-warning alert-success');
                if (response.status === 'success') {
                    $mensajeGeneral.addClass('alert-success').html('<i class="bi bi-check-circle-fill me-2"></i>' + response.message);
                    $formCambiarPassword[0].reset(); // Limpiar formulario
                    clearValidation($formCambiarPassword); // Limpiar estilos de validación
                } else if (response.status === 'auth_error') {
                    $mensajeGeneral.addClass('alert-warning').html('<i class="bi bi-exclamation-triangle-fill me-2"></i>' + response.message);
                    setValidationMessage($passActual, response.message, false);
                } else { // validation_error u otro error
                    $mensajeGeneral.addClass('alert-danger').html('<i class="bi bi-x-circle-fill me-2"></i>' + (response.message || 'Error desconocido al cambiar contraseña.'));
                }
                 $('html, body').animate({ scrollTop: $mensajeGeneral.offset().top - 70 }, 500);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                 $mensajeGeneral.removeClass('d-none alert-success alert-warning').addClass('alert-danger')
                    .html('<i class="bi bi-exclamation-octagon-fill me-2"></i>Error de conexión o del servidor. Intenta de nuevo.');
                console.error("Error AJAX (password): ", textStatus, errorThrown, jqXHR.responseText);
                 $('html, body').animate({ scrollTop: $mensajeGeneral.offset().top - 70 }, 500);
            },
            complete: function() {
                $btnCambiarPassword.prop('disabled', false).html(originalButtonText);
            }
        });
    });
});
