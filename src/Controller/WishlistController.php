<?php

namespace App\Controller;

use App\Model\Wishlist;

class WishlistController extends BaseController
{
    /**
     * Affiche la wishlist
     */
    public function index(): void
    {
        $this->requireRole('etudiant');

        $page = (int) $this->getParam('page', 1);

        try {
            $result = Wishlist::findByUser($_SESSION['user']['id'], $page, 9);
        } catch (\Exception $e) {
            $result = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }

        $this->render('etudiant/wishlist.html.twig', [
            'wishlist' => $result['data'],
            'pagination' => $result,
        ]);
    }

    /**
     * Ajouter à la wishlist (POST)
     */
    public function add(): void
    {
        $this->requireRole('etudiant');

        $offreId = (int) $this->postParam('offre_id', 0);
        $redirect = $this->postParam('redirect', '/offres');

        if ($offreId > 0) {
            try {
                Wishlist::add($_SESSION['user']['id'], $offreId);
            } catch (\Exception $e) {}
        }

        $this->redirect($redirect);
    }

    /**
     * Retirer de la wishlist (POST)
     */
    public function remove(): void
    {
        $this->requireRole('etudiant');

        $offreId = (int) $this->postParam('offre_id', 0);
        $redirect = $this->postParam('redirect', '/etudiant/wishlist');

        if ($offreId > 0) {
            try {
                Wishlist::remove($_SESSION['user']['id'], $offreId);
            } catch (\Exception $e) {}
        }

        $this->redirect($redirect);
    }
}
