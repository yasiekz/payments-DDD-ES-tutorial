<?php

namespace App\Domain\Payment\Event;

use App\Domain\DomainEvent;

class PaymentConfirmed extends DomainEvent
{
    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }
}