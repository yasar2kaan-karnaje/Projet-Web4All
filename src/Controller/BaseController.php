<?php

namespace App\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class BaseController
{
    protected Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->twig = new Environment($loader, [
            'cache' => false,
            'debug' => true,
        ]);

        // Variables globales accessibles dans tous les templates
        $this->twig->addGlobal('session', $_SESSION ?? []);
        $this->twig->addGlobal('user', $_SESSION['user'] ?? null);
        $this->twig->addGlobal('role', $_SESSION['user']['role'] ?? 'guest');
        $this->twig->addGlobal('is_logged_in', isset($_SESSION['user']));
    }

    protected function render(string $template, array $data = []): void
    {
        echo $this->twig->render($template, $data);
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function requireLogin(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }
    }

    protected function requireRole(string ...$roles): void
    {
        $this->requireLogin();
        if (!in_array($_SESSION['user']['role'], $roles)) {
            http_response_code(403);
            $this->render('errors/403.html.twig');
            exit;
        }
    }

    protected function getParam(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    protected function postParam(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }
}
