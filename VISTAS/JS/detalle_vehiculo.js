$(document).ready(function() {
    // Inicializar Lightbox
    lightbox.option({
      'resizeDuration': 200,
      'wrapAround': true,
      'fadeDuration': 300,
      'imageFadeDuration': 300
    });

    // Galería de miniaturas (si se quiere cambiar imagen principal al hacer clic en miniatura, sin lightbox)
    // $('.galeria-miniaturas img').on('click', function(e) {
    //     if (!$(this).closest('a[data-lightbox]').length) { // Evitar si es para lightbox
    //         e.preventDefault();
    //         var nuevaImagenSrc = $(this).data('fullimage');
    //         $('#imagenPrincipalVehiculo').attr('src', nuevaImagenSrc);
    //         // Opcional: actualizar el link del lightbox de la imagen principal
    //         $('#imagenPrincipalVehiculo').parent('a').attr('href', nuevaImagenSrc);
            
    //         $('.galeria-miniaturas img').removeClass('active-thumb');
    //         $(this).addClass('active-thumb');
    //     }
    // });


    // Lógica para "Agregar a Favoritos" (necesitará un endpoint AJAX)
    const $btnFavoritos = $('.btn-agregar-favoritos');
    const $favText = $('#favText');
    const vehiculoId = $btnFavoritos.data('veh-id');

    // TODO: Deberías verificar el estado inicial de favoritos al cargar la página (si el usuario está logueado)
    // function verificarEstadoFavorito() {
    //     if (vehiculoId && /* usuario está logueado - puedes obtenerlo de una variable JS global o data attribute */ ) {
    //         $.ajax({
    //             url: '../AJAX/favoritos_ajax.php', // Crear este archivo
    //             type: 'GET',
    //             data: { accion: 'verificarEstado', veh_id: vehiculoId },
    //             dataType: 'json',
    //             success: function(response) {
    //                 if (response.status === 'success' && response.esFavorito) {
    //                     $favText.text('Quitar de Favoritos');
    //                     $btnFavoritos.addClass('active'); // O una clase específica para "ya es favorito"
    //                 } else {
    //                     $favText.text('Agregar a Favoritos');
    //                     $btnFavoritos.removeClass('active');
    //                 }
    //             }
    //         });
    //     }
    // }
    // verificarEstadoFavorito();

    function actualizarBotonFavorito(esFavorito) {
        if (esFavorito) {
            $favTextSpan.text('Quitar de Favoritos');
            $btnFavoritos.addClass('active btn-danger').removeClass('btn-outline-danger');
            $btnFavoritos.find('i').removeClass('bi-heart').addClass('bi-heart-fill');
        } else {
            $favTextSpan.text('Agregar a Favoritos');
            $btnFavoritos.removeClass('active btn-danger').addClass('btn-outline-danger');
            $btnFavoritos.find('i').removeClass('bi-heart-fill').addClass('bi-heart');
        }
    }

    function verificarEstadoFavorito() {
        // Solo ejecutar si el botón existe (significa que el usuario está logueado y el botón se renderizó)
        if (vehiculoId && $btnFavoritos.length) { 
            $.ajax({
                url: '../AJAX/favoritos_ajax.php',
                type: 'GET',
                data: { accion: 'verificarEstado', veh_id: vehiculoId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        actualizarBotonFavorito(response.esFavorito);
                    } else {
                        console.warn("No se pudo verificar el estado de favorito:", response.message);
                    }
                },
                error: function() {
                    console.error("Error de conexión al verificar estado de favorito.");
                }
            });
        }
    }
    verificarEstadoFavorito(); // Llamar al cargar la página


    $btnFavoritos.on('click', function() {
        if (!vehiculoId) return;

        const esActualmenteFavorito = $(this).hasClass('active'); // Más fiable que el texto
        const accionFavorito = esActualmenteFavorito ? 'quitarFavorito' : 'agregarFavorito';
        const $self = $(this); // Guardar referencia al botón

        $self.prop('disabled', true).find('span:not(.spinner-border)').hide(); // Ocultar texto, no el spinner
        $self.prepend('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>');


        $.ajax({
            url: '../AJAX/favoritos_ajax.php',
            type: 'POST',
            data: { accion: accionFavorito, veh_id: vehiculoId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' || (response.status === 'info' && (accionFavorito === 'agregarFavorito' || accionFavorito === 'quitarFavorito'))) {
                    // 'info' puede ser cuando ya era favorito y se intentó agregar, o no era y se intentó quitar.
                    // En cualquier caso, actualizamos el botón al estado real.
                    if (response.message && (response.status !== 'info' || accionFavorito === 'agregarFavorito' && !esActualmenteFavorito || accionFavorito === 'quitarFavorito' && esActualmenteFavorito )) {
                       // Mostrar mensaje solo si realmente cambió o hubo un mensaje específico de éxito.
                       // showGlobalSuccess(response.message); // Si tienes una función global de toast
                    }
                    verificarEstadoFavorito(); // Recargar estado del botón desde la BD
                } else {
                    alert('Error: ' + (response.message || 'No se pudo actualizar favoritos.'));
                }
            },
            error: function() { alert('Error de conexión al procesar favoritos.'); },
            complete: function() {
                $self.prop('disabled', false).find('.spinner-border').remove();
                $self.find('span#favText').show(); // Mostrar de nuevo el texto del botón
                // El texto del botón se actualizará con verificarEstadoFavorito
            }
        });
    });

});