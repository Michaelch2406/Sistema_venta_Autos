$(document).ready(function() {
    var tablaUsuarios;
    var modalUsuario = new bootstrap.Modal(document.getElementById('modalUsuario'));
    var $formUsuario = $('#formUsuario');
    var $modalTitle = $('#modalUsuarioLabel');
    var $btnGuardarUsuario = $('#btnGuardarUsuario');

    // Contadores para estadísticas
    let totalUsuarios = 0;
    let usuariosVerificados = 0;
    let totalRoles = 0;
    let usuariosRecientes = 0;

    function actualizarEstadisticas() {
        $('#totalUsuarios').text(totalUsuarios);
        $('#usuariosVerificados').text(usuariosVerificados);
        $('#totalRoles').text(totalRoles);
        $('#usuariosRecientes').text(usuariosRecientes);
        
        // Animación de conteo
        $('#totalUsuarios, #usuariosVerificados, #totalRoles, #usuariosRecientes').each(function() {
            $(this).addClass('pulse');
            setTimeout(() => {
                $(this).removeClass('pulse');
            }, 600);
        });
    }

    function cargarEstadisticas() {
        // Cargar estadísticas desde el servidor
        $.ajax({
            url: '../AJAX/admin_usuarios_ajax.php',
            type: 'GET',
            data: { accion: 'getEstadisticas' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    totalUsuarios = response.data.total_usuarios || 0;
                    usuariosVerificados = response.data.usuarios_verificados || 0;
                    totalRoles = response.data.total_roles || 0;
                    usuariosRecientes = response.data.usuarios_recientes || 0;
                } else {
                    // Si no hay endpoint de estadísticas, usar datos de la tabla
                    calcularEstadisticasDesdeTabla();
                }
                actualizarEstadisticas();
            },
            error: function() {
                // Fallback: calcular desde los datos de la tabla
                calcularEstadisticasDesdeTabla();
                actualizarEstadisticas();
            }
        });
    }

    function calcularEstadisticasDesdeTabla() {
        if (tablaUsuarios && tablaUsuarios.data()) {
            const data = tablaUsuarios.data().toArray();
            totalUsuarios = data.length;
            usuariosVerificados = data.filter(user => user.usu_verificado == 1).length;
            
            // Calcular usuarios recientes (últimos 30 días)
            const treintaDiasAtras = new Date();
            treintaDiasAtras.setDate(treintaDiasAtras.getDate() - 30);
            
            usuariosRecientes = data.filter(user => {
                if (user.usu_creado_en) {
                    const fechaCreacion = new Date(user.usu_creado_en.replace(/-/g, "/"));
                    return fechaCreacion >= treintaDiasAtras;
                }
                return false;
            }).length;
        }
    }

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
                    
                    totalRoles = response.data.length; // Actualizar contador de roles
                    
                    $.each(response.data, function(i, rol) {
                        $selectRoles.append($('<option>', {
                            value: rol.rol_id,
                            text: rol.rol_nombre
                        }));
                    });
                    
                    actualizarEstadisticas();
                } else {
                    console.error("Error cargando roles: ", response.message);
                    showNotification('Error al cargar roles del sistema', 'error');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error cargando roles: ", textStatus, errorThrown);
                showNotification('Error de conexión al cargar roles', 'error');
            }
        });
    }

    // Inicializar DataTable con mejoras
    tablaUsuarios = $('#tablaUsuariosAdmin').DataTable({
        "ajax": {
            "url": "../AJAX/admin_usuarios_ajax.php?accion=listarUsuarios",
            "type": "GET",
            "dataType": "json",
            "dataSrc": function(json) {
                if (json.status === 'success') {
                    // Calcular estadísticas después de cargar datos
                    setTimeout(() => {
                        calcularEstadisticasDesdeTabla();
                        actualizarEstadisticas();
                    }, 100);
                    return json.data;
                } else {
                    console.error("Error cargando usuarios para DataTable: " + json.message);
                    $('#tablaUsuariosAdmin_processing').hide();
                    $('#tablaUsuariosAdmin tbody').html(`
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-exclamation-triangle display-1 text-danger mb-3"></i>
                                    <h5 class="text-danger">Error al cargar datos</h5>
                                    <p class="text-muted">${json.message}</p>
                                    <button class="btn btn-outline-primary" onclick="location.reload()">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Reintentar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `);
                    return [];
                }
            },
            "error": function(jqXHR, textStatus, errorThrown) {
                console.error("DataTables AJAX error:", jqXHR.responseText, textStatus, errorThrown);
                $('#tablaUsuariosAdmin_processing').hide();
                $('#tablaUsuariosAdmin tbody').html(`
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-wifi-off display-1 text-danger mb-3"></i>
                                <h5 class="text-danger">Error de conexión</h5>
                                <p class="text-muted">No se pudieron cargar los usuarios. Verifica tu conexión.</p>
                                <button class="btn btn-outline-primary" onclick="location.reload()">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Reintentar
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
            }
        },
        "columns": [
            { 
                "data": "usu_id",
                "render": function(data, type, row) {
                    return `<span class="fw-bold text-primary">#${data}</span>`;
                },
                "responsivePriority": 1 // ID es importante, alta prioridad
            },
            { 
                "data": "usu_usuario",
                "render": function(data, type, row) {
                    return `<div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                            <i class="bi bi-person text-white"></i>
                        </div>
                        <span class="fw-semibold">${data}</span>
                    </div>`;
                },
                "responsivePriority": 2 // Usuario también es importante
            },
            { 
                "data": null, 
                "render": function(data, type, row) {
                    const nombreCompleto = (row.usu_nombre || '') + ' ' + (row.usu_apellido || '');
                    return `<div class="fw-medium">${nombreCompleto.trim()}</div>`;
                },
                "responsivePriority": 3 // Nombre Completo
            },
            { 
                "data": "usu_email",
                "render": function(data, type, row) {
                    return `<div class="d-flex align-items-center">
                        <i class="bi bi-envelope me-2 text-muted"></i>
                        <span>${data}</span>
                    </div>`;
                },
                "responsivePriority": 4 // Email
            },
            { 
                "data": "usu_cedula", 
                "defaultContent": "<em class='text-muted'>N/A</em>",
                "render": function(data, type, row) {
                    if (data) {
                        return `<div class="d-flex align-items-center">
                            <i class="bi bi-card-text me-2 text-muted"></i>
                            <span class="font-monospace">${data}</span>
                        </div>`;
                    }
                    return "<em class='text-muted'>N/A</em>";
                },
                "responsivePriority": 5 // Cédula
            },
            { 
                "data": "usu_telefono", 
                "defaultContent": "<em class='text-muted'>N/A</em>",
                "render": function(data, type, row) {
                    if (data) {
                        return `<div class="d-flex align-items-center">
                            <i class="bi bi-telephone me-2 text-muted"></i>
                            <span>${data}</span>
                        </div>`;
                    }
                    return "<em class='text-muted'>N/A</em>";
                },
                "responsivePriority": 6 // Teléfono
            },
            { 
                "data": "rol_nombre",
                "render": function(data, type, row) {
                    const colorClass = getRoleColorClass(data);
                    return `<span class="badge ${colorClass} px-3 py-2">
                        <i class="bi bi-person-badge me-1"></i>
                        ${data}
                    </span>`;
                },
                "responsivePriority": 7 // Rol
            },
            { 
                "data": "usu_verificado",
                "render": function(data, type, row) {
                    if (data == 1) {
                        return '<span class="badge badge-custom badge-verified"><i class="bi bi-check-circle me-1"></i>Verificado</span>';
                    } else {
                        return '<span class="badge badge-custom badge-unverified"><i class="bi bi-x-circle me-1"></i>No Verificado</span>';
                    }
                },
                "responsivePriority": 8 // Verificado
            },
            { 
                "data": "usu_creado_en",
                "render": function(data, type, row) {
                    try {
                        if (data) {
                            const fecha = new Date(data.replace(/-/g, "/"));
                            const fechaFormateada = fecha.toLocaleDateString('es-EC', { 
                                year: 'numeric', 
                                month: 'short', 
                                day: 'numeric'
                            });
                            
                            // Verificar si es reciente (últimos 7 días)
                            const sieteDiasAtras = new Date();
                            sieteDiasAtras.setDate(sieteDiasAtras.getDate() - 7);
                            const esReciente = fecha >= sieteDiasAtras;
                            
                            return `<div class="d-flex align-items-center">
                                <i class="bi bi-calendar-date me-2 text-muted"></i>
                                <span>${fechaFormateada}</span>
                                ${esReciente ? '<span class="badge bg-success ms-2">Nuevo</span>' : ''}
                            </div>`;
                        }
                        return '<em class="text-muted">N/A</em>';
                    } catch (e) { 
                        return data || '<em class="text-muted">N/A</em>';
                    }
                },
                "responsivePriority": 9 // Registrado
            },
            {
                "data": null,
                "orderable": false,
                "searchable": false,
                "render": function(data, type, row) {
                    return `
                        <div class="btn-group-actions">
                            <button class="btn btn-action btn-edit btn-editar-usuario" data-id="${row.usu_id}" title="Editar Usuario" data-bs-toggle="tooltip">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-action btn-role btn-cambiar-rol" data-id="${row.usu_id}" data-rol-actual="${row.rol_id}" title="Cambiar Rol" data-bs-toggle="tooltip">
                                <i class="bi bi-person-badge"></i>
                            </button>
                        </div>
                    `;
                },
                "responsivePriority": 1 // Acciones siempre visible
            }
        ],
        "responsive": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "processing": true,
        "dom": 'Bfrtip',
        "buttons": [
            { 
                extend: 'copyHtml5', 
                text: '<i class="bi bi-files"></i> Copiar',
                titleAttr: 'Copiar al portapapeles',
                exportOptions: { columns: ':visible:not(:last-child)' },
                className: 'dt-button'
            },
            { 
                extend: 'excelHtml5', 
                text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                titleAttr: 'Exportar a Excel',
                title: 'Listado de Usuarios - AutoMercado Total',
                exportOptions: { columns: ':visible:not(:last-child)' },
                className: 'dt-button'
            },
            { 
                extend: 'csvHtml5', 
                text: '<i class="bi bi-filetype-csv"></i> CSV',
                titleAttr: 'Exportar a CSV',
                title: 'Listado_de_Usuarios_AutoMercado_Total',
                exportOptions: { columns: ':visible:not(:last-child)' },
                className: 'dt-button'
            },
            { 
                extend: 'pdfHtml5', 
                text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                titleAttr: 'Exportar a PDF',
                title: 'Listado de Usuarios - AutoMercado Total',
                exportOptions: { columns: ':visible:not(:last-child)' },
                orientation: 'landscape',
                pageSize: 'LEGAL',
                className: 'dt-button'
            },
            { 
                extend: 'print', 
                text: '<i class="bi bi-printer"></i> Imprimir',
                titleAttr: 'Imprimir tabla',
                // title: 'Listado de Usuarios - AutoMercado Total',
                exportOptions: { columns: ':visible:not(:last-child)' },
                className: 'dt-button',
                customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '10pt' )
                        .prepend(
                            '<img src="../PUBLIC/Img/Auto_Mercado_Total_LOGO4_SIN_FONDO.png" style="position:absolute; top:10px; left:10px; height:50px;" />'
                        );
 
                    $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                }
            },
            { 
                extend: 'colvis', 
                text: '<i class="bi bi-layout-three-columns"></i> Columnas',
                titleAttr: 'Mostrar/ocultar columnas',
                className: 'dt-button'
            }
        ],
        "order": [[0, 'desc']],
        "pageLength": 25,
        "drawCallback": function(settings) {
            // Inicializar tooltips después de cada redibujado
            $('[data-bs-toggle="tooltip"]').tooltip();
            
            // Agregar animación a las filas
            $('#tablaUsuariosAdmin tbody tr').each(function(index) {
                $(this).css('animation-delay', (index * 0.05) + 's');
                $(this).addClass('fade-in');
            });
        }
    });

    function getRoleColorClass(roleName) {
        const roleColors = {
            'Administrador': 'bg-danger',
            'Vendedor': 'bg-primary',
            'Cliente': 'bg-success',
            'Supervisor': 'bg-warning',
            'Gerente': 'bg-info'
        };
        return roleColors[roleName] || 'bg-secondary';
    }

    function validarCedulaRucEc(numero) {
        numero = String(numero).trim();
        if (!/^\d+$/.test(numero)) {
            return { valido: false, mensaje: "Debe contener solo números." };
        }

        // Validación de Cédula (10 dígitos)
        if (numero.length === 10) {
            let provincia = parseInt(numero.substring(0, 2));
            if (provincia < 1 || provincia > 24) {
                return { valido: false, mensaje: "Código de provincia inválido." };
            }

            let tercerDigito = parseInt(numero[2]);
            if (tercerDigito < 0 || tercerDigito > 5) {
                // Permitir algunos casos especiales
            }

            let coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
            let suma = 0;
            for (let i = 0; i < 9; i++) {
                let producto = parseInt(numero[i]) * coeficientes[i];
                if (producto >= 10) {
                    producto -= 9;
                }
                suma += producto;
            }

            let residuo = suma % 10;
            let digitoVerificadorCalculado = (residuo === 0) ? 0 : 10 - residuo;
            let digitoVerificadorReal = parseInt(numero[9]);

            if (digitoVerificadorCalculado === digitoVerificadorReal) {
                return { valido: true, mensaje: "Cédula válida." };
            } else {
                return { valido: false, mensaje: "Dígito verificador incorrecto. Cédula no válida." };
            }
        }
        // Validación de RUC Persona Natural (13 dígitos, cédula + "001")
        else if (numero.length === 13) {
            if (!numero.endsWith("001")) {
                return { valido: false, mensaje: "RUC de persona natural debe terminar en 001." };
            }
            let primerosDiez = numero.substring(0, 10);
            let resultadoCedula = validarCedulaRucEc(primerosDiez);
            if (resultadoCedula.valido) {
                if (parseInt(primerosDiez[2]) < 6) {
                    return { valido: true, mensaje: "RUC de persona natural válido." };
                } else {
                    return { valido: false, mensaje: "Tercer dígito inválido para RUC de persona natural."};
                }
            } else {
                return { valido: false, mensaje: "Parte de cédula del RUC es inválida: " + resultadoCedula.mensaje };
            }
        }

        return { valido: false, mensaje: "Longitud incorrecta (debe ser 10 para cédula o 13 para RUC)." };
    }

    // Validación en tiempo real
    $('#usu_cedula_form').on('input', function() {
        const valor = $(this).val().trim();
        const $feedback = $(this).siblings('.invalid-feedback');
        
        if (valor.length > 0) {
            const validacion = validarCedulaRucEc(valor);
            if (validacion.valido) {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $feedback.text('✓ ' + validacion.mensaje);
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
                $feedback.text(validacion.mensaje);
            }
        } else {
            $(this).removeClass('is-valid is-invalid');
        }
    });

    // Validación de email en tiempo real
    $('#usu_email_form').on('input', function() {
    const email = $(this).val().trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email.length > 0) {
        if (emailRegex.test(email)) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
    } else {
        $(this).removeClass('is-valid is-invalid');
    }
});


    // Validación de usuario en tiempo real
    $('#usu_usuario_form').on('input', function() {
        const usuario = $(this).val().trim();
        
        if (usuario.length >= 3) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else if (usuario.length > 0) {
            $(this).removeClass('is-valid').addClass('is-invalid');
        } else {
            $(this).removeClass('is-valid is-invalid');
        }
    });

    $('#btnNuevoUsuario').on('click', function() {
        $formUsuario[0].reset();
        $formUsuario.removeClass('was-validated');
        $('.form-control').removeClass('is-valid is-invalid');
        $('#usu_id_form').val('');
        $('#accionForm').val('crearUsuario');
        $modalTitle.html('<i class="bi bi-person-plus"></i> Crear Nuevo Usuario');
        $('#usu_password_form').prop('required', true).attr('placeholder', 'Mínimo 8 caracteres');
        modalUsuario.show();
        
        // Enfocar el primer campo
        setTimeout(() => {
            $('#usu_usuario_form').focus();
        }, 500);
    });

    $('#tablaUsuariosAdmin tbody').on('click', '.btn-editar-usuario, .btn-cambiar-rol', function() {
        var usu_id = $(this).data('id');
        var $row = $(this).closest('tr');
        
        // Agregar efecto visual
        $row.addClass('table-row-highlight');
        setTimeout(() => {
            $row.removeClass('table-row-highlight');
        }, 2000);
        
        $formUsuario[0].reset();
        $formUsuario.removeClass('was-validated');
        $('.form-control').removeClass('is-valid is-invalid');
        $('#accionForm').val('actualizarUsuario');
        $modalTitle.html('<i class="bi bi-person-gear"></i> Editar Usuario (ID: ' + usu_id + ')');
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
                    $('#usu_cedula_form').val(data.usu_cedula);
                    $('#rol_id_form').val(data.rol_id);
                    $('#usu_telefono_form').val(data.usu_telefono);
                    $('#usu_direccion_form').val(data.usu_direccion);
                    $('#usu_fnacimiento_form').val(data.usu_fnacimiento);
                    $('#usu_verificado_form').prop('checked', data.usu_verificado == 1 || data.usu_verificado === true);
                    modalUsuario.show();
                    
                    // Enfocar el primer campo
                    setTimeout(() => {
                        $('#usu_usuario_form').focus().select();
                    }, 500);
                } else {
                    showNotification("Error al cargar datos del usuario: " + (response.message || 'Datos no encontrados.'), 'error');
                }
            },
            error: function() {
                showNotification("Error de conexión al obtener datos del usuario.", 'error');
            }
        });
    });

    $formUsuario.on('submit', function(event) {
        event.preventDefault();
        event.stopPropagation();

        var form = this;
        var $cedulaInput = $('#usu_cedula_form');
        var cedulaVal = $cedulaInput.val().trim();

        // Validación de cédula
        if (cedulaVal) {
            var validacionEc = validarCedulaRucEc(cedulaVal);
            if (!validacionEc.valido) {
                $cedulaInput.addClass('is-invalid');
                $cedulaInput.siblings('.invalid-feedback').text(validacionEc.mensaje);
                return;
            }
        }

        if (!form.checkValidity()) {
            $(form).addClass('was-validated');
            return;
        }
        
        $(form).addClass('was-validated');

        var formData = $(this).serialize();
        var originalButtonText = $btnGuardarUsuario.html();
        $btnGuardarUsuario.prop('disabled', true).html('<span class="loading-spinner me-2"></span>Guardando...');

        $.ajax({
            url: '../AJAX/admin_usuarios_ajax.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    modalUsuario.hide();
                    tablaUsuarios.ajax.reload(null, false);
                    showNotification(response.message, 'success');
                    
                    // Limpiar formulario
                    $formUsuario[0].reset();
                    $formUsuario.removeClass('was-validated');
                    $('.form-control').removeClass('is-valid is-invalid');
                    
                    // Actualizar estadísticas
                    setTimeout(() => {
                        cargarEstadisticas();
                    }, 500);
                } else {
                    showNotification("Error: " + response.message, 'error');
                }
            },
            error: function() {
                showNotification("Error de conexión al guardar el usuario.", 'error');
            },
            complete: function() {
                $btnGuardarUsuario.prop('disabled', false).html(originalButtonText);
            }
        });
    });

    // Sistema de notificaciones mejorado
    function showNotification(message, type = 'info') {
        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-exclamation-triangle-fill',
            warning: 'bi-exclamation-circle-fill',
            info: 'bi-info-circle-fill'
        };
        
        const colors = {
            success: 'alert-success',
            error: 'alert-danger',
            warning: 'alert-warning',
            info: 'alert-info'
        };
        
        const titles = {
            success: '¡Éxito!',
            error: 'Error',
            warning: 'Advertencia',
            info: 'Información'
        };

        // Remover notificación anterior
        $('#globalNotification').remove();
        
        const notification = $(`
            <div id="globalNotification" class="alert ${colors[type]} position-fixed bottom-0 end-0 m-3 p-3 slide-in" role="alert" style="z-index: 1056; max-width: 400px; min-width: 300px;">
                <div class="d-flex align-items-center">
                    <i class="bi ${icons[type]} me-3 fs-4"></i>
                    <div>
                        <div class="fw-bold">${titles[type]}</div>
                        <div>${message}</div>
                    </div>
                    <button type="button" class="btn-close ms-auto" onclick="$('#globalNotification').fadeOut()"></button>
                </div>
            </div>
        `);
        
        $('body').append(notification);
        
        // Auto-ocultar después de 5 segundos
        setTimeout(function() { 
            notification.fadeOut(500, function() { 
                $(this).remove(); 
            }); 
        }, 5000);
    }

    // Inicialización
    cargarRolesSelect();
    cargarEstadisticas();
    
    // Actualizar estadísticas cada 2 minutos
    setInterval(function() {
        if (!modalUsuario._isShown) {
            cargarEstadisticas();
        }
    }, 120000);
});



