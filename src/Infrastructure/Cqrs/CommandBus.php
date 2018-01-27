<?php

namespace App\Infrastructure\Cqrs;

class CommandBus implements CommandRunnerInterface
{
    /**
     * @var CommandHandlersManager
     */
    private $commandHandlersManager;

    /**
     * @param CommandHandlersManager $commandHandlersManager
     */
    public function __construct(CommandHandlersManager $commandHandlersManager)
    {
        $this->commandHandlersManager = $commandHandlersManager;
    }

    /**
     * @inheritdoc
     * @throws HandlerNotFoundException
     */
    public function dispatch(Command $command): void
    {
        foreach ($this->commandHandlersManager->getHandlers() as $handler) {
            if ($handler->canHandle($command)) {
                $handler->handle($command);

                return;
            }
        }

        throw new HandlerNotFoundException('Handler for command '.get_class($command).' does not exist');
    }
}