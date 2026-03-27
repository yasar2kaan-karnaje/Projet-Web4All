<?php

namespace App\Controller;

use App\Model\User;

class AuthController extends BaseController
{
    /**
     * Affiche le formulaire de connexion
     */
    public function loginForm(): void
    {
        // Si déjà connecté, rediriger selon le rôle
        if (isset($_SESSION['user'])) {
            $this->redirectByRole();
            return;
        }

        $this->render('auth/login.html.twig', [
            'error' => $this->getParam('error'),
        ]);
    }

    /**
     * Traite la connexion
     */
    public function login(): void
    {
        $email = $this->postParam('email', '');
        $password = $this->postParam('password', '');

        if (empty($email) || empty($password)) {
            $this->redirect('/login?error=Veuillez remplir tous les champs');
            return;
        }

        try {
            $user = User::verifyPassword($email, $password);
        } catch (\Exception $e) {
            $this->redirect('/login?error=Erreur de connexion à la base de données');
            return;
        }

        if ($user) {
            // Stocker l'utilisateur en session (sans le mot de passe)
            unset($user['password']);
            $_SESSION['user'] = $user;

            // Création d'un cookie sécurisé chiffré (pas en clair)
            $tokenData = json_encode(['id' => $user['id'], 'role' => $user['role'], 'time' => time()]);
            $secretKey = 'DepiStageSecretKey2026'; // Clé secrète (devrait être dans config)
            $encryptedToken = openssl_encrypt($tokenData, 'AES-128-ECB', $secretKey);
            // Expiration en fonction du consentement (si refusé, expire avec la session, sinon 30 jours)
            $cookieConsent = $_COOKIE['cookie_consent'] ?? 'accepted';
            $expireTime = ($cookieConsent === 'rejected') ? 0 : time() + 86400 * 30; // 0 = session
            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            
            setcookie('auth_token', $encryptedToken, [
                'expires' => $expireTime,
                'path' => '/',
                'domain' => '',
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            $this->redirectByRole();
        } else {
            $this->redirect('/login?error=Email ou mot de passe incorrect');
        }
    }

    /**
     * Déconnexion
     */
    public function logout(): void
    {
        // Supprimer le cookie
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        setcookie('auth_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => '',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        session_destroy();
        $this->redirect('/login');
    }

    /**
     * Redirige selon le rôle de l'utilisateur
     */
    private function redirectByRole(): void
    {
        $role = $_SESSION['user']['role'] ?? 'guest';
        switch ($role) {
            case 'admin':
                $this->redirect('/admin');
                break;
            case 'pilote':
                $this->redirect('/pilote/candidatures');
                break;
            case 'etudiant':
                $this->redirect('/');
                break;
            default:
                $this->redirect('/');
        }
    }
}
