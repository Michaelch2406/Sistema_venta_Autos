// JavaScript Document
$(document).ready(function() {
    // --- Script de Validación de Formularios Bootstrap para la página de contacto ---
    // Busca todos los formularios a los que queremos aplicar estilos de validación
    var forms = document.querySelectorAll('.needs-validation');

    // Bucle sobre ellos y previene el envío si no son válidos
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');

                // Opcional: Si el formulario es válido, podrías mostrar un mensaje de "Enviando..."
                // y deshabilitar el botón para prevenir envíos múltiples.
                // Esto requeriría una lógica de backend para procesar el envío.
                // if (form.checkValidity()) {
                //     // Por ejemplo:
                //     $(form).find('button[type="submit"]').prop('disabled', true).html(`
                //         <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                //         Enviando...
                //     `);
                //     // Aquí iría la lógica de envío real (AJAX, etc.)
                // }

            }, false);
        });
    // --- Fin Script de Validación ---

    // Podrías añadir aquí más interacciones si fueran necesarias:
    // - Animaciones sutiles al hacer scroll a ciertas secciones.
    // - Interacción con un mapa (si se integra uno).
    // - Efectos en los inputs del formulario al obtener foco.

    // Ejemplo: Efecto de borde al hacer foco en los inputs del formulario de contacto
    $('#contactForm .form-control').on('focus', function() {
        $(this).css('border-color', '#0d6efd'); // Color primario de Bootstrap
    });
    $('#contactForm .form-control').on('blur', function() {
        $(this).css('border-color', ''); // Restablecer color de borde
    });

});