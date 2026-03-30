<?php

namespace App\Controller;

use App\Model\User;
use App\Model\Offre;
use App\Model\Entreprise;

class AdminController extends BaseController
{
    /**
     * Tableau de bord admin
     */
    public function dashboard(): void
    {
        $this->requireRole('admin', 'pilote');

        try {
            $stats = [
                'etudiants' => User::countByRole('etudiant'),
                'pilotes' => User::countByRole('pilote'),
                'entreprises' => Entreprise::count(),
                'offres' => Offre::count(),
            ];
        } catch (\Exception $e) {
            $stats = ['etudiants' => 0, 'pilotes' => 0, 'entreprises' => 0, 'offres' => 0];
        }

        $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
        ]);
    }

    /**
     * Gestion des offres (admin/pilote)
     */
    public function offres(): void
    {
        $this->requireRole('admin', 'pilote');

        $page = (int) $this->getParam('page', 1);
        $search = $this->getParam('q', '');

        try {
            $result = Offre::findAll($page, 10, $search);
            $stats = Offre::getStats();
        } catch (\Exception $e) {
            $result = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
            $stats = ['total' => 0, 'avg_candidatures' => 0];
        }

        $this->render('admin/offres.html.twig', [
            'offres' => $result['data'],
            'pagination' => $result,
            'search' => $search,
            'stats' => $stats,
        ]);
    }

    /**
     * Gestion des entreprises (admin/pilote)
     */
    public function entreprises(): void
    {
        $this->requireRole('admin', 'pilote');

        $page = (int) $this->getParam('page', 1);
        $search = $this->getParam('q', '');

        try {
            $result = Entreprise::findAll($page, 10, $search);
        } catch (\Exception $e) {
            $result = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }

        $this->render('admin/entreprises.html.twig', [
            'entreprises' => $result['data'],
            'pagination' => $result,
            'search' => $search,
        ]);
    }

    /**
     * Gestion des étudiants (admin/pilote)
     */
    public function etudiants(): void
    {
        $this->requireRole('admin', 'pilote');

        $page = (int) $this->getParam('page', 1);
        $search = $this->getParam('q', '');

        try {
            // Pilote : voit uniquement ses promotions/centre (étanchéité)
            if ($_SESSION['user']['role'] === 'pilote') {
                $result = User::findStudentsByPilote($_SESSION['user']['id'], $page, 10, $search);
            } else {
                $result = User::findByRole('etudiant', $page, 10, $search);
            }
        } catch (\Exception $e) {
            $result = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }

        $this->render('admin/etudiants.html.twig', [
            'etudiants' => $result['data'],
            'pagination' => $result,
            'search' => $search,
        ]);
    }

    /**
     * Gestion des pilotes (admin uniquement)
     */
    public function pilotes(): void
    {
        $this->requireRole('admin');

        $page = (int) $this->getParam('page', 1);
        $search = $this->getParam('q', '');

        try {
            $result = User::findByRole('pilote', $page, 10, $search);
        } catch (\Exception $e) {
            $result = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }

        $this->render('admin/pilotes.html.twig', [
            'pilotes' => $result['data'],
            'pagination' => $result,
            'search' => $search,
        ]);
    }
    // ===================================
    // CRUD ENTREPRISES
    // ===================================
    public function entrepriseForm(string $id = null): void
    {
        $this->requireRole('admin', 'pilote');
        $entreprise = $id ? Entreprise::findById((int) $id) : null;
        $this->render('admin/entreprise_form.html.twig', ['entreprise' => $entreprise]);
    }

    public function entrepriseCreate(): void
    {
        $this->requireRole('admin', 'pilote');
        $data = $this->sanitizePostData($_POST);
        Entreprise::create($data);
        $this->redirect('/admin/entreprises?success=Entreprise créée');
    }

    public function entrepriseUpdate(string $id): void
    {
        $this->requireRole('admin', 'pilote');
        $data = $this->sanitizePostData($_POST);
        Entreprise::update((int) $id, $data);
        $this->redirect('/admin/entreprises?success=Entreprise modifiée');
    }

    public function entrepriseDelete(string $id): void
    {
        $this->requireRole('admin', 'pilote');
        Entreprise::delete((int) $id);
        $this->redirect('/admin/entreprises?success=Entreprise supprimée');
    }

    // ===================================
    // CRUD OFFRES
    // ===================================
    public function offreForm(string $id = null): void
    {
        $this->requireRole('admin', 'pilote');
        $offre = $id ? Offre::findById((int) $id) : null;
        // On récupère toutes les entreprises pour le select du formulaire
        $entreprisesResult = Entreprise::findAll(1, 1000); 
        $this->render('admin/offre_form.html.twig', [
            'offre' => $offre,
            'entreprises' => $entreprisesResult['data']
        ]);
    }

    public function offreCreate(): void
    {
        $this->requireRole('admin', 'pilote');
        $data = $this->sanitizePostData($_POST);
        Offre::create($data);
        $this->redirect('/admin/offres?success=Offre créée');
    }

    public function offreUpdate(string $id): void
    {
        $this->requireRole('admin', 'pilote');
        $data = $this->sanitizePostData($_POST);
        Offre::update((int) $id, $data);
        $this->redirect('/admin/offres?success=Offre modifiée');
    }

    public function offreDelete(string $id): void
    {
        $this->requireRole('admin', 'pilote');
        Offre::delete((int) $id);
        $this->redirect('/admin/offres?success=Offre supprimée');
    }

    // ===================================
    // CRUD ÉTUDIANTS
    // ===================================
    public function etudiantForm(string $id = null): void
    {
        $this->requireRole('admin', 'pilote');
        $etudiant = $id ? User::findById((int) $id) : null;
        
        $piloteCentre = $_SESSION['user']['role'] === 'pilote' ? $_SESSION['user']['centre'] : null;
        $allCentres = User::getAllCentres();
        $allPromotions = User::getAllPromotions($piloteCentre);

        $this->render('admin/user_form.html.twig', [
            'user' => $etudiant,
            'type' => 'etudiant',
            'all_centres' => $allCentres,
            'all_promotions' => $allPromotions
        ]);
    }

    public function etudiantCreate(): void
    {
        $this->requireRole('admin', 'pilote');
        $data = $this->sanitizePostData($_POST);
        $data['role'] = 'etudiant';
        $data['updated_by'] = $_SESSION['user']['id'];
        
        // Auto-assign centre if pilot
        if ($_SESSION['user']['role'] === 'pilote' && !empty($_SESSION['user']['centre'])) {
            $data['centre'] = $_SESSION['user']['centre'];
        }
        
        User::create($data);
        $this->redirect('/admin/etudiants?success=Étudiant créé');
    }

    public function etudiantUpdate(string $id): void
    {
        $this->requireRole('admin', 'pilote');
        $data = $this->sanitizePostData($_POST);
        $data['updated_by'] = $_SESSION['user']['id'];
        User::update((int) $id, $data);
        $this->redirect('/admin/etudiants?success=Étudiant modifié');
    }

    public function etudiantDelete(string $id): void
    {
        $this->requireRole('admin', 'pilote');
        User::delete((int) $id);
        $this->redirect('/admin/etudiants?success=Étudiant supprimé');
    }

    /**
     * Détail d'un étudiant (wishlist + candidatures)
     */
    public function etudiantDetail(string $id): void
    {
        $this->requireRole('admin', 'pilote');

        $etudiant = User::findById((int) $id);
        if (!$etudiant || $etudiant['role'] !== 'etudiant') {
            http_response_code(404);
            echo '<h1>Étudiant non trouvé</h1><a href="/admin/etudiants">Retour</a>';
            return;
        }

        try {
            $candidatures = \App\Model\Candidature::findByUser((int) $id, 1, 100);
            $wishlist = \App\Model\Wishlist::findByUser((int) $id, 1, 100);
        } catch (\Exception $e) {
            $candidatures = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
            $wishlist = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }

        $this->render('admin/etudiant_detail.html.twig', [
            'etudiant' => $etudiant,
            'candidatures' => $candidatures['data'],
            'wishlist' => $wishlist['data'],
        ]);
    }

    // ===================================
    // CRUD PILOTES
    // ===================================
    public function piloteForm(string $id = null): void
    {
        $this->requireRole('admin');
        $pilote = $id ? User::findById((int) $id) : null;

        // Données pour le formulaire enrichi
        $allPromotions = User::getAllPromotions();
        $allCentres = User::getAllCentres();
        $entreprisesResult = Entreprise::findAll(1, 1000);

        $this->render('admin/pilote_form.html.twig', [
            'user' => $pilote,
            'all_promotions' => $allPromotions,
            'all_centres' => $allCentres,
            'entreprises' => $entreprisesResult['data'],
        ]);
    }

    public function piloteCreate(): void
    {
        $this->requireRole('admin');
        $data = $this->sanitizePostData($_POST);
        $data['role'] = 'pilote';
        $data['is_recruteur'] = isset($data['is_recruteur']) ? 1 : 0;
        $data['updated_by'] = $_SESSION['user']['id'];

        // Traiter les promotions (checkboxes qui envoient des IDs)
        $promotions = $data['promotions'] ?? [];
        $data['promotions'] = $promotions;

        User::create($data);
        $this->redirect('/admin/pilotes?success=Pilote créé');
    }

    public function piloteUpdate(string $id): void
    {
        $this->requireRole('admin');
        $data = $this->sanitizePostData($_POST);
        $data['role'] = 'pilote';
        $data['is_recruteur'] = isset($data['is_recruteur']) ? 1 : 0;
        $data['updated_by'] = $_SESSION['user']['id'];

        // Traiter les promotions
        $promotions = $data['promotions'] ?? [];
        $data['promotions'] = $promotions;

        User::update((int) $id, $data);
        $this->redirect('/admin/pilotes?success=Pilote modifié');
    }

    public function piloteDelete(string $id): void
    {
        $this->requireRole('admin');
        User::delete((int) $id);
        $this->redirect('/admin/pilotes?success=Pilote supprimé');
    }

    // ===================================
    // GESTION DES PROMOTIONS (REFERENTIEL)
    // ===================================
    public function promotions(): void
    {
        $this->requireRole('admin', 'pilote');
        
        $promotions = User::getAllPromotions($_SESSION['user']['role'] === 'pilote' ? $_SESSION['user']['centre'] : null);
        $centres = User::getAllCentres();
        
        $this->render('admin/promotions.html.twig', [
            'promotions' => $promotions,
            'centres' => $centres
        ]);
    }

    public function promotionCreate(): void
    {
        $this->requireRole('admin', 'pilote');
        
        $nom = htmlspecialchars(trim($_POST['nom'] ?? ''), ENT_QUOTES, 'UTF-8');
        $centreId = (int)($_POST['centre_id'] ?? 0);
        $role = $_SESSION['user']['role'];
        $centreNom = $_SESSION['user']['centre'] ?? null;

        User::createPromotion($nom, $centreId, $_SESSION['user']['id'], $role, $centreNom);

        $this->redirect('/admin/promotions?success=Promotion créée');
    }

    public function centreCreate(): void
    {
        $this->requireRole('admin');
        $nom = htmlspecialchars(trim($_POST['nom'] ?? ''), ENT_QUOTES, 'UTF-8');
        User::createCentre($nom);
        $this->redirect('/admin/promotions?success=Centre créé');
    }

    /**
     * Sanitise rÃ©cursivement un tableau de donnÃ©es issues de $_POST.
     */
    private function sanitizePostData(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizePostData($value);
            } else {
                $sanitized[$key] = htmlspecialchars(trim((string)$value), ENT_QUOTES, 'UTF-8');
            }
        }
        return $sanitized;
    }
}
