<?php

namespace Tests\Model;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use App\Model\Database;
use PDO;

#[TestDox('Base de Données')]
class DatabaseTest extends TestCase
{
    #[TestDox('L\'instance Singleton retourne bien une connexion PDO valide')]
    public function testSingletonInstanceReturnsPDO()
    {
        $instance1 = Database::getInstance();
        $this->assertInstanceOf(PDO::class, $instance1);

        $instance2 = Database::getInstance();
        $this->assertSame($instance1, $instance2, 'Database::getInstance should return the same singleton instance.');
    }
}
