<?php

/**
 * Dépi'Stage - Front Controller
 * Toutes les requêtes passent par ce fichier
 */

// Configuration sécurisée des cookies de session
$isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => '',
    'secure' => $isSecure,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// Autoload Composer
require_once __DIR__ . '/../vendor/autoload.php';

use App\Router;
use App\Controller\HomeController;
use App\Controller\AuthController;
use App\Controller\OffreController;
use App\Controller\EntrepriseController;
use App\Controller\MentionsController;
use App\Controller\ProfileController;

// Créer le routeur
$router = new Router();

// =============================================
// Routes publiques (accessibles à tous)
// =============================================
$router->get('/', [HomeController::class, 'index']);
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/offres', [OffreController::class, 'catalogue']);
$router->get('/offre/{id}', [OffreController::class, 'detail']);
$router->get('/entreprises', [EntrepriseController::class, 'liste']);
$router->get('/entreprise/{id}', [EntrepriseController::class, 'detail']);
$router->get('/mentions-legales', [MentionsController::class, 'index']);
$router->get('/profil', [ProfileController::class, 'index']);

// =============================================
// Routes étudiant (Phase 4 - à compléter)
// =============================================
$router->post('/offre/{id}/postuler', [OffreController::class, 'postuler']);
$router->post('/entreprise/{id}/evaluer', [EntrepriseController::class, 'evaluer']);

// Résoudre la requête
$url = $_GET['url'] ?? '/';
if (($pos = strpos($url, '?')) !== false) {
    $url = substr($url, 0, $pos);
}
$method = $_SERVER['REQUEST_METHOD'];

try {
    $router->resolve($url, $method);
} catch (\Throwable $e) {
    http_response_code(500);
    try {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
        $twig = new \Twig\Environment($loader, ['cache' => false]);
        $twig->addGlobal('session', $_SESSION ?? []);
        $twig->addGlobal('user', $_SESSION['user'] ?? null);
        $twig->addGlobal('role', $_SESSION['user']['role'] ?? 'guest');
        $twig->addGlobal('is_logged_in', isset($_SESSION['user']));
        echo $twig->render('errors/500.html.twig');
    } catch (\Throwable $e2) {
        echo '<h1>500 - Erreur technique</h1>';
        echo '<p>Une erreur est survenue. <a href="/">Retour à l\'accueil</a></p>';
    }
}
