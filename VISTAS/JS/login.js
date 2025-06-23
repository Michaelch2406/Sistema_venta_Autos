$(document).ready(function () {
    $('#loginForm').on('submit', function (event) {
        var form = this; // Referencia al formulario
        // Validar primero con Bootstrap antes de enviar
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            $(form).addClass('was-validated');
            return;
        }
        $(form).addClass('was-validated'); // Aplicar estilos si pasa la validación inicial

        event.preventDefault(); // Siempre prevenir envío normal para AJAX

        var formData = $(this).serialize();
        var $submitButton = $(this).find('button[type="submit"]');
        var originalButtonText = $submitButton.html();
        $submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Ingresando...');

        $.ajax({
            url: '../AJAX/login_ajax.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            // En VISTAS/JS/login.js, dentro del success de AJAX:
            success: function (response) {
                if (response.status === 'success') {
                    if (response.redirect_url) { // Usar la URL de redirección proporcionada por el backend
                        window.location.href = response.redirect_url;
                    } else {
                        // Fallback por si redirect_url no viniera, aunque debería
                        window.location.href = 'escritorio.php';
                    }
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error de conexión o del servidor: ' + textStatus + " - " + errorThrown);
                console.error("AJAX Error en login:", jqXHR.responseText);
            },
            complete: function () {
                $submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });
});