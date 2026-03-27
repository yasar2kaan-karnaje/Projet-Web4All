<?php

namespace App\Controller;

use App\Model\User;
use App\Model\Candidature;

class ProfileController extends BaseController
{
    /**
     * Affiche le profil de l'utilisateur connecté
     */
    public function index(): void
    {
        $this->requireLogin();

        $userId = $_SESSION['user']['id'];
        $user = User::findById($userId);

        if (!$user) {
            $this->redirect('/');
            return;
        }

        // Statistiques selon le rôle
        $stats = [];

        if ($user['role'] === 'etudiant') {
            $candidatures = Candidature::findByUser($userId);
            $stats['total_candidatures'] = count($candidatures['data'] ?? []);
            $stats['en_attente'] = 0;
            $stats['acceptees'] = 0;
            $stats['refusees'] = 0;

            foreach ($candidatures['data'] ?? [] as $c) {
                if (($c['statut'] ?? '') === 'en_attente') $stats['en_attente']++;
                elseif (($c['statut'] ?? '') === 'acceptee') $stats['acceptees']++;
                elseif (($c['statut'] ?? '') === 'refusee') $stats['refusees']++;
            }
        } elseif ($user['role'] === 'pilote') {
            $stats['etudiants'] = User::countByRole('etudiant');
        } elseif ($user['role'] === 'admin') {
            $stats['etudiants'] = User::countByRole('etudiant');
            $stats['pilotes'] = User::countByRole('pilote');
        }

        $this->render('profil/index.html.twig', [
            'active_page' => 'profil',
            'profil' => $user,
            'stats' => $stats,
        ]);
    }
}
