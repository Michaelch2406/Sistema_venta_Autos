$(document).ready(function() {
    const $listaFavoritosContainer = $('#lista-favoritos-container');

    // Usar delegación de eventos para los botones "Quitar de Favoritos"
    // ya que las tarjetas se renderizan por PHP.
    $listaFavoritosContainer.on('click', '.btn-quitar-favorito-lista', function() {
        const $btnQuitar = $(this);
        const vehId = $btnQuitar.data('veh-id');
        const $tarjetaVehiculo = $btnQuitar.closest('.favorito-card-item'); // El div que contiene toda la tarjeta

        if (!vehId) {
            console.error('No se pudo obtener el ID del vehículo desde el botón.');
            alert('Error: No se pudo identificar el vehículo para quitar.');
            return;
        }

        // Confirmación (opcional pero recomendado)
        if (!confirm('¿Estás seguro de que quieres quitar este vehículo de tus favoritos?')) {
            return;
        }

        // Feedback visual mientras se procesa
        $btnQuitar.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Eliminando...');

        $.ajax({
            url: '../AJAX/favoritos_ajax.php',
            type: 'POST',
            data: {
                accion: 'quitar',
                veh_id: vehId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' || (response.status === 'info' && !response.esFavorito) ) {
                    // Si se quitó exitosamente o si ya no era favorito (info)
                    $tarjetaVehiculo.fadeOut(400, function() {
                        $(this).remove();
                        // Verificar si quedan más favoritos
                        if ($listaFavoritosContainer.find('.favorito-card-item').length === 0) {
                            // Si no quedan, mostrar mensaje de "no hay favoritos"
                            // Este mensaje debería estar preparado en el HTML y oculto, o se genera aquí.
                            // Por simplicidad, asumimos que el usuario recargará o que un mensaje se muestra por PHP si la lista está vacía en la carga.
                            // Para una mejor UX, aquí se podría inyectar el mensaje de "no tienes favoritos".
                            // Ejemplo simple:
                            $listaFavoritosContainer.html(
                                '<div class="col-12 text-center py-5">' +
                                '  <i class="bi bi-emoji-neutral display-1 text-muted mb-3"></i>' +
                                '  <h4 class="mt-3">Ya no tienes vehículos favoritos guardados.</h4>' +
                                '  <p class="text-muted">Explora nuestro catálogo para encontrar nuevos.</p>' +
                                '  <a href="autos_usados.php" class="btn btn-primary mt-3">' +
                                '    <i class="bi bi-search me-2"></i>Explorar Vehículos' +
                                '  </a>' +
                                '</div>'
                            );
                        }
                    });
                    
                    // Opcional: Notificación de éxito
                    // if (response.message) { console.log(response.message); }
                    // Ejemplo con Noty si estuviera disponible:
                    // if (typeof Noty !== 'undefined') { new Noty({ text: response.message || 'Vehículo quitado de favoritos.', type: 'success' }).show(); }

                } else {
                    alert('Error: ' + (response.message || 'No se pudo quitar el vehículo de favoritos.'));
                    $btnQuitar.prop('disabled', false).html('<i class="bi bi-trash3 me-2"></i>Quitar de Favoritos'); // Restaurar botón
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error de conexión al intentar quitar el favorito.');
                console.error('Error AJAX al quitar favorito:', textStatus, errorThrown, jqXHR.responseText);
                $btnQuitar.prop('disabled', false).html('<i class="bi bi-trash3 me-2"></i>Quitar de Favoritos'); // Restaurar botón
            }
        });
    });
});
