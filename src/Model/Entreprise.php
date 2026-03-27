<?php

namespace App\Model;

use PDO;

class Entreprise
{
    /**
     * Récupère toutes les entreprises avec pagination et recherche
     */
    public static function findAll(int $page = 1, int $perPage = 10, string $search = '', string $secteur = ''): array
    {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        $where = 'WHERE 1=1';
        $params = [];

        if ($search !== '') {
            $where .= ' AND (e.nom LIKE :search1 OR e.localisation LIKE :search2)';
            $params['search1'] = '%' . $search . '%';
            $params['search2'] = '%' . $search . '%';
        }
        if ($secteur !== '') {
            $where .= ' AND e.secteur LIKE :secteur';
            $params['secteur'] = '%' . $secteur . '%';
        }

        // Total
        $stmtCount = $db->prepare("SELECT COUNT(*) FROM entreprises e $where");
        $stmtCount->execute($params);
        $total = (int) $stmtCount->fetchColumn();

        // Résultats avec nombre d'offres et note moyenne
        $sql = "SELECT e.*,
                (SELECT COUNT(*) FROM offres o WHERE o.entreprise_id = e.id) AS nb_offres,
                (SELECT ROUND(AVG(ev.note), 1) FROM evaluations ev WHERE ev.entreprise_id = e.id) AS note_moyenne
                FROM entreprises e
                $where
                ORDER BY e.nom ASC
                LIMIT :limit OFFSET :offset";

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
     * Trouve une entreprise par ID
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT e.*,
                   (SELECT COUNT(*) FROM offres o WHERE o.entreprise_id = e.id) AS nb_offres,
                   (SELECT ROUND(AVG(ev.note), 1) FROM evaluations ev WHERE ev.entreprise_id = e.id) AS note_moyenne,
                   (SELECT COUNT(*) FROM evaluations ev WHERE ev.entreprise_id = e.id) AS nb_evaluations,
                   (SELECT COUNT(DISTINCT c.user_id) FROM candidatures c JOIN offres o ON c.offre_id = o.id WHERE o.entreprise_id = e.id) AS nb_candidats
            FROM entreprises e
            WHERE e.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $entreprise = $stmt->fetch();
        return $entreprise ?: null;
    }

    /**
     * Crée une entreprise
     */
    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO entreprises (nom, secteur, localisation, description, email_contact, tel_contact, taille) VALUES (:nom, :secteur, :localisation, :description, :email_contact, :tel_contact, :taille)');
        $stmt->execute([
            'nom' => $data['nom'],
            'secteur' => $data['secteur'],
            'localisation' => $data['localisation'],
            'description' => $data['description'] ?? '',
            'email_contact' => $data['email_contact'] ?? null,
            'tel_contact' => $data['tel_contact'] ?? null,
            'taille' => $data['taille'] ?? null,
        ]);
        return (int) $db->lastInsertId();
    }

    /**
     * Met à jour une entreprise
     */
    public static function update(int $id, array $data): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE entreprises SET nom = :nom, secteur = :secteur, localisation = :localisation, description = :description, email_contact = :email_contact, tel_contact = :tel_contact, taille = :taille WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'nom' => $data['nom'],
            'secteur' => $data['secteur'],
            'localisation' => $data['localisation'],
            'description' => $data['description'] ?? '',
            'email_contact' => $data['email_contact'] ?? null,
            'tel_contact' => $data['tel_contact'] ?? null,
            'taille' => $data['taille'] ?? null,
        ]);
    }

    /**
     * Supprime une entreprise
     */
    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM entreprises WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Compte le total d'entreprises
     */
    public static function count(): int
    {
        $db = Database::getInstance();
        return (int) $db->query('SELECT COUNT(*) FROM entreprises')->fetchColumn();
    }

    /**
     * Récupère les évaluations d'une entreprise
     */
    public static function getEvaluations(int $entrepriseId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT ev.*, u.nom AS user_nom, u.prenom AS user_prenom
            FROM evaluations ev
            LEFT JOIN users u ON ev.user_id = u.id
            WHERE ev.entreprise_id = :entreprise_id
            ORDER BY ev.date_evaluation DESC
        ");
        $stmt->execute(['entreprise_id' => $entrepriseId]);
        return $stmt->fetchAll();
    }

    /**
     * Ajoute une évaluation
     */
    public static function addEvaluation(int $userId, int $entrepriseId, int $note, string $commentaire): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO evaluations (user_id, entreprise_id, note, commentaire, date_evaluation) VALUES (:user_id, :entreprise_id, :note, :commentaire, NOW())');
        return $stmt->execute([
            'user_id' => $userId,
            'entreprise_id' => $entrepriseId,
            'note' => $note,
            'commentaire' => $commentaire,
        ]);
    }
}
