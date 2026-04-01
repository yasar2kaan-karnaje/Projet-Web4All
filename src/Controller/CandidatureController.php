<?php

namespace App\Controller;

use App\Model\Candidature;

class CandidatureController extends BaseController
{
    /**
     * Mes candidatures (étudiant)
     */
    public function mesCandidatures(): void
    {
        $this->requireRole('etudiant');

        $page = (int) $this->getParam('page', 1);

        try {
            $result = Candidature::findByUser($_SESSION['user']['id'], $page, 10);
        } catch (\Exception $e) {
            $result = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }

        $this->render('etudiant/candidatures.html.twig', [
            'candidatures' => $result['data'],
            'pagination' => $result,
            'success' => $this->getParam('success'),
        ]);
    }

    /**
     * Candidatures des élèves (pilote)
     */
    public function candidaturesPilote(): void
    {
        $this->requireRole('pilote');

        $page = (int) $this->getParam('page', 1);
        $search = $this->getParam('q', '');
        $statut = $this->getParam('statut', '');

        try {
            $result = Candidature::findByPilote($_SESSION['user']['id'], $page, 10, $search, $statut);
            $stats = Candidature::getStatsPilote($_SESSION['user']['id']);
        } catch (\Exception $e) {
            $result = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
            $stats = ['nb_etudiants' => 0, 'total_candidatures' => 0, 'en_attente' => 0, 'acceptees' => 0];
        }

        $this->render('pilote/candidatures.html.twig', [
            'candidatures' => $result['data'],
            'pagination' => $result,
            'stats' => $stats,
            'search' => $search,
            'statut' => $statut,
        ]);
    }

    /**
     * Télécharger un document (CV ou LM)
     * Protège et sert le fichier via PHP pour éviter les erreurs Forbidden d'Apache
     */
    public function telechargerDocument(): void
    {
        $this->requireLogin();
        $file = $this->getParam('f');
        if (!$file) {
            http_response_code(400);
            die("Fichier non spécifié.");
        }

        // Sécurité de base: interdir l'accès hors dossier public/uploads
        if (strpos($file, '/uploads/') !== 0 || strpos($file, '..') !== false) {
            http_response_code(403);
            die("Accès refusé.");
        }

        $absolutePath = __DIR__ . '/../..' . $file;
        if (!file_exists($absolutePath) || !is_file($absolutePath)) {
            http_response_code(404);
            die("Fichier introuvable.");
        }

        $mimeType = mime_content_type($absolutePath) ?: 'application/octet-stream';
        
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . basename($absolutePath) . '"');
        header('Content-Length: ' . filesize($absolutePath));
        readfile($absolutePath);
        exit;
    }
}
