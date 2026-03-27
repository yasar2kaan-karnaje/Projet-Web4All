<?php

namespace App\Controller;

use App\Model\Offre;

class HomeController extends BaseController
{
    /**
     * Page d'accueil (dashboard)
     */
    public function index(): void
    {
        $offres = [];
        try {
            $offres = Offre::findLatest(3);
        } catch (\Exception $e) {
            // La base de données n'est peut-être pas encore configurée
        }

        $this->render('home/dashboard.html.twig', [
            'latest_offres' => $offres,
        ]);
    }
}
