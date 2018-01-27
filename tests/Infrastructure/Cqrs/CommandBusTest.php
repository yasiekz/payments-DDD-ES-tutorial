<?php

namespace App\Tests\Infrastructure\Cqrs;

use App\Infrastructure\Cqrs\Command;
use App\Infrastructure\Cqrs\CommandBus;
use App\Infrastructure\Cqrs\CommandHandlersManager;
use App\Infrastructure\Cqrs\Handler\CommandHandlerInterface;
use App\Infrastructure\Cqrs\HandlerNotFoundException;
use PHPUnit\Framework\TestCase;

class CommandBusTest extends TestCase
{
    private $command1;
    private $command2;

    private $handler1;
    private $handler2;

    private $bus;

    public function setUp()
    {
        $manager = $this->createMock(CommandHandlersManager::class);

        $this->command1 = $this->createMock(Command::class);
        $this->command2 = $this->createMock(Command::class);

        $this->handler1 = $this->createMock(CommandHandlerInterface::class);
        $this->handler2 = $this->createMock(CommandHandlerInterface::class);

        $manager->method('getHandlers')->willReturn([$this->handler1, $this->handler2]);

        $this->bus = new CommandBus($manager);
    }

    public function testObject()
    {
        $map1 = [
            [$this->command1, true],
            [$this->command2, false],
        ];

        $map2 = [
            [$this->command1, false],
            [$this->command2, true],
        ];

        $this->handler1->method('canHandle')->will($this->returnValueMap($map1));
        $this->handler2->method('canHandle')->will($this->returnValueMap($map2));

        $this->handler1->expects($this->exactly(2))->method('canHandle');
        // method below should be called only once because in first invoke loop will be stopped after first transition
        $this->handler2->expects($this->exactly(1))->method('canHandle');

        $this->handler1->expects($this->exactly(1))->method('handle');
        $this->handler2->expects($this->exactly(1))->method('handle');

        $this->bus->dispatch($this->command1);
        $this->bus->dispatch($this->command2);
    }

    public function testExceptionInDispatch()
    {
        $this->expectException(HandlerNotFoundException::class);
        $this->handler1->method('canHandle')->willReturn(false);
        $this->handler2->method('canHandle')->willReturn(false);

        $command = $this->createMock(Command::class);
        $this->bus->dispatch($command);
    }
}