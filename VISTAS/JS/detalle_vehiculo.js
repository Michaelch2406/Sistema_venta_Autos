$(document).ready(function() {
    
    // --- LÓGICA DE LIGHTBOX PARA GALERÍA ---
    if (typeof lightbox !== 'undefined') {
        lightbox.option({
          'resizeDuration': 200,
          'wrapAround': true,
          'fadeDuration': 300,
          'imageFadeDuration': 300
        });
    }

    // --- LÓGICA PARA AGREGAR/QUITAR FAVORITOS ---
    const $btnFavorito = $('.btn-agregar-favoritos');
    const $favTextSpan = $('#favText');
    const $favIcon = $btnFavorito.find('i.bi');

    if ($btnFavorito.length > 0) {
        const vehId = $btnFavorito.data('veh-id');

        function actualizarBotonUI(esFavorito) {
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

        function toggleBotonCarga(cargando) {
            $btnFavorito.prop('disabled', cargando);
            if (cargando) {
                if ($favTextSpan.length) $favTextSpan.text('Procesando...');
            } else {
                actualizarBotonUI($btnFavorito.data('es-favorito'));
            }
        }

        if (vehId) {
            toggleBotonCarga(true);
            $.ajax({
                url: '../AJAX/favoritos_ajax.php',
                type: 'POST',
                data: { accion: 'verificar', veh_id: vehId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        actualizarBotonUI(response.esFavorito);
                    }
                },
                error: function() { if ($favTextSpan.length) $favTextSpan.text('Error'); },
                complete: function() { toggleBotonCarga(false); }
            });
        }

        $btnFavorito.on('click', function(e) {
            e.preventDefault();
            const esFavoritoActual = $(this).data('es-favorito');
            const accionParaEnviar = esFavoritoActual ? 'quitar' : 'agregar';
            toggleBotonCarga(true);
            $.ajax({
                url: '../AJAX/favoritos_ajax.php',
                type: 'POST',
                data: { accion: accionParaEnviar, veh_id: vehId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' || response.status === 'info') {
                        actualizarBotonUI(response.esFavorito);
                    } else {
                        alert('Error: ' + (response.message || 'No se pudo actualizar el favorito.'));
                    }
                },
                error: function() { alert('Error de conexión al guardar favorito.'); },
                complete: function() { toggleBotonCarga(false); }
            });
        });
    }

    // --- LÓGICA PARA EL FORMULARIO DE CONTACTO/COTIZACIÓN ---
    $('#formContactoVendedor').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $submitButton = $('#btnEnviarCotizacion');
        var $messageContainer = $('#contactFormMessage');
        var originalButtonHtml = $submitButton.html();

        $submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...');
        $messageContainer.html('').removeClass('alert alert-success alert-danger');

        $.ajax({
            url: '../AJAX/cotizaciones_ajax.php',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $messageContainer.html('<div class="alert alert-success">' + response.message + '</div>');
                    $form.find('textarea, button').prop('disabled', true);
                    
                    setTimeout(function() {
                        var modalEl = document.getElementById('modalContactoVendedor');
                        if(modalEl) {
                           var modalInstance = bootstrap.Modal.getInstance(modalEl);
                           if(modalInstance) modalInstance.hide();
                        }
                    }, 4000);
                } else {
                    $messageContainer.html('<div class="alert alert-danger">' + (response.message || 'Ocurrió un error.') + '</div>');
                    $submitButton.prop('disabled', false).html(originalButtonHtml);
                }
            },
            error: function() {
                $messageContainer.html('<div class="alert alert-danger">Error de conexión. Por favor, inténtalo de nuevo.</div>');
                $submitButton.prop('disabled', false).html(originalButtonHtml);
            }
        });
    });
});