<?php

namespace App\Domain\Account\Balance\Event;

use App\Domain\DomainEvent;

abstract class BalanceEvent extends DomainEvent
{
    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }
}