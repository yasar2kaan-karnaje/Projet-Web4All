document.addEventListener('DOMContentLoaded', function() {
    // Gestion du bandeau de cookies
    const cookieBanner = document.getElementById('cookie-banner');
    const btnAccept = document.getElementById('btn-accept-cookies');
    const btnRefuse = document.getElementById('btn-refuse-cookies');
    
    if (!document.cookie.includes('cookie_consent=')) {
        setTimeout(() => {
            if (cookieBanner) cookieBanner.classList.add('show');
        }, 500);
    }
    
    function setCookieChoice(choice) {
        document.cookie = `cookie_consent=${choice}; max-age=${60 * 60 * 24 * 365}; path=/; samesite=strict`;
        if (cookieBanner) cookieBanner.classList.remove('show');
    }
    
    if (btnAccept && btnRefuse) {
        btnAccept.addEventListener('click', () => setCookieChoice('accepted'));
        btnRefuse.addEventListener('click', () => setCookieChoice('rejected'));
    }
});
