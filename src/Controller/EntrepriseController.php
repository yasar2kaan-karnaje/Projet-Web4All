<?php

namespace App\Controller;

use App\Model\Entreprise;
use App\Model\Offre;

class EntrepriseController extends BaseController
{
    /**
     * Liste des entreprises (accessible à tous)
     */
    public function liste(): void
    {
        $page = (int) $this->getParam('page', 1);
        $search = $this->getParam('q', '');
        $secteur = $this->getParam('secteur', '');

        try {
            $result = Entreprise::findAll($page, 12, $search, $secteur);
        } catch (\Exception $e) {
            $result = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }

        $this->render('entreprise/liste.html.twig', [
            'entreprises' => $result['data'],
            'pagination' => $result,
            'search' => $search,
            'secteur' => $secteur,
        ]);
    }

    /**
     * Détail d'une entreprise
     */
    public function detail(string $id): void
    {
        try {
            $entreprise = Entreprise::findById((int) $id);
            $offres = Offre::findByEntreprise((int) $id);
            $evaluations = Entreprise::getEvaluations((int) $id);
        } catch (\Exception $e) {
            $entreprise = null;
            $offres = [];
            $evaluations = [];
        }

        if (!$entreprise) {
            http_response_code(404);
            echo '<h1>Entreprise non trouvée</h1><a href="/entreprises">Retour aux entreprises</a>';
            return;
        }

        $this->render('entreprise/detail.html.twig', [
            'entreprise' => $entreprise,
            'offres' => $offres,
            'evaluations' => $evaluations,
        ]);
    }

    /**
     * Évaluer une entreprise (POST)
     */
    public function evaluer(string $id): void
    {
        $this->requireRole('pilote', 'admin');

        $note = (int) $this->postParam('note', 3);
        $note = max(1, min(5, $note)); // Assure que la note est entre 1 et 5
        $commentaire = htmlspecialchars(trim((string)$this->postParam('commentaire', '')), ENT_QUOTES, 'UTF-8');

        try {
            \App\Model\Entreprise::addEvaluation($_SESSION['user']['id'], (int) $id, $note, $commentaire);
        } catch (\Exception $e) {
            // Erreur silencieuse
        }

        $this->redirect('/entreprise/' . $id . '?success=Évaluation enregistrée');
    }
}
