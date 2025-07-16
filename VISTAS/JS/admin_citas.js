// JS/admin_citas.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('admin_citas.js cargado');

    const modalDetalleAdmin = document.getElementById('modal-detalle-cita-admin');
    const spanCerrarModalAdmin = document.getElementById('modal-cerrar-detalle-admin');
    const tablaCitasBody = document.getElementById('tabla-citas-admin-body');

    // Escapa caracteres especiales en texto para HTML
    function escapeHTML(str) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        };
        return String(str || '').replace(/[&<>"']/g, m => map[m]);
    }

    // Convierte saltos de línea en <br>
    function nl2br(str) {
        return String(str || '').replace(/(\r\n|\n\r|\r|\n)/g, '<br>');
    }

    // Formatea fechas en locale español
    function formatearFecha(fechaString) {
        if (!fechaString) return 'No definida';
        try {
            return new Date(fechaString)
                .toLocaleString('es-ES', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
        } catch (e) {
            return fechaString;
        }
    }

    // Manejo de clics en la tabla de citas
    if (tablaCitasBody) {
        tablaCitasBody.addEventListener('click', function(event) {
            const boton = event.target.closest('.btn-admin-accion');
            if (!boton) return;

            const citaId = boton.dataset.id;
            if (!citaId) return;

            if (boton.classList.contains('btn-admin-ver-detalle')) {
                abrirDetalleAdminModal(citaId);
            } else if (boton.classList.contains('btn-admin-aprobar')) {
                gestionarEstadoCita(citaId, 'aprobada', boton);
            } else if (boton.classList.contains('btn-admin-rechazar')) {
                gestionarEstadoCita(citaId, 'rechazado', boton);
            } else if (boton.classList.contains('btn-registrar-venta')) {
                registrarVenta(citaId, boton);
            }
        });
    }

    // Abre el modal y carga el detalle via AJAX
    function abrirDetalleAdminModal(citaId) {
        const modalIdSpan = document.getElementById('detalle-cita-admin-id-modal');
        const modalContent = document.getElementById('detalle-cita-admin-contenido-modal');
        modalIdSpan.textContent = citaId;
        modalContent.innerHTML = '<p class="loading-message">Cargando detalles...</p>';
        modalDetalleAdmin.style.display = 'block';
        fetchDetalleCitaAdmin(citaId);
    }

    // Cierra el modal al hacer click en la "X" o fuera del contenido
    if (spanCerrarModalAdmin) {
        spanCerrarModalAdmin.onclick = () => {
            modalDetalleAdmin.style.display = 'none';
        };
    }
    window.onclick = (event) => {
        if (event.target === modalDetalleAdmin) {
            modalDetalleAdmin.style.display = 'none';
        }
    };

    // Petición AJAX para obtener detalle de la cita
    async function fetchDetalleCitaAdmin(citaId) {
        try {
            const response = await fetch(`./../AJAX/citas_ajax.php?action=obtener_detalle_cita_admin&id_cita=${citaId}`);
            const resultado = await response.json();
            if (resultado.success) {
                renderizarDetalleAdminModal(resultado.data);
            } else {
                document.getElementById('detalle-cita-admin-contenido-modal').innerHTML =
                    `<p class="error">${escapeHTML(resultado.message)}</p>`;
            }
        } catch (error) {
            console.error('Error fetching cita:', error);
            document.getElementById('detalle-cita-admin-contenido-modal').innerHTML =
                '<p class="error">Error de conexión.</p>';
        }
    }

    // Rellena el contenido del modal con los datos recibidos
    function renderizarDetalleAdminModal(data) {
        const contenidoModal = document.getElementById('detalle-cita-admin-contenido-modal');
        const html = `
            <div class="detalle-grid-admin">
                <div><i class="bi bi-person me-1"></i><strong>Cliente:</strong></div>
                <div>${escapeHTML(data.nombre_solicitante)} (ID: ${data.usu_id_solicitante})</div>
                
                <div><i class="bi bi-envelope me-1"></i><strong>Email:</strong></div>
                <div><a href="mailto:${escapeHTML(data.email_solicitante)}">${escapeHTML(data.email_solicitante)}</a></div>
                
                <div><i class="bi bi-calendar-plus me-1"></i><strong>Fecha Solicitud:</strong></div>
                <div>${formatearFecha(data.cit_fecha_solicitud)}</div>
                
                <div><i class="bi bi-calendar-check me-1"></i><strong>Fecha Estimada:</strong></div>
                <div>${formatearFecha(data.cit_fecha_estimada)} <em class="text-muted">(En desarrollo)</em></div>
                
                <div><i class="bi bi-flag me-1"></i><strong>Estado:</strong></div>
                <div><span class="estado-tag estado-${escapeHTML(data.cit_estado.toLowerCase())}">${escapeHTML(data.cit_estado)}</span></div>
                
                <div><i class="bi bi-car-front me-1"></i><strong>Vehículo:</strong></div>
                <div>${escapeHTML(data.cit_detalles_vehiculo_solicitado)}</div>
            </div>
            <div class="mt-4">
                <h4><i class="bi bi-chat-text me-2"></i>Mensaje del Solicitante:</h4>
                <div class="p-3 bg-light rounded border">
                    ${data.cit_mensaje ? nl2br(escapeHTML(data.cit_mensaje)) : '<em>Sin mensaje.</em>'}
                </div>
            </div>
            <div class="mt-4">
                <h4><i class="bi bi-journal-text me-2"></i>Notas Internas (Admin):</h4>
                <textarea id="admin-notas-internas-modal" class="form-control" rows="3" placeholder="Añadir notas...">${escapeHTML(data.cit_notas_admin)}</textarea>
                <button id="btn-guardar-notas-admin-modal" class="btn btn-primary mt-2" data-id="${data.cit_id}">
                    <i class="bi bi-save me-2"></i>Guardar Notas
                </button>
            </div>
        `;
        contenidoModal.innerHTML = html;

        // Asigna el evento al botón de guardar notas
        document.getElementById('btn-guardar-notas-admin-modal')
            .addEventListener('click', function() {
                const notas = document.getElementById('admin-notas-internas-modal').value;
                guardarNotasInternas(this.dataset.id, notas, this);
            });
    }

    // Cambia el estado de la cita (aprobar/rechazar)
    async function gestionarEstadoCita(citaId, nuevoEstado, boton) {
        if (!confirm(`¿Confirmas cambiar el estado de la cita #${citaId} a "${nuevoEstado}"?`)) return;

        boton.disabled = true;
        boton.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        const formData = new FormData();
        formData.append('action', 'cambiar_estado_cita');
        formData.append('id_cita', citaId);
        formData.append('nuevo_estado', nuevoEstado);

        try {
            const response = await fetch('./../AJAX/citas_ajax.php', {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();
            if (resultado.success) {
                alert('Estado actualizado.');
                location.reload();
            } else {
                alert(`Error: ${resultado.message}`);
                boton.disabled = false;
            }
        } catch (error) {
            alert('Error de conexión al cambiar estado.');
            boton.disabled = false;
        }
    }

    // Guarda las notas internas del admin
    async function guardarNotasInternas(citaId, notas, boton) {
        boton.disabled = true;
        boton.innerHTML = '<i class="bi bi-hourglass-split"></i> Guardando...';
        const formData = new FormData();
        formData.append('action', 'guardar_notas_admin');
        formData.append('id_cita', citaId);
        formData.append('notas_internas', notas);

        try {
            const response = await fetch('./../AJAX/citas_ajax.php', {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();
            alert(resultado.message);
        } catch (error) {
            alert('Error de conexión al guardar notas.');
        } finally {
            boton.disabled = false;
            boton.innerHTML = '<i class="bi bi-save me-2"></i>Guardar Notas';
        }
    }

    // Registra la venta de un vehículo
    async function registrarVenta(citaId, boton) {
        const precio = prompt('Ingrese el precio final de la venta:');
        if (precio === null || precio.trim() === '') {
            return;
        }

        boton.disabled = true;
        boton.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        const formData = new FormData();
        formData.append('action', 'registrar_venta');
        formData.append('id_cita', citaId);
        formData.append('precio_final', precio);

        try {
            const response = await fetch('./../AJAX/citas_ajax.php', {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();
            if (resultado.success) {
                alert('Venta registrada exitosamente.');
                location.reload();
            } else {
                alert(`Error: ${resultado.message}`);
                boton.disabled = false;
            }
        } catch (error) {
            alert('Error de conexión al registrar la venta.');
            boton.disabled = false;
        }
    }
});
