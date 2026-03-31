document.addEventListener('DOMContentLoaded', function() {
    // Validation JS globale pour tous les formulaires
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                // Trouver le premier champ invalide et lui donner le focus
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.classList.add('input-error');
                }
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Validation JS spécifique pour le formulaire de candidature
    const cvForm = document.querySelector('form[action*="/postuler"]');

    // Formatage global : Nom en MAJUSCULES, Prénom avec 1ère lettre en Majuscule, Lettres uniquement
    document.querySelectorAll('input[name="nom"], input[id="nom"], input[id="nom_entreprise"]').forEach(input => {
        input.addEventListener('input', function() {
            // Supprime tout ce qui n'est pas : une lettre (y compris accents), un espace, un tiret ou une apostrophe
            this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s'-]/g, '');
            // Mise en majuscules
            this.value = this.value.toUpperCase();
        });
    });

    document.querySelectorAll('input[name="prenom"], input[id="prenom"]').forEach(input => {
        input.addEventListener('input', function() {
            // Supprime tout ce qui n'est pas : une lettre (y compris accents), un espace, un tiret ou une apostrophe
            this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s'-]/g, '');
            // Remplace la première lettre et les lettres après un espace ou un tiret par une majuscule
            this.value = this.value.replace(/(?:^|\s|-)\S/g, function(lettre) { 
                return lettre.toUpperCase(); 
            });
        });
    });

    if (cvForm) {
        const inputNom = document.getElementById('nom');
        const inputPrenom = document.getElementById('prenom');
        const inputCourriel = document.getElementById('courriel');
        const inputCv = document.getElementById('cv');
        const inputMessage = document.getElementById('message_recruteur');

        cvForm.addEventListener('submit', function(e) {
            let isCvValid = true;
            
            const showError = (input, show) => {
                if (!input) return;
                const errorSpan = document.getElementById('error-' + input.id);
                if (errorSpan) {
                    errorSpan.style.display = show ? 'block' : 'none';
                }
                if (show) isCvValid = false;
            };

            const validateRequired = (input) => {
                if (input && input.value.trim() === '') {
                    showError(input, true);
                } else {
                    showError(input, false);
                }
            };

            // Vérifications
            validateRequired(inputNom);
            validateRequired(inputPrenom);
            validateRequired(inputMessage);

            if (inputCourriel) {
                // Regex basique pour email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(inputCourriel.value.trim())) {
                    showError(inputCourriel, true);
                } else {
                    showError(inputCourriel, false);
                }
            }

            if (inputCv && inputCv.files.length > 0) {
                const file = inputCv.files[0];
                const validExtensions = ['pdf', 'doc', 'docx', 'odt', 'rtf', 'jpg', 'png'];
                const fileExtension = file.name.split('.').pop().toLowerCase();
                const maxSize = 2 * 1024 * 1024; // 2 Mo en octets

                if (!validExtensions.includes(fileExtension) || file.size > maxSize) {
                    showError(inputCv, true);
                    // On empêche le reset global d'erreur HTML5 si extension custom
                } else {
                    showError(inputCv, false);
                }
            } else if (inputCv && inputCv.files.length === 0) {
                showError(inputCv, true);
            }

            if (!isCvValid) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        }, false);
    }
});
