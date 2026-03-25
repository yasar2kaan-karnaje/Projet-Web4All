<?php

namespace App\Controller;

class ProfileController extends BaseController
{
    public function index()
    {
        $this->requireLogin();
        $this->render('profil/index.html.twig', [
            'active_page' => 'profil',
        ]);
    }
}
