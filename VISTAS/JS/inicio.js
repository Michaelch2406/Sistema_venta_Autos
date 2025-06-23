$(document).ready(function() {
    var heroCarousel = document.getElementById('heroVideoCarousel');
    var videos = heroCarousel.querySelectorAll('video');

    // Pausar todos los videos inicialmente, excepto el del item activo si ya está en play.
    // Bootstrap podría intentar reproducir el primero si tiene 'autoplay'.
    // El atributo 'loop' en cada video los hará repetirse individualmente si no se maneja el 'ended'.
    // Para un carrusel continuo, es mejor quitar 'loop' de los videos individuales y manejarlo con JS.
    // Por ahora, dejaremos 'loop' y 'autoplay' y nos centraremos en la transición.

    // Quitar 'loop' de todos los videos para que el evento 'ended' funcione para la transición
    videos.forEach(function(video) {
        video.loop = false; 
    });
    
    // Función para reproducir el video del slide activo y pausar los demás
    function playActiveVideo(event) {
        // Pausar todos los videos
        videos.forEach(function(video) {
            video.pause();
        });
        // Reproducir el video en el slide activo
        var activeItem = heroCarousel.querySelector('.carousel-item.active');
        if (activeItem) {
            var activeVideo = activeItem.querySelector('video');
            if (activeVideo) {
                activeVideo.play().catch(function(error) {
                    console.error("Error al intentar reproducir video:", error);
                });
            }
        }
    }

    // Cuando un nuevo slide se muestra (después de la transición)
    heroCarousel.addEventListener('slid.bs.carousel', playActiveVideo);

    // Manejar el evento 'ended' de cada video para pasar al siguiente slide
    videos.forEach(function(video) {
        video.addEventListener('ended', function() {
            // Mueve el carrusel al siguiente slide
            var carouselInstance = bootstrap.Carousel.getInstance(heroCarousel);
            if (carouselInstance) {
                carouselInstance.next();
            }
        });
    });

    // Intentar reproducir el video del primer slide activo al cargar la página
    // Esto es importante si el `autoplay` no funciona consistentemente en todos los navegadores
    // o si se quita el atributo autoplay de los videos.
    var firstActiveVideo = heroCarousel.querySelector('.carousel-item.active video');
    if (firstActiveVideo) {
        firstActiveVideo.play().catch(function(error) {
            console.error("Error al intentar reproducir el primer video:", error);
            // Podrías mostrar una imagen de fallback o un mensaje si la reproducción automática falla.
        });
    }
});