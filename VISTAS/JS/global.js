$(document).ready(function() {
    // Cargar el Navbar y luego ejecutar el código dependiente del Navbar
    $("#navbar-placeholder").load("../VISTAS/partials/navbar.php", function(response, status, xhr) {
        if (status == "error") {
            console.error("Error al cargar navbar.php: " + xhr.status + " " + xhr.statusText);
            $("#navbar-placeholder").html("<p class='text-center text-danger'>Error al cargar la barra de navegación.</p>");
            return;
        }

        // Efecto: Cambiar la apariencia del Navbar al hacer scroll
        $(window).scroll(function() {
            if ($(document).scrollTop() > 50) {
                $('#navbar-placeholder .navbar.fixed-top, .navbar.fixed-top').addClass('navbar-scrolled');
            } else {
                $('#navbar-placeholder .navbar.fixed-top, .navbar.fixed-top').removeClass('navbar-scrolled');
            }
        });

        // Lógica de Active Link (manejada en navbar.php, pero si se necesita un fallback o ajuste JS)
        // Opcional: El active link es mejor manejarlo en PHP en navbar.php, pero si se requiere JS:
        var currentPageForNav = window.location.pathname.split("/").pop();
        if (currentPageForNav === "" || currentPageForNav === "index.php") { // Asumiendo que tu raíz podría ser index.php
             currentPageForNav = "inicio.php"; // Si estás en la raíz, considera inicio.php como activa
        }
        
        $('#navbar-placeholder .navbar-nav .nav-link').each(function() {
            $(this).removeClass('active').removeAttr('aria-current');
            var linkPage = $(this).attr('href').split("/").pop();
            if (linkPage === currentPageForNav) {
                $(this).addClass('active');
                $(this).attr('aria-current', 'page');
            }
        });

        // Lógica para mostrar/ocultar contraseña (general, para cualquier botón con la clase)
        // Se puede mover a un archivo auth_utils.js si se quiere ser más granular
        $('.toggle-password').off('click').on('click', function() {
            var icon = $(this).find('i');
            var targetInputId = $(this).data('target');
            var input = $('#' + targetInputId);
            if (input.length) { // Verificar que el input existe
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                }
            }
        });
    }); // Fin del callback de .load() para navbar

    // --- Lógica para el Page Loader ---
    $(window).on('load', function() {
        $('#page-loader').fadeOut(500, function() {
            // $(this).remove(); // Opcional
        });
        $('main.content-hidden').removeClass('content-hidden').fadeIn(500);
        $('footer.content-hidden').removeClass('content-hidden').fadeIn(500);
    });

    // --- Script de Validación de Formularios Bootstrap (General) ---
    // Este script se aplica a cualquier formulario con la clase .needs-validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                // La validación específica (como coincidencia de contraseñas)
                // se manejará en el JS específico de la página antes de esta validación general.
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
});