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


    // --- TRIANGLES ANIMÉS HERO ---
    const hero = document.querySelector('.hero');
    if (hero) {
        const c = document.createElement('canvas');
        c.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;pointer-events:none;z-index:0;';
        Object.assign(hero.style, { position: 'relative', overflow: 'hidden' });
        const hc = hero.querySelector('.hero-content');
        if (hc) Object.assign(hc.style, { position: 'relative', zIndex: '1' });
        hero.prepend(c);
        const ctx = c.getContext('2d');
        const r = () => Math.random();
        const resize = () => { c.width = hero.offsetWidth; c.height = hero.offsetHeight; };
        const mk = () => ({ x: r() * c.width, y: r() * c.height, s: r() * 18 + 8, vx: (r() - .5) * .6, vy: (r() - .5) * .6, a: r() * Math.PI * 2, va: (r() - .5) * .02, o: r() * .3 + .08 });
        let T, mouse = { x: -999, y: -999 };
        const init = () => { resize(); T = Array.from({ length: 30 }, mk); };
        const draw = t => {
            ctx.save(); ctx.translate(t.x, t.y); ctx.rotate(t.a);
            ctx.beginPath(); ctx.moveTo(0, -t.s); ctx.lineTo(t.s * .866, t.s * .5); ctx.lineTo(-t.s * .866, t.s * .5); ctx.closePath();
            ctx.strokeStyle = `rgba(255,215,0,${t.o})`; ctx.lineWidth = 1.5; ctx.stroke(); ctx.restore();
        };
        const loop = () => {
            ctx.clearRect(0, 0, c.width, c.height);
            T.forEach(t => {
                // Répulsion souris : pousse le triangle si la souris est à moins de 100px
                const dx = t.x - mouse.x, dy = t.y - mouse.y, dist = Math.hypot(dx, dy);
                if (dist < 100 && dist > 0) { t.x += (dx / dist) * 2.5; t.y += (dy / dist) * 2.5; }
                t.x += t.vx; t.y += t.vy; t.a += t.va;
                if (t.x < -t.s) t.x = c.width + t.s; if (t.x > c.width + t.s) t.x = -t.s;
                if (t.y < -t.s) t.y = c.height + t.s; if (t.y > c.height + t.s) t.y = -t.s;
                draw(t);
            });
            requestAnimationFrame(loop);
        };
        hero.addEventListener('mousemove', e => { const b = c.getBoundingClientRect(); mouse = { x: e.clientX - b.left, y: e.clientY - b.top }; });
        hero.addEventListener('mouseleave', () => { mouse = { x: -999, y: -999 }; });
        init(); loop();
        window.addEventListener('resize', init);
    }

});
