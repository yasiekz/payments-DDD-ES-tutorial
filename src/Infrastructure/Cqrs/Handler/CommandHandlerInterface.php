<?php

namespace App\Infrastructure\Cqrs\Handler;

use App\Infrastructure\Cqrs\Command;

interface CommandHandlerInterface
{
    /**
     * @param Command $command
     */
    public function handle(Command $command): void;

    /**
     * @param Command $command
     * @return bool
     */
    public function canHandle(Command $command): bool;
}