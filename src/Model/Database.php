<?php

namespace App\Model;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    /**
     * Singleton - Retourne l'instance PDO unique
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';

            try {
                $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
                self::$instance = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die('Erreur de connexion à la base de données : ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
