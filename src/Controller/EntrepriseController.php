<?php

namespace App\Controller;

class EntrepriseController extends BaseController
{
    public function liste()
    {
        $entreprises = [
            ['id' => 1, 'nom' => 'TechCorp', 'secteur' => 'IT', 'localisation' => 'Lyon', 'nb_offres' => 5, 'note_moyenne' => 4.2],
            ['id' => 2, 'nom' => 'DataSoft', 'secteur' => 'Data', 'localisation' => 'Paris', 'nb_offres' => 3, 'note_moyenne' => 3.8],
        ];

        $this->render('entreprise/liste.html.twig', [
            'entreprises' => $entreprises,
            'total' => count($entreprises),
            'pages' => 1,
            'current_page' => 1,
            'active_page' => 'entreprises',
        ]);
    }

    public function detail(string $id)
    {
        $entreprise = [
            'id' => $id,
            'nom' => 'TechCorp',
            'secteur' => 'IT / Développement Web',
            'localisation' => 'Lyon',
            'description' => 'Entreprise spécialisée dans le développement web.',
            'nb_offres' => 5,
            'note_moyenne' => 4.2,
            'nb_evaluations' => 10,
        ];

        $this->render('entreprise/detail.html.twig', [
            'entreprise' => $entreprise,
            'offres' => [],
            'evaluations' => [],
            'active_page' => 'entreprises',
        ]);
    }

    public function evaluer(string $id)
    {
        $this->requireLogin();
        // TODO: Phase 4
        $this->redirect('/entreprise/' . $id);
    }
}
