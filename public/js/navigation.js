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

    // Menu utilisateur dropdown
    const userToggle = document.getElementById('user-menu-toggle');
    const userDropdown = document.getElementById('user-dropdown');
    if (userToggle && userDropdown) {
        userToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });
        document.addEventListener('click', function (e) {
            if (!userToggle.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });
    }

});