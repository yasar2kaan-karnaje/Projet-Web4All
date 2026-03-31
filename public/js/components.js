document.addEventListener('DOMContentLoaded', function() {

    // 1. CARROUSEL DES OFFRES (catalogue.html.twig)
    const pisteCarrousel = document.getElementById('carousel-track');
    if (pisteCarrousel) {
        const diapositives = pisteCarrousel.querySelectorAll('.carousel-slide');
        const boutonPrecedent = document.getElementById('carousel-prev');
        const boutonSuivant = document.getElementById('carousel-next');
        const points = document.querySelectorAll('.carousel-dot');
        let indexActuel = 0;
        const totalDiapositives = diapositives.length;
        let minuteurAutomatique;

        function allerA(index) {
            if (index < 0) index = totalDiapositives - 1;
            if (index >= totalDiapositives) index = 0;
            indexActuel = index;
            pisteCarrousel.style.transform = 'translateX(-' + (indexActuel * 100) + '%)';
            points.forEach(function(point, i) {
                point.classList.toggle('active', i === indexActuel);
            });
        }

        if (boutonPrecedent) boutonPrecedent.addEventListener('click', function() { allerA(indexActuel - 1); relancerMinuteur(); });
        if (boutonSuivant) boutonSuivant.addEventListener('click', function() { allerA(indexActuel + 1); relancerMinuteur(); });
        points.forEach(function(point) {
            point.addEventListener('click', function() { allerA(parseInt(this.dataset.index)); relancerMinuteur(); });
        });

        function relancerMinuteur() {
            clearInterval(minuteurAutomatique);
            minuteurAutomatique = setInterval(function() { allerA(indexActuel + 1); }, 5000); // Défilement toutes les 5 secondes
        }
        relancerMinuteur();
    }

    // 2. MODALE LETTRE DE MOTIVATION (candidatures.html.twig)
    const boiteModale = document.getElementById('lm-modal');
    if (boiteModale) {
        const corpsModale = document.getElementById('lm-modal-body');
        const boutonFermerModale = document.getElementById('modal-close');

        document.querySelectorAll('.lm-toggle').forEach(function(bouton) {
            bouton.addEventListener('click', function() {
                var identifiantLm = this.dataset.lmId;
                var donneesLettre = document.getElementById(identifiantLm);
                if (donneesLettre) {
                    corpsModale.textContent = donneesLettre.textContent;
                    boiteModale.classList.add('show');
                }
            });
        });

        if (boutonFermerModale) {
            boutonFermerModale.addEventListener('click', function() { boiteModale.classList.remove('show'); });
        }
        boiteModale.addEventListener('click', function(evenement) {
            if (evenement.target === boiteModale) boiteModale.classList.remove('show');
        });
    }

    // 3. FORMULAIRES ADMIN : Filtre des promotions et mode recruteur (user_form, pilote_form)
    const selectionCentre = document.getElementById('centre');
    if (selectionCentre) {
        selectionCentre.addEventListener('change', function(evenement) {
            const nomCentreChoisi = this.value;
            
            // Pour la liste déroulante classique (Édition Étudiant)
            const selectionPromotion = document.getElementById('promotion');
            if (selectionPromotion) {
                const optionsPromo = selectionPromotion.options;
                for (let i = 1; i < optionsPromo.length; i++) {
                    if (nomCentreChoisi === '' || optionsPromo[i].getAttribute('data-centre') === nomCentreChoisi) {
                        optionsPromo[i].style.display = 'block';
                    } else {
                        optionsPromo[i].style.display = 'none';
                    }
                }
                // Réinitialise la promotion uniquement si l'utilisateur a changé le centre manuellement (pas au chargement)
                if (evenement.isTrusted) {
                    selectionPromotion.value = '';
                }
            }

            // Pour les cases à cocher (Édition Pilote)
            const etiquettesPromotions = document.querySelectorAll('.promo-label');
            etiquettesPromotions.forEach(etiquette => {
                if (nomCentreChoisi === '' || etiquette.getAttribute('data-centre') === nomCentreChoisi) {
                    etiquette.style.display = 'flex';
                } else {
                    etiquette.style.display = 'none';
                }
            });
        });

        // Déclencher le filtre automatiquement au chargement de la page
        selectionCentre.dispatchEvent(new Event('change'));
    }

    const caseRecruteur = document.getElementById('toggle-recruteur');
    if (caseRecruteur) {
        caseRecruteur.addEventListener('change', function() {
            const champsEntreprise = document.getElementById('recruteur-fields');
            if (champsEntreprise) {
                champsEntreprise.style.display = this.checked ? 'block' : 'none';
            }
        });
    }

    // 4. AFFICHER/MASQUER LE MOT DE PASSE (login, user_form, pilote_form)
    const boutonsMotDePasse = document.querySelectorAll('.toggle-password-btn');
    boutonsMotDePasse.forEach(bouton => {
        bouton.addEventListener('click', function() {
            const blocParent = this.parentElement;
            const champSaisie = blocParent.querySelector('input');
            const imageIcone = this.querySelector('img');

            if (champSaisie && imageIcone) {
                if (champSaisie.type === 'password') {
                    champSaisie.type = 'text';
                    imageIcone.src = '/logo/cadenas.png';
                } else {
                    champSaisie.type = 'password';
                    imageIcone.src = '/logo/oeil.png';
                }
            }
        });
    });

});
