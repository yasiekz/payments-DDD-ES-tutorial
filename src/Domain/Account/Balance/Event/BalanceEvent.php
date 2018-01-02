<?php

namespace App\Domain\Account\Balance\Event;

use App\Domain\DomainEvent;

abstract class BalanceEvent implements DomainEvent
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}