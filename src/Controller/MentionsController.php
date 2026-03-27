<?php

namespace App\Controller;

class MentionsController extends BaseController
{
    /**
     * Page des mentions légales
     */
    public function index(): void
    {
        $this->render('mentions.html.twig');
    }
}
