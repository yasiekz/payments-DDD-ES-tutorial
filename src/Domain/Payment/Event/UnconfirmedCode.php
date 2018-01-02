<?php

namespace App\Domain\Payment\Event;

use App\Domain\DomainEvent;

class UnconfirmedCode implements DomainEvent
{
    /**
     * @var string
     */
    private $id;

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

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}