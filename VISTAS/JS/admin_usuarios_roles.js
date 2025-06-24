$(document).ready(function() {
    var tablaUsuarios;
    var modalUsuario = new bootstrap.Modal(document.getElementById('modalUsuario'));
    var $formUsuario = $('#formUsuario');
    var $modalTitle = $('#modalUsuarioLabel');
    var $btnGuardarUsuario = $('#btnGuardarUsuario');

    function cargarRolesSelect() {
        $.ajax({
            url: '../AJAX/admin_usuarios_ajax.php',
            type: 'GET',
            data: { accion: 'getRoles' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    var $selectRoles = $('#rol_id_form');
                    $selectRoles.empty().append('<option value="" selected disabled>Selecciona un rol...</option>');
                    $.each(response.data, function(i, rol) {
                        $selectRoles.append($('<option>', {
                            value: rol.rol_id,
                            text: rol.rol_nombre
                        }));
                    });
                } else {
                    console.error("Error cargando roles: ", response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error cargando roles: ", textStatus, errorThrown);
            }
        });
    }
    cargarRolesSelect();

    tablaUsuarios = $('#tablaUsuariosAdmin').DataTable({
        "ajax": {
            "url": "../AJAX/admin_usuarios_ajax.php?accion=listarUsuarios",
            "type": "GET",
            "dataType": "json",
            "dataSrc": function(json) {
                if (json.status === 'success') {
                    return json.data;
                } else {
                    // No mostrar alert aquí, DataTables puede manejarlo o mostrar mensaje de error en tabla
                    console.error("Error cargando usuarios para DataTable: " + json.message);
                    // Podrías personalizar el mensaje de error que muestra DataTables
                    $('#tablaUsuariosAdmin_processing').hide(); // Ocultar "Procesando..." si es visible
                    $('#tablaUsuariosAdmin tbody').html('<tr><td colspan="9" class="text-center text-danger">Error al cargar datos: ' + json.message + '</td></tr>');
                    return [];
                }
            },
            "error": function(jqXHR, textStatus, errorThrown) {
                 console.error("DataTables AJAX error:", jqXHR.responseText, textStatus, errorThrown);
                 $('#tablaUsuariosAdmin_processing').hide();
                 $('#tablaUsuariosAdmin tbody').html('<tr><td colspan="9" class="text-center text-danger">Error de conexión al cargar usuarios. Intente más tarde.</td></tr>');
            }
        },
        "columns": [
            { "data": "usu_id" },
            { "data": "usu_usuario" },
            { "data": null, "render": function(data, type, row) {
                return (row.usu_nombre || '') + ' ' + (row.usu_apellido || '');
            }},
            { "data": "usu_email" },
            { "data": "usu_telefono", "defaultContent": "<em>N/A</em>" },
            { "data": "rol_nombre" },
            { 
                "data": "usu_verificado",
                "render": function(data, type, row) {
                    return data == 1 ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-danger">No</span>';
                }
            },
            { 
                "data": "usu_creado_en",
                "render": function(data,type,row){
                    try {
                        return data ? new Date(data.replace(/-/g, "/")).toLocaleDateString('es-EC', { year: 'numeric', month: 'short', day: 'numeric'}) : '';
                    } catch (e) { return data; } // Fallback si la fecha no es parseable
                }
            },
            {
                "data": null,
                "orderable": false,
                "searchable": false,
                "render": function(data, type, row) {
                    return `
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-info btn-editar-usuario" data-id="${row.usu_id}" title="Editar Usuario">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-warning btn-cambiar-rol" data-id="${row.usu_id}" data-rol-actual="${row.rol_id}" title="Cambiar Rol">
                                <i class="bi bi-person-badge"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        "responsive": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" // Apuntando al CDN de DataTables
        },
        "processing": true, // Mostrar mensaje de "Procesando..."
        // "serverSide": false, // Si tus datos son muchos, considera serverSide true y adaptar el backend
        "dom": 'Bfrtip', // Define dónde aparecerán los botones
        "buttons": [
            { 
                extend: 'copyHtml5', 
                text: '<i class="bi bi-files"></i> Copiar',
                titleAttr: 'Copiar al portapapeles',
                exportOptions: { columns: ':visible:not(:last-child)' } // Exportar todas las visibles excepto la última (acciones)
            },
            { 
                extend: 'excelHtml5', 
                text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                titleAttr: 'Exportar a Excel',
                title: 'Listado de Usuarios - AutoMercado Total', // Título del archivo Excel
                exportOptions: { columns: ':visible:not(:last-child)' }
            },
            { 
                extend: 'csvHtml5', 
                text: '<i class="bi bi-filetype-csv"></i> CSV',
                titleAttr: 'Exportar a CSV',
                title: 'Listado_de_Usuarios_AutoMercado_Total', // Nombre del archivo CSV
                exportOptions: { columns: ':visible:not(:last-child)' }
            },
            { 
                extend: 'pdfHtml5', 
                text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                titleAttr: 'Exportar a PDF',
                title: 'Listado de Usuarios - AutoMercado Total', // Título del documento PDF
                exportOptions: { columns: ':visible:not(:last-child)' },
                orientation: 'landscape', // Orientación horizontal para más columnas
                pageSize: 'LEGAL'
            },
            { 
                extend: 'print', 
                text: '<i class="bi bi-printer"></i> Imprimir',
                titleAttr: 'Imprimir tabla',
                title: 'Listado de Usuarios - AutoMercado Total', // Título para la impresión
                exportOptions: { columns: ':visible:not(:last-child)' },
                customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '10pt' )
                        .prepend(
                            '<img src="../PUBLIC/Img/Auto_Mercado_Total_LOGO4_SIN_FONDO.png" style="position:absolute; top:10px; left:10px; height:50px;" />' // Ejemplo de logo
                        );
 
                    $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                }
            },
            { 
                extend: 'colvis', 
                text: '<i class="bi bi-layout-three-columns"></i> Visibilidad Columnas',
                titleAttr: 'Mostrar/ocultar columnas'
            }
        ],
        "order": [[0, 'desc']] // Ordenar por ID descendente por defecto
    });

    $('#btnNuevoUsuario').on('click', function() {
        $formUsuario[0].reset();
        $formUsuario.removeClass('was-validated');
        $('#usu_id_form').val('');
        $('#accionForm').val('crearUsuario');
        $modalTitle.text('Crear Nuevo Usuario');
        $('#usu_password_form').prop('required', true).attr('placeholder', 'Mínimo 8 caracteres');
        modalUsuario.show();
    });

    $('#tablaUsuariosAdmin tbody').on('click', '.btn-editar-usuario, .btn-cambiar-rol', function() {
        var usu_id = $(this).data('id');
        $formUsuario[0].reset();
        $formUsuario.removeClass('was-validated');
        $('#accionForm').val('actualizarUsuario');
        $modalTitle.text('Editar Usuario (ID: ' + usu_id + ')');
        $('#usu_password_form').prop('required', false).attr('placeholder', 'Dejar en blanco para no cambiar');

        $.ajax({
            url: '../AJAX/admin_usuarios_ajax.php',
            type: 'GET',
            data: { accion: 'getUsuario', usu_id: usu_id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    var data = response.data;
                    $('#usu_id_form').val(data.usu_id);
                    $('#usu_usuario_form').val(data.usu_usuario);
                    $('#usu_nombre_form').val(data.usu_nombre);
                    $('#usu_apellido_form').val(data.usu_apellido);
                    $('#usu_email_form').val(data.usu_email);
                    $('#rol_id_form').val(data.rol_id);
                    $('#usu_telefono_form').val(data.usu_telefono);
                    $('#usu_direccion_form').val(data.usu_direccion);
                    $('#usu_fnacimiento_form').val(data.usu_fnacimiento);
                    $('#usu_verificado_form').prop('checked', data.usu_verificado == 1 || data.usu_verificado === true);
                    modalUsuario.show();
                } else {
                    alert("Error al cargar datos del usuario: " + (response.message || 'Datos no encontrados.'));
                }
            },
            error: function() {
                alert("Error de conexión al obtener datos del usuario.");
            }
        });
    });

    $formUsuario.on('submit', function(event) {
        event.preventDefault();
        var form = this;
        if (!form.checkValidity()) {
            event.stopPropagation();
            $(form).addClass('was-validated');
            return;
        }
        $(form).addClass('was-validated');

        var formData = $(this).serialize();
        var originalButtonText = $btnGuardarUsuario.html();
        $btnGuardarUsuario.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...');

        $.ajax({
            url: '../AJAX/admin_usuarios_ajax.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    modalUsuario.hide();
                    tablaUsuarios.ajax.reload(null, false); // Recargar DataTable sin resetear paginación
                    alert(response.message);
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function() {
                alert("Error de conexión al guardar el usuario.");
            },
            complete: function() {
                 $btnGuardarUsuario.prop('disabled', false).html(originalButtonText);
            }
        });
    });
});