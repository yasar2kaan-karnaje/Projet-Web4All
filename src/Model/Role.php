<?php

namespace App\Model;

use PDO;

class Role
{
    /**
     * Récupère tous les rôles
     */
    public static function findAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query('SELECT * FROM roles ORDER BY id');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trouve un rôle par son ID
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM roles WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        return $role ?: null;
    }

    /**
     * Trouve un rôle par son nom
     */
    public static function findByName(string $nom): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM roles WHERE nom = :nom');
        $stmt->execute(['nom' => $nom]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        return $role ?: null;
    }

    /**
     * Récupère l'ID d'un rôle par son nom
     */
    public static function getIdByName(string $nom): ?int
    {
        $role = self::findByName($nom);
        return $role ? (int) $role['id'] : null;
    }

    /**
     * Crée un nouveau rôle
     */
    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO roles (nom, label) VALUES (:nom, :label)');
        $stmt->execute([
            'nom' => $data['nom'],
            'label' => $data['label'],
        ]);
        return (int) $db->lastInsertId();
    }

    /**
     * Met à jour un rôle
     */
    public static function update(int $id, array $data): bool
    {
        $db = Database::getInstance();
        $fields = [];
        $params = ['id' => $id];

        foreach (['nom', 'label'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = 'UPDATE roles SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Supprime un rôle (attention: vérifier qu'aucun user n'utilise ce rôle)
     */
    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM roles WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Compte les utilisateurs pour un rôle donné
     */
    public static function countUsers(int $roleId): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE role_id = :role_id');
        $stmt->execute(['role_id' => $roleId]);
        return (int) $stmt->fetchColumn();
    }
}
