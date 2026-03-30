<?php

namespace Tests\Controller;

use PHPUnit\Framework\TestCase;

class AdminControllerTest extends TestCase
{
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
