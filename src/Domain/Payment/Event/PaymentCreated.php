<?php

namespace App\Domain\Payment\Event;

use App\Domain\DomainEvent;
use App\Domain\Payment\Code;
use Money\Money;

final class PaymentCreated extends DomainEvent
{
    /**
     * @var string
     */
    private $accountFrom;
    /**
     * @var string
     */
    private $accountTo;
    /**
     * @var Money
     */
    private $amount;
    /**
     * @var string
     */
    private $code;

    /**
     * @param string $id
     * @param string $accountFrom
     * @param string $accountTo
     * @param Money $amount
     * @param Code $code
     */
    public function __construct(string $id, string $accountFrom, string $accountTo, Money $amount, Code $code)
    {
        $this->id = $id;
        $this->accountFrom = $accountFrom;
        $this->accountTo = $accountTo;
        $this->amount = $amount;
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getAccountFrom(): string
    {
        return $this->accountFrom;
    }

    /**
     * @return string
     */
    public function getAccountTo(): string
    {
        return $this->accountTo;
    }

    /**
     * @return Money
     */
    public function getAmount(): Money
    {
        return $this->amount;
    }

    /**
     * @return Code
     */
    public function getCode(): Code
    {
        return $this->code;
    }
}