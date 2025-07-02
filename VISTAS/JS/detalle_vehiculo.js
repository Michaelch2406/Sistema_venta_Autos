/**
 * DETALLE VEHICULO - JAVASCRIPT INTERACTIVO
 * Maneja animaciones, interacciones y funcionalidades dinámicas reales.
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // --- LÓGICA DE LIGHTBOX PARA GALERÍA ---
    // Asegurarse que lightbox está definido antes de usarlo.
    if (typeof lightbox !== 'undefined') {
        lightbox.option({
          'resizeDuration': 200,
          'wrapAround': true,
          'fadeDuration': 300,
          'imageFadeDuration': 300
        });
    }

    // --- LÓGICA PARA CAMBIAR IMAGEN PRINCIPAL CON MINIATURAS ---
    const imagenPrincipal = document.getElementById('imagenPrincipalVehiculo');
    const miniaturas = document.querySelectorAll('.thumbnail-item');

    miniaturas.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Obtener la imagen dentro del enlace de la miniatura
            const thumbnailImg = this.querySelector('.thumbnail-image');
            const nuevaSrc = thumbnailImg.getAttribute('data-fullimage');

            if (imagenPrincipal && nuevaSrc) {
                // Animación de cambio
                imagenPrincipal.style.opacity = '0';
                setTimeout(() => {
                    imagenPrincipal.src = nuevaSrc;
                    
                    // Actualizar también el enlace del lightbox de la imagen principal
                    const linkPrincipal = imagenPrincipal.closest('a');
                    if (linkPrincipal) {
                        linkPrincipal.href = nuevaSrc;
                    }

                    imagenPrincipal.style.opacity = '1';
                }, 300); // Coincide con la duración de la transición en CSS
            }

            // Actualizar la clase activa
            miniaturas.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // --- LÓGICA PARA AGREGAR/QUITAR FAVORITOS (ADAPTADA DE ARCHIVO ANTIGUO) ---
    const btnFavorito = document.querySelector('.btn-favoritos');

    if (btnFavorito) {
        const vehId = btnFavorito.dataset.vehId;
        const favTextSpan = document.getElementById('favText');
        const favIcon = btnFavorito.querySelector('i.bi');

        const actualizarBotonUI = (esFavorito) => {
            btnFavorito.dataset.esFavorito = esFavorito;
            if (esFavorito) {
                favTextSpan.textContent = 'Quitar de Favoritos';
                favIcon.classList.remove('bi-heart');
                favIcon.classList.add('bi-heart-fill');
                btnFavorito.classList.add('active'); // Clase para estilizar estado activo si se desea
            } else {
                favTextSpan.textContent = 'Agregar a Favoritos';
                favIcon.classList.remove('bi-heart-fill');
                favIcon.classList.add('bi-heart');
                btnFavorito.classList.remove('active');
            }
        };
        
        const toggleBotonCarga = (cargando) => {
            btnFavorito.disabled = cargando;
            if (cargando) {
                favTextSpan.textContent = 'Procesando...';
            } else {
                // La UI se actualiza en las funciones de éxito/error
            }
        };

        // Verificar estado inicial al cargar la página
        toggleBotonCarga(true);
        const dataVerificar = new FormData();
        dataVerificar.append('accion', 'verificar');
        dataVerificar.append('veh_id', vehId);

        fetch('../AJAX/favoritos_ajax.php', {
            method: 'POST',
            body: dataVerificar
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                actualizarBotonUI(data.esFavorito);
            }
        })
        .catch(error => {
            console.error('Error al verificar favorito:', error);
            favTextSpan.textContent = 'Error';
        })
        .finally(() => {
            toggleBotonCarga(false);
        });
        

        // Evento click para agregar/quitar
        btnFavorito.addEventListener('click', (e) => {
            e.preventDefault();
            const esFavoritoActual = btnFavorito.dataset.esFavorito === 'true';
            const accionParaEnviar = esFavoritoActual ? 'quitar' : 'agregar';
            
            toggleBotonCarga(true);

            const dataAccion = new FormData();
            dataAccion.append('accion', accionParaEnviar);
            dataAccion.append('veh_id', vehId);

            fetch('../AJAX/favoritos_ajax.php', {
                method: 'POST',
                body: dataAccion
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' || data.status === 'info') {
                    actualizarBotonUI(data.esFavorito);
                } else {
                    alert('Error: ' + (data.message || 'No se pudo actualizar el favorito.'));
                    actualizarBotonUI(esFavoritoActual); // Revertir UI en caso de error
                }
            })
            .catch(error => {
                console.error('Error de conexión al guardar favorito:', error);
                alert('Error de conexión al guardar favorito.');
                actualizarBotonUI(esFavoritoActual); // Revertir UI
            })
            .finally(() => {
                toggleBotonCarga(false);
            });
        });
    }

    // --- LÓGICA PARA EL FORMULARIO DE CONTACTO/COTIZACIÓN (ADAPTADA DE ARCHIVO ANTIGUO) ---
    const formContacto = document.getElementById('formContactoVendedor');

    if (formContacto) {
        formContacto.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitButton = document.getElementById('btnEnviarCotizacion');
            const messageContainer = document.getElementById('contactFormMessage');
            const originalButtonContent = submitButton.innerHTML;

            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...`;
            messageContainer.innerHTML = '';
            messageContainer.className = 'mt-3';
            
            const formData = new FormData(formContacto);

            fetch('../AJAX/cotizaciones_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    messageContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    formContacto.querySelector('textarea').disabled = true;
                    submitButton.disabled = true;

                    setTimeout(() => {
                        const modalEl = document.getElementById('modalContactoVendedor');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if(modalInstance) modalInstance.hide();
                    }, 4000);
                } else {
                    messageContainer.innerHTML = `<div class="alert alert-danger">${data.message || 'Ocurrió un error.'}</div>`;
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonContent;
                }
            })
            .catch(error => {
                console.error('Error de conexión en formulario de contacto:', error);
                messageContainer.innerHTML = `<div class="alert alert-danger">Error de conexión. Por favor, inténtalo de nuevo.</div>`;
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonContent;
            });
        });
    }

    // --- LÓGICA PARA COMPARTIR ---
    const shareButton = document.querySelector('.btn-share');
    if (shareButton) {
        shareButton.addEventListener('click', () => {
            const title = shareButton.dataset.shareTitle;
            const url = shareButton.dataset.shareUrl;

            if (navigator.share) {
                navigator.share({
                    title: decodeURIComponent(title),
                    text: `¡Mira este increíble vehículo que encontré!`,
                    url: decodeURIComponent(url)
                }).catch(console.error);
            } else {
                // Fallback para navegadores que no soportan la API
                alert('Usa los botones de tu navegador para compartir esta página.');
            }
        });
    }

    // --- LÓGICA DE ANIMACIONES AL SCROLL ---
    const revealElements = document.querySelectorAll('.reveal-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, {
        threshold: 0.1
    });

    revealElements.forEach(el => observer.observe(el));


    // --- LÓGICA PARA OCULTAR EL LOADER ---
    const loader = document.getElementById('page-loader');
    const mainContent = document.querySelector('.main-content');
    
    // Ocultar el loader cuando la página esté completamente cargada
    window.addEventListener('load', () => {
      if (loader) loader.classList.add('hidden');
      if (mainContent) {
          mainContent.classList.remove('content-hidden');
          mainContent.classList.add('visible');
      }

      // Disparar animaciones de entrada después de que el loader se oculte
      setTimeout(() => {
        document.querySelectorAll('.fade-in-left, .fade-in-right, .fade-in-up').forEach(el => {
            el.style.opacity = '1';
            el.style.transform = 'translate(0, 0)';
        });
      }, 100);
    });
});