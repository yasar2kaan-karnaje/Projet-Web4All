<?php

namespace Tests\Controller;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Contrôleur Administrateur (AdminController)')]
class AdminControllerTest extends TestCase
{
    #[TestDox('Le contrôleur Admin hérite bien du contrôleur de base')]
    public function testAdminControllerInheritsBaseController()
    {
        // Instanciation simple pour s'assurer que la classe existe et hérite correctement sans lever d'erreur
        // Note: $_SESSION et $_SERVER sont souvent indisponibles en CLI, nous n'appelons donc pas de méthodes nécessitant ces globales.
        $reflection = new \ReflectionClass(\App\Controller\AdminController::class);
        
        $this->assertTrue(
            $reflection->isSubclassOf(\App\Controller\BaseController::class),
            'AdminController doit hériter de BaseController'
        );
    }
}
