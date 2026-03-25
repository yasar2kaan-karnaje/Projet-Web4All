<?php

namespace App\Controller;

class OffreController extends BaseController
{
    public function catalogue()
    {
        // Données simulées (Phase 3 - pas encore de BDD)
        $offres = [
            ['id' => 1, 'titre' => 'Stage Développeur Web', 'entreprise_nom' => 'TechCorp', 'lieu' => 'Lyon', 'duree' => '6 mois', 'competences' => 'PHP, JavaScript'],
            ['id' => 2, 'titre' => 'Stage Data Analyst', 'entreprise_nom' => 'DataSoft', 'lieu' => 'Paris', 'duree' => '4 mois', 'competences' => 'Python, SQL'],
        ];

        $this->render('offre/catalogue.html.twig', [
            'offres' => $offres,
            'total' => count($offres),
            'pages' => 1,
            'current_page' => 1,
            'active_page' => 'offres',
        ]);
    }

    public function detail(string $id)
    {
        $offre = [
            'id' => $id,
            'titre' => 'Stage Développeur Web',
            'description' => 'Rejoignez notre équipe pour un stage passionnant.',
            'competences' => 'PHP, JavaScript, MySQL',
            'remuneration' => '600€/mois',
            'duree' => '6 mois',
            'lieu' => 'Lyon',
            'entreprise_nom' => 'TechCorp',
            'nb_candidatures' => 5,
        ];

        $this->render('offre/detail.html.twig', [
            'offre' => $offre,
            'active_page' => 'offres',
        ]);
    }

    public function postuler(string $id)
    {
        $this->requireLogin();
        // TODO: Phase 4 - Enregistrer la candidature en BDD
        $this->redirect('/offre/' . $id);
    }
}
