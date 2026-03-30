<?php

namespace Tests\Model;

use PHPUnit\Framework\TestCase;
use App\Model\Database;
use PDO;

class DatabaseTest extends TestCase
{
    public function testSingletonInstanceReturnsPDO()
    {
        $instance1 = Database::getInstance();
        $this->assertInstanceOf(PDO::class, $instance1);

        $instance2 = Database::getInstance();
        $this->assertSame($instance1, $instance2, 'Database::getInstance should return the same singleton instance.');
    }
}
