document.addEventListener('DOMContentLoaded', function() {
    const footerEnhanced = document.querySelector('.footer-enhanced');
    if (!footerEnhanced) return; // Salir si el footer no está en la página

    // Animación de aparición de elementos del footer
    const observerOptions = {
        threshold: 0.1, // Aparece cuando al menos el 10% es visible
        rootMargin: '0px 0px -50px 0px' // Se activa un poco antes de que entre completamente en viewport
    };

    const sectionObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target); // Dejar de observar una vez animado
            }
        });
    }, observerOptions);

    const footerSections = footerEnhanced.querySelectorAll('.footer-section');
    footerSections.forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(30px)';
        section.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
        sectionObserver.observe(section);
    });

    // Efecto de hover mejorado para los enlaces sociales (opcional, CSS ya hace algo)
    // const socialLinks = footerEnhanced.querySelectorAll('.social-links a');
    // socialLinks.forEach(link => {
    //     link.addEventListener('mouseenter', function() {
    //         // this.style.transform = 'translateY(-5px) scale(1.15) rotate(5deg)'; // Ya manejado por CSS :hover
    //     });
        
    //     link.addEventListener('mouseleave', function() {
    //         // this.style.transform = 'translateY(0) scale(1) rotate(0deg)'; // Ya manejado por CSS :hover
    //     });
    // });

    // Animación de "typewriter" para el copyright (solo si el elemento existe)
    const copyrightElement = footerEnhanced.querySelector('.copyright-text-animated'); // Necesitas añadir esta clase al span del texto
    if (copyrightElement) {
        const originalText = copyrightElement.textContent.trim();
        copyrightElement.textContent = ''; // Limpiar para el efecto
        let i = 0;
        let isTag = false;
        let tempText = '';

        function typeWriterEffect() {
            if (i < originalText.length) {
                const char = originalText.charAt(i);
                if (char === '<') isTag = true;
                if (char === '>') isTag = false;

                if (isTag) {
                    tempText += char;
                } else {
                    if (tempText) {
                         copyrightElement.innerHTML += tempText; // Añadir tags HTML acumulados
                         tempText = '';
                    }
                    copyrightElement.innerHTML += char;
                }
                i++;
                setTimeout(typeWriterEffect, 30); // Ajusta la velocidad
            } else {
                if (tempText) copyrightElement.innerHTML += tempText; // Añadir tags HTML restantes
            }
        }
        // Iniciar el efecto después de un pequeño delay para que el footer sea visible
        setTimeout(typeWriterEffect, 500);
    }
});