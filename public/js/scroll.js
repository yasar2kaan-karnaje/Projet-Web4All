document.addEventListener('DOMContentLoaded', function() {
    const scrollTopBtn = document.createElement('button');
    scrollTopBtn.innerHTML = '&uarr;';
    scrollTopBtn.id = 'scroll-top-btn';
    scrollTopBtn.style.display = 'none';
    scrollTopBtn.style.position = 'fixed';
    scrollTopBtn.style.bottom = '20px';
    scrollTopBtn.style.right = '20px';
    document.body.appendChild(scrollTopBtn);
    scrollTopBtn.style.zIndex = '1000';
    scrollTopBtn.style.padding = '10px 15px';
    scrollTopBtn.style.fontSize = '20px';
    scrollTopBtn.style.cursor = 'pointer';
    scrollTopBtn.style.backgroundColor = 'var(--primary, #2A9D8F)';
    scrollTopBtn.style.color = '#ffffff';
    scrollTopBtn.style.border = 'none';
    scrollTopBtn.style.borderRadius = '50%';
    scrollTopBtn.style.boxShadow = '0 2px 5px rgba(0,0,0,0.3)';
    scrollTopBtn.style.width = '45px';
    scrollTopBtn.style.height = '45px';
    scrollTopBtn.style.lineHeight = '24px';
    scrollTopBtn.style.textAlign = 'center';
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