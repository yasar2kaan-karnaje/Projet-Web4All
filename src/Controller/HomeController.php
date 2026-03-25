<?php

namespace App\Controller;

class HomeController extends BaseController
{
    /**
     * Page d'accueil (dashboard)
     */
    public function index(): void
    {
        // Données simulées pour la Phase 3
        $stats = [
            'nb_offres' => 42,
            'nb_entreprises' => 15,
            'nb_etudiants' => 120,
        ];

        $dernieres_offres = [
            ['id' => 1, 'titre' => 'Stage Développeur Web', 'entreprise_nom' => 'TechCorp', 'lieu' => 'Lyon'],
            ['id' => 2, 'titre' => 'Stage Data Analyst', 'entreprise_nom' => 'DataSoft', 'lieu' => 'Paris'],
            ['id' => 3, 'titre' => 'Stage DevOps', 'entreprise_nom' => 'CloudIO', 'lieu' => 'Bordeaux'],
        ];

        $this->render('home/dashboard.html.twig', [
            'stats' => $stats,
            'latest_offres' => $dernieres_offres,
            'active_page' => 'home',
        ]);
    }
}
