<?php

namespace App;

class Router {
    private array $routes = [];

    //Enregistre une route GET
    public function get(string $path, array $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    
    //Enregistre une route POST
    public function post(string $path, array $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    //Résout la requête et appelle le bon contrôleur
    public function resolve(string $url, string $method): void
    {
        // Nettoyer l'URL
        $url = trim($url, '/');
        if ($url === '') {
            $url = '/';
        }
        else {
            $url = '/' . $url;
        }

        $method = strtoupper($method);

        // Chercher une correspondance exacte
        if (isset($this->routes[$method][$url])) {
            $handler = $this->routes[$method][$url];
            $controller = new $handler[0]();
            $action = $handler[1];
            $controller->$action();
            return;
        }

        // Chercher une correspondance avec paramètre (ex: /offre/{id})
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = preg_replace('#\{(\w+)\}#', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Enlever le match complet
                $controller = new $handler[0]();
                $action = $handler[1];
                $controller->$action(...$matches);
                return;
            }
        }

        // 404 - Page non trouvée
        http_response_code(404);
        try {
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
            $twig = new \Twig\Environment($loader, ['cache' => false]);
            $twig->addGlobal('session', $_SESSION ?? []);
            $twig->addGlobal('user', $_SESSION['user'] ?? null);
            $twig->addGlobal('role', $_SESSION['user']['role'] ?? 'guest');
            $twig->addGlobal('is_logged_in', isset($_SESSION['user']));
            echo $twig->render('errors/404.html.twig');
        } 
        catch (\Throwable $e) {
            echo '<h1>404 - Page non trouvée</h1>';
            echo '<p>La page demandée n\'existe pas. <a href="/">Retour à l\'accueil</a></p>';
        }
    }
}
