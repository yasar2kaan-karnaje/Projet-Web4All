document.addEventListener('DOMContentLoaded', function() {
    const scrollTopBtn = document.createElement('button');
    scrollTopBtn.innerHTML = '&uarr;';
    scrollTopBtn.id = 'scroll-top-btn';
    scrollTopBtn.style.display = 'none';
    scrollTopBtn.style.position = 'fixed';
    scrollTopBtn.style.bottom = '20px';
    scrollTopBtn.style.right = '20px';
    document.body.appendChild(scrollTopBtn);

    //la le bouton apparait au bout de 150 pixel
    window.addEventListener('scroll', () => {
        if (window.scrollY > 150) {
            scrollTopBtn.style.display = 'block';
        }
        else {
            scrollTopBtn.style.display = 'none';
        }
    });

    scrollTopBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});