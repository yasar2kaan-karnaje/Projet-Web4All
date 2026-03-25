<?php

namespace App\Controller;

class AuthController extends BaseController
{
    public function loginForm()
    {
        $this->render('auth/login.html.twig', [
            'active_page' => 'login',
        ]);
    }

    public function login()
    {
        $email = $this->postParam('email', '');
        $password = $this->postParam('password', '');

        // Phase 3 : vérification basique sans BDD
        if ($email === '' || $password === '') {
            $this->render('auth/login.html.twig', [
                'error' => 'Veuillez remplir tous les champs.',
                'active_page' => 'login',
            ]);
            return;
        }

        // TODO: Phase 4 - Connecter à la BDD avec PDO
        $this->render('auth/login.html.twig', [
            'error' => 'Fonctionnalité en cours de développement.',
            'active_page' => 'login',
        ]);
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/');
    }
}
