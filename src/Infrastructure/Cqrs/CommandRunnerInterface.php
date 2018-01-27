<?php

namespace App\Infrastructure\Cqrs;

interface CommandRunnerInterface
{
    /**
     * @param Command $command
     */
    public function dispatch(Command $command): void;
}