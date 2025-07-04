// JavaScript Document - Contacto Enhanced
$(document).ready(function() {
    
    // ===== CONFIGURACIÓN INICIAL =====
    initializeEnhancedContact();
    
    function initializeEnhancedContact() {
        setupCustomCursor();
        setupParticles();
        setupScrollAnimations();
        setupFormEnhancements();
        setupValidation();
        setupTypingEffect();
        setupCounters();
        setupSmoothScrolling();
        setupScrollToTop();
        hideLoader();
    }

    // ===== CURSOR PERSONALIZADO =====
    function setupCustomCursor() {
        const cursor = $('.custom-cursor');
        const cursorDot = $('.custom-cursor-dot');
        
        if (window.innerWidth > 768) { // Solo en desktop
            $(document).mousemove(function(e) {
                cursor.css({
                    'left': e.clientX + 'px',
                    'top': e.clientY + 'px'
                });
                cursorDot.css({
                    'left': e.clientX + 'px',
                    'top': e.clientY + 'px'
                });
            });

            // Efectos hover
            $('a, button, .enhanced-card, .social-icon').hover(
                function() {
                    cursor.addClass('cursor-hover');
                    cursorDot.addClass('cursor-hover');
                },
                function() {
                    cursor.removeClass('cursor-hover');
                    cursorDot.removeClass('cursor-hover');
                }
            );
        }
    }

    // ===== PARTÍCULAS FLOTANTES =====
    function setupParticles() {
        $('.particle').each(function(index) {
            const particle = $(this);
            const delay = Math.random() * 2;
            const duration = 3 + Math.random() * 4;
            
            particle.css({
                'animation-delay': delay + 's',
                'animation-duration': duration + 's',
                'left': Math.random() * 100 + '%',
                'top': Math.random() * 100 + '%'
            });
        });
    }

    // ===== ANIMACIONES DE SCROLL =====
    function setupScrollAnimations() {
        // Intersection Observer para animaciones
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = $(entry.target);
                    const animationType = element.data('aos');
                    const delay = element.data('aos-delay') || 0;
                    
                    setTimeout(() => {
                        element.addClass('aos-animate');
                        
                        // Animaciones específicas
                        switch(animationType) {
                            case 'fade-up':
                                element.addClass('fade-up-animate');
                                break;
                            case 'slide-right':
                                element.addClass('slide-right-animate');
                                break;
                            case 'slide-left':
                                element.addClass('slide-left-animate');
                                break;
                            case 'zoom-in':
                                element.addClass('zoom-in-animate');
                                break;
                        }
                    }, delay);
                }
            });
        }, observerOptions);

        // Observar elementos con animaciones
        $('[data-aos]').each(function() {
            observer.observe(this);
        });
    }

    // ===== MEJORAS DEL FORMULARIO =====
    function setupFormEnhancements() {
        // Floating labels
        $('.enhanced-input').on('focus blur', function() {
            const input = $(this);
            const label = input.siblings('.floating-label');
            
            if (input.val() !== '' || input.is(':focus')) {
                label.addClass('active');
            } else {
                label.removeClass('active');
            }
        });

        // Contador de caracteres
        $('#contactMessage').on('input', function() {
            const count = $(this).val().length;
            $('#messageCount').text(count);
            
            if (count > 450) {
                $('#messageCount').addClass('text-warning');
            } else {
                $('#messageCount').removeClass('text-warning');
            }
        });

        // Progress bar del formulario
        $('.enhanced-input, .form-check-input').on('input change', function() {
            updateFormProgress();
        });

        // Efectos de ripple en botones
        $('.btn-enhanced, .btn-enhanced-submit').on('click', function(e) {
            const button = $(this);
            const ripple = button.find('.btn-ripple');
            
            if (ripple.length === 0) {
                button.append('<div class="btn-ripple"></div>');
            }
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            button.find('.btn-ripple').css({
                width: size + 'px',
                height: size + 'px',
                left: x + 'px',
                top: y + 'px'
            }).addClass('ripple-animate');
            
            setTimeout(() => {
                button.find('.btn-ripple').removeClass('ripple-animate');
            }, 600);
        });

        // Efectos hover en tarjetas de información
        $('.enhanced-card').hover(
            function() {
                $(this).addClass('card-hover');
                $(this).find('.info-icon i').addClass('icon-bounce');
            },
            function() {
                $(this).removeClass('card-hover');
                $(this).find('.info-icon i').removeClass('icon-bounce');
            }
        );

        // Efectos en iconos sociales
        $('.social-icon').hover(
            function() {
                $(this).addClass('social-hover');
            },
            function() {
                $(this).removeClass('social-hover');
            }
        );
    }

    // ===== VALIDACIÓN MEJORADA =====
    function setupValidation() {
        const forms = document.querySelectorAll('.needs-validation');

        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                event.stopPropagation();

                if (!form.checkValidity()) {
                    // Animación de shake para errores
                    $(form).addClass('shake-animation');
                    setTimeout(() => {
                        $(form).removeClass('shake-animation');
                    }, 500);
                    
                    // Focus en el primer campo con error
                    const firstInvalid = $(form).find(':invalid').first();
                    firstInvalid.focus();
                    
                } else {
                    // Formulario válido - simular envío
                    handleFormSubmission();
                }

                form.classList.add('was-validated');
                updateValidationIcons();
            }, false);
        });

        // Validación en tiempo real
        $('.enhanced-input').on('input blur', function() {
            const input = $(this);
            const isValid = this.checkValidity();
            
            if (input.val() !== '') {
                if (isValid) {
                    input.addClass('is-valid').removeClass('is-invalid');
                } else {
                    input.addClass('is-invalid').removeClass('is-valid');
                }
            }
            
            updateValidationIcons();
            updateFormProgress();
        });
    }

    function updateValidationIcons() {
        $('.enhanced-input').each(function() {
            const input = $(this);
            const validIcon = input.siblings('.validation-icon').find('.bi-check-circle-fill');
            const invalidIcon = input.siblings('.validation-icon').find('.bi-x-circle-fill');
            
            if (input.hasClass('is-valid')) {
                validIcon.show();
                invalidIcon.hide();
            } else if (input.hasClass('is-invalid')) {
                validIcon.hide();
                invalidIcon.show();
            } else {
                validIcon.hide();
                invalidIcon.hide();
            }
        });
    }

    function updateFormProgress() {
        const totalFields = $('.enhanced-input[required], .form-check-input[required]').length;
        const validFields = $('.enhanced-input[required].is-valid, .form-check-input[required]:checked').length;
        const progress = (validFields / totalFields) * 100;
        
        $('#formProgress').css('width', progress + '%');
    }

    function handleFormSubmission() {
        const submitBtn = $('#submitBtn');
        const btnText = submitBtn.find('.btn-text');
        const btnLoading = submitBtn.find('.btn-loading');
        const btnSuccess = submitBtn.find('.btn-success');
        
        // Estado de carga
        btnText.addClass('d-none');
        btnLoading.removeClass('d-none');
        submitBtn.prop('disabled', true);
        
        // Simular envío (reemplazar con lógica real)
        setTimeout(() => {
            // Estado de éxito
            btnLoading.addClass('d-none');
            btnSuccess.removeClass('d-none');
            
            // Mostrar mensaje de éxito
            showSuccessMessage();
            
            // Resetear después de 3 segundos
            setTimeout(() => {
                resetForm();
            }, 3000);
            
        }, 2000);
    }

    function showSuccessMessage() {
        $('#contactForm').fadeOut(300, function() {
            $('#successMessage').removeClass('d-none').hide().fadeIn(300);
            
            // Animación del checkmark
            setTimeout(() => {
                $('.checkmark-circle').addClass('animate');
                $('.checkmark-check').addClass('animate');
            }, 200);
            
            // Confetti effect (opcional)
            createConfetti();
        });
    }

    function resetForm() {
        const form = $('#contactForm')[0];
        const submitBtn = $('#submitBtn');
        
        form.reset();
        form.classList.remove('was-validated');
        
        $('.enhanced-input').removeClass('is-valid is-invalid');
        $('.floating-label').removeClass('active');
        updateValidationIcons();
        updateFormProgress();
        
        // Resetear botón
        submitBtn.find('.btn-success').addClass('d-none');
        submitBtn.find('.btn-text').removeClass('d-none');
        submitBtn.prop('disabled', false);
        
        // Mostrar formulario nuevamente
        $('#successMessage').fadeOut(300, function() {
            $('#contactForm').fadeIn(300);
        });
    }

    // ===== EFECTO DE TYPING =====
    function setupTypingEffect() {
        const text = "Contáctanos";
        const typingElement = $('.typing-text');
        let index = 0;
        
        typingElement.text('');
        
        function typeWriter() {
            if (index < text.length) {
                typingElement.text(typingElement.text() + text.charAt(index));
                index++;
                setTimeout(typeWriter, 100);
            } else {
                typingElement.addClass('typing-complete');
            }
        }
        
        setTimeout(typeWriter, 1000);
    }

    // ===== CONTADORES ANIMADOS =====
    function setupCounters() {
        const counters = $('.stat-number');
        
        const counterObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = $(entry.target);
                    const target = parseInt(counter.data('count'));
                    let current = 0;
                    const increment = target / 100;
                    
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        counter.text(Math.floor(current));
                    }, 20);
                    
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        counters.each(function() {
            counterObserver.observe(this);
        });
    }

    // ===== SMOOTH SCROLLING =====
    function setupSmoothScrolling() {
        $('.smooth-scroll').on('click', function(e) {
            e.preventDefault();
            const target = $($(this).attr('href'));
            
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 800, 'easeInOutCubic');
            }
        });
    }

    // ===== SCROLL TO TOP =====
    function setupScrollToTop() {
        const scrollBtn = $('#scrollToTop');
        
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                scrollBtn.addClass('show');
            } else {
                scrollBtn.removeClass('show');
            }
        });
        
        scrollBtn.on('click', function() {
            $('html, body').animate({
                scrollTop: 0
            }, 800, 'easeInOutCubic');
        });
    }

    // ===== OCULTAR LOADER =====
    function hideLoader() {
        setTimeout(() => {
            $('#page-loader').fadeOut(500, function() {
                $('.content-hidden').removeClass('content-hidden').addClass('content-visible');
            });
        }, 1500);
    }

    // ===== CONFETTI EFFECT =====
    function createConfetti() {
        const colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1'];
        
        for (let i = 0; i < 50; i++) {
            const confetti = $('<div class="confetti"></div>');
            confetti.css({
                'background-color': colors[Math.floor(Math.random() * colors.length)],
                'left': Math.random() * 100 + '%',
                'animation-delay': Math.random() * 2 + 's',
                'animation-duration': (Math.random() * 3 + 2) + 's'
            });
            
            $('body').append(confetti);
            
            setTimeout(() => {
                confetti.remove();
            }, 5000);
        }
    }

    // ===== EFECTOS ADICIONALES =====
    
    // Parallax sutil en hero
    $(window).scroll(function() {
        const scrolled = $(this).scrollTop();
        const parallax = $('.hero-background-animation');
        const speed = scrolled * 0.5;
        
        parallax.css('transform', 'translateY(' + speed + 'px)');
    });

    // Magnetic effect en botones (solo desktop)
    if (window.innerWidth > 768) {
        $('.btn-enhanced, .social-icon').on('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            $(this).css('transform', `translate(${x * 0.1}px, ${y * 0.1}px)`);
        });
        
        $('.btn-enhanced, .social-icon').on('mouseleave', function() {
            $(this).css('transform', 'translate(0, 0)');
        });
    }

    // Efectos de entrada escalonados
    function staggerAnimation(selector, delay = 100) {
        $(selector).each(function(index) {
            $(this).css('animation-delay', (index * delay) + 'ms');
        });
    }
    
    staggerAnimation('.contact-info-item', 150);
    staggerAnimation('.social-icon', 100);
    staggerAnimation('.stat-item', 200);

    // ===== EASING PERSONALIZADO =====
    $.easing.easeInOutCubic = function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t*t + b;
        return c/2*((t-=2)*t*t + 2) + b;
    };

    // ===== RESPONSIVE ADJUSTMENTS =====
    function handleResize() {
        if (window.innerWidth <= 768) {
            // Deshabilitar efectos pesados en móvil
            $('.custom-cursor, .custom-cursor-dot').hide();
            $('.particle').css('animation-play-state', 'paused');
        } else {
            $('.custom-cursor, .custom-cursor-dot').show();
            $('.particle').css('animation-play-state', 'running');
        }
    }
    
    $(window).resize(handleResize);
    handleResize(); // Ejecutar al cargar

    // ===== ACCESIBILIDAD =====
    
    // Reducir movimiento si el usuario lo prefiere
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        $('*').css({
            'animation-duration': '0.01ms !important',
            'animation-iteration-count': '1 !important',
            'transition-duration': '0.01ms !important'
        });
    }

    // Focus visible para navegación por teclado
    $('.enhanced-input, .btn-enhanced').on('focus', function() {
        $(this).addClass('keyboard-focus');
    }).on('blur', function() {
        $(this).removeClass('keyboard-focus');
    });

    console.log('🚗 AutoMercado Total - Contacto Enhanced cargado correctamente');
});

