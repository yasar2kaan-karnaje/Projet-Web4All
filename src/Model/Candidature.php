<?php

namespace App\Model;

use PDO;

class Candidature
{
    /**
     * Récupère les candidatures d'un étudiant
     */
    public static function findByUser(int $userId, int $page = 1, int $perPage = 10): array
    {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        $stmtCount = $db->prepare('SELECT COUNT(*) FROM candidatures WHERE user_id = :user_id');
        $stmtCount->execute(['user_id' => $userId]);
        $total = (int) $stmtCount->fetchColumn();

        $stmt = $db->prepare("
            SELECT c.*, o.titre AS offre_titre, e.nom AS entreprise_nom
            FROM candidatures c
            LEFT JOIN offres o ON c.offre_id = o.id
            LEFT JOIN entreprises e ON o.entreprise_id = e.id
            WHERE c.user_id = :user_id
            ORDER BY c.date_candidature DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
            'current_page' => $page,
        ];
    }

    /**
     * Récupère les candidatures des élèves d'un pilote (même centre/promotion)
     */
    public static function findByPilote(int $piloteId, int $page = 1, int $perPage = 10, string $search = '', string $statut = ''): array
    {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        // Récupérer le pilote pour son centre et ses promotions
        $pilote = User::findById($piloteId);
        if (!$pilote) {
            return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }

        $promotions = User::getPilotePromotions($piloteId);

        $where = "WHERE r_role.nom = 'etudiant'";
        $params = [];

        // Filtrage par centre
        if ($pilote['centre']) {
            $where .= ' AND u.centre = :centre';
            $params['centre'] = $pilote['centre'];
        }

        // Filtrage par les promotions du pilote (étanchéité)
        if (!empty($promotions)) {
            $placeholders = [];
            foreach ($promotions as $i => $promo) {
                $key = 'promo_' . $i;
                $placeholders[] = ':' . $key;
                $params[$key] = $promo;
            }
            $where .= ' AND et.promotion IN (' . implode(', ', $placeholders) . ')';
        }

        if ($search !== '') {
            $where .= ' AND (u.nom LIKE :search1 OR u.prenom LIKE :search2 OR u.email LIKE :search3)';
            $params['search1'] = '%' . $search . '%';
            $params['search2'] = '%' . $search . '%';
            $params['search3'] = '%' . $search . '%';
        }

        if ($statut !== '') {
            $where .= ' AND c.statut = :statut';
            $params['statut'] = $statut;
        }

        $stmtCount = $db->prepare("SELECT COUNT(*) FROM candidatures c LEFT JOIN users u ON c.user_id = u.id JOIN roles r_role ON u.role_id = r_role.id LEFT JOIN etudiants et ON u.id = et.user_id LEFT JOIN offres o ON c.offre_id = o.id $where");
        $stmtCount->execute($params);
        $total = (int) $stmtCount->fetchColumn();

        $sql = "
            SELECT c.*, o.titre AS offre_titre, e.nom AS entreprise_nom,
                   u.nom AS etudiant_nom, u.prenom AS etudiant_prenom, u.email AS etudiant_email
            FROM candidatures c
            LEFT JOIN users u ON c.user_id = u.id
            JOIN roles r_role ON u.role_id = r_role.id
            LEFT JOIN etudiants et ON u.id = et.user_id
            LEFT JOIN offres o ON c.offre_id = o.id
            LEFT JOIN entreprises e ON o.entreprise_id = e.id
            $where
            ORDER BY c.date_candidature DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
            'current_page' => $page,
        ];
    }

    /**
     * Crée une candidature
     */
    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO candidatures (user_id, offre_id, cv_path, lettre_motivation, statut, date_candidature) VALUES (:user_id, :offre_id, :cv_path, :lettre_motivation, :statut, NOW())');
        $stmt->execute([
            'user_id' => $data['user_id'],
            'offre_id' => $data['offre_id'],
            'cv_path' => $data['cv_path'] ?? null,
            'lettre_motivation' => $data['lettre_motivation'] ?? '',
            'statut' => 'en_attente',
        ]);
        return (int) $db->lastInsertId();
    }

    /**
     * Vérifie si un étudiant a déjà postulé à une offre
     */
    public static function hasApplied(int $userId, int $offreId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) FROM candidatures WHERE user_id = :user_id AND offre_id = :offre_id');
        $stmt->execute(['user_id' => $userId, 'offre_id' => $offreId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Statistiques des candidatures pour le pilote
     */
    public static function getStatsPilote(int $piloteId): array
    {
        $db = Database::getInstance();
        $pilote = User::findById($piloteId);

        $whereBase = "WHERE r_role.nom = 'etudiant'";
        $params = [];

        if ($pilote && $pilote['centre']) {
            $whereBase .= ' AND u.centre = :centre';
            $params['centre'] = $pilote['centre'];
        }

        // Filtrage par promotions du pilote
        $promotions = User::getPilotePromotions($piloteId);
        if (!empty($promotions)) {
            $placeholders = [];
            foreach ($promotions as $i => $promo) {
                $key = 'promo_' . $i;
                $placeholders[] = ':' . $key;
                $params[$key] = $promo;
            }
            $whereBase .= ' AND et.promotion IN (' . implode(', ', $placeholders) . ')';
        }

        $nbEtudiants = $db->prepare("SELECT COUNT(DISTINCT u.id) FROM users u JOIN roles r_role ON u.role_id = r_role.id LEFT JOIN etudiants et ON u.id = et.user_id $whereBase");
        $nbEtudiants->execute($params);

        $totalCandidatures = $db->prepare("SELECT COUNT(*) FROM candidatures c LEFT JOIN users u ON c.user_id = u.id JOIN roles r_role ON u.role_id = r_role.id LEFT JOIN etudiants et ON u.id = et.user_id $whereBase");
        $totalCandidatures->execute($params);

        $enAttente = $db->prepare("SELECT COUNT(*) FROM candidatures c LEFT JOIN users u ON c.user_id = u.id JOIN roles r_role ON u.role_id = r_role.id LEFT JOIN etudiants et ON u.id = et.user_id $whereBase AND c.statut = 'en_attente'");
        $enAttente->execute($params);

        $acceptees = $db->prepare("SELECT COUNT(*) FROM candidatures c LEFT JOIN users u ON c.user_id = u.id JOIN roles r_role ON u.role_id = r_role.id LEFT JOIN etudiants et ON u.id = et.user_id $whereBase AND c.statut = 'acceptee'");
        $acceptees->execute($params);

        return [
            'nb_etudiants' => (int) $nbEtudiants->fetchColumn(),
            'total_candidatures' => (int) $totalCandidatures->fetchColumn(),
            'en_attente' => (int) $enAttente->fetchColumn(),
            'acceptees' => (int) $acceptees->fetchColumn(),
        ];
    }
}
