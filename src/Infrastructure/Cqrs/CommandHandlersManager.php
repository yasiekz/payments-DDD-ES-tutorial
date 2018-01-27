<?php

namespace App\Infrastructure\Cqrs;

use App\Infrastructure\Cqrs\Handler\CommandHandlerInterface;

class CommandHandlersManager
{
    /**
     * @var CommandHandlerInterface[]
     */
    private $handlers = [];

    /**
     * @param CommandHandlerInterface $handler
     */
    public function registerHandler(CommandHandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * @return CommandHandlerInterface[]
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }
}