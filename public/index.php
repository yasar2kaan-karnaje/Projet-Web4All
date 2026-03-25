<?php

/**
 * Dépi'Stage - Front Controller
 * Toutes les requêtes passent par ce fichier
 */

session_start();

// Autoload Composer (sera activé après composer install)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Pour l'instant, routage basique
$url = $_GET['url'] ?? '/';

// Page d'accueil par défaut
echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Dépi\'Stage</title><link rel="stylesheet" href="/css/styles.css"></head><body><h1>Dépi\'Stage - En construction</h1></body></html>';
