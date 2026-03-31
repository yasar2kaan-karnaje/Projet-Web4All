/**
 * Gestion nav avec menu burger
 * Attend que le DOM soit complètement chargé avant d'exécuter le script.
 */
document.addEventListener('DOMContentLoaded', function () {

    // --- SECTION : MENU BURGER (MOBILE) ---
    const burgerMenu = document.getElementById('burger-menu');
    const navDesktop = document.querySelector('.nav-desktop');

    if (burgerMenu) {
        burgerMenu.addEventListener('click', function () {
            // Icône burger mode toggle pour afficher en basculement
            navDesktop.classList.toggle('active');
            burgerMenu.classList.toggle('open');
        });
    }

});