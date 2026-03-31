<?php

namespace App\Model;

use PDO;

class User {

    //Renvoie en string la requete SQL de tous les champs de users + les champs spécifiques au rôle    
    private static function baseSelectQuery(): string {
        return "SELECT u.*, r.nom AS role, r.label AS role_label,
                       et.promotion,
                       p.is_recruteur, p.entreprise_id, ent.nom AS entreprise_nom,
                       updater.nom AS updated_by_nom, updater.prenom AS updated_by_prenom
                FROM users u
                JOIN roles r ON u.role_id = r.id
                LEFT JOIN etudiants et ON u.id = et.user_id
                LEFT JOIN pilotes p ON u.id = p.user_id
                LEFT JOIN administrateurs a ON u.id = a.user_id
                LEFT JOIN entreprises ent ON p.entreprise_id = ent.id
                LEFT JOIN users updater ON u.updated_by = updater.id";
    }

    //Trouve un utilisateur par email et renvoie le tableau des infos du users
    public static function findByEmail(string $email): ?array {
        $db = Database::getInstance();
        $sql = self::baseSelectQuery() . " WHERE u.email = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    //Trouve un utilisateur par ID (avec ses promotions si pilote) et renvoie le tableau
    public static function findById(int $id): ?array {
        $db = Database::getInstance();
        $sql = self::baseSelectQuery() . " WHERE u.id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        if (!$user) {
            return null;
        }

        // Si c'est un pilote, charger ses promotions
        if ($user['role'] === 'pilote') {
            $user['promotions'] = self::getPilotePromotions($id);
        }

        return $user;
    }

    //Récupère tous les utilisateurs d'un rôle donné avec pagination et les mets dans un tableau
    public static function findByRole(string $role, int $page = 1, int $perPage = 10, string $search = ''): array {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        $where = 'WHERE r.nom = :role';
        $params = ['role' => $role];

        if ($search !== '') {
            $where .= ' AND (u.nom LIKE :search1 OR u.prenom LIKE :search2 OR u.email LIKE :search3)';
            $params['search1'] = '%' . $search . '%';
            $params['search2'] = '%' . $search . '%';
            $params['search3'] = '%' . $search . '%';
        }

        // Total
        $stmtCount = $db->prepare("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id $where");
        $stmtCount->execute($params);
        $total = (int) $stmtCount->fetchColumn();

        // Résultats avec JOINs spécifiques au rôle
        if ($role === 'etudiant') {
            $sql = "SELECT u.*, r.nom AS role, r.label AS role_label, et.promotion,
                    (SELECT statut FROM candidatures c WHERE c.user_id = u.id ORDER BY FIELD(statut, 'acceptee', 'en_attente', 'refusee') LIMIT 1) AS statut_recherche 
                    FROM users u 
                    JOIN roles r ON u.role_id = r.id
                    JOIN etudiants et ON u.id = et.user_id
                    $where ORDER BY u.nom ASC LIMIT :limit OFFSET :offset";
        }
        elseif ($role === 'pilote') {
            $sql = "SELECT u.*, r.nom AS role, r.label AS role_label, 
                    p.is_recruteur, p.entreprise_id, ent.nom AS entreprise_nom,
                    (SELECT GROUP_CONCAT(rp.nom SEPARATOR ', ') 
                     FROM pilote_promotions pp 
                     JOIN ref_promotions rp ON pp.promotion_id = rp.id 
                     WHERE pp.pilote_id = u.id) AS promotions_list
                    FROM users u 
                    JOIN roles r ON u.role_id = r.id
                    JOIN pilotes p ON u.id = p.user_id
                    LEFT JOIN entreprises ent ON p.entreprise_id = ent.id
                    $where ORDER BY u.nom ASC LIMIT :limit OFFSET :offset";
        }
        else {
            $sql = "SELECT u.*, r.nom AS role, r.label AS role_label 
                    FROM users u 
                    JOIN roles r ON u.role_id = r.id 
                    $where ORDER BY u.nom ASC LIMIT :limit OFFSET :offset";
        }
        
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

    //Récupère les étudiants visibles par un pilote (filtrage par centre + promotions)
    public static function findStudentsByPilote(int $piloteId, int $page = 1, int $perPage = 10, string $search = ''): array {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        $pilote = self::findById($piloteId);
        if (!$pilote) {
            return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }

        $promotions = self::getPilotePromotions($piloteId);
        $roleEtudiant = Role::getIdByName('etudiant');

        $where = "WHERE u.role_id = :role_id";
        $params = ['role_id' => $roleEtudiant];

        // Filtrage par centre du pilote
        if ($pilote['centre']) {
            $where .= ' AND u.centre = :centre';
            $params['centre'] = $pilote['centre'];
        }

        // Filtrage par les promotions du pilote (via sous-table etudiants)
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

        // Total
        $stmtCount = $db->prepare("SELECT COUNT(*) FROM users u JOIN etudiants et ON u.id = et.user_id $where");
        $stmtCount->execute($params);
        $total = (int) $stmtCount->fetchColumn();

        // Résultats
        $sql = "SELECT u.*, r.nom AS role, r.label AS role_label, et.promotion,
                (SELECT statut FROM candidatures c WHERE c.user_id = u.id ORDER BY FIELD(statut, 'acceptee', 'en_attente', 'refusee') LIMIT 1) AS statut_recherche 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                JOIN etudiants et ON u.id = et.user_id
                $where ORDER BY u.nom ASC LIMIT :limit OFFSET :offset";

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

    //Crée un utilisateur + sa ligne dans la sous-table du rôle renvoie son ID
    public static function create(array $data): int {
        $db = Database::getInstance();

        // Résoudre le role_id
        $roleId = null;
        $roleName = $data['role'] ?? '';
        if (!empty($data['role_id'])) {
            $roleId = (int) $data['role_id'];
            // Résoudre le nom du rôle si pas fourni
            if ($roleName === '') {
                $role = Role::findById($roleId);
                $roleName = $role ? $role['nom'] : '';
            }
        } elseif ($roleName !== '') {
            $roleId = Role::getIdByName($roleName);
        }

        // 1) INSERT dans users
        $stmt = $db->prepare('INSERT INTO users (nom, prenom, email, password, role_id, centre, updated_by) VALUES (:nom, :prenom, :email, :password, :role_id, :centre, :updated_by)');
        $stmt->execute([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role_id' => $roleId,
            'centre' => $data['centre'] ?? null,
            'updated_by' => $data['updated_by'] ?? null,
        ]);
        $userId = (int) $db->lastInsertId();

        // 2) INSERT dans la sous-table correspondante
        if ($roleName === 'etudiant') {
            $stmtSub = $db->prepare('INSERT INTO etudiants (user_id, promotion) VALUES (:user_id, :promotion)');
            $stmtSub->execute([
                'user_id' => $userId,
                'promotion' => $data['promotion'] ?? null,
            ]);
        }
        elseif ($roleName === 'pilote') {
            $stmtSub = $db->prepare('INSERT INTO pilotes (user_id, is_recruteur, entreprise_id) VALUES (:user_id, :is_recruteur, :entreprise_id)');
            $stmtSub->execute([
                'user_id' => $userId,
                'is_recruteur' => (int) ($data['is_recruteur'] ?? 0),
                'entreprise_id' => !empty($data['entreprise_id']) ? (int) $data['entreprise_id'] : null,
            ]);
            // Sauvegarder les promotions pilote
            if (!empty($data['promotions'])) {
                self::setPilotePromotionsId($userId, $data['promotions']);
            }
        }
        elseif ($roleName === 'admin') {
            $stmtSub = $db->prepare('INSERT INTO administrateurs (user_id) VALUES (:user_id)');
            $stmtSub->execute(['user_id' => $userId]);
        }

        return $userId;
    }

    //Met à jour un utilisateur + sa sous-table renvoie un bool
    public static function update(int $id, array $data): bool {
        $db = Database::getInstance();

        // --- Mise à jour de la table users ---
        $fields = [];
        $params = ['id' => $id];

        foreach (['nom', 'prenom', 'email', 'centre'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        // Gérer le rôle
        if (isset($data['role'])) {
            $roleId = Role::getIdByName($data['role']);
            if ($roleId) {
                $fields[] = 'role_id = :role_id';
                $params['role_id'] = $roleId;
            }
        }
        elseif (isset($data['role_id'])) {
            $fields[] = 'role_id = :role_id';
            $params['role_id'] = (int) $data['role_id'];
        }

        // Mot de passe
        if (isset($data['password']) && $data['password'] !== '') {
            $fields[] = 'password = :password';
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        // Traçabilité : qui a fait la modification
        if (isset($data['updated_by'])) {
            $fields[] = 'updated_by = :updated_by';
            $params['updated_by'] = (int) $data['updated_by'];
        }

        $result = true;
        if (!empty($fields)) {
            $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($params);
        }

        // --- Mise à jour de la sous-table ---
        $roleName = $data['role'] ?? '';

        // Si le rôle n'est pas dans $data, le récupérer depuis la BDD
        if ($roleName === '') {
            $user = self::findById($id);
            $roleName = $user ? $user['role'] : '';
        }

        if ($roleName === 'etudiant') {
            if (isset($data['promotion'])) {
                // UPSERT : mettre à jour ou créer la ligne étudiant
                $stmtSub = $db->prepare('INSERT INTO etudiants (user_id, promotion) VALUES (:user_id, :promotion) ON DUPLICATE KEY UPDATE promotion = VALUES(promotion)');
                $stmtSub->execute([
                    'user_id' => $id,
                    'promotion' => $data['promotion'],
                ]);
            }
        } elseif ($roleName === 'pilote') {
            $subFields = [];
            $subParams = ['user_id' => $id];

            if (array_key_exists('is_recruteur', $data)) {
                $subFields[] = 'is_recruteur = :is_recruteur';
                $subParams['is_recruteur'] = (int) $data['is_recruteur'];
            }
            if (array_key_exists('entreprise_id', $data)) {
                $subFields[] = 'entreprise_id = :entreprise_id';
                $subParams['entreprise_id'] = !empty($data['entreprise_id']) ? (int) $data['entreprise_id'] : null;
            }

            if (!empty($subFields)) {
                // S'assurer que la ligne existe
                $stmtCheck = $db->prepare('SELECT COUNT(*) FROM pilotes WHERE user_id = :uid');
                $stmtCheck->execute(['uid' => $id]);
                if ((int)$stmtCheck->fetchColumn() === 0) {
                    $db->prepare('INSERT INTO pilotes (user_id) VALUES (:uid)')->execute(['uid' => $id]);
                }
                $sqlSub = 'UPDATE pilotes SET ' . implode(', ', $subFields) . ' WHERE user_id = :user_id';
                $stmtSub = $db->prepare($sqlSub);
                $stmtSub->execute($subParams);
            }

            // Mettre à jour les promotions si fournies
            if (isset($data['promotions'])) {
                self::setPilotePromotionsId($id, $data['promotions']);
            }
        }
        return $result;
    }

    //Supprime un utilisateur (CASCADE supprime la sous-table)
    public static function delete(int $id): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    //Vérifie le mot de passe et renvoie le tableau du user si c'est bon
    public static function verifyPassword(string $email, string $password): ?array {
        $user = self::findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    //Compte les utilisateurs par rôle et revnoie le nombre
    public static function countByRole(string $role): int {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id WHERE r.nom = :role');
        $stmt->execute(['role' => $role]);
        return (int) $stmt->fetchColumn();
    }

    //Récupère les promotions d'un pilote
    public static function getPilotePromotions(int $piloteId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT rp.nom FROM pilote_promotions pp JOIN ref_promotions rp ON pp.promotion_id = rp.id WHERE pp.pilote_id = :pid ORDER BY rp.nom');
        $stmt->execute(['pid' => $piloteId]);
        return array_column($stmt->fetchAll(), 'nom');
    }

    /**
     * Associe un pilote à des promotions (par IDs)
     */
    public static function setPilotePromotionsId(int $piloteId, array $promotionIds): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM pilote_promotions WHERE pilote_id = :pid');
        $stmt->execute(['pid' => $piloteId]);

        $stmtInsert = $db->prepare('INSERT INTO pilote_promotions (pilote_id, promotion_id) VALUES (:pid, :proid)');
        foreach ($promotionIds as $proid) {
            $proid = (int)$proid;
            if ($proid > 0) {
                $stmtInsert->execute(['pid' => $piloteId, 'proid' => $proid]);
            }
        }
    }

    // ===================================
    // RÉFÉRENTIELS (PROMOTIONS / CENTRES)
    // ===================================

    /**
     * Récupère toutes les promotions existantes depuis le référentiel
     */
    public static function getAllPromotions(string $centreNom = null): array
    {
        $db = Database::getInstance();
        $sql = "SELECT rp.*, c.nom as centre_nom FROM ref_promotions rp JOIN centres c ON rp.centre_id = c.id";
        $params = [];
        if ($centreNom) {
            $sql .= " WHERE c.nom = :centre";
            $params['centre'] = $centreNom;
        }
        $sql .= " ORDER BY c.nom, rp.nom";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les centres depuis le référentiel
     */
    public static function getAllCentres(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT id, nom FROM centres ORDER BY nom");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createPromotion(string $nom, int $centreId, int $creatorId, string $role, ?string $centreNom): void
    {
        $db = Database::getInstance();
        
        if ($role === 'pilote' && $centreNom) {
            $stmtC = $db->prepare('SELECT id FROM centres WHERE nom = :nom');
            $stmtC->execute(['nom' => $centreNom]);
            $centreId = (int) $stmtC->fetchColumn();
        }

        if ($nom !== '' && $centreId > 0) {
            try {
                $stmt = $db->prepare('INSERT INTO ref_promotions (centre_id, nom, created_by) VALUES (:cid, :nom, :uid)');
                $stmt->execute(['cid' => $centreId, 'nom' => $nom, 'uid' => $creatorId]);
                
                if ($role === 'pilote') {
                    $promoId = $db->lastInsertId();
                    $stmtAdd = $db->prepare('INSERT IGNORE INTO pilote_promotions (pilote_id, promotion_id) VALUES (:pid, :proid)');
                    $stmtAdd->execute(['pid' => $creatorId, 'proid' => $promoId]);
                }
            } catch (\Exception $e) {
                // Ignore duplicates
            }
        }
    }

    public static function createCentre(string $nom): void
    {
        $db = Database::getInstance();
        if ($nom !== '') {
            try {
                $stmt = $db->prepare('INSERT INTO centres (nom) VALUES (:nom)');
                $stmt->execute(['nom' => $nom]);
            } catch (\Exception $e) {
                // Ignore duplicates
            }
        }
    }
}
