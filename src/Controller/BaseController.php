<?php

namespace App\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class BaseController
{
    protected Environment $twig;

    public function __construct()
    {
        // Vérifier si un cookie de connexion existe mais pas de session active
        if (!isset($_SESSION['user']) && isset($_COOKIE['auth_token'])) {
            $secretKey = 'DepiStageSecretKey2026';
            $decryptedToken = openssl_decrypt($_COOKIE['auth_token'], 'AES-128-ECB', $secretKey);
            if ($decryptedToken) {
                $tokenData = json_decode($decryptedToken, true);
                if ($tokenData && isset($tokenData['id'])) {
                    $user = \App\Model\User::findById($tokenData['id']);
                    if ($user) {
                        unset($user['password']);
                        $_SESSION['user'] = $user;
                    } else {
                        // Cookie invalide ou utilisateur supprimé, on nettoie
                        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
                        setcookie('auth_token', '', time() - 3600, '/', '', $isSecure, true);
                    }
                }
            }
        }

        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->twig = new Environment($loader, [
            'cache' => false, // En dev, pas de cache
            'debug' => true,
        ]);

        // Variables globales accessibles dans tous les templates
        $this->twig->addGlobal('session', $_SESSION ?? []);
        $this->twig->addGlobal('user', $_SESSION['user'] ?? null);
        $this->twig->addGlobal('role', $_SESSION['user']['role'] ?? 'guest');
        $this->twig->addGlobal('is_logged_in', isset($_SESSION['user']));
    }

    /**
     * Rend un template Twig
     */
    protected function render(string $template, array $data = []): void
    {
        echo $this->twig->render($template, $data);
    }

    /**
     * Redirige vers une URL
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Vérifie si l'utilisateur est connecté
     */
    protected function requireLogin(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    protected function requireRole(string ...$roles): void
    {
        $this->requireLogin();
        if (!in_array($_SESSION['user']['role'], $roles)) {
            http_response_code(403);
            $this->render('errors/403.html.twig');
            exit;
        }
    }

    /**
     * Récupère un paramètre GET
     */
    protected function getParam(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Récupère un paramètre POST
     */
    protected function postParam(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }
}
