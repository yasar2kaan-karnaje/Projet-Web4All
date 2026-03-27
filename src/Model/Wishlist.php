<?php

namespace App\Model;

use PDO;

class Wishlist
{
    /**
     * Récupère la wishlist d'un utilisateur
     */
    public static function findByUser(int $userId, int $page = 1, int $perPage = 10): array
    {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        $stmtCount = $db->prepare('SELECT COUNT(*) FROM wishlist WHERE user_id = :user_id');
        $stmtCount->execute(['user_id' => $userId]);
        $total = (int) $stmtCount->fetchColumn();

        $stmt = $db->prepare("
            SELECT w.*, o.titre AS offre_titre, o.description AS offre_description,
                   o.lieu, o.duree, o.competences, e.nom AS entreprise_nom
            FROM wishlist w
            LEFT JOIN offres o ON w.offre_id = o.id
            LEFT JOIN entreprises e ON o.entreprise_id = e.id
            WHERE w.user_id = :user_id
            ORDER BY w.date_ajout DESC
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
     * Ajoute une offre à la wishlist
     */
    public static function add(int $userId, int $offreId): bool
    {
        if (self::exists($userId, $offreId)) {
            return false;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO wishlist (user_id, offre_id, date_ajout) VALUES (:user_id, :offre_id, NOW())');
        return $stmt->execute(['user_id' => $userId, 'offre_id' => $offreId]);
    }

    /**
     * Retire une offre de la wishlist
     */
    public static function remove(int $userId, int $offreId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM wishlist WHERE user_id = :user_id AND offre_id = :offre_id');
        return $stmt->execute(['user_id' => $userId, 'offre_id' => $offreId]);
    }

    /**
     * Vérifie si une offre est dans la wishlist
     */
    public static function exists(int $userId, int $offreId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) FROM wishlist WHERE user_id = :user_id AND offre_id = :offre_id');
        $stmt->execute(['user_id' => $userId, 'offre_id' => $offreId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Récupère les IDs des offres dans la wishlist d'un utilisateur
     */
    public static function getOffreIds(int $userId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT offre_id FROM wishlist WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        return array_column($stmt->fetchAll(), 'offre_id');
    }
}
