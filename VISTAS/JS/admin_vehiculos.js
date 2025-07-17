$(document).ready(function() {
    const modalConfirmarAccion = new bootstrap.Modal(document.getElementById('modalConfirmarAccion'));
    let tablaVehiculosAdmin;

    function inicializarTablaVehiculos() {
        if ($.fn.DataTable.isDataTable('#tablaVehiculosAdmin')) {
            tablaVehiculosAdmin.ajax.reload(null, false); // Recargar sin resetear paginación
            return;
        }

        tablaVehiculosAdmin = $('#tablaVehiculosAdmin').DataTable({
            processing: true,
            serverSide: false, // Se cambiará a true si se implementa server-side processing completo
            ajax: {
                url: './../AJAX/vehiculos_ajax.php?accion=getTodosLosVehiculosAdmin', // Esta acción necesita ser creada
                type: 'GET',
                dataType: 'json',
                dataSrc: function(json) {
                    if (json.status === 'success') {
                        return json.vehiculos;
                    } else {
                        console.error("Error al cargar vehículos:", json.message);
                        mostrarAlerta('error', json.message || 'Error al cargar los vehículos.');
                        return [];
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error AJAX:", status, error, xhr.responseText);
                    mostrarAlerta('error', 'Error de conexión al cargar vehículos: ' + error);
                }
            },
            columns: [
                { data: 'veh_id', title: 'ID' },
                { 
                    data: 'imagen_principal_url', 
                    title: 'Imagen',
                    render: function(data, type, row) {
                        let img_path = data ? '../' + data : '../PUBLIC/Img/auto_placeholder.png';
                        return `<img src="${img_path}" alt="Vehículo" class="img-thumbnail-list">`;
                    },
                    orderable: false 
                },
                { 
                    data: null, 
                    title: 'Título',
                    render: function(data, type, row) {
                        return `${row.mar_nombre || ''} ${row.mod_nombre || ''} (${row.veh_anio || ''})`;
                    }
                },
                { data: 'usu_nombre_completo', title: 'Publicador' }, // Asumiendo que el SP devolverá este campo
                { 
                    data: 'veh_precio', 
                    title: 'Precio',
                    render: function(data, type, row) {
                        return data ? '$' + parseFloat(data).toLocaleString('es-EC', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : 'N/A';
                    }
                },
                { data: 'veh_condicion', title: 'Condición' },
                { 
                    data: 'veh_estado_actual', 
                    title: 'Estado',
                    render: function(data, type, row) {
                        let badgeClass = 'estado-pendiente'; // Default
                        if (data) {
                            switch (data.toLowerCase()) {
                                case 'disponible': badgeClass = 'estado-disponible'; break;
                                case 'reservado': badgeClass = 'estado-reservado'; break;
                                case 'vendido': badgeClass = 'estado-vendido'; break;
                                case 'pendiente': badgeClass = 'estado-pendiente'; break;
                                case 'desactivado': badgeClass = 'estado-desactivado'; break;
                            }
                        }
                        return `<span class="badge ${badgeClass}">${data || 'Pendiente'}</span>`;
                    }
                },
                { 
                    data: 'veh_fecha_publicacion', 
                    title: 'Publicado',
                    render: function(data, type, row) {
                        return data ? new Date(data).toLocaleDateString() : 'N/A';
                    }
                },
                {
                    data: null,
                    title: 'Ubicación',
                    render: function(data, type, row) {
                        return `${row.veh_ubicacion_ciudad || ''}, ${row.veh_ubicacion_provincia || ''}`;
                    }
                },
                { 
                    data: null, 
                    title: 'Acciones',
                    render: function(data, type, row) {
                        let btnAprobar = '';
                        // Asumimos que 'pendiente' es el estado que requiere aprobación.
                        // O si se introduce un estado específico como 'requiere_aprobacion'
                        if (row.veh_estado_actual && (row.veh_estado_actual.toLowerCase() === 'pendiente' || row.veh_estado_actual.toLowerCase() === 'requiere_aprobacion')) {
                            btnAprobar = `<button class="btn btn-sm btn-success btn-accion-vehiculo" data-id="${row.veh_id}" data-accion="aprobar" title="Aprobar Vehículo"><i class="bi bi-check-circle-fill"></i></button>`;
                        }
                        
                        let btnDesactivar = `<button class="btn btn-sm btn-warning btn-accion-vehiculo" data-id="${row.veh_id}" data-accion="desactivar" title="Desactivar Anuncio"><i class="bi bi-eye-slash-fill"></i></button>`;
                        if (row.veh_estado_actual && row.veh_estado_actual.toLowerCase() === 'desactivado') {
                            btnDesactivar = `<button class="btn btn-sm btn-info btn-accion-vehiculo" data-id="${row.veh_id}" data-accion="reactivar" title="Reactivar Anuncio"><i class="bi bi-eye-fill"></i></button>`;
                        }

                        // El botón de editar ahora apunta a editar_auto.php
                        let btnEditar = `<a href="editar_auto.php?veh_id=${row.veh_id}" class="btn btn-sm btn-primary" title="Editar Vehículo"><i class="bi bi-pencil-square"></i></a>`;
                        
                        let btnEliminar = `<button class="btn btn-sm btn-danger btn-accion-vehiculo" data-id="${row.veh_id}" data-accion="eliminar" title="Eliminar Vehículo (Permanente)"><i class="bi bi-trash-fill"></i></button>`;

                        return `${btnAprobar} ${btnDesactivar} ${btnEditar} ${btnEliminar}`;
                    },
                    orderable: false 
                }
            ],
            language: {
                "decimal": ",",
                "emptyTable": "No hay datos disponibles en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "infoPostFix": "",
                "thousands": ".",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron registros coincidentes",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "aria": {
                    "sortAscending": ": activar para ordenar la columna ascendente",
                    "sortDescending": ": activar para ordenar la columna descendente"
                }
            },
            responsive: true,
            autoWidth: false,
            createdRow: function(row, data, dataIndex) {
                // Se puede usar para añadir clases o atributos a las filas si es necesario
            }
        });
    }

    $('#tablaVehiculosAdmin').on('click', '.btn-accion-vehiculo', function() {
        const vehId = $(this).data('id');
        const accion = $(this).data('accion');
        let mensajeModal = '';
        let nuevoEstado = '';
        let headerClass = 'bg-primary'; // Default
        let btnClass = 'btn-primary';

        $('#vehiculoIdConfirmar').val(vehId);
        $('#accionConfirmar').val(accion);

        switch (accion) {
            case 'aprobar':
                mensajeModal = `¿Está seguro de que desea APROBAR el vehículo ID ${vehId}? Esto lo hará visible.`;
                nuevoEstado = 'disponible';
                headerClass = 'bg-success';
                btnClass = 'btn-success';
                break;
            case 'desactivar':
                mensajeModal = `¿Está seguro de que desea DESACTIVAR el anuncio del vehículo ID ${vehId}? No será visible.`;
                nuevoEstado = 'desactivado';
                headerClass = 'bg-warning text-dark';
                btnClass = 'btn-warning';
                break;
            case 'reactivar':
                mensajeModal = `¿Está seguro de que desea REACTIVAR el anuncio del vehículo ID ${vehId}? Será visible nuevamente.`;
                nuevoEstado = 'disponible'; // O podría ser 'pendiente' si requiere revisión
                headerClass = 'bg-info text-dark';
                btnClass = 'btn-info';
                break;
            case 'eliminar': // Esta acción es más destructiva, podría necesitar un SP diferente.
                mensajeModal = `¡ATENCIÓN! ¿Está seguro de que desea ELIMINAR PERMANENTEMENTE el vehículo ID ${vehId}? Esta acción no se puede deshacer.`;
                nuevoEstado = 'eliminado'; // Un estado conceptual para el frontend, el backend lo manejará.
                headerClass = 'bg-danger';
                btnClass = 'btn-danger';
                break;
            default:
                console.error('Acción desconocida:', accion);
                return;
        }

        $('#nuevoEstadoConfirmar').val(nuevoEstado);
        $('#modalConfirmarAccionLabel').text(`Confirmar ${accion.charAt(0).toUpperCase() + accion.slice(1)}`);
        $('#modalConfirmarMensaje').text(mensajeModal);
        $('#modalConfirmarHeader').removeClass().addClass(`modal-header ${headerClass}`);
        $('#btnEjecutarAccion').removeClass().addClass(`btn ${btnClass}`).text('Confirmar');
        modalConfirmarAccion.show();
    });

    $('#btnEjecutarAccion').on('click', function() {
        const vehId = $('#vehiculoIdConfirmar').val();
        const accionOriginal = $('#accionConfirmar').val(); // 'aprobar', 'desactivar', 'reactivar', 'eliminar'
        const nuevoEstado = $('#nuevoEstadoConfirmar').val(); // 'disponible', 'desactivado', etc.

        let urlAjax = './../AJAX/vehiculos_ajax.php';
        let datosAjax = {
            accion: '', // Se determinará a continuación
            veh_id: vehId,
        };

        // Mapeo de acciones del frontend a acciones del backend
        if (accionOriginal === 'eliminar') {
            datosAjax.accion = 'eliminarVehiculo'; // Necesitará un SP y manejo en vehiculos_ajax y vehiculos_m
        } else {
            // Para aprobar, desactivar, reactivar, usamos cambiarEstadoVehiculo
            datosAjax.accion = 'cambiarEstadoVehiculo';
            datosAjax.nuevo_estado = nuevoEstado;
        }

        $.ajax({
            url: urlAjax,
            type: 'POST',
            data: datosAjax,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    mostrarAlerta('success', response.message || 'Acción realizada con éxito.');
                    if (tablaVehiculosAdmin) {
                        tablaVehiculosAdmin.ajax.reload(null, false);
                    }
                } else {
                    mostrarAlerta('error', response.message || 'Error al realizar la acción.');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error AJAX al cambiar estado:", status, error, xhr.responseText);
                mostrarAlerta('error', 'Error de conexión al realizar la acción: ' + error);
            },
            complete: function() {
                modalConfirmarAccion.hide();
            }
        });
    });
    
    // Carga inicial de la tabla
    inicializarTablaVehiculos();

    // Función global para mostrar alertas (si no existe ya en global.js)
    // Asumimos que existe una función mostrarAlerta(tipo, mensaje) en global.js
    // Si no, se debe implementar aquí o incluirla.
    // Ejemplo:
    /*
    function mostrarAlerta(tipo, mensaje) {
        // Implementación simple de alerta, considera usar algo más robusto como Toastr o SweetAlert
        const alertaDiv = $(`<div class="alert alert-${tipo === 'error' ? 'danger' : tipo} alert-dismissible fade show" role="alert">
                                ${mensaje}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                             </div>`);
        // Añadir al DOM, por ejemplo, al principio del main container
        $('main.container-fluid').prepend(alertaDiv);
        setTimeout(() => alertaDiv.alert('close'), 5000); // Auto-cerrar después de 5 segundos
    }
    */

});
