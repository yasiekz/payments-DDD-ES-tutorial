<?php

namespace App\Tests\Infrastructure\Cqrs;

use App\Infrastructure\Cqrs\CommandHandlersManager;
use App\Infrastructure\Cqrs\Handler\CommandHandlerInterface;
use PHPUnit\Framework\TestCase;

class CommandHandlersManagerTest extends TestCase
{
    public function testObject()
    {
        $manager = new CommandHandlersManager();

        $handler1 = $this->createMock(CommandHandlerInterface::class);
        $handler2 = $this->createMock(CommandHandlerInterface::class);

        $manager->registerHandler($handler1);
        $manager->registerHandler($handler2);

        $this->assertEquals([$handler1, $handler2], $manager->getHandlers());
    }
}