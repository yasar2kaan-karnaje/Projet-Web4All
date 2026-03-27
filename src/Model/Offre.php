<?php

namespace App\Model;

use PDO;

class Offre
{
    /**
     * Récupère toutes les offres avec pagination et recherche
     */
    public static function findAll(int $page = 1, int $perPage = 10, string $search = ''): array
    {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        $where = '';
        $params = [];

        if ($search !== '') {
            $where = 'WHERE (o.titre LIKE :search1 OR e.nom LIKE :search2 OR o.lieu LIKE :search3 OR o.competences LIKE :search4)';
            $params['search1'] = '%' . $search . '%';
            $params['search2'] = '%' . $search . '%';
            $params['search3'] = '%' . $search . '%';
            $params['search4'] = '%' . $search . '%';
        }

        // Total
        $stmtCount = $db->prepare("SELECT COUNT(*) FROM offres o LEFT JOIN entreprises e ON o.entreprise_id = e.id $where");
        $stmtCount->execute($params);
        $total = (int) $stmtCount->fetchColumn();

        // Résultats
        $sql = "SELECT o.*, e.nom AS entreprise_nom, e.secteur AS entreprise_secteur,
                (SELECT COUNT(*) FROM candidatures c WHERE c.offre_id = o.id) AS nb_candidatures
                FROM offres o
                LEFT JOIN entreprises e ON o.entreprise_id = e.id
                $where
                ORDER BY o.date_creation DESC
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
     * Trouve une offre par ID
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT o.*, e.nom AS entreprise_nom, e.secteur AS entreprise_secteur,
                   e.localisation AS entreprise_localisation, e.description AS entreprise_description,
                   e.taille AS entreprise_taille,
                   (SELECT COUNT(*) FROM candidatures c WHERE c.offre_id = o.id) AS nb_candidatures
            FROM offres o
            LEFT JOIN entreprises e ON o.entreprise_id = e.id
            WHERE o.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $offre = $stmt->fetch();
        return $offre ?: null;
    }

    /**
     * Crée une offre
     */
    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO offres (titre, description, competences, remuneration, duree, lieu, entreprise_id, date_creation) VALUES (:titre, :description, :competences, :remuneration, :duree, :lieu, :entreprise_id, NOW())');
        $stmt->execute([
            'titre' => $data['titre'],
            'description' => $data['description'],
            'competences' => $data['competences'],
            'remuneration' => $data['remuneration'],
            'duree' => $data['duree'],
            'lieu' => $data['lieu'],
            'entreprise_id' => $data['entreprise_id'],
        ]);
        return (int) $db->lastInsertId();
    }

    /**
     * Met à jour une offre
     */
    public static function update(int $id, array $data): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE offres SET titre = :titre, description = :description, competences = :competences, remuneration = :remuneration, duree = :duree, lieu = :lieu, entreprise_id = :entreprise_id WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'titre' => $data['titre'],
            'description' => $data['description'],
            'competences' => $data['competences'],
            'remuneration' => $data['remuneration'],
            'duree' => $data['duree'],
            'lieu' => $data['lieu'],
            'entreprise_id' => $data['entreprise_id'],
        ]);
    }

    /**
     * Supprime une offre
     */
    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM offres WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Récupère les offres d'une entreprise
     */
    public static function findByEntreprise(int $entrepriseId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT o.*, (SELECT COUNT(*) FROM candidatures c WHERE c.offre_id = o.id) AS nb_candidatures FROM offres o WHERE o.entreprise_id = :entreprise_id ORDER BY o.date_creation DESC');
        $stmt->execute(['entreprise_id' => $entrepriseId]);
        return $stmt->fetchAll();
    }

    /**
     * Compte le nombre total d'offres
     */
    public static function count(): int
    {
        $db = Database::getInstance();
        return (int) $db->query('SELECT COUNT(*) FROM offres')->fetchColumn();
    }

    /**
     * Récupère les dernières offres (pour la page d'accueil)
     */
    public static function findLatest(int $limit = 3): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT o.*, e.nom AS entreprise_nom
            FROM offres o
            LEFT JOIN entreprises e ON o.entreprise_id = e.id
            ORDER BY o.date_creation DESC
            LIMIT :limit
        ");
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Statistiques des offres
     */
    public static function getStats(): array
    {
        $db = Database::getInstance();
        $total = (int) $db->query('SELECT COUNT(*) FROM offres')->fetchColumn();
        $avgCandidatures = $db->query('SELECT ROUND(AVG(cnt), 1) FROM (SELECT COUNT(*) AS cnt FROM candidatures GROUP BY offre_id) AS sub')->fetchColumn();

        $repartitionDuree = $db->query('SELECT IFNULL(duree, "Non spécifiée") as duree, COUNT(*) as nb FROM offres GROUP BY duree ORDER BY nb DESC LIMIT 5')->fetchAll();
        $topWishlist = $db->query('SELECT o.titre, COUNT(w.user_id) as nb FROM offres o JOIN wishlist w ON o.id = w.offre_id GROUP BY o.id ORDER BY nb DESC LIMIT 5')->fetchAll();

        return [
            'total' => $total,
            'avg_candidatures' => $avgCandidatures ?: 0,
            'repartition_duree' => $repartitionDuree,
            'top_wishlist' => $topWishlist,
        ];
    }
}
