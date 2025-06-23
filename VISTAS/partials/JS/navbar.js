// Script para efectos dinámicos del navbar
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar-enhanced.fixed-top'); // Más específico
    
    if (navbar) { // Asegurarse que el navbar exista
        // Efecto de scroll
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }
    
    // Efecto de hover mejorado para los dropdowns (usando Bootstrap events es más robusto)
    // El código original de hover con setTimeout puede ser un poco problemático.
    // Bootstrap 5 maneja los dropdowns bien con sus atributos data-bs-toggle.
    // Si quieres un efecto de hover para abrir, es mejor usar jQuery o más JS
    // pero por ahora, nos basaremos en el click de Bootstrap que es más estándar.
    // Si el objetivo era abrir con hover, se necesitaría jQuery para hacerlo más fácil
    // o un manejo más complejo de eventos 'mouseenter' y 'mouseleave' con timeouts.

    // Ejemplo simple si quisieras reactivar un hover (puede requerir ajustes para Bootstrap 5)
    /*
    const dropdowns = document.querySelectorAll('.navbar-enhanced .dropdown');
    dropdowns.forEach(dropdown => {
        let leaveTimeout;
        const dropdownToggle = dropdown.querySelector('.dropdown-toggle');
        const dropdownMenu = dropdown.querySelector('.dropdown-menu');

        if (dropdownToggle && dropdownMenu) {
            dropdown.addEventListener('mouseenter', function() {
                clearTimeout(leaveTimeout);
                // Para que funcione con Bootstrap 5, necesitarías instanciar y mostrar el dropdown
                var bsDropdown = bootstrap.Dropdown.getInstance(dropdownToggle) || new bootstrap.Dropdown(dropdownToggle);
                bsDropdown.show();
            });
            
            dropdown.addEventListener('mouseleave', function() {
                leaveTimeout = setTimeout(() => {
                    var bsDropdown = bootstrap.Dropdown.getInstance(dropdownToggle);
                    if (bsDropdown) {
                        bsDropdown.hide();
                    }
                }, 200); // Un pequeño delay antes de cerrar
            });
        }
    });
    */
});

// Nota: El script de jQuery para el active link y otros efectos globales
// debería permanecer en VISTAS/JS/global.js porque se aplica al navbar
// DESPUÉS de que este es cargado en el placeholder.
// Este archivo navbar.js es para JS que es intrínseco al HTML del navbar.php mismo
// si lo hubiera, o para inicializar efectos que no dependen de jQuery.