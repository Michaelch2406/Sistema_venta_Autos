$(document).ready(function() {

    // --- GESTIÓN DEL CARRUSEL DE VIDEOS ---
    const heroCarousel = document.getElementById('heroVideoCarousel');
    if (heroCarousel) {
        const videos = heroCarousel.querySelectorAll('video');

        // Inicializamos el carrusel de Bootstrap con el intervalo desactivado.
        // Nosotros controlaremos cuándo avanza.
        const carouselInstance = new bootstrap.Carousel(heroCarousel, {
            interval: false, // Desactiva el avance automático por tiempo.
            ride: false,     // Desactiva el inicio automático al cargar la página.
        });

        // Función para reproducir el video activo y pausar los demás.
        function playActiveVideo() {
            videos.forEach(video => {
                video.pause();
                video.currentTime = 0; // Reinicia los videos no activos.
            });

            const activeItem = heroCarousel.querySelector('.carousel-item.active');
            if (activeItem) {
                const activeVideo = activeItem.querySelector('video');
                if (activeVideo) {
                    // Intenta reproducir el video, capturando posibles errores si el navegador lo bloquea.
                    activeVideo.play().catch(error => {
                        console.error("El navegador bloqueó la reproducción automática:", error);
                    });
                }
            }
        }

        // Para cada video, cuando termine, pasamos al siguiente slide.
        videos.forEach(video => {
            video.addEventListener('ended', () => {
                carouselInstance.next();
            });
        });

        // Cuando el carrusel termina de cambiar de slide (manualmente o automáticamente).
        heroCarousel.addEventListener('slid.bs.carousel', playActiveVideo);

        // Inicia la reproducción del primer video al cargar la página.
        playActiveVideo();
    }


    // --- ANIMACIONES AL HACER SCROLL ---
    function animateOnScroll() {
        const elements = document.querySelectorAll('.animate-on-scroll');
        const elementVisible = 150; // Distancia desde la parte inferior de la ventana para activar la animación.

        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;

            if (elementTop < window.innerHeight - elementVisible) {
                element.classList.add('animate-in');
            }
        });
    }

    // Ejecutar animaciones cuando se carga la página y al hacer scroll.
    window.addEventListener('scroll', animateOnScroll);
    window.addEventListener('load', animateOnScroll);
    // Primera ejecución por si hay elementos visibles sin hacer scroll.
    animateOnScroll();


    // --- EFECTO PARALLAX SUTIL EN EL HERO ---
    const heroSection = document.querySelector('.hero-section');
    const heroContent = document.querySelector('.hero-content');

    if (heroSection && heroContent) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;

            // Solo aplicar el efecto si estamos viendo la sección del hero.
            if (scrolled < heroSection.offsetHeight) {
                // Mueve el contenido hacia abajo a la mitad de la velocidad del scroll.
                // Usamos `translate` para un rendimiento más fluido.
                heroContent.style.transform = `translate(-50%, calc(-50% + ${scrolled * 0.4}px))`;
            }
        });
    }

});