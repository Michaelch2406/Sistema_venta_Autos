$(document).ready(function() {
    const $listaFavoritosContainer = $('#lista-favoritos-container');
    const $viewControls = $('.view-controls .btn');
    
    // Initialize view mode
    let currentView = 'grid';
    
    // View mode switcher
    $viewControls.on('click', function() {
        const $this = $(this);
        const view = $this.data('view');
        
        if (view === currentView) return;
        
        $viewControls.removeClass('active');
        $this.addClass('active');
        
        switchView(view);
        currentView = view;
    });
    
    // Switch between grid and list view
    function switchView(view) {
        $listaFavoritosContainer.removeClass('vehicles-grid vehicles-list');
        
        if (view === 'list') {
            $listaFavoritosContainer.addClass('vehicles-list');
            // Add list-specific styles
            $listaFavoritosContainer.css({
                'display': 'flex',
                'flex-direction': 'column',
                'gap': '1rem'
            });
            
            // Modify cards for list view
            $('.vehicle-card').addClass('list-view-card');
        } else {
            $listaFavoritosContainer.addClass('vehicles-grid');
            $listaFavoritosContainer.css({
                'display': 'grid',
                'grid-template-columns': 'repeat(auto-fill, minmax(350px, 1fr))',
                'gap': '2rem'
            });
            
            // Remove list view modifications
            $('.vehicle-card').removeClass('list-view-card');
        }
        
        // Animate view change
        $('.vehicle-card').each(function(index) {
            $(this).css({
                'animation': `fadeInUp 0.3s ease-out ${index * 0.05}s both`
            });
        });
    }
    
    // Show toast notification
    function showToast(message, type = 'success') {
        const $toast = $('#favoriteToast');
        const $toastBody = $toast.find('.toast-body');
        
        // Set message and icon based on type
        if (type === 'success') {
            $toastBody.html(`<i class="bi bi-check-circle-fill text-success me-2"></i>${message}`);
        } else if (type === 'error') {
            $toastBody.html(`<i class="bi bi-exclamation-circle-fill text-danger me-2"></i>${message}`);
        } else {
            $toastBody.html(`<i class="bi bi-info-circle-fill text-info me-2"></i>${message}`);
        }
        
        // Show toast
        const toast = new bootstrap.Toast($toast[0], {
            delay: 4000
        });
        toast.show();
    }
    
    // Add loading state to button
    function setButtonLoading($button, loading = true) {
        if (loading) {
            $button.prop('disabled', true);
            $button.addClass('btn-loading');
            $button.data('original-html', $button.html());
            $button.html('<span class="spinner-border spinner-border-sm me-2"></span>Eliminando...');
        } else {
            $button.prop('disabled', false);
            $button.removeClass('btn-loading');
            $button.html($button.data('original-html') || $button.html());
        }
    }
    
    // Animate card removal
    function removeCardWithAnimation($card, callback) {
        // Add removal animation
        $card.css({
            'transform': 'scale(0.95)',
            'opacity': '0.7'
        });
        
        setTimeout(() => {
            $card.animate({
                opacity: 0,
                transform: 'translateY(-20px)'
            }, 300, function() {
                $card.slideUp(200, callback);
            });
        }, 100);
    }
    
    // Show empty state when no favorites remain
    function showEmptyState() {
        const emptyStateHTML = `
            <div class="empty-state-dynamic">
                <div class="empty-icon">
                    <i class="bi bi-heart"></i>
                </div>
                <h3>Ya no tienes favoritos guardados</h3>
                <p>¡Explora nuestro catálogo para encontrar nuevos vehículos que te gusten!</p>
                <div class="empty-actions">
                    <a href="autos_usados.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-search me-2"></i>Explorar Vehículos
                    </a>
                </div>
            </div>
        `;
        
        $listaFavoritosContainer.fadeOut(300, function() {
            $listaFavoritosContainer.html(emptyStateHTML).fadeIn(400);
        });
        
        // Update results count
        $('.results-count').text('0');
        $('.results-text').text('vehículos en favoritos');
    }
    
    // Update results count
    function updateResultsCount() {
        const count = $('.favorito-card-item').length;
        $('.results-count').text(count);
        $('.results-text').text(count === 1 ? 'vehículo en favoritos' : 'vehículos en favoritos');
    }
    
    // Handle remove favorite button click
    $listaFavoritosContainer.on('click', '.btn-quitar-favorito-lista', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $btnQuitar = $(this);
        const vehId = $btnQuitar.data('veh-id');
        const $tarjetaVehiculo = $btnQuitar.closest('.favorito-card-item');
        
        if (!vehId) {
            console.error('No se pudo obtener el ID del vehículo desde el botón.');
            showToast('Error: No se pudo identificar el vehículo para quitar.', 'error');
            return;
        }
        
        // Show confirmation dialog with better styling
        const result = confirm('¿Estás seguro de que quieres quitar este vehículo de tus favoritos?\n\nEsta acción no se puede deshacer.');
        
        if (!result) {
            return;
        }
        
        // Set loading state
        setButtonLoading($btnQuitar, true);
        
        // Add visual feedback to the card
        $tarjetaVehiculo.addClass('removing');
        
        $.ajax({
            url: '../AJAX/favoritos_ajax.php',
            type: 'POST',
            data: {
                accion: 'quitar',
                veh_id: vehId
            },
            dataType: 'json',
            timeout: 10000, // 10 seconds timeout
            success: function(response) {
                if (response.status === 'success' || (response.status === 'info' && !response.esFavorito)) {
                    // Success - remove the card with animation
                    removeCardWithAnimation($tarjetaVehiculo, function() {
                        $tarjetaVehiculo.remove();
                        
                        // Check if there are more favorites
                        const remainingFavorites = $('.favorito-card-item').length;
                        
                        if (remainingFavorites === 0) {
                            showEmptyState();
                        } else {
                            updateResultsCount();
                            // Re-animate remaining cards
                            $('.favorito-card-item').each(function(index) {
                                $(this).css({
                                    'animation': `fadeInUp 0.3s ease-out ${index * 0.05}s both`
                                });
                            });
                        }
                    });
                    
                    // Show success message
                    showToast(response.message || 'Vehículo eliminado de favoritos correctamente.', 'success');
                    
                } else {
                    // Error response
                    $tarjetaVehiculo.removeClass('removing');
                    setButtonLoading($btnQuitar, false);
                    showToast(response.message || 'No se pudo quitar el vehículo de favoritos.', 'error');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // AJAX error
                $tarjetaVehiculo.removeClass('removing');
                setButtonLoading($btnQuitar, false);
                
                let errorMessage = 'Error de conexión al intentar quitar el favorito.';
                
                if (textStatus === 'timeout') {
                    errorMessage = 'La operación tardó demasiado tiempo. Por favor, intenta nuevamente.';
                } else if (jqXHR.status === 0) {
                    errorMessage = 'No se pudo conectar con el servidor. Verifica tu conexión a internet.';
                } else if (jqXHR.status >= 500) {
                    errorMessage = 'Error del servidor. Por favor, intenta más tarde.';
                }
                
                showToast(errorMessage, 'error');
                console.error('Error AJAX al quitar favorito:', {
                    status: jqXHR.status,
                    statusText: textStatus,
                    error: errorThrown,
                    response: jqXHR.responseText
                });
            }
        });
    });
    
    // Handle image loading errors
    $listaFavoritosContainer.on('error', 'img', function() {
        $(this).attr('src', '../PUBLIC/Img/auto_placeholder.png');
    });
    
    // Add hover effects for better UX
    $listaFavoritosContainer.on('mouseenter', '.vehicle-card', function() {
        $(this).addClass('card-hover');
    }).on('mouseleave', '.vehicle-card', function() {
        $(this).removeClass('card-hover');
    });
    
    // Intersection Observer for lazy loading animations
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '50px'
        });
        
        // Observe all vehicle cards
        $('.vehicle-card').each(function() {
            observer.observe(this);
        });
    }
    
    // Handle click on card image for better UX
    $listaFavoritosContainer.on('click', '.card-image', function(e) {
        if (e.target.tagName === 'IMG' || e.target.tagName === 'A') {
            // Let default behavior handle navigation
            return;
        }
        
        // If clicked on overlay or other elements, navigate to detail page
        const href = $(this).find('a').attr('href');
        if (href) {
            window.location.href = href;
        }
    });
    
    // Keyboard navigation support
    $listaFavoritosContainer.on('keydown', '.vehicle-card', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            const href = $(this).find('.card-image a').attr('href');
            if (href) {
                window.location.href = href;
            }
        }
    });
    
    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Performance optimization: debounce resize events
    let resizeTimeout;
    $(window).on('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            // Recalculate grid layout if needed
            if (currentView === 'grid') {
                $listaFavoritosContainer.css({
                    'grid-template-columns': 'repeat(auto-fill, minmax(350px, 1fr))'
                });
            }
        }, 250);
    });
    
    // Initialize page
    setTimeout(function() {
        $('.vehicle-card').addClass('loaded');
    }, 100);
    
    console.log('Mis Favoritos JS initialized successfully');
});