<?php

namespace App\Controller;

use App\Model\Offre;
use App\Model\Candidature;
use App\Model\Wishlist;

class OffreController extends BaseController
{
    /**
     * Catalogue des offres (accessible à tous)
     */
    public function catalogue(): void
    {
        $page = (int) $this->getParam('page', 1);
        $search = $this->getParam('q', '');

        try {
            $result = Offre::findAll($page, 9, $search);
            $stats = Offre::getStats();
        } catch (\Exception $e) {
            $result = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
            $stats = ['total' => 0, 'avg_candidatures' => 0];
        }

        // Récupérer la wishlist si connecté
        $wishlistIds = [];
        if (isset($_SESSION['user'])) {
            try {
                $wishlistIds = Wishlist::getOffreIds($_SESSION['user']['id']);
            } catch (\Exception $e) {}
        }

        $this->render('offre/catalogue.html.twig', [
            'offres' => $result['data'],
            'pagination' => $result,
            'search' => $search,
            'stats' => $stats,
            'wishlist_ids' => $wishlistIds,
        ]);
    }

    /**
     * Détail d'une offre
     */
    public function detail(string $id): void
    {
        try {
            $offre = Offre::findById((int) $id);
        } catch (\Exception $e) {
            $offre = null;
        }

        if (!$offre) {
            http_response_code(404);
            echo '<h1>Offre non trouvée</h1><a href="/offres">Retour aux offres</a>';
            return;
        }

        $hasApplied = false;
        $inWishlist = false;
        if (isset($_SESSION['user'])) {
            try {
                $hasApplied = Candidature::hasApplied($_SESSION['user']['id'], (int) $id);
                $inWishlist = Wishlist::exists($_SESSION['user']['id'], (int) $id);
            } catch (\Exception $e) {}
        }

        $this->render('offre/detail.html.twig', [
            'offre' => $offre,
            'has_applied' => $hasApplied,
            'in_wishlist' => $inWishlist,
        ]);
    }

    /**
     * Postuler à une offre (POST)
     */
    public function postuler(string $id): void
    {
        $this->requireRole('etudiant');

        $userId = $_SESSION['user']['id'];
        $offreId = (int) $id;

        try {
            if (Candidature::hasApplied($userId, $offreId)) {
                $this->redirect('/offre/' . $id . '?error=Vous avez déjà postulé');
                return;
            }

            // Upload du CV
            $cvPath = null;
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/cv/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $filename = uniqid() . '_' . basename($_FILES['cv']['name']);
                move_uploaded_file($_FILES['cv']['tmp_name'], $uploadDir . $filename);
                $cvPath = '/uploads/cv/' . $filename;
            }

            Candidature::create([
                'user_id' => $userId,
                'offre_id' => $offreId,
                'cv_path' => $cvPath,
                'lettre_motivation' => $this->postParam('lettre_motivation', ''),
            ]);

            $this->redirect('/etudiant/candidatures?success=Candidature envoyée avec succès');
        } catch (\Exception $e) {
            $this->redirect('/offre/' . $id . '?error=Erreur lors de l\'envoi');
        }
    }
}
