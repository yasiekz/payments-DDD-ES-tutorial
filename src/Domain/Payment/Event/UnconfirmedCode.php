<?php

namespace App\Domain\Payment\Event;

use App\Domain\DomainEvent;

class UnconfirmedCode extends DomainEvent
{
    /**
     * @var string
     */
    private $code;

    /**
     * @param string $id
     * @param string $code
     */
    public function __construct(string $id, string $code)
    {
        $this->id = $id;
        $this->code = $code;
    }
}