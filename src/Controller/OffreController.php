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
            if (isset($_FILES['cv']) && $_FILES['cv']['size'] > 0) {
                if ($_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
                    throw new \Exception("Erreur upload CV (Code: " . $_FILES['cv']['error'] . "). Fichier peut-être trop volumineux.");
                }
                $uploadDir = __DIR__ . '/../../uploads/cv/';
                if (!is_dir($uploadDir)) {
                    if (!@mkdir($uploadDir, 0777, true)) {
                        throw new \Exception("Dossier upload CV inaccessible. Problème de droits sur le serveur.");
                    }
                }
                $filename = uniqid() . '_' . basename($_FILES['cv']['name']);
                if (move_uploaded_file($_FILES['cv']['tmp_name'], $uploadDir . $filename)) {
                    chmod($uploadDir . $filename, 0644);
                    $cvPath = '/uploads/cv/' . $filename;
                } else {
                    throw new \Exception("Enregistrement du CV refusé par le serveur. Permissions bloquées.");
                }
            }

            // Upload de la Lettre de motivation (LM)
            $lmPath = null;
            if (isset($_FILES['lettre_motivation']) && $_FILES['lettre_motivation']['size'] > 0) {
                if ($_FILES['lettre_motivation']['error'] !== UPLOAD_ERR_OK) {
                    throw new \Exception("Erreur upload LM (Code: " . $_FILES['lettre_motivation']['error'] . "). Fichier peut-être trop volumineux.");
                }
                $uploadDirLm = __DIR__ . '/../../uploads/lm/';
                if (!is_dir($uploadDirLm)) {
                    if (!@mkdir($uploadDirLm, 0777, true)) {
                        throw new \Exception("Dossier upload LM inaccessible. Problème de droits sur le serveur.");
                    }
                }
                $filenameLm = uniqid() . '_' . basename($_FILES['lettre_motivation']['name']);
                if (move_uploaded_file($_FILES['lettre_motivation']['tmp_name'], $uploadDirLm . $filenameLm)) {
                    chmod($uploadDirLm . $filenameLm, 0644);
                    $lmPath = '/uploads/lm/' . $filenameLm;
                } else {
                    throw new \Exception("Enregistrement de la LM refusé par le serveur. Permissions bloquées.");
                }
            }

            Candidature::create([
                'user_id' => $userId,
                'offre_id' => $offreId,
                'cv_path' => $cvPath,
                'lettre_motivation' => $lmPath ?? $this->postParam('lettre_motivation', ''),
            ]);

            $this->redirect('/etudiant/candidatures?success=Candidature envoyée avec succès');
        } catch (\Exception $e) {
            $this->redirect('/offre/' . $id . '?error=' . urlencode($e->getMessage()));
        }
    }
}
