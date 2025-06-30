$(document).ready(function() {
    const $listaVehiculosContainer = $('#listaVehiculosContainer');
    const $loadingVehiculos = $('#loadingVehiculos');
    const $noVehiculosMessage = $('#noVehiculosMessage');

    function cargarMisVehiculos() {
        $loadingVehiculos.show();
        $noVehiculosMessage.hide();
        $listaVehiculosContainer.find('.vehiculo-item-col').remove(); // Limpiar items anteriores

        $.ajax({
            url: '../AJAX/vehiculos_ajax.php',
            type: 'GET',
            data: { accion: 'getMisVehiculos' },
            dataType: 'json',
            success: function(response) {
                $loadingVehiculos.hide();
                if (response.status === 'success' && response.vehiculos && response.vehiculos.length > 0) {
                    $.each(response.vehiculos, function(index, vehiculo) {
                        let imagenUrl = vehiculo.imagen_principal_url ? vehiculo.imagen_principal_url : '../PUBLIC/Img/auto_placeholder.png'; // Placeholder si no hay imagen
                        // Asegurarse que la URL relativa de la imagen funcione desde la perspectiva del HTML
                        if (imagenUrl.startsWith('PUBLIC/')) { // Si la URL viene como PUBLIC/uploads/...
                           imagenUrl = '../' + imagenUrl;
                        }


                        let estadoClass = 'estado-' + vehiculo.veh_estado.toLowerCase();
                        let estadoTexto = vehiculo.veh_estado.charAt(0).toUpperCase() + vehiculo.veh_estado.slice(1);

                        const vehiculoCardHtml = `
                            <div class="col-md-6 col-lg-4 vehiculo-item-col">
                                <div class="card h-100 vehiculo-card shadow-sm">
                                    <img src="${imagenUrl}" class="card-img-top" alt="${vehiculo.mar_nombre} ${vehiculo.mod_nombre}">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">${vehiculo.mar_nombre} ${vehiculo.mod_nombre} <small class="text-muted">(${vehiculo.veh_anio})</small></h5>
                                        <p class="card-text mb-1"><span class="fw-bold">Condición:</span> ${vehiculo.veh_condicion.charAt(0).toUpperCase() + vehiculo.veh_condicion.slice(1)}</p>
                                        <p class="card-text mb-1"><span class="fw-bold">Precio:</span> $${parseFloat(vehiculo.veh_precio).toLocaleString('es-EC', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</p>
                                        <p class="card-text mb-1"><span class="fw-bold">Publicado:</span> ${new Date(vehiculo.veh_fecha_publicacion + 'T00:00:00').toLocaleDateString('es-EC')}</p>
                                        <p class="card-text mb-2"><span class="fw-bold">Estado:</span> <span class="${estadoClass}">${estadoTexto}</span></p>
                                        
                                        <div class="mt-auto d-flex justify-content-between align-items-center">
                                            <a href="editar_vehiculo.php?id=${vehiculo.veh_id}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i> Editar</a>
                                            <div class="dropdown actions-dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton-${vehiculo.veh_id}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Más Acciones
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton-${vehiculo.veh_id}">
                                                    <li><a class="dropdown-item" href="detalle_vehiculo.php?id=${vehiculo.veh_id}" target="_blank"><i class="bi bi-eye-fill me-2"></i>Ver Anuncio</a></li>
                                                    <li><a class="dropdown-item cambiar-estado-btn" href="#" data-id="${vehiculo.veh_id}" data-estado-actual="${vehiculo.veh_estado}" data-nuevo-estado="reservado"><i class="bi bi-calendar-check me-2"></i>Marcar como Reservado</a></li>
                                                    <li><a class="dropdown-item cambiar-estado-btn" href="#" data-id="${vehiculo.veh_id}" data-estado-actual="${vehiculo.veh_estado}" data-nuevo-estado="vendido"><i class="bi bi-currency-dollar me-2"></i>Marcar como Vendido</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item cambiar-estado-btn text-warning" href="#" data-id="${vehiculo.veh_id}" data-estado-actual="${vehiculo.veh_estado}" data-nuevo-estado="desactivado"><i class="bi bi-pause-circle me-2"></i>Desactivar Anuncio</a></li>
                                                    <li><a class="dropdown-item cambiar-estado-btn text-success" href="#" data-id="${vehiculo.veh_id}" data-estado-actual="${vehiculo.veh_estado}" data-nuevo-estado="disponible"><i class="bi bi-play-circle me-2"></i>Reactivar Anuncio</a></li>
                                                    <!-- <li><a class="dropdown-item text-danger eliminar-vehiculo-btn" href="#" data-id="${vehiculo.veh_id}"><i class="bi bi-trash3-fill me-2"></i>Eliminar Anuncio</a></li> -->
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-muted small">
                                        Total de Imágenes: ${vehiculo.total_imagenes}
                                    </div>
                                </div>
                            </div>
                        `;
                        $listaVehiculosContainer.append(vehiculoCardHtml);
                    });
                } else if (response.status === 'success' && response.vehiculos && response.vehiculos.length === 0) {
                    $noVehiculosMessage.show();
                } else {
                    $listaVehiculosContainer.append('<div class="col-12"><div class="alert alert-danger">Error al cargar tus vehículos: ' + response.message + '</div></div>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $loadingVehiculos.hide();
                $listaVehiculosContainer.append('<div class="col-12"><div class="alert alert-danger">Error de conexión al cargar tus vehículos. Por favor, intenta de nuevo.</div></div>');
                console.error("AJAX Error en getMisVehiculos:", jqXHR.responseText, textStatus, errorThrown);
            }
        });
    }

    // Manejar clic en botones de "Cambiar Estado" (Ejemplo)
    // Necesitarás un endpoint AJAX para esto (ej: accion=cambiarEstadoVehiculo)
    $listaVehiculosContainer.on('click', '.cambiar-estado-btn', function(e) {
        e.preventDefault();
        const vehiculoId = $(this).data('id');
        const nuevoEstado = $(this).data('nuevo-estado');
        const estadoActual = $(this).data('estado-actual');

        if (nuevoEstado === estadoActual) {
            alert("El vehículo ya se encuentra en este estado.");
            return;
        }

        if (confirm(`¿Estás seguro de que quieres cambiar el estado de este vehículo a "${nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1)}"?`)) {
            // console.log(`Simulando cambio de estado para vehículo ID: ${vehiculoId} a ${nuevoEstado}`);
            // Aquí harías la llamada AJAX para actualizar el estado en la BD
            $.ajax({
                url: '../AJAX/vehiculos_ajax.php', // Asegúrate que este endpoint maneje la acción
                type: 'POST',
                data: {
                    accion: 'cambiarEstadoVehiculo', // Necesitas implementar esta acción en el PHP
                    veh_id: vehiculoId,
                    nuevo_estado: nuevoEstado,
                    // Podrías enviar un token CSRF aquí por seguridad
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message || 'Estado del vehículo actualizado.');
                        cargarMisVehiculos(); // Recargar la lista para ver el cambio
                    } else {
                        alert('Error al actualizar estado: ' + (response.message || 'Error desconocido.'));
                    }
                },
                error: function() {
                    alert('Error de conexión al intentar cambiar el estado.');
                }
            });
        }
    });
    
    // Cargar vehículos al iniciar la página
    cargarMisVehiculos();
});