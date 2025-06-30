$(document).ready(function() {
    // Animación de entrada escalonada para las tarjetas del dashboard
    $('.dashboard-card').each(function(index) {
        // Se aplica un pequeño retraso a cada tarjeta para que no aparezcan todas a la vez
        $(this).css('animation-delay', (index * 100) + 'ms');
    });
});