<?php

/**
 * Dépi'Stage - Front Controller
 * Toutes les requêtes passent par ce fichier
 */

// Configuration sécurisée des cookies de session
// 'secure' => false pour le développement local (HTTP). Passer à true en production (HTTPS).
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
use App\Controller\CandidatureController;
use App\Controller\WishlistController;
use App\Controller\AdminController;
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
// Routes étudiant
// =============================================
$router->post('/offre/{id}/postuler', [OffreController::class, 'postuler']);
$router->get('/etudiant/candidatures', [CandidatureController::class, 'mesCandidatures']);
$router->get('/etudiant/wishlist', [WishlistController::class, 'index']);
$router->post('/wishlist/add', [WishlistController::class, 'add']);
$router->post('/wishlist/remove', [WishlistController::class, 'remove']);
$router->post('/entreprise/{id}/evaluer', [EntrepriseController::class, 'evaluer']);
$router->get('/download', [CandidatureController::class, 'telechargerDocument']);

// =============================================
// Routes pilote
// =============================================
$router->get('/pilote/candidatures', [CandidatureController::class, 'candidaturesPilote']);

// =============================================
// Routes admin & pilote (gestion CRUD)
// =============================================
$router->get('/admin', [AdminController::class, 'dashboard']);

// Entreprises
$router->get('/admin/entreprises', [AdminController::class, 'entreprises']);
$router->get('/admin/entreprise/creer', [AdminController::class, 'entrepriseForm']);
$router->post('/admin/entreprise/creer', [AdminController::class, 'entrepriseCreate']);
$router->get('/admin/entreprise/{id}/modifier', [AdminController::class, 'entrepriseForm']);
$router->post('/admin/entreprise/{id}/modifier', [AdminController::class, 'entrepriseUpdate']);
$router->post('/admin/entreprise/{id}/supprimer', [AdminController::class, 'entrepriseDelete']);

// Offres
$router->get('/admin/offres', [AdminController::class, 'offres']);
$router->get('/admin/offre/creer', [AdminController::class, 'offreForm']);
$router->post('/admin/offre/creer', [AdminController::class, 'offreCreate']);
$router->get('/admin/offre/{id}/modifier', [AdminController::class, 'offreForm']);
$router->post('/admin/offre/{id}/modifier', [AdminController::class, 'offreUpdate']);
$router->post('/admin/offre/{id}/supprimer', [AdminController::class, 'offreDelete']);

// Étudiants
$router->get('/admin/etudiants', [AdminController::class, 'etudiants']);
$router->get('/admin/etudiant/creer', [AdminController::class, 'etudiantForm']);
$router->get('/admin/etudiant/{id}', [AdminController::class, 'etudiantDetail']);
$router->post('/admin/etudiant/creer', [AdminController::class, 'etudiantCreate']);
$router->get('/admin/etudiant/{id}/modifier', [AdminController::class, 'etudiantForm']);
$router->post('/admin/etudiant/{id}/modifier', [AdminController::class, 'etudiantUpdate']);
$router->post('/admin/etudiant/{id}/supprimer', [AdminController::class, 'etudiantDelete']);

// Pilotes (Admin uniquement géré dans le contrôleur)
$router->get('/admin/pilotes', [AdminController::class, 'pilotes']);
$router->get('/admin/pilote/creer', [AdminController::class, 'piloteForm']);
$router->post('/admin/pilote/creer', [AdminController::class, 'piloteCreate']);
$router->get('/admin/pilote/{id}/modifier', [AdminController::class, 'piloteForm']);
$router->post('/admin/pilote/{id}/modifier', [AdminController::class, 'piloteUpdate']);
$router->post('/admin/pilote/{id}/supprimer', [AdminController::class, 'piloteDelete']);

// Promotions (Référentiel)
$router->get('/admin/promotions', [AdminController::class, 'promotions']);
$router->post('/admin/promotion/creer', [AdminController::class, 'promotionCreate']);
$router->post('/admin/centre/creer', [AdminController::class, 'centreCreate']);

// Résoudre la requête
$url = $_GET['url'] ?? '/';
// Supprimer la query string de l'URL si présente
if (($pos = strpos($url, '?')) !== false) {
    $url = substr($url, 0, $pos);
}
$method = $_SERVER['REQUEST_METHOD'];

try {
    $router->resolve($url, $method);
} catch (\Throwable $e) {
    http_response_code(500);
    // Tenter d'afficher la page 500 personnalisée
    try {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
        $twig = new \Twig\Environment($loader, ['cache' => false]);
        $twig->addGlobal('session', $_SESSION ?? []);
        $twig->addGlobal('user', $_SESSION['user'] ?? null);
        $twig->addGlobal('role', $_SESSION['user']['role'] ?? 'guest');
        $twig->addGlobal('is_logged_in', isset($_SESSION['user']));
        echo $twig->render('errors/500.html.twig');
    } catch (\Throwable $e2) {
        // Fallback si Twig n'est pas disponible
        echo '<h1>500 - Erreur technique</h1>';
        echo '<p>Une erreur est survenue. <a href="/">Retour à l\'accueil</a></p>';
    }
}
