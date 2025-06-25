$(document).ready(function() {
    // Inicializar Lightbox (código existente)
    if (typeof lightbox !== 'undefined') {
        lightbox.option({
          'resizeDuration': 200,
          'wrapAround': true,
          'fadeDuration': 300,
          'imageFadeDuration': 300
        });
    }

    // Lógica para Favoritos
    const $btnFavorito = $('.btn-agregar-favoritos'); // Selector del botón que proporcionaste en la tarea
    const $favTextSpan = $('#favText'); // El span que contiene el texto del botón
    const $favIcon = $btnFavorito.find('i.bi'); // El icono dentro del botón

    if ($btnFavorito.length > 0) {
        const vehId = $btnFavorito.data('veh-id');

        function actualizarBotonUI(esFavorito) {
            // Guardar el estado actual en el botón para referencia en el click handler
            $btnFavorito.data('es-favorito', esFavorito);

            if (esFavorito) {
                if ($favTextSpan.length) $favTextSpan.text('Quitar de Favoritos');
                if ($favIcon.length) $favIcon.removeClass('bi-heart').addClass('bi-heart-fill');
                $btnFavorito.removeClass('btn-outline-danger').addClass('btn-danger');
            } else {
                if ($favTextSpan.length) $favTextSpan.text('Agregar a Favoritos');
                if ($favIcon.length) $favIcon.removeClass('bi-heart-fill').addClass('bi-heart');
                $btnFavorito.removeClass('btn-danger').addClass('btn-outline-danger');
            }
        }

        // Función para mostrar el spinner y deshabilitar el botón
        function iniciarCargaBoton() {
            $btnFavorito.prop('disabled', true);
            if ($favTextSpan.length) $favTextSpan.text('Procesando...'); // Ocultar texto y mostrar spinner
            // Si tienes un spinner específico, puedes añadirlo aquí. El código original tenía:
            // $btnFavorito.prepend('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>');
        }

        // Función para quitar el spinner y habilitar el botón
        function finalizarCargaBoton(esFavoritoOriginal) {
            $btnFavorito.prop('disabled', false);
            // $btnFavorito.find('.spinner-border').remove();
            // El texto se actualizará con actualizarBotonUI
            actualizarBotonUI(esFavoritoOriginal); // Restaurar texto basado en el estado conocido
        }


        // 1. Verificar estado inicial al cargar la página
        if (vehId) {
            iniciarCargaBoton(); // Mostrar carga mientras se verifica

            $.ajax({
                url: '../AJAX/favoritos_ajax.php',
                type: 'POST', // Estandarizado a POST en favoritos_ajax.php
                data: {
                    accion: 'verificar', // Acción estandarizada
                    veh_id: vehId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        actualizarBotonUI(response.esFavorito);
                    } else {
                        if ($favTextSpan.length) $favTextSpan.text('Error al verificar');
                        console.error('Error al verificar favorito:', response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if ($favTextSpan.length) $favTextSpan.text('Error (conexión)');
                    console.error('Error AJAX al verificar favorito:', textStatus, errorThrown, jqXHR.responseText);
                },
                complete: function() {
                    // Habilitar botón y restaurar texto según el estado obtenido (o error)
                    // La función actualizarBotonUI ya se llamó en success, o se mostró texto de error.
                    // Solo necesitamos asegurar que el botón esté habilitado si no lo hizo success/error.
                    $btnFavorito.prop('disabled', false);
                    // Si no hubo success, el texto de "Procesando..." o "Error..." podría quedarse.
                    // Para evitarlo, si no es success, podemos llamar a actualizarBotonUI con un estado conocido,
                    // o el estado que tenía antes de la llamada (si lo guardamos).
                    // Por ahora, success/error manejan el texto.
                }
            });
        }

        // 2. Manejar clic en el botón
        $btnFavorito.on('click', function(e) {
            e.preventDefault();

            if (!vehId) {
                console.error('No se pudo obtener el ID del vehículo desde el botón.');
                alert('Error: No se pudo identificar el vehículo.');
                return;
            }

            const esFavoritoActual = $(this).data('es-favorito'); // Leer estado guardado
            const accionParaEnviar = esFavoritoActual ? 'quitar' : 'agregar'; // Acciones estandarizadas

            iniciarCargaBoton();

            $.ajax({
                url: '../AJAX/favoritos_ajax.php',
                type: 'POST',
                data: {
                    accion: accionParaEnviar,
                    veh_id: vehId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' || response.status === 'info') {
                        actualizarBotonUI(response.esFavorito); // Actualizar con el estado devuelto por el servidor

                        // Notificación
                        if (response.message) {
                            console.log('Favoritos:', response.message);
                            // Aquí se podría integrar una librería de notificaciones (Toastr, Noty, etc.)
                            // Ejemplo: if (typeof Noty !== 'undefined') { new Noty({ text: response.message, type: response.status }).show(); }
                        }
                    } else {
                        // Error en la operación, revertir UI al estado anterior al clic
                        finalizarCargaBoton(esFavoritoActual);
                        alert('Error: ' + (response.message || 'No se pudo actualizar el favorito.'));
                        console.error('Error en acción de favorito:', response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    finalizarCargaBoton(esFavoritoActual); // Revertir UI
                    alert('Error de conexión al guardar favorito. Intente de nuevo.');
                    console.error('Error AJAX en acción de favorito:', textStatus, errorThrown, jqXHR.responseText);
                },
                complete: function() {
                    // El complete de la llamada de acción no necesita hacer mucho si success/error manejan el estado del botón.
                    // Asegurar que el botón esté habilitado si no lo hizo success/error.
                     $btnFavorito.prop('disabled', false);
                }
            });
        });
    } else {
        // console.log("Botón de favoritos no encontrado.");
    }
});