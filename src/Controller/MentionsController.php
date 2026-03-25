<?php

namespace App\Controller;

class MentionsController extends BaseController
{
    public function index()
    {
        $this->render('mentions.html.twig', [
            'active_page' => 'mentions',
        ]);
    }
}
